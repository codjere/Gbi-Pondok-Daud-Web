@extends('layouts.app')

@section('title', 'Cari Ayat')
@section('breadcrumb', 'Jemaat / Alkitab / Cari')

@section('content')
<div class="mx-auto max-w-4xl">
    @include('partials.flash')

    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-forest">Hasil Pencarian</h2>
        <p class="mt-1 text-sm text-stone-500">Kata kunci: "{{ $kataKunci }}"</p>
    </div>

    {{-- Search Form --}}
    <div class="mb-6 rounded-2xl bg-white p-4 ring-1 ring-stone-200/80">
        <form action="{{ route('jemaat.alkitab.cari') }}" method="GET" class="flex gap-3">
            <input type="text" name="kata_kunci" value="{{ $kataKunci }}" placeholder="Cari ayat lain..." 
                   class="flex-1 rounded-xl bg-cream px-4 py-2.5 text-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40">
            <button type="submit" class="rounded-xl bg-forest px-6 py-2.5 text-sm font-bold text-white hover:bg-forest-mid">
                <i class="fa-solid fa-search mr-1"></i> Cari
            </button>
        </form>
    </div>

    {{-- Search Results --}}
    @if($ayats->count() > 0)
        <div class="rounded-2xl bg-white ring-1 ring-stone-200/80">
            <div class="border-b border-stone-100 px-5 py-4">
                <p class="text-sm font-bold text-forest">Ditemukan {{ $ayats->count() }} ayat</p>
            </div>
            <div class="divide-y divide-stone-100">
                @foreach($ayats as $ayat)
                    <div class="p-5 hover:bg-cream/50">
                        <div class="mb-2 flex items-center gap-2">
                            <span class="rounded-full bg-gold-soft px-2 py-0.5 text-xs font-bold text-gold">
                                {{ $ayat->referensi() }}
                            </span>
                            <span class="text-xs text-stone-500">{{ $ayat->pasal->kitab->nama }}</span>
                        </div>
                        <p class="text-sm text-stone-700 leading-relaxed">{{ $ayat->isi_ayat }}</p>
                        <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $ayat->pasal->kitab->id, 'nomorPasal' => $ayat->pasal->nomor_pasal]) }}" 
                           class="mt-2 inline-flex items-center text-xs font-semibold text-forest hover:underline">
                            Baca Selengkapnya <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-2xl bg-white p-10 text-center ring-1 ring-stone-200">
            <i class="fa-solid fa-search text-3xl text-stone-300"></i>
            <p class="mt-3 text-sm text-stone-500">Tidak ditemukan ayat dengan kata kunci "{{ $kataKunci }}"</p>
            <a href="{{ route('jemaat.alkitab') }}" class="mt-4 inline-block text-sm font-semibold text-forest hover:underline">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Alkitab
            </a>
        </div>
    @endif
</div>
@endsection
