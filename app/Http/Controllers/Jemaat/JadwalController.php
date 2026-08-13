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

        return view('jemaat.jadwal', compact('jadwals', 'reservasiUser'));
    }
}
