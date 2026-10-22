<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\JadwalIbadah;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = JadwalIbadah::aktif()->terbaru()->get();
        $reservasiUser = Auth::user()
            ->reservasi()
            ->pluck('jadwal_ibadah_id')
            ->toArray();

        // Morning Prayer: kegiatan harian tetap jam 05:00 WITA yang selalu ada.
        // Disuntikkan sebagai entri virtual (tidak disimpan di DB) agar selalu
        // muncul setiap hari tanpa perlu scheduler. Bukan ibadah ber-kuota, jadi
        // ditandai is_daily agar view tidak menampilkan reservasi/kuota.
        $jadwals->prepend(JadwalIbadah::morningPrayer());

        return view('jemaat.jadwal', compact('jadwals', 'reservasiUser'));
    }
}
