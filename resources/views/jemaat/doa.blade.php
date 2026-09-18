@extends('layouts.app')

@section('title', 'Permohonan Doa')
@section('breadcrumb', 'Jemaat / Permohonan Doa')

@section('content')
<div class="mx-auto max-w-2xl pattern-dots">
    @include('partials.flash')

    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Permohonan Doa Syafaat</h2>
        <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">Tim pendoa GBI Pondok Daud siap mendoakan permohonan Anda</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 sm:p-8 dark:bg-slate-700 dark:ring-slate-600">
        <form action="{{ route('jemaat.doa.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="pokok" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-300">Pokok Doa</label>
                <textarea id="pokok" name="pokok" rows="6" required minlength="10" placeholder="Tuliskan permohonan doa Anda…"
                          class="w-full resize-none rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-4 py-3 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">{{ old('pokok') }}</textarea>
            </div>
            <div class="rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 p-4 dark:from-slate-600 dark:to-slate-500">
                <p class="mb-3 text-sm font-semibold text-stone-700 dark:text-slate-300">Tampilkan identitas?</p>
                <div class="flex gap-4">
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl bg-white px-4 py-2.5 shadow-sm ring-1 ring-cyan-200/60 dark:bg-slate-500 dark:ring-slate-500">
                        <input type="radio" name="anonim" value="0" @checked(old('anonim', '0') === '0') class="text-forest dark:text-cyan-400"> <span class="text-sm text-stone-700 dark:text-slate-300">Cantumkan nama</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl bg-white px-4 py-2.5 shadow-sm ring-1 ring-cyan-200/60 dark:bg-slate-500 dark:ring-slate-500">
                        <input type="radio" name="anonim" value="1" @checked(old('anonim') === '1') class="text-forest dark:text-cyan-400"> <span class="text-sm text-stone-700 dark:text-slate-300">Anonim</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-forest to-forest-mid py-3.5 text-sm font-bold text-white shadow-md hover:shadow-lg transition hover:from-cyan-600 hover:to-sky-600">
                <i class="fa-solid fa-paper-plane"></i> Kirim Permohonan
            </button>
        </form>
    </div>
</div>
@endsection
