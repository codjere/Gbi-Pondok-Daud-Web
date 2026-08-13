@extends('layouts.app')

@section('title', 'Rekomendasi AI')
@section('breadcrumb', 'Jemaat / Sistem Rekomendasi')

@section('content')
<div class="mx-auto max-w-5xl">
    @include('partials.flash')

    <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="rounded-full bg-gold-soft px-3 py-1 text-[11px] font-bold text-forest inline-block">Content-Based + Collaborative Filtering</p>
            <h2 class="mt-3 text-xl font-extrabold text-forest">Rekomendasi Pelayanan & COOL</h2>
            <p class="mt-1 text-sm text-stone-500">Hybrid score: 60% Content-Based + 40% Collaborative Filtering</p>
        </div>
        <a href="{{ route('jemaat.kuesioner') }}" class="rounded-xl bg-white px-4 py-2 text-xs font-bold text-forest ring-1 ring-stone-200 hover:bg-cream">
            <i class="fa-solid fa-pen mr-1"></i> Edit Profil
        </a>
    </div>

    {{-- Tim Pelayanan --}}
    <div class="mb-8">
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-stone-400">Tim Pelayanan Rekomendasi</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recommendations['tim_pelayanan'] ?? [] as $rec)
                @php $detail = $timDetails[$rec['id']] ?? null; @endphp
                <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
                    <div class="flex items-start justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-forest/10 text-forest">
                            <i class="fa-solid {{ $detail?->icon ?? 'fa-hands' }}"></i>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">{{ $rec['score_pct'] }}% cocok</span>
                    </div>
                    <h4 class="mt-3 font-bold text-forest">{{ $rec['nama'] }}</h4>
                    <p class="mt-1 text-xs text-stone-500 line-clamp-2">{{ $detail?->deskripsi }}</p>

                    <div class="mt-4 space-y-1 text-[10px] text-stone-400">
                        <div class="flex justify-between"><span>Minat (Jaccard)</span><span>{{ ($rec['content_score']['minat'] ?? 0) * 100 }}%</span></div>
                        <div class="flex justify-between"><span>Bakat (Jaccard)</span><span>{{ ($rec['content_score']['bakat'] ?? 0) * 100 }}%</span></div>
                        <div class="flex justify-between"><span>Collaborative</span><span>{{ ($rec['collab_score'] ?? 0) * 100 }}%</span></div>
                    </div>

                    <form action="{{ route('jemaat.rekomendasi.tim') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="tim_id" value="{{ $rec['id'] }}">
                        <input type="hidden" name="score" value="{{ $rec['score'] }}">
                        <button type="submit" class="w-full rounded-xl bg-gold py-2 text-xs font-bold text-forest hover:bg-gold/90">
                            Pilih Tim Ini
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-stone-500">Belum ada rekomendasi. Isi kuesioner terlebih dahulu.</p>
            @endforelse
        </div>
    </div>

    {{-- COOL --}}
    <div>
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-stone-400">COOL / Kelompok Sel Terdekat</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recommendations['cool_groups'] ?? [] as $rec)
                @php $detail = $coolDetails[$rec['id']] ?? null; @endphp
                <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/80">
                    <div class="flex items-start justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold/20 text-gold">
                            <i class="fa-solid fa-people-group"></i>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">{{ $rec['score_pct'] }}% cocok</span>
                    </div>
                    <h4 class="mt-3 font-bold text-forest">{{ $rec['nama'] }}</h4>
                    <p class="mt-1 text-xs text-stone-500"><i class="fa-solid fa-location-dot mr-1"></i>{{ $rec['kecamatan'] }}</p>
                    @if ($detail)
                        <p class="mt-1 text-xs text-stone-400">Pemimpin: {{ $detail->pemimpin }} · {{ $detail->jadwal }}</p>
                    @endif

                    <div class="mt-4 space-y-1 text-[10px] text-stone-400">
                        <div class="flex justify-between"><span>Minat</span><span>{{ ($rec['content_score']['minat'] ?? 0) * 100 }}%</span></div>
                        <div class="flex justify-between"><span>Lokasi</span><span>{{ ($rec['content_score']['lokasi'] ?? 0) * 100 }}%</span></div>
                        <div class="flex justify-between"><span>Collaborative</span><span>{{ ($rec['collab_score'] ?? 0) * 100 }}%</span></div>
                    </div>

                    <form action="{{ route('jemaat.rekomendasi.cool') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="cool_id" value="{{ $rec['id'] }}">
                        <input type="hidden" name="score" value="{{ $rec['score'] }}">
                        <button type="submit" class="w-full rounded-xl bg-forest py-2 text-xs font-bold text-white hover:bg-forest-mid">
                            Tertarik COOL Ini
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-stone-500">Belum ada rekomendasi COOL.</p>
            @endforelse
        </div>
    </div>

    {{-- Formula --}}
    @if (isset($recommendations['weights']))
    <div class="mt-8 rounded-2xl bg-forest/5 p-5 ring-1 ring-forest/10">
        <p class="text-xs font-bold text-forest">Rumus Hybrid (untuk dokumentasi tesis)</p>
        <p class="mt-2 font-mono text-xs text-stone-600">
            Score = 0.60 × Content-Based + 0.40 × Collaborative Filtering<br>
            Content-Based = 0.30×Minat + 0.25×Bakat + 0.15×Usia + 0.20×Ketersediaan + 0.10×Lokasi
        </p>
    </div>
    @endif
</div>
@endsection
