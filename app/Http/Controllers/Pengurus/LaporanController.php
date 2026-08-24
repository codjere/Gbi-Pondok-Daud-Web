<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\JadwalIbadah;
use App\Models\Kehadiran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jadwals = JadwalIbadah::orderByDesc('tanggal')->get();
        $selectedId = $request->validate([
            'jadwal_id' => ['nullable', 'integer', 'exists:jadwal_ibadah,id'],
        ])['jadwal_id'] ?? $jadwals->first()?->id;

        $jadwal = $selectedId
            ? JadwalIbadah::find($selectedId)
            : null;

        $kehadiran = collect();
        $stats = ['hadir' => 0, 'kuota' => 0, 'pct' => 0];

        if ($jadwal) {
            $kehadiran = Kehadiran::with('user')
                ->where('jadwal_ibadah_id', $jadwal->id)
                ->orderBy('waktu_checkin')
                ->get();

            $stats['hadir'] = $kehadiran->count();
            $stats['kuota'] = $jadwal->batas_kuota;
            $stats['pct'] = $jadwal->batas_kuota > 0
                ? (int) round(($stats['hadir'] / $jadwal->batas_kuota) * 100)
                : 0;
        }

        return view('pengurus.laporan', compact('jadwals', 'jadwal', 'kehadiran', 'stats'));
    }
}
