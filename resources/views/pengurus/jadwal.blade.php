@extends('layouts.app')

@section('title', 'Kelola Jadwal')
@section('breadcrumb', 'Pengurus / Manajemen Jadwal Ibadah')

@section('content')
<div class="mx-auto max-w-6xl">
    @include('partials.flash')

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-forest">Manajemen Jadwal Ibadah</h2>
            <p class="mt-1 text-sm text-stone-500">GBI Pondok Daud — CRUD agenda operasional gereja</p>
        </div>
    </div>

    <div class="mb-6 rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
        <p class="mb-4 text-sm font-bold text-forest">Tambah Jadwal Baru</p>
        <form action="{{ route('pengurus.jadwal.store') }}" method="POST" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-500">Nama Ibadah</label>
                <input type="text" name="nama_sesi" value="{{ old('nama_sesi') }}" required placeholder="Ibadah Raya 1"
                       class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-500">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" required class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-500">Jam Mulai</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" required class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-500">Jam Selesai</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" required class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-500">Pembicara</label>
                <select name="pembicara" required class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
                    <option value="Pdt. Samsu Hadi">Pdt. Samsu Hadi</option>
                    <option value="Pdt. Lisna Tappi">Pdt. Lisna Tappi</option>
                    <option value="Pdt. Ficky Benyaminsz">Pdt. Ficky Benyaminsz</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-stone-500">Batas Kuota</label>
                <input type="number" name="batas_kuota" value="{{ old('batas_kuota', 200) }}" required min="1" class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <button type="submit" class="rounded-xl bg-gold px-6 py-2.5 text-sm font-bold text-forest hover:bg-gold-soft">
                    <i class="fa-solid fa-plus mr-1"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white ring-1 ring-stone-200/80">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-stone-100 bg-cream/80">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Nama Sesi</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Tanggal & Jam</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Pembicara</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Kuota</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-stone-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($jadwals as $jadwal)
                        <tr class="hover:bg-cream/50">
                            <td class="px-5 py-4 font-semibold text-forest">{{ $jadwal->nama_sesi }}</td>
                            <td class="px-5 py-4 text-stone-500">{{ $jadwal->tanggal->format('d M Y') }} · {{ $jadwal->jamMulaiFormatted() }}</td>
                            <td class="px-5 py-4 text-stone-500">{{ $jadwal->pembicara }}</td>
                            <td class="px-5 py-4 font-medium">{{ $jadwal->jumlahTerisi() }}/{{ $jadwal->batas_kuota }}</td>
                            <td class="px-5 py-4">
                                @if ($jadwal->isPenuh())
                                    <span class="rounded-full bg-red-50 px-2.5 py-0.5 text-[10px] font-bold text-red-600">Penuh</span>
                                @elseif ($jadwal->status === 'selesai')
                                    <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-[10px] font-bold text-stone-500">Selesai</span>
                                @else
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">Terpublikasi</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <form action="{{ route('pengurus.jadwal.destroy', $jadwal) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg bg-red-50 px-2.5 py-1.5 text-xs text-red-500 hover:bg-red-100"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
