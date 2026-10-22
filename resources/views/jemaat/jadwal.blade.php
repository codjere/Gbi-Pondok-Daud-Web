@extends('layouts.app')

@section('title', 'Jadwal Ibadah')
@section('breadcrumb', 'Jemaat / Jadwal Ibadah & Kegiatan')

@section('content')
<div class="mx-auto max-w-5xl pattern-dots">
    @include('partials.flash')

    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Jadwal Ibadah & Kegiatan</h2>
        <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">GBI Pondok Daud — agenda rohani yang dipublikasikan pengurus</p>
    </div>

    @forelse ($jadwals as $jadwal)
        @php
            $isDaily = $jadwal->is_daily ?? false;
            $terisi = $isDaily ? 0 : $jadwal->jumlahTerisi();
            $pct = (! $isDaily && $jadwal->batas_kuota > 0) ? ($terisi / $jadwal->batas_kuota) * 100 : 0;
            $status = $isDaily ? 'tersedia' : $jadwal->statusKuota();
            $sudah = ! $isDaily && in_array($jadwal->id, $reservasiUser);
        @endphp
        <div class="mb-4 rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 {{ $status === 'penuh' ? 'opacity-75' : '' }} dark:bg-slate-700 dark:ring-slate-600">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex gap-4">
                    <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl {{ $status === 'penuh' ? 'bg-gradient-to-r from-stone-100 to-slate-100 text-stone-400 dark:from-slate-600 dark:to-slate-500 dark:text-slate-400' : 'bg-gradient-to-r from-gold-soft to-amber-100 text-gold dark:from-slate-600 dark:to-slate-500 dark:text-amber-400' }}">
                        <span class="text-[10px] font-bold uppercase">{{ $jadwal->tanggal->translatedFormat('D') }}</span>
                        <span class="text-lg font-extrabold leading-none">{{ $jadwal->tanggal->format('d') }}</span>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-bold text-forest dark:text-cyan-400">{{ $jadwal->nama_sesi }}</h3>
                            @if ($isDaily)
                                <span class="rounded-full bg-gradient-to-r from-sky-100 to-cyan-100 px-2 py-0.5 text-[10px] font-bold text-sky-700 dark:from-slate-600 dark:to-slate-500 dark:text-sky-400">Setiap Hari · Online</span>
                            @elseif ($sudah)
                                <span class="rounded-full bg-gradient-to-r from-cyan-100 to-sky-100 px-2 py-0.5 text-[10px] font-bold text-cyan-700 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">Sudah Reservasi</span>
                            @elseif ($status === 'penuh')
                                <span class="rounded-full bg-gradient-to-r from-red-50 to-orange-50 px-2 py-0.5 text-[10px] font-bold text-red-600 dark:from-slate-600 dark:to-slate-500 dark:text-red-400">Kuota Penuh</span>
                            @elseif ($status === 'hampir')
                                <span class="rounded-full bg-gradient-to-r from-amber-50 to-orange-100 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:from-slate-600 dark:to-slate-500 dark:text-amber-400">Hampir Penuh</span>
                            @else
                                <span class="rounded-full bg-gradient-to-r from-emerald-50 to-green-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:from-slate-600 dark:to-slate-500 dark:text-emerald-400">Tersedia</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">
                            {{ $jadwal->jamMulaiFormatted() }} — {{ $jadwal->jamSelesaiFormatted() }} WITA · {{ $jadwal->lokasi }}
                        </p>
                        <p class="text-xs text-stone-500 dark:text-slate-500">Pembicara: {{ $jadwal->pembicara }}</p>
                        @if ($jadwal->deskripsi)
                            <p class="mt-2 text-xs text-stone-600 dark:text-slate-400 italic">{{ $jadwal->deskripsi }}</p>
                        @endif
                        @if ($jadwal->meeting_link)
                            <a href="{{ $jadwal->meeting_link }}" target="_blank" rel="noopener"
                               class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-sky-500 to-cyan-500 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:from-sky-600 hover:to-cyan-600">
                                <i class="fa-solid fa-video"></i> Gabung Meeting
                            </a>
                        @endif
                    </div>
                </div>
                <div class="sm:w-48">
                    @if ($isDaily)
                        <div class="rounded-xl bg-gradient-to-r from-sky-50 to-cyan-50 p-3 text-center dark:from-slate-600 dark:to-slate-500">
                            <p class="text-[11px] font-semibold text-sky-700 dark:text-sky-300">Tanpa reservasi</p>
                            <p class="mt-0.5 text-[10px] text-stone-500 dark:text-slate-400">Langsung gabung lewat link di samping setiap pagi.</p>
                        </div>
                    @else
                        <div class="mb-1 flex justify-between text-[11px] text-stone-500 dark:text-slate-500">
                            <span>{{ $terisi }}/{{ $jadwal->batas_kuota }}</span>
                            @if (!$jadwal->isPenuh())<span class="font-semibold text-forest-mid dark:text-cyan-400">{{ $jadwal->sisaKuota() }} tersisa</span>@endif
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-stone-100 dark:bg-slate-600">
                            <div class="h-full rounded-full {{ $status === 'penuh' ? 'bg-red-400' : ($status === 'hampir' ? 'bg-amber-400' : 'bg-forest-mid') }}" style="width: {{ $pct }}%"></div>
                        </div>
                        @if ($sudah)
                            <button disabled class="mt-3 w-full cursor-default rounded-xl bg-gradient-to-r from-cyan-100 to-sky-100 py-2 text-xs font-bold text-cyan-700 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">Terdaftar</button>
                        @elseif ($jadwal->isPenuh())
                            <button disabled class="mt-3 w-full cursor-not-allowed rounded-xl bg-gradient-to-r from-stone-100 to-slate-100 py-2 text-xs font-bold text-stone-400 dark:from-slate-600 dark:to-slate-500 dark:text-slate-400">Kuota Penuh</button>
                        @else
                            <form action="{{ route('jemaat.reservasi.store', $jadwal) }}" method="POST" class="mt-3">
                                @csrf
                                <button class="w-full rounded-xl bg-gradient-to-r from-forest to-forest-mid py-2 text-xs font-bold text-white shadow-md hover:shadow-lg transition hover:from-cyan-600 hover:to-sky-600">Reservasi Kehadiran</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-2xl bg-white p-10 text-center shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <p class="text-stone-600 dark:text-slate-400">Belum ada jadwal kegiatan ibadah yang tersedia saat ini.</p>
        </div>
    @endforelse
</div>
@endsection
