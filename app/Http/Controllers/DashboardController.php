<?php

namespace App\Http\Controllers;

use App\Models\JadwalIbadah;
use App\Models\KontenWarta;
use App\Models\PermohonanDoa;
use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'jemaat' => User::where('role', 'jemaat')->count(),
            'reservasi_minggu' => Reservasi::where('status', 'confirmed')
                ->whereHas('jadwalIbadah', fn ($q) => $q->whereBetween('tanggal', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ]))->count(),
            'permohonan_doa' => PermohonanDoa::where('status', 'pending')->count(),
            'kehadiran_pct' => $this->hitungPersenKehadiran(),
        ];

        $jadwalTerdekat = JadwalIbadah::aktif()->terbaru()->take(3)->get();
        $renungan = KontenWarta::published()->with('author')->latest('published_at')->first();

        return view('dashboard', compact('user', 'stats', 'jadwalTerdekat', 'renungan'));
    }

    private function hitungPersenKehadiran(): int
    {
        $jadwalTerakhir = JadwalIbadah::where('status', 'selesai')
            ->orderByDesc('tanggal')
            ->first();

        if (! $jadwalTerakhir || $jadwalTerakhir->batas_kuota === 0) {
            return 0;
        }

        $hadir = $jadwalTerakhir->kehadiran()->count();

        return (int) round(($hadir / $jadwalTerakhir->batas_kuota) * 100);
    }
}
