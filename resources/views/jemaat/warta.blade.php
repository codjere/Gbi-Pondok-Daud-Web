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
        <div class="mb-7 overflow-hidden rounded-2xl bg-forest text-white">
            <div class="p-7 sm:p-9">
                <span class="rounded-full bg-gold/25 px-3 py-1 text-[10px] font-bold uppercase text-gold">{{ $featured->kategori }}</span>
                <h3 class="mt-4 text-2xl font-extrabold leading-snug">{{ $featured->judul }}</h3>
                <p class="mt-3 text-sm text-white/60">{{ Str::limit($featured->isi, 200) }}</p>
                <p class="mt-4 text-xs text-white/40">{{ $featured->author->name }} · {{ $featured->published_at->translatedFormat('d F Y') }}</p>
                <a href="{{ route('jemaat.warta.show', $featured) }}" class="mt-4 inline-block text-sm font-bold text-gold hover:underline">Baca selengkapnya →</a>
            </div>
        </div>
    @else
        <div class="mb-7 rounded-2xl bg-white p-8 text-center ring-1 ring-stone-200">
            <p class="text-stone-500">Belum ada warta atau renungan terbaru.</p>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($artikels->skip(1) as $artikel)
            <a href="{{ route('jemaat.warta.show', $artikel) }}" class="group rounded-2xl bg-white p-5 ring-1 ring-stone-200/80 transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="rounded-full bg-cream px-2.5 py-0.5 text-[10px] font-bold text-forest-mid">{{ $artikel->kategori }}</span>
                <h4 class="mt-3 font-bold leading-snug text-forest group-hover:text-forest-mid">{{ $artikel->judul }}</h4>
                <p class="mt-3 text-[11px] text-stone-400">{{ $artikel->author->name }} · {{ $artikel->published_at->translatedFormat('d M Y') }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
