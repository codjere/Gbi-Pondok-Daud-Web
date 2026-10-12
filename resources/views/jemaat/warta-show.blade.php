@extends('layouts.app')

@section('title', $warta->judul)
@section('breadcrumb', 'Jemaat / Renungan & Warta')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('jemaat.warta') }}" class="mb-5 inline-flex items-center gap-1 text-sm font-semibold text-forest-mid hover:underline">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
    </a>

    <article class="overflow-hidden rounded-2xl bg-white ring-1 ring-stone-200/80">
        @if ($warta->gambarUrl())
            <img src="{{ $warta->gambarUrl() }}" alt="{{ $warta->judul }}" class="max-h-96 w-full object-cover">
        @endif
        <div class="p-7 sm:p-10">
        <span class="rounded-full bg-cream px-3 py-1 text-[10px] font-bold text-forest-mid">{{ $warta->kategori }}</span>
        <h1 class="mt-4 text-2xl font-extrabold leading-snug text-forest sm:text-3xl">{{ $warta->judul }}</h1>
        <p class="mt-3 text-sm text-stone-400">{{ $warta->author->name }} · {{ $warta->published_at->translatedFormat('l, d F Y') }}</p>
        <div class="prose prose-stone mt-8 max-w-none whitespace-pre-line text-base leading-relaxed text-stone-700">{{ $warta->isi }}</div>
        </div>
    </article>
</div>
@endsection
