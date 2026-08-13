<?php

namespace App\Services;

use App\Models\CoolGroup;
use App\Models\TimPelayanan;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(3)->get($this->url('/health'));

            return $response->successful();
        } catch (ConnectionException) {
            return false;
        }
    }

    /**
     * @return array{tim_pelayanan: array, cool_groups: array, weights: array, hybrid: array}|null
     */
    public function generate(User $user, UserProfile $profile): ?array
    {
        $payload = [
            'profile' => [
                'usia' => $profile->usia,
                'kecamatan' => $profile->kecamatan ?? '',
                'minat' => $profile->minat ?? [],
                'bakat' => $profile->bakat ?? [],
                'ketersediaan_hari' => $profile->ketersediaan_hari ?? [],
                'ketersediaan_waktu' => $profile->ketersediaan_waktu ?? 'fleksibel',
            ],
            'tim_pelayanan' => TimPelayanan::all()->map(fn ($t) => [
                'id' => $t->id,
                'nama' => $t->nama,
                'minat_tags' => $t->minat_tags,
                'bakat_tags' => $t->bakat_tags,
                'min_usia' => $t->min_usia,
                'max_usia' => $t->max_usia,
                'hari_pelayanan' => $t->hari_pelayanan,
            ])->values()->all(),
            'cool_groups' => CoolGroup::all()->map(fn ($c) => [
                'id' => $c->id,
                'nama' => $c->nama,
                'kecamatan' => $c->kecamatan,
                'tema_minat' => $c->tema_minat,
                'min_usia' => $c->min_usia,
                'max_usia' => $c->max_usia,
                'jadwal' => $c->jadwal,
            ])->values()->all(),
            'other_users' => $this->buildCollaborativeData($user),
            'top_k' => config('ai.top_k', 3),
        ];

        try {
            if ($this->isAvailable()) {
                $response = Http::timeout(config('ai.timeout'))
                    ->post($this->url('/recommend'), $payload);

                if ($response->successful()) {
                    return $response->json();
                }
            }

            return $this->fallbackRecommend($payload);
        } catch (ConnectionException $e) {
            Log::warning('AI recommendation service unreachable', ['error' => $e->getMessage()]);

            return $this->fallbackRecommend($payload);
        }
    }

    private function buildCollaborativeData(User $user): array
    {
        return UserProfile::whereNotNull('completed_at')
            ->where('user_id', '!=', $user->id)
            ->with(['user.pelayananChoices', 'user.coolChoices'])
            ->get()
            ->map(fn (UserProfile $p) => [
                'user_id' => $p->user_id,
                'minat' => $p->minat ?? [],
                'bakat' => $p->bakat ?? [],
                'kecamatan' => $p->kecamatan ?? '',
                'pelayanan_ids' => $p->user->pelayananChoices->pluck('tim_pelayanan_id')->all(),
                'cool_ids' => $p->user->coolChoices->pluck('cool_group_id')->all(),
            ])
            ->all();
    }

    private function fallbackRecommend(array $payload): array
    {
        $profile = $payload['profile'];
        $timResults = collect($payload['tim_pelayanan'])->map(function ($item) use ($profile) {
            $sMinat = $this->jaccard($profile['minat'], $item['minat_tags']);
            $sBakat = $this->jaccard($profile['bakat'], $item['bakat_tags']);
            $score = 0.55 * $sMinat + 0.45 * $sBakat;

            return [
                'id' => $item['id'],
                'nama' => $item['nama'],
                'score' => round($score, 4),
                'score_pct' => round($score * 100, 1),
                'content_score' => ['total' => round($score, 4), 'minat' => $sMinat, 'bakat' => $sBakat],
                'collab_score' => 0,
                'formula' => 'fallback content-based',
            ];
        })->sortByDesc('score')->values()->take(config('ai.top_k', 3))->all();

        $coolResults = collect($payload['cool_groups'])->map(function ($item) use ($profile) {
            $sMinat = $this->jaccard($profile['minat'], $item['tema_minat']);
            $sLokasi = strtolower($profile['kecamatan'] ?? '') === strtolower($item['kecamatan']) ? 1.0 : 0.3;
            $score = 0.5 * $sMinat + 0.5 * $sLokasi;

            return [
                'id' => $item['id'],
                'nama' => $item['nama'],
                'kecamatan' => $item['kecamatan'],
                'score' => round($score, 4),
                'score_pct' => round($score * 100, 1),
                'content_score' => ['total' => round($score, 4), 'minat' => $sMinat, 'lokasi' => $sLokasi],
                'collab_score' => 0,
                'formula' => 'fallback content-based',
            ];
        })->sortByDesc('score')->values()->take(config('ai.top_k', 3))->all();

        return [
            'tim_pelayanan' => $timResults,
            'cool_groups' => $coolResults,
            'weights' => ['minat' => 0.30, 'bakat' => 0.25],
            'hybrid' => ['alpha_content' => 0.60, 'beta_collab' => 0.40],
        ];
    }

    private function jaccard(array $a, array $b): float
    {
        $setA = collect($a)->map(fn ($v) => strtolower($v))->unique();
        $setB = collect($b)->map(fn ($v) => strtolower($v))->unique();
        $inter = $setA->intersect($setB)->count();
        $union = $setA->merge($setB)->unique()->count();

        return $union > 0 ? round($inter / $union, 4) : 0.0;
    }

    private function url(string $path): string
    {
        return rtrim(config('ai.service_url'), '/').$path;
    }
}
