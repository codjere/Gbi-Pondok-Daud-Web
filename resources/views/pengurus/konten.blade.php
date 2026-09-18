@extends('layouts.app')

@section('title', 'Konten & Warta')
@section('breadcrumb', 'Pengurus / Manajemen Konten & Warta')

@section('content')
<div class="mx-auto max-w-6xl pattern-dots">
    @include('partials.flash')

    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Manajemen Konten & Warta</h2>
        <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">Publikasikan renungan dan pengumuman GBI Pondok Daud</p>
    </div>

    <div class="mb-6 rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <form action="{{ route('pengurus.konten.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Kategori</label>
                    <select name="kategori" required class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
                        <option value="Renungan Harian">Renungan Harian</option>
                        <option value="Berita Kegiatan">Berita Kegiatan</option>
                        <option value="Pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Judul Artikel</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Isi Materi</label>
                <textarea name="isi" rows="5" required minlength="20" class="w-full resize-none rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">{{ old('isi') }}</textarea>
            </div>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-gold to-amber-500 px-6 py-2.5 text-sm font-bold text-forest shadow-md hover:shadow-lg transition hover:from-amber-400 hover:to-orange-400">
                <i class="fa-solid fa-upload mr-1"></i> Publikasikan
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-cyan-200/50 bg-gradient-to-r from-cyan-50 to-sky-50 dark:border-slate-600 dark:from-slate-600 dark:to-slate-500">
                <tr>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Judul</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Kategori</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Penulis</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Tanggal</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cyan-200/50 dark:divide-slate-600">
                @foreach ($kontens as $konten)
                    <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-sky-50 dark:hover:from-slate-600 dark:hover:to-slate-700">
                        <td class="px-5 py-4 font-semibold text-forest dark:text-cyan-400">{{ $konten->judul }}</td>
                        <td class="px-5 py-4"><span class="rounded-full bg-gradient-to-r from-cyan-100 to-sky-100 px-2.5 py-0.5 text-[10px] font-bold text-cyan-700 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">{{ $konten->kategori }}</span></td>
                        <td class="px-5 py-4 text-stone-600 dark:text-slate-400">{{ $konten->author->name }}</td>
                        <td class="px-5 py-4 text-stone-600 dark:text-slate-400">{{ $konten->published_at?->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <form action="{{ route('pengurus.konten.destroy', $konten) }}" method="POST" onsubmit="return confirm('Hapus konten ini?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg bg-gradient-to-r from-red-50 to-orange-50 px-2.5 py-1.5 text-xs text-red-600 hover:from-red-100 hover:to-orange-100 dark:from-slate-600 dark:to-slate-500 dark:text-red-400"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
