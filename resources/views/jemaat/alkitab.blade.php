@extends('layouts.app')

@section('title', 'Alkitab Digital')
@section('breadcrumb', 'Jemaat / Alkitab Digital')

@section('content')
<div class="mx-auto max-w-6xl">
    @include('partials.flash')

    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-forest">Alkitab Digital</h2>
        <p class="mt-1 text-sm text-stone-500">Baca firman Tuhan dengan fitur lengkap dan rencana bacaan terstruktur</p>
    </div>

    {{-- Search Section --}}
    <div class="mb-6 rounded-2xl bg-white p-4 ring-1 ring-stone-200/80">
        <form action="{{ route('jemaat.alkitab.cari') }}" method="GET" class="flex gap-3">
            <input type="text" name="kata_kunci" placeholder="Cari ayat..." 
                   class="flex-1 rounded-xl bg-cream px-4 py-2.5 text-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40">
            <button type="submit" class="rounded-xl bg-forest px-6 py-2.5 text-sm font-bold text-white hover:bg-forest-mid">
                <i class="fa-solid fa-search mr-1"></i> Cari
            </button>
        </form>
    </div>

    {{-- Reading Plans Section --}}
    <div class="mb-6 rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-bold text-forest">Rencana Bacaan</h3>
            <a href="{{ route('jemaat.alkitab.plans') }}" class="text-xs font-semibold text-forest hover:underline">
                Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($readingPlans as $plan)
                <div class="rounded-xl bg-cream p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-bold text-forest">{{ $plan->nama }}</h4>
                            <p class="mt-1 text-xs text-stone-500">{{ $plan->deskripsi }}</p>
                            <p class="mt-2 text-xs font-semibold text-gold">{{ $plan->durasiFormatted() }}</p>
                        </div>
                        @if(isset($userProgress[$plan->id]))
                            <div class="text-right">
                                <p class="text-xs font-semibold text-emerald-600">{{ $userProgress[$plan->id]->persentaseProgress() }}%</p>
                                <p class="text-[10px] text-stone-400">{{ $userProgress[$plan->id]->status() }}</p>
                            </div>
                        @endif
                    </div>
                    @if(isset($userProgress[$plan->id]))
                        <a href="{{ route('jemaat.alkitab.plan', $plan->id) }}" 
                           class="mt-3 block w-full rounded-xl bg-forest py-2 text-center text-xs font-bold text-white hover:bg-forest-mid">
                            Lanjut Baca
                        </a>
                    @else
                        <form action="{{ route('jemaat.alkitab.mulai-plan') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="reading_plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="w-full rounded-xl bg-gold py-2 text-xs font-bold text-forest hover:bg-gold-soft">
                                Mulai Plan
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Books Section --}}
    <div class="mb-6 rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
        <h3 class="mb-4 text-sm font-bold text-forest">Kitab-Kitab Alkitab</h3>
        
        {{-- Testament Tabs --}}
        <div class="mb-4 flex gap-2">
            <button onclick="showTestament('PL')" id="tab-PL" class="rounded-xl bg-forest px-4 py-2 text-xs font-bold text-white">
                Perjanjian Lama
            </button>
            <button onclick="showTestament('PB')" id="tab-PB" class="rounded-xl bg-stone-100 px-4 py-2 text-xs font-bold text-stone-600 hover:bg-stone-200">
                Perjanjian Baru
            </button>
        </div>

        {{-- PL Books --}}
        <div id="PL-books" class="grid gap-2 sm:grid-cols-4 md:grid-cols-6">
            @foreach ($kitabs->where('testament', 'PL') as $kitab)
                <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => 1]) }}" 
                   class="rounded-lg bg-cream p-3 text-center hover:bg-gold-soft transition-colors">
                    <p class="text-xs font-bold text-forest">{{ $kitab->singkatan }}</p>
                    <p class="text-[10px] text-stone-500">{{ $kitab->jumlah_pasal }} pasal</p>
                </a>
            @endforeach
        </div>

        {{-- PB Books --}}
        <div id="PB-books" class="hidden grid gap-2 sm:grid-cols-4 md:grid-cols-6">
            @foreach ($kitabs->where('testament', 'PB') as $kitab)
                <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => 1]) }}" 
                   class="rounded-lg bg-cream p-3 text-center hover:bg-gold-soft transition-colors">
                    <p class="text-xs font-bold text-forest">{{ $kitab->singkatan }}</p>
                    <p class="text-[10px] text-stone-500">{{ $kitab->jumlah_pasal }} pasal</p>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTestament(testament) {
    // Hide all
    document.getElementById('PL-books').classList.add('hidden');
    document.getElementById('PB-books').classList.add('hidden');
    
    // Reset button styles
    document.getElementById('tab-PL').classList.remove('bg-forest', 'text-white');
    document.getElementById('tab-PL').classList.add('bg-stone-100', 'text-stone-600');
    document.getElementById('tab-PB').classList.remove('bg-forest', 'text-white');
    document.getElementById('tab-PB').classList.add('bg-stone-100', 'text-stone-600');
    
    // Show selected
    document.getElementById(testament + '-books').classList.remove('hidden');
    document.getElementById('tab-' + testament).classList.remove('bg-stone-100', 'text-stone-600');
    document.getElementById('tab-' + testament).classList.add('bg-forest', 'text-white');
}
</script>
@endpush
