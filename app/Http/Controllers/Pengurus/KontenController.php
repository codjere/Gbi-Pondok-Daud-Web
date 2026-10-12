<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\KontenWarta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KontenController extends Controller
{
    public function index()
    {
        $kontens = KontenWarta::with('author')->latest()->get();

        return view('pengurus.konten', compact('kontens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori' => ['required', 'in:Renungan Harian,Berita Kegiatan,Pengumuman'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string', 'min:20'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], // maks 4 MB
        ], [
            'gambar.image' => 'Berkas harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 4 MB.',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('warta', 'public');
        }

        KontenWarta::create([
            'user_id' => Auth::id(),
            'kategori' => $data['kategori'],
            'judul' => $data['judul'],
            'slug' => Str::slug($data['judul']).'-'.Str::random(5),
            'isi' => $data['isi'],
            'gambar' => $gambar,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Konten berhasil dipublikasikan.');
    }

    public function destroy(KontenWarta $konten)
    {
        // Hapus file gambar dari storage bila ada, sebelum menghapus record.
        if ($konten->gambar) {
            Storage::disk('public')->delete($konten->gambar);
        }

        $konten->delete();

        return back()->with('success', 'Konten berhasil dihapus.');
    }
}
