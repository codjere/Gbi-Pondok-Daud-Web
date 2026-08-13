@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('breadcrumb', 'Pengurus / Laporan & Rekapitulasi')

@section('content')
<div class="mx-auto max-w-6xl">
    @include('partials.flash')

    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest">Laporan Evaluasi Kehadiran</h2>
        <p class="mt-1 text-sm text-stone-500">Rekapitulasi kehadiran jemaat GBI Pondok Daud · verifikasi FaceNet</p>
    </div>

    <form method="GET" action="{{ route('pengurus.laporan') }}" class="mb-6 flex flex-wrap gap-3 rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
        <div class="flex-1 min-w-[200px]">
            <label class="mb-1 block text-xs font-semibold text-stone-500">Pilih Sesi Ibadah</label>
            <select name="jadwal_id" class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
                @foreach ($jadwals as $j)
                    <option value="{{ $j->id }}" @selected($jadwal?->id === $j->id)>
                        {{ $j->nama_sesi }} — {{ $j->tanggal->format('d M Y') }} ({{ substr($j->jam_mulai, 0, 5) }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-xl bg-forest px-5 py-2.5 text-sm font-bold text-white hover:bg-forest-mid">Tampilkan</button>
        </div>
    </form>

    @if ($jadwal)
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
                <p class="text-xs font-semibold text-stone-400">Total Hadir</p>
                <p class="mt-1 text-3xl font-extrabold text-forest">{{ $stats['hadir'] }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
                <p class="text-xs font-semibold text-stone-400">Kuota Ruangan</p>
                <p class="mt-1 text-3xl font-extrabold text-forest">{{ $stats['kuota'] }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
                <p class="text-xs font-semibold text-stone-400">Tingkat Kehadiran</p>
                <p class="mt-1 text-3xl font-extrabold text-emerald-600">{{ $stats['pct'] }}%</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white ring-1 ring-stone-200/80">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                <p class="text-sm font-bold text-forest">Daftar Hadir — {{ $jadwal->nama_sesi }}</p>
                @if ($kehadiran->isNotEmpty())
                    <button class="inline-flex items-center gap-2 rounded-xl bg-gold px-4 py-2 text-xs font-bold text-forest">
                        <i class="fa-solid fa-download"></i> Unduh Laporan PDF
                    </button>
                @else
                    <button disabled class="inline-flex items-center gap-2 rounded-xl bg-stone-100 px-4 py-2 text-xs font-bold text-stone-400 cursor-not-allowed">
                        Unduh Laporan PDF
                    </button>
                @endif
            </div>

            @if ($kehadiran->isEmpty())
                <div class="p-10 text-center text-sm text-stone-500">
                    Belum ada data kehadiran untuk sesi ibadah ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-stone-100 bg-cream/80">
                            <tr>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-400">No</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-400">Nama</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-400">Status</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-400">Email</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-400">Waktu Check-in</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-400">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($kehadiran as $i => $h)
                                <tr class="hover:bg-cream/50">
                                    <td class="px-5 py-3.5 text-stone-400">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-forest">{{ $h->user->name }}</td>
                                    <td class="px-5 py-3.5"><span class="rounded-full bg-gold-soft px-2 py-0.5 text-[10px] font-bold capitalize text-forest">{{ $h->user->role }}</span></td>
                                    <td class="px-5 py-3.5 text-stone-500">{{ $h->user->email }}</td>
                                    <td class="px-5 py-3.5 text-stone-500">{{ $h->waktu_checkin->format('H:i') }} WIB</td>
                                    <td class="px-5 py-3.5"><span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">{{ $h->metode_verifikasi }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
