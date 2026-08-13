@extends('layouts.app')

@section('title', 'Konten & Warta')
@section('breadcrumb', 'Pengurus / Manajemen Konten & Warta')

@section('content')
<div class="mx-auto max-w-6xl">
    @include('partials.flash')

    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-forest">Manajemen Konten & Warta</h2>
        <p class="mt-1 text-sm text-stone-500">Publikasikan renungan dan pengumuman GBI Pondok Daud</p>
    </div>

    <div class="mb-6 rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
        <form action="{{ route('pengurus.konten.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-stone-500">Kategori</label>
                    <select name="kategori" required class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
                        <option value="Renungan Harian">Renungan Harian</option>
                        <option value="Berita Kegiatan">Berita Kegiatan</option>
                        <option value="Pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-stone-500">Judul Artikel</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-500">Isi Materi</label>
                <textarea name="isi" rows="5" required minlength="20" class="w-full resize-none rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">{{ old('isi') }}</textarea>
            </div>
            <button type="submit" class="rounded-xl bg-gold px-6 py-2.5 text-sm font-bold text-forest hover:bg-gold-soft">
                <i class="fa-solid fa-upload mr-1"></i> Publikasikan
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white ring-1 ring-stone-200/80">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-stone-100 bg-cream/80">
                <tr>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Judul</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Kategori</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Penulis</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Tanggal</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach ($kontens as $konten)
                    <tr class="hover:bg-cream/50">
                        <td class="px-5 py-4 font-semibold text-forest">{{ $konten->judul }}</td>
                        <td class="px-5 py-4"><span class="rounded-full bg-cream px-2.5 py-0.5 text-[10px] font-bold text-forest-mid">{{ $konten->kategori }}</span></td>
                        <td class="px-5 py-4 text-stone-500">{{ $konten->author->name }}</td>
                        <td class="px-5 py-4 text-stone-500">{{ $konten->published_at?->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <form action="{{ route('pengurus.konten.destroy', $konten) }}" method="POST" onsubmit="return confirm('Hapus konten ini?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg bg-red-50 px-2.5 py-1.5 text-xs text-red-500"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
