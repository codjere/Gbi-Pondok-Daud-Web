@extends('layouts.app')

@section('title', 'Renungan & Warta')
@section('breadcrumb', 'Jemaat / Renungan & Warta')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest">Renungan & Warta Jemaat</h2>
        <p class="mt-1 text-sm text-stone-500">GBI Pondok Daud — artikel rohani & pengumuman</p>
    </div>

    @if ($featured)
        <a href="{{ route('jemaat.warta.show', $featured) }}" class="mb-7 grid overflow-hidden rounded-2xl bg-forest text-white transition hover:shadow-lg {{ $featured->gambarUrl() ? 'md:grid-cols-2' : '' }}">
            @if ($featured->gambarUrl())
                <img src="{{ $featured->gambarUrl() }}" alt="{{ $featured->judul }}" class="h-56 w-full object-cover md:h-full">
            @endif
            <div class="p-7 sm:p-9">
                <span class="rounded-full bg-gold/25 px-3 py-1 text-[10px] font-bold uppercase text-gold">{{ $featured->kategori }}</span>
                <h3 class="mt-4 text-2xl font-extrabold leading-snug">{{ $featured->judul }}</h3>
                <p class="mt-3 text-sm text-white/60">{{ Str::limit($featured->isi, 200) }}</p>
                <p class="mt-4 text-xs text-white/40">{{ $featured->author->name }} · {{ $featured->published_at->translatedFormat('d F Y') }}</p>
                <span class="mt-4 inline-block text-sm font-bold text-gold">Baca selengkapnya →</span>
            </div>
        </a>
    @else
        <div class="mb-7 rounded-2xl bg-white p-8 text-center ring-1 ring-stone-200">
            <p class="text-stone-500">Belum ada warta atau renungan terbaru.</p>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($artikels->skip(1) as $artikel)
            <a href="{{ route('jemaat.warta.show', $artikel) }}" class="group overflow-hidden rounded-2xl bg-white ring-1 ring-stone-200/80 transition hover:-translate-y-0.5 hover:shadow-md">
                @if ($artikel->gambarUrl())
                    <img src="{{ $artikel->gambarUrl() }}" alt="{{ $artikel->judul }}" class="h-40 w-full object-cover">
                @else
                    <div class="flex h-40 w-full items-center justify-center bg-gradient-to-br from-cream to-gold-soft text-gold/50">
                        <i class="fa-solid fa-book-bible text-4xl"></i>
                    </div>
                @endif
                <div class="p-5">
                    <span class="rounded-full bg-cream px-2.5 py-0.5 text-[10px] font-bold text-forest-mid">{{ $artikel->kategori }}</span>
                    <h4 class="mt-3 font-bold leading-snug text-forest group-hover:text-forest-mid">{{ $artikel->judul }}</h4>
                    <p class="mt-3 text-[11px] text-stone-400">{{ $artikel->author->name }} · {{ $artikel->published_at->translatedFormat('d M Y') }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
