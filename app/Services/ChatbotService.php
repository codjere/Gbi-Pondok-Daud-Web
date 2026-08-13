<?php

namespace App\Services;

use App\Models\FaqItem;
use App\Models\JadwalIbadah;
use App\Models\KontenWarta;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    public function isAvailable(): bool
    {
        try {
            return Http::timeout(3)->get($this->url('/health'))->successful();
        } catch (ConnectionException) {
            return false;
        }
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{answer: string, sources: array, intent: string}|null
     */
    public function chat(string $message, array $history = []): ?array
    {
        $payload = [
            'message' => $message,
            'documents' => $this->buildKnowledgeBase(),
            'history' => $history,
            'openai_api_key' => config('ai.openai_api_key', ''),
        ];

        try {
            if ($this->isAvailable()) {
                $response = Http::timeout(config('ai.timeout'))
                    ->post($this->url('/chat'), $payload);

                if ($response->successful()) {
                    return $response->json();
                }
            }

            return $this->fallbackChat($message);
        } catch (ConnectionException $e) {
            Log::warning('AI chatbot service unreachable', ['error' => $e->getMessage()]);

            return $this->fallbackChat($message);
        }
    }

    private function buildKnowledgeBase(): array
    {
        $docs = [];

        FaqItem::all()->each(function (FaqItem $faq) use (&$docs) {
            $docs[] = [
                'id' => 'faq-'.$faq->id,
                'title' => $faq->pertanyaan,
                'content' => $faq->jawaban,
                'category' => $faq->kategori,
            ];
        });

        JadwalIbadah::aktif()->terbaru()->get()->each(function (JadwalIbadah $j) use (&$docs) {
            $docs[] = [
                'id' => 'jadwal-'.$j->id,
                'title' => $j->nama_sesi,
                'content' => "Ibadah {$j->nama_sesi} pada {$j->tanggal->format('d M Y')} jam ".substr($j->jam_mulai, 0, 5)." WIB. Pembicara: {$j->pembicara}. Lokasi: {$j->lokasi}.",
                'category' => 'jadwal',
            ];
        });

        KontenWarta::published()->latest('published_at')->take(10)->get()->each(function (KontenWarta $k) use (&$docs) {
            $docs[] = [
                'id' => 'warta-'.$k->id,
                'title' => $k->judul,
                'content' => strip_tags($k->isi),
                'category' => $k->kategori,
            ];
        });

        $docs[] = [
            'id' => 'info-gereja',
            'title' => 'GBI Pondok Daud',
            'content' => 'GBI Pondok Daud adalah gereja yang melayani jemaat melalui ibadah, COOL/kelompok sel, tim pelayanan, pembaptisan, pernikahan, dan konseling dasar. Portal GBI Pondok Daud tersedia untuk reservasi ibadah, permohonan doa, dan rekomendasi pelayanan.',
            'category' => 'umum',
        ];

        return $docs;
    }

    private function fallbackChat(string $message): array
    {
        $msg = strtolower($message);

        $faq = FaqItem::all()->first(function (FaqItem $f) use ($msg) {
            $keywords = $f->keywords ?? [];
            foreach ($keywords as $kw) {
                if (str_contains($msg, strtolower($kw))) {
                    return true;
                }
            }

            return str_contains($msg, strtolower(substr($f->pertanyaan, 0, 10)));
        });

        if ($faq) {
            return [
                'answer' => $faq->jawaban,
                'sources' => [['title' => $faq->pertanyaan, 'score' => 0.9]],
                'intent' => $faq->kategori,
            ];
        }

        return [
            'answer' => 'Shalom! Saya Asisten GBI Pondok Daud. Tanyakan seputar jadwal ibadah, COOL, pelayanan, pembaptisan, atau renungan. 🙏',
            'sources' => [],
            'intent' => 'umum',
        ];
    }

    private function url(string $path): string
    {
        return rtrim(config('ai.service_url'), '/').$path;
    }
}
