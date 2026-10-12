@extends('layouts.app')

@section('title', 'Beranda')
@section('breadcrumb', 'Beranda / Dashboard')

@section('content')
<div class="mx-auto max-w-6xl space-y-6 pattern-dots">
    @include('partials.flash')

    {{-- Welcome Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-forest via-forest-mid to-cyan-500 p-6 text-white shadow-lg sm:p-8 fade-in dark:from-slate-700 dark:via-slate-600 dark:to-cyan-600">
        <div class="absolute right-4 top-4 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute bottom-4 right-20 h-24 w-24 rounded-full bg-gold/20 blur-xl"></div>
        <div class="relative">
            <p class="text-sm text-white/70">Selamat datang,</p>
            <h2 class="mt-1 text-2xl font-bold sm:text-3xl">{{ $user->name }}</h2>
            <p class="mt-2 max-w-md text-sm text-white/80">
                @if ($jadwalTerdekat->first())
                    Ibadah berikutnya: <strong class="text-gold-soft">{{ $jadwalTerdekat->first()->nama_sesi }}</strong>,
                    {{ $jadwalTerdekat->first()->tanggal->translatedFormat('l, d F Y') }}.
                @else
                    Belum ada jadwal ibadah aktif saat ini.
                @endif
            </p>
            <a href="{{ route('jemaat.reservasi') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gold px-5 py-2.5 text-sm font-semibold text-forest btn-primary">
                <i class="fa-solid fa-ticket"></i> Reservasi Sekarang
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 hover-card fade-in stagger-1 dark:bg-slate-700 dark:ring-slate-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-stone-600 dark:text-slate-400">Jemaat Terdaftar</p>
                    <p class="mt-2 text-2xl font-bold text-forest dark:text-cyan-400">{{ $stats['jemaat'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-100 to-sky-100 text-cyan-600 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 hover-card fade-in stagger-2 dark:bg-slate-700 dark:ring-slate-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-stone-600 dark:text-slate-400">Reservasi Minggu Ini</p>
                    <p class="mt-2 text-2xl font-bold text-forest dark:text-cyan-400">{{ $stats['reservasi_minggu'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 dark:from-slate-600 dark:to-slate-500 dark:text-amber-400">
                    <i class="fa-solid fa-calendar-check text-lg"></i>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 hover-card fade-in stagger-3 dark:bg-slate-700 dark:ring-slate-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-stone-600 dark:text-slate-300">Permohonan Doa</p>
                    <p class="mt-2 text-2xl font-bold text-forest dark:text-cyan-400">{{ $stats['permohonan_doa'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 dark:from-slate-600 dark:to-slate-500 dark:text-blue-400">
                    <i class="fa-solid fa-hands-praying text-lg"></i>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 hover-card fade-in stagger-4 dark:bg-slate-700 dark:ring-slate-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-stone-600 dark:text-slate-300">Kehadiran Terakhir</p>
                    <p class="mt-2 text-2xl font-bold text-cyan-500 dark:text-cyan-400">{{ $stats['kehadiran_pct'] }}%</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-100 to-sky-100 text-cyan-600 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                    <i class="fa-solid fa-chart-line text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid gap-5 lg:grid-cols-5">
        {{-- Jadwal Ibadah --}}
        <div class="lg:col-span-3 rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 fade-in stagger-2 dark:bg-slate-700 dark:ring-slate-600">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-forest dark:text-cyan-400">Jadwal Ibadah Terdekat</h3>
                <a href="{{ route('jemaat.jadwal') }}" class="text-xs font-medium text-forest-mid hover:underline dark:text-cyan-500">Lihat semua</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($jadwalTerdekat as $jadwal)
                    <div class="flex items-center gap-4 rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-4 py-3.5 transition-all hover:from-cyan-100 dark:from-slate-600 dark:to-slate-700">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-gold-soft to-amber-100 text-gold">
                            <i class="fa-solid fa-church text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-stone-800 dark:text-white">{{ $jadwal->nama_sesi }}</p>
                            <p class="text-xs text-stone-600 dark:text-slate-400">
                                {{ $jadwal->tanggal->translatedFormat('D, d M Y') }} ·
                                {{ $jadwal->jamMulaiFormatted() }} · {{ $jadwal->pembicara }}
                            </p>
                        </div>
                        <span class="shrink-0 rounded-full bg-gradient-to-r from-cyan-100 to-sky-100 px-2.5 py-1 text-[10px] font-semibold text-cyan-700 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                            {{ $jadwal->sisaKuota() }} tersisa
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-stone-600 dark:text-slate-400">Belum ada jadwal kegiatan ibadah yang tersedia saat ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Renungan --}}
        <div class="lg:col-span-2 rounded-2xl bg-gradient-to-br from-gold-soft via-white to-cyan-50 p-6 ring-1 ring-gold/20 fade-in stagger-3 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700 dark:ring-slate-600">
            @if ($renungan)
                <p class="text-xs font-semibold uppercase tracking-wider text-gold">Renungan Terbaru</p>
                @if ($renungan->gambarUrl())
                    <img src="{{ $renungan->gambarUrl() }}" alt="{{ $renungan->judul }}" class="mt-3 h-40 w-full rounded-xl object-cover">
                @endif
                <p class="mt-3 text-lg font-bold leading-snug text-forest dark:text-cyan-400">{{ $renungan->judul }}</p>
                <p class="mt-2 line-clamp-4 text-sm text-stone-600 dark:text-slate-400">{{ Str::limit($renungan->isi, 160) }}</p>
                <p class="mt-2 text-xs text-stone-500 dark:text-slate-500">{{ $renungan->author->name ?? 'GBI Pondok Daud' }} · {{ $renungan->published_at->translatedFormat('d M Y') }}</p>
                <a href="{{ route('jemaat.warta.show', $renungan) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-forest-mid hover:underline dark:text-cyan-500">
                    Baca selengkapnya <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            @else
                <p class="text-sm text-stone-600 dark:text-slate-400">Belum ada warta atau renungan terbaru.</p>
            @endif
        </div>
    </div>
</div>
@endsection
