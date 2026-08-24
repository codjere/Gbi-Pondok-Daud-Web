<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\JadwalIbadah;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        try {
            DB::transaction(function () use ($user, $jadwal) {
                $locked = JadwalIbadah::whereKey($jadwal->id)->lockForUpdate()->firstOrFail();

                if ($locked->status !== 'terpublikasi') {
                    throw new \RuntimeException('Sesi ibadah ini tidak tersedia untuk reservasi.');
                }

                if ($locked->isPenuh()) {
                    throw new \RuntimeException('Mohon maaf, kuota untuk sesi ibadah ini telah penuh.');
                }

                $sudahReservasi = Reservasi::where('user_id', $user->id)
                    ->where('jadwal_ibadah_id', $locked->id)
                    ->exists();

                if ($sudahReservasi) {
                    throw new \RuntimeException('Anda sudah terdaftar pada sesi ibadah ini.');
                }

                Reservasi::create([
                    'user_id' => $user->id,
                    'jadwal_ibadah_id' => $locked->id,
                    'status' => 'confirmed',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->with('error', 'Anda sudah terdaftar pada sesi ibadah ini.');
        }

        return back()->with('success', 'Reservasi berhasil! Anda siap diverifikasi kamera pada hari-H.');
    }
}
