@extends('layouts.app')

@section('title', 'Kelola Jadwal')
@section('breadcrumb', 'Pengurus / Manajemen Jadwal Ibadah')

@section('content')
<div class="mx-auto max-w-6xl pattern-dots">
    @include('partials.flash')

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Manajemen Jadwal Ibadah</h2>
            <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">GBI Pondok Daud — CRUD agenda operasional gereja</p>
        </div>
    </div>

    <div class="mb-6 rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <p class="mb-4 text-sm font-bold text-forest dark:text-cyan-400">Tambah Jadwal Baru</p>
        <form action="{{ route('pengurus.jadwal.store') }}" method="POST" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Nama Ibadah</label>
                <input type="text" name="nama_sesi" value="{{ old('nama_sesi') }}" required placeholder="Ibadah Raya 1"
                       class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="2" placeholder="Deskripsi kegiatan..."
                          class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">{{ old('deskripsi') }}</textarea>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" required class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Jam Mulai</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" required class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Jam Selesai</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" required class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Pembicara</label>
                <select name="pembicara" required class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
                    <option value="Pdt. Ficky Benyaminsz">Pdt. Ficky Benyaminsz</option>
                    <option value="Pdt. Lisna Tappi">Pdt. Lisna Tappi</option>
                    <option value="Pdt. Samsu Hadi">Pdt. Samsu Hadi</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Batas Kuota</label>
                <input type="number" name="batas_kuota" value="{{ old('batas_kuota', 200) }}" required min="1" class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Link Meeting / Zoom (Opsional)</label>
                <input type="url" name="meeting_link" value="{{ old('meeting_link') }}" placeholder="https://zoom.us/j/..."
                       class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <button type="submit" class="rounded-xl bg-gradient-to-r from-gold to-amber-500 px-6 py-2.5 text-sm font-bold text-forest shadow-md hover:shadow-lg transition hover:from-amber-400 hover:to-orange-400">
                    <i class="fa-solid fa-plus mr-1"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-cyan-200/50 bg-gradient-to-r from-cyan-50 to-sky-50 dark:border-slate-600 dark:from-slate-600 dark:to-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Nama Sesi</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Tanggal & Jam</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Pembicara</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Kuota</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cyan-200/50 dark:divide-slate-600">
                    @foreach ($jadwals as $jadwal)
                        <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-sky-50 dark:hover:from-slate-600 dark:hover:to-slate-700">
                            <td class="px-5 py-4 font-semibold text-forest dark:text-cyan-400">
                                {{ $jadwal->nama_sesi }}
                                @if ($jadwal->meeting_link)
                                    <a href="{{ $jadwal->meeting_link }}" target="_blank" rel="noopener" class="ml-1 text-xs font-medium text-cyan-600 hover:underline dark:text-cyan-400"><i class="fa-solid fa-video"></i> Link</a>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-stone-600 dark:text-slate-400">{{ $jadwal->tanggal->format('d M Y') }} · {{ $jadwal->jamMulaiFormatted() }}</td>
                            <td class="px-5 py-4 text-stone-600 dark:text-slate-400">{{ $jadwal->pembicara }}</td>
                            <td class="px-5 py-4 font-medium text-stone-600 dark:text-slate-400">{{ $jadwal->jumlahTerisi() }}/{{ $jadwal->batas_kuota }}</td>
                            <td class="px-5 py-4">
                                @if ($jadwal->isPenuh())
                                    <span class="rounded-full bg-gradient-to-r from-red-50 to-orange-50 px-2.5 py-0.5 text-[10px] font-bold text-red-600 dark:from-slate-600 dark:to-slate-500 dark:text-red-400">Penuh</span>
                                @elseif ($jadwal->status === 'selesai')
                                    <span class="rounded-full bg-gradient-to-r from-stone-100 to-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-stone-500 dark:from-slate-600 dark:to-slate-500 dark:text-slate-400">Selesai</span>
                                @else
                                    <span class="rounded-full bg-gradient-to-r from-emerald-50 to-green-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:from-slate-600 dark:to-slate-500 dark:text-emerald-400">Terpublikasi</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('pengurus.absen.participants') }}?jadwal_id={{ $jadwal->id }}"
                                       class="rounded-lg bg-gradient-to-r from-cyan-100 to-sky-100 px-2.5 py-1.5 text-xs text-cyan-600 hover:from-cyan-200 hover:to-sky-200 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                                        <i class="fa-solid fa-users"></i>
                                    </a>
                                    <form action="{{ route('pengurus.jadwal.destroy', $jadwal) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg bg-gradient-to-r from-red-50 to-orange-50 px-2.5 py-1.5 text-xs text-red-600 hover:from-red-100 hover:to-orange-100 dark:from-slate-600 dark:to-slate-500 dark:text-red-400"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
