@extends('layouts.app')

@section('title', 'Beranda')
@section('breadcrumb', 'Beranda / Dashboard')

@section('content')
<div class="mx-auto max-w-6xl">
    @include('partials.flash')

    <div class="relative overflow-hidden rounded-3xl bg-forest p-7 text-white shadow-xl sm:p-9">
        <div class="relative">
            <p class="text-sm text-white/60">Selamat datang,</p>
            <h2 class="mt-1 text-2xl font-extrabold sm:text-3xl">{{ $user->name }}</h2>
            <p class="mt-2 max-w-md text-sm text-white/55">
                @if ($jadwalTerdekat->first())
                    Ibadah berikutnya: <strong>{{ $jadwalTerdekat->first()->nama_sesi }}</strong>,
                    {{ $jadwalTerdekat->first()->tanggal->translatedFormat('l, d F Y') }}.
                @else
                    Belum ada jadwal ibadah aktif saat ini.
                @endif
            </p>
            <a href="{{ route('jemaat.reservasi') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gold px-5 py-2.5 text-sm font-bold text-forest">
                <i class="fa-solid fa-ticket"></i> Reservasi Sekarang
            </a>
        </div>
    </div>

    <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
            <p class="text-xs font-semibold text-stone-400">Jemaat Terdaftar</p>
            <p class="mt-1 text-3xl font-extrabold text-forest">{{ $stats['jemaat'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
            <p class="text-xs font-semibold text-stone-400">Reservasi Minggu Ini</p>
            <p class="mt-1 text-3xl font-extrabold text-forest">{{ $stats['reservasi_minggu'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
            <p class="text-xs font-semibold text-stone-400">Permohonan Doa</p>
            <p class="mt-1 text-3xl font-extrabold text-forest">{{ $stats['permohonan_doa'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
            <p class="text-xs font-semibold text-stone-400">Kehadiran Terakhir</p>
            <p class="mt-1 text-3xl font-extrabold text-emerald-600">{{ $stats['kehadiran_pct'] }}%</p>
        </div>
    </div>

    <div class="mt-7 grid gap-5 lg:grid-cols-5">
        <div class="lg:col-span-3 rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-forest">Jadwal Ibadah Terdekat</h3>
                <a href="{{ route('jemaat.jadwal') }}" class="text-xs font-semibold text-forest-mid hover:underline">Lihat semua</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($jadwalTerdekat as $jadwal)
                    <div class="flex items-center gap-4 rounded-xl bg-cream px-4 py-3.5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-soft text-gold">
                            <i class="fa-solid fa-church text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-stone-800">{{ $jadwal->nama_sesi }}</p>
                            <p class="text-xs text-stone-400">
                                {{ $jadwal->tanggal->translatedFormat('D, d M Y') }} ·
                                {{ $jadwal->jamMulaiFormatted() }} · {{ $jadwal->pembicara }}
                            </p>
                        </div>
                        <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">
                            {{ $jadwal->sisaKuota() }} tersisa
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-stone-400">Belum ada jadwal kegiatan ibadah yang tersedia saat ini.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-2 rounded-2xl bg-gold-soft/60 p-6 ring-1 ring-gold/20">
            @if ($renungan)
                <p class="text-xs font-bold uppercase tracking-wider text-gold">Renungan Terbaru</p>
                <p class="mt-3 text-lg font-bold leading-snug text-forest">{{ $renungan->judul }}</p>
                <p class="mt-2 line-clamp-4 text-sm text-stone-600">{{ Str::limit($renungan->isi, 160) }}</p>
                <p class="mt-2 text-xs text-stone-500">{{ $renungan->author->name ?? 'GBI Pondok Daud' }} · {{ $renungan->published_at->translatedFormat('d M Y') }}</p>
                <a href="{{ route('jemaat.warta.show', $renungan) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-forest-mid hover:underline">
                    Baca selengkapnya <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            @else
                <p class="text-sm text-stone-500">Belum ada warta atau renungan terbaru.</p>
            @endif
        </div>
    </div>
</div>
@endsection
