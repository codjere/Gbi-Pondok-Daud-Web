@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('breadcrumb', 'Pengurus / Laporan & Rekapitulasi')

@section('content')
<div class="mx-auto max-w-6xl pattern-dots">
    @include('partials.flash')

    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Laporan Evaluasi Kehadiran</h2>
        <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">Rekapitulasi kehadiran jemaat GBI Pondok Daud · verifikasi FaceNet</p>
    </div>

    <form method="GET" action="{{ route('pengurus.laporan') }}" class="mb-6 flex flex-wrap gap-3 rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <div class="flex-1 min-w-[200px]">
            <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Pilih Sesi Ibadah</label>
            <select name="jadwal_id" class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
                @foreach ($jadwals as $j)
                    <option value="{{ $j->id }}" @selected($jadwal?->id === $j->id)>
                        {{ $j->nama_sesi }} — {{ $j->tanggal->format('d M Y') }} ({{ substr($j->jam_mulai, 0, 5) }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-xl bg-gradient-to-r from-forest to-forest-mid px-5 py-2.5 text-sm font-bold text-white shadow-md hover:shadow-lg transition hover:from-cyan-600 hover:to-sky-600">Tampilkan</button>
        </div>
    </form>

    @if ($jadwal)
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
                <p class="text-xs font-semibold text-stone-500 dark:text-slate-500">Total Hadir</p>
                <p class="mt-1 text-3xl font-extrabold text-forest dark:text-cyan-400">{{ $stats['hadir'] }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
                <p class="text-xs font-semibold text-stone-500 dark:text-slate-500">Kuota Ruangan</p>
                <p class="mt-1 text-3xl font-extrabold text-forest dark:text-cyan-400">{{ $stats['kuota'] }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
                <p class="text-xs font-semibold text-stone-500 dark:text-slate-500">Tingkat Kehadiran</p>
                <p class="mt-1 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $stats['pct'] }}%</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <div class="flex items-center justify-between border-b border-cyan-200/50 px-5 py-4 dark:border-slate-600">
                <p class="text-sm font-bold text-forest dark:text-cyan-400">Daftar Hadir — {{ $jadwal->nama_sesi }}</p>
                @if ($kehadiran->isNotEmpty())
                    <button class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-gold to-amber-500 px-4 py-2 text-xs font-bold text-forest shadow-md hover:shadow-lg transition hover:from-amber-400 hover:to-orange-400">
                        <i class="fa-solid fa-download"></i> Unduh Laporan PDF
                    </button>
                @else
                    <button disabled class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-stone-100 to-slate-100 px-4 py-2 text-xs font-bold text-stone-400 cursor-not-allowed dark:from-slate-600 dark:to-slate-500">
                        Unduh Laporan PDF
                    </button>
                @endif
            </div>

            @if ($kehadiran->isEmpty())
                <div class="p-10 text-center text-sm text-stone-600 dark:text-slate-400">
                    Belum ada data kehadiran untuk sesi ibadah ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-cyan-200/50 bg-gradient-to-r from-cyan-50 to-sky-50 dark:border-slate-600 dark:from-slate-600 dark:to-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">No</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Nama</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Status</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Email</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Waktu Check-in</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cyan-200/50 dark:divide-slate-600">
                            @foreach ($kehadiran as $i => $h)
                                <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-sky-50 dark:hover:from-slate-600 dark:hover:to-slate-700">
                                    <td class="px-5 py-3.5 text-stone-500 dark:text-slate-500">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-forest dark:text-cyan-400">{{ $h->user->name }}</td>
                                    <td class="px-5 py-3.5"><span class="rounded-full bg-gradient-to-r from-gold-soft to-amber-100 px-2 py-0.5 text-[10px] font-bold capitalize text-forest dark:from-slate-600 dark:to-slate-500 dark:text-amber-400">{{ $h->user->role }}</span></td>
                                    <td class="px-5 py-3.5 text-stone-600 dark:text-slate-400">{{ $h->user->email }}</td>
                                    <td class="px-5 py-3.5 text-stone-600 dark:text-slate-400">{{ $h->waktu_checkin->format('H:i') }} WIB</td>
                                    <td class="px-5 py-3.5"><span class="rounded-full bg-gradient-to-r from-emerald-50 to-green-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:from-slate-600 dark:to-slate-500 dark:text-emerald-400">{{ $h->metode_verifikasi }}</span></td>
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
