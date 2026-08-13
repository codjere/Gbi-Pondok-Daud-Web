@extends('layouts.app')

@section('title', 'Permohonan Doa')
@section('breadcrumb', 'Jemaat / Permohonan Doa')

@section('content')
<div class="mx-auto max-w-2xl">
    @include('partials.flash')

    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest">Permohonan Doa Syafaat</h2>
        <p class="mt-1 text-sm text-stone-500">Tim pendoa GBI Pondok Daud siap mendoakan permohonan Anda</p>
    </div>

    <div class="rounded-2xl bg-white p-6 ring-1 ring-stone-200/80 sm:p-8">
        <form action="{{ route('jemaat.doa.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="pokok" class="mb-1.5 block text-sm font-semibold text-stone-700">Pokok Doa</label>
                <textarea id="pokok" name="pokok" rows="6" required minlength="10" placeholder="Tuliskan permohonan doa Anda…"
                          class="w-full resize-none rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/30">{{ old('pokok') }}</textarea>
            </div>
            <div class="rounded-xl bg-cream p-4">
                <p class="mb-3 text-sm font-semibold text-stone-700">Tampilkan identitas?</p>
                <div class="flex gap-4">
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl bg-white px-4 py-2.5 ring-1 ring-stone-200">
                        <input type="radio" name="anonim" value="0" @checked(old('anonim', '0') === '0') class="text-forest"> <span class="text-sm">Cantumkan nama</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl bg-white px-4 py-2.5 ring-1 ring-stone-200">
                        <input type="radio" name="anonim" value="1" @checked(old('anonim') === '1') class="text-forest"> <span class="text-sm">Anonim</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-forest py-3.5 text-sm font-bold text-white hover:bg-forest-mid">
                <i class="fa-solid fa-paper-plane"></i> Kirim Permohonan
            </button>
        </form>
    </div>
</div>
@endsection
