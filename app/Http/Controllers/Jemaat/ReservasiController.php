<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\JadwalIbadah;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservasiController extends Controller
{
    public function index()
    {
        $jadwals = JadwalIbadah::aktif()->terbaru()->get();
        $reservasiUser = Auth::user()
            ->reservasi()
            ->pluck('jadwal_ibadah_id')
            ->toArray();

        return view('jemaat.reservasi', compact('jadwals', 'reservasiUser'));
    }

    public function store(Request $request, JadwalIbadah $jadwal)
    {
        $user = Auth::user();

        if ($jadwal->status !== 'terpublikasi') {
            return back()->with('error', 'Sesi ibadah ini tidak tersedia untuk reservasi.');
        }

        if ($jadwal->isPenuh()) {
            return back()->with('error', 'Mohon maaf, kuota untuk sesi ibadah ini telah penuh.');
        }

        $sudahReservasi = Reservasi::where('user_id', $user->id)
            ->where('jadwal_ibadah_id', $jadwal->id)
            ->exists();

        if ($sudahReservasi) {
            return back()->with('error', 'Anda sudah terdaftar pada sesi ibadah ini.');
        }

        Reservasi::create([
            'user_id' => $user->id,
            'jadwal_ibadah_id' => $jadwal->id,
            'status' => 'confirmed',
        ]);

        return back()->with('success', 'Reservasi berhasil! Anda siap diverifikasi kamera pada hari-H.');
    }
}
