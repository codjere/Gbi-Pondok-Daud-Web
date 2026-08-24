@extends('layouts.app')

@section('title', 'Rencana Bacaan Alkitab')
@section('breadcrumb', 'Jemaat / Alkitab / Rencana Bacaan')

@section('content')
<div class="mx-auto max-w-5xl">
    @include('partials.flash')

    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-forest">Rencana Bacaan Alkitab</h2>
        <p class="mt-1 text-sm text-stone-500">Pilih rencana bacaan dan konsisten membaca firman Tuhan setiap hari</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        @foreach ($readingPlans as $plan)
            <div class="rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-forest">{{ $plan->nama }}</h3>
                    <p class="mt-2 text-sm text-stone-600">{{ $plan->deskripsi }}</p>
                    <div class="mt-3 flex items-center gap-4">
                        <span class="rounded-full bg-gold-soft px-3 py-1 text-xs font-bold text-gold">
                            {{ $plan->durasiFormatted() }}
                        </span>
                        <span class="text-xs text-stone-500">{{ $plan->durasi_hari }} hari bacaan</span>
                    </div>
                </div>

                {{-- Progress Section --}}
                @if(isset($userProgress[$plan->id]))
                    <div class="mb-4 rounded-xl bg-cream p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-semibold text-forest">Progress Anda</span>
                            <span class="text-xs font-bold text-emerald-600">{{ $userProgress[$plan->id]->persentaseProgress() }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-stone-200">
                            <div class="h-full rounded-full bg-emerald-500 transition-all" 
                                 style="width: {{ $userProgress[$plan->id]->persentaseProgress() }}%"></div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-[10px] text-stone-500">
                            <span>Hari {{ $userProgress[$plan->id]->hari_terakhir }} dari {{ $plan->durasi_hari }}</span>
                            <span>{{ $userProgress[$plan->id]->status() }}</span>
                        </div>
                        @if($userProgress[$plan->id]->tanggal_mulai)
                            <p class="mt-2 text-[10px] text-stone-400">
                                Mulai: {{ $userProgress[$plan->id]->tanggal_mulai->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Action Button --}}
                @if(isset($userProgress[$plan->id]))
                    <a href="{{ route('jemaat.alkitab.plan', $plan->id) }}" 
                       class="block w-full rounded-xl bg-forest py-3 text-center text-sm font-bold text-white hover:bg-forest-mid">
                        <i class="fa-solid fa-book-open mr-1"></i> Lanjut Baca Hari Ini
                    </a>
                @else
                    <form action="{{ route('jemaat.alkitab.mulai-plan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="reading_plan_id" value="{{ $plan->id }}">
                        <button type="submit" class="w-full rounded-xl bg-gold py-3 text-sm font-bold text-forest hover:bg-gold-soft">
                            <i class="fa-solid fa-play mr-1"></i> Mulai Rencana Ini
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('jemaat.alkitab') }}" class="text-sm font-semibold text-forest hover:underline">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Alkitab
        </a>
    </div>
</div>
@endsection
