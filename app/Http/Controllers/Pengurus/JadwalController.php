<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\JadwalIbadah;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = JadwalIbadah::orderByDesc('tanggal')->orderBy('jam_mulai')->get();

        return view('pengurus.jadwal', compact('jadwals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_sesi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required'],
            'jam_selesai' => ['required', 'after:jam_mulai'],
            'pembicara' => ['required', 'string', 'max:255'],
            'batas_kuota' => ['required', 'integer', 'min:1'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
        ]);

        JadwalIbadah::create([
            ...$data,
            'lokasi' => $data['lokasi'] ?? 'Ruang Utama',
            'status' => 'terpublikasi',
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan dan terpublikasi.');
    }

    public function destroy(JadwalIbadah $jadwal)
    {
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
