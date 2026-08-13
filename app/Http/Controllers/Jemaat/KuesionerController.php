<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KuesionerController extends Controller
{
    public function __construct(
        private RecommendationService $recommendationService
    ) {}

    public function index()
    {
        $profile = Auth::user()->profile ?? new UserProfile(['user_id' => Auth::id()]);

        return view('jemaat.kuesioner', [
            'profile' => $profile,
            'minatOptions' => $this->minatOptions(),
            'bakatOptions' => $this->bakatOptions(),
            'hariOptions' => $this->hariOptions(),
            'kecamatanOptions' => $this->kecamatanOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usia' => ['required', 'integer', 'min:15', 'max:80'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'minat' => ['required', 'array', 'min:1'],
            'minat.*' => ['string'],
            'bakat' => ['required', 'array', 'min:1'],
            'bakat.*' => ['string'],
            'ketersediaan_hari' => ['required', 'array', 'min:1'],
            'ketersediaan_hari.*' => ['string'],
            'ketersediaan_waktu' => ['required', 'in:pagi,siang,sore,malam,fleksibel'],
        ]);

        $profile = Auth::user()->profile()->updateOrCreate(
            ['user_id' => Auth::id()],
            array_merge($data, ['completed_at' => now()])
        );

        $recommendations = $this->recommendationService->generate(Auth::user(), $profile);

        if ($recommendations) {
            session(['recommendations' => $recommendations]);
        }

        return redirect()->route('jemaat.rekomendasi')
            ->with('success', 'Profil tersimpan! Berikut rekomendasi AI untuk Anda.');
    }

    private function minatOptions(): array
    {
        return [
            'musik' => 'Musik & Worship',
            'multimedia' => 'Multimedia & Teknologi',
            'teknologi' => 'Teknologi IT',
            'melayani' => 'Melayani Jemaat',
            'anak_muda' => 'Pelayanan Anak Muda / Youth',
            'doa' => 'Doa & Intercessory',
            'evangelisme' => 'Evangelisme & Outreach',
            'sosial' => 'Pelayanan Sosial',
            'administrasi' => 'Administrasi & Organisasi',
            'kreatif' => 'Seni & Kreatif',
        ];
    }

    private function bakatOptions(): array
    {
        return [
            'vokal' => 'Vokal / Nyanyi',
            'gitar' => 'Gitar',
            'keyboard' => 'Keyboard / Piano',
            'drum' => 'Drum',
            'fotografi' => 'Fotografi',
            'video' => 'Videografi / Live Stream',
            'desain' => 'Desain Grafis',
            'public_speaking' => 'Public Speaking / MC',
            'organisasi' => 'Kepemimpinan & Organisasi',
            'komputer' => 'Komputer / Programming',
        ];
    }

    private function hariOptions(): array
    {
        return [
            'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
            'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu',
        ];
    }

    private function kecamatanOptions(): array
    {
        return [
            'Medan Kota', 'Medan Area', 'Medan Barat', 'Medan Timur',
            'Medan Denai', 'Medan Sunggal', 'Medan Helvetia', 'Medan Polonia',
            'Medan Maimun', 'Medan Petisah', 'Medan Amplas', 'Medan Johor',
        ];
    }
}
