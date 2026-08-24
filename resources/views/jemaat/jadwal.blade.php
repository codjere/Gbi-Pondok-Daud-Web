@extends('layouts.app')

@section('title', 'Jadwal Ibadah')
@section('breadcrumb', 'Jemaat / Jadwal Ibadah & Kegiatan')

@section('content')
<div class="mx-auto max-w-5xl">
    @include('partials.flash')

    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest">Jadwal Ibadah & Kegiatan</h2>
        <p class="mt-1 text-sm text-stone-500">GBI Pondok Daud — agenda rohani yang dipublikasikan pengurus</p>
    </div>

    @forelse ($jadwals as $jadwal)
        @php
            $terisi = $jadwal->jumlahTerisi();
            $pct = $jadwal->batas_kuota > 0 ? ($terisi / $jadwal->batas_kuota) * 100 : 0;
            $status = $jadwal->statusKuota();
            $sudah = in_array($jadwal->id, $reservasiUser);
        @endphp
        <div class="mb-4 rounded-2xl bg-white p-5 ring-1 ring-stone-200/80 {{ $status === 'penuh' ? 'opacity-75' : '' }}">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex gap-4">
                    <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl {{ $status === 'penuh' ? 'bg-stone-100 text-stone-400' : 'bg-gold-soft text-gold' }}">
                        <span class="text-[10px] font-bold uppercase">{{ $jadwal->tanggal->translatedFormat('D') }}</span>
                        <span class="text-lg font-extrabold leading-none">{{ $jadwal->tanggal->format('d') }}</span>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-bold text-forest">{{ $jadwal->nama_sesi }}</h3>
                            @if ($sudah)
                                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">Sudah Reservasi</span>
                            @elseif ($status === 'penuh')
                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-bold text-red-600">Kuota Penuh</span>
                            @elseif ($status === 'hampir')
                                <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">Hampir Penuh</span>
                            @else
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Tersedia</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-stone-500">
                            {{ $jadwal->jamMulaiFormatted() }} — {{ $jadwal->jamSelesaiFormatted() }} WIB · {{ $jadwal->lokasi }}
                        </p>
                        <p class="text-xs text-stone-400">Pembicara: {{ $jadwal->pembicara }}</p>
                        @if ($jadwal->deskripsi)
                            <p class="mt-2 text-xs text-stone-500 italic">{{ $jadwal->deskripsi }}</p>
                        @endif
                    </div>
                </div>
                <div class="sm:w-48">
                    <div class="mb-1 flex justify-between text-[11px] text-stone-400">
                        <span>{{ $terisi }}/{{ $jadwal->batas_kuota }}</span>
                        @if (!$jadwal->isPenuh())<span class="font-semibold text-forest-mid">{{ $jadwal->sisaKuota() }} tersisa</span>@endif
                    </div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-stone-100">
                        <div class="h-full rounded-full {{ $status === 'penuh' ? 'bg-red-400' : ($status === 'hampir' ? 'bg-amber-400' : 'bg-forest-mid') }}" style="width: {{ $pct }}%"></div>
                    </div>
                    @if ($sudah)
                        <button disabled class="mt-3 w-full cursor-default rounded-xl bg-blue-50 py-2 text-xs font-bold text-blue-700">Terdaftar</button>
                    @elseif ($jadwal->isPenuh())
                        <button disabled class="mt-3 w-full cursor-not-allowed rounded-xl bg-stone-100 py-2 text-xs font-bold text-stone-400">Kuota Penuh</button>
                    @else
                        <form action="{{ route('jemaat.reservasi.store', $jadwal) }}" method="POST" class="mt-3">
                            @csrf
                            <button class="w-full rounded-xl bg-forest py-2 text-xs font-bold text-white hover:bg-forest-mid">Reservasi Kehadiran</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-2xl bg-white p-10 text-center ring-1 ring-stone-200">
            <p class="text-stone-500">Belum ada jadwal kegiatan ibadah yang tersedia saat ini.</p>
        </div>
    @endforelse
</div>
@endsection
