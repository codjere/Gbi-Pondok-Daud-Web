@extends('layouts.app')

@section('title', 'Alkitab Digital')
@section('breadcrumb', 'Jemaat / Alkitab Digital')

@section('content')
<div class="mx-auto max-w-6xl pattern-dots">
    @include('partials.flash')

    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Alkitab Digital</h2>
        <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">Baca firman Tuhan dengan fitur lengkap dan rencana bacaan terstruktur</p>
    </div>

    {{-- Search Section --}}
    <div class="mb-6 rounded-2xl bg-white p-4 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <form action="{{ route('jemaat.alkitab.cari') }}" method="GET" class="flex gap-3">
            <input type="text" name="kata_kunci" placeholder="Cari ayat..." 
                   class="flex-1 rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-4 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500 dark:focus:ring-cyan-500/40">
            <button type="submit" class="rounded-xl bg-gradient-to-r from-forest to-forest-mid px-6 py-2.5 text-sm font-bold text-white shadow-md hover:shadow-lg transition hover:from-cyan-600 hover:to-sky-600">
                <i class="fa-solid fa-search mr-1"></i> Cari
            </button>
        </form>
    </div>

    {{-- Reading Plans Section --}}
    <div class="mb-6 rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-bold text-forest dark:text-cyan-400">Rencana Bacaan</h3>
            <a href="{{ route('jemaat.alkitab.plans') }}" class="text-xs font-semibold text-forest hover:underline dark:text-cyan-500">
                Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($readingPlans as $plan)
                <div class="rounded-xl bg-gradient-to-br from-cyan-50 to-sky-50 p-4 dark:from-slate-600 dark:to-slate-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-bold text-forest dark:text-cyan-400">{{ $plan->nama }}</h4>
                            <p class="mt-1 text-xs text-stone-600 dark:text-slate-400">{{ $plan->deskripsi }}</p>
                            <p class="mt-2 text-xs font-semibold text-gold">{{ $plan->durasiFormatted() }}</p>
                        </div>
                        @if(isset($userProgress[$plan->id]))
                            <div class="text-right">
                                <p class="text-xs font-semibold text-cyan-600 dark:text-cyan-400">{{ $userProgress[$plan->id]->persentaseProgress() }}%</p>
                                <p class="text-[10px] text-stone-500 dark:text-slate-500">{{ $userProgress[$plan->id]->status() }}</p>
                            </div>
                        @endif
                    </div>
                    @if(isset($userProgress[$plan->id]))
                        <a href="{{ route('jemaat.alkitab.plan', $plan->id) }}" 
                           class="mt-3 block w-full rounded-xl bg-gradient-to-r from-forest to-forest-mid py-2 text-center text-xs font-bold text-white shadow-md hover:shadow-lg transition hover:from-cyan-600 hover:to-sky-600">
                            Lanjut Baca
                        </a>
                    @else
                        <form action="{{ route('jemaat.alkitab.mulai-plan') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="reading_plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-gold to-amber-500 py-2 text-xs font-bold text-forest shadow-md hover:shadow-lg transition hover:from-amber-400 hover:to-orange-400">
                                Mulai Plan
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Books Section --}}
    <div class="mb-6 rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <h3 class="mb-4 text-sm font-bold text-forest dark:text-cyan-400">Kitab-Kitab Alkitab</h3>
        
        {{-- Testament Tabs --}}
        <div class="mb-4 flex gap-2">
            <button onclick="showTestament('PL')" id="tab-PL" class="rounded-xl bg-gradient-to-r from-forest to-forest-mid px-4 py-2 text-xs font-bold text-white shadow-md transition hover:from-cyan-600 hover:to-sky-600 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                Perjanjian Lama
            </button>
            <button onclick="showTestament('PB')" id="tab-PB" class="rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-4 py-2 text-xs font-bold text-forest shadow-sm ring-1 ring-cyan-200/60 hover:from-cyan-100 hover:to-sky-100 transition dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                Perjanjian Baru
            </button>
        </div>

        {{-- PL Books --}}
        <div id="PL-books" class="grid gap-2 sm:grid-cols-4 md:grid-cols-6">
            @foreach ($kitabs->where('testament', 'PL') as $kitab)
                <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => 1]) }}" 
                   class="rounded-xl bg-gradient-to-br from-cyan-50 to-sky-50 p-3 text-center shadow-sm ring-1 ring-cyan-200/60 hover:from-cyan-100 hover:to-sky-100 transition dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                    <p class="text-xs font-bold text-forest dark:text-cyan-400">{{ $kitab->singkatan }}</p>
                    <p class="text-[10px] text-stone-600 dark:text-slate-400">{{ $kitab->jumlah_pasal }} pasal</p>
                </a>
            @endforeach
        </div>

        {{-- PB Books --}}
        <div id="PB-books" class="hidden grid gap-2 sm:grid-cols-4 md:grid-cols-6">
            @foreach ($kitabs->where('testament', 'PB') as $kitab)
                <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => 1]) }}" 
                   class="rounded-xl bg-gradient-to-br from-cyan-50 to-sky-50 p-3 text-center shadow-sm ring-1 ring-cyan-200/60 hover:from-cyan-100 hover:to-sky-100 transition dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                    <p class="text-xs font-bold text-forest dark:text-cyan-400">{{ $kitab->singkatan }}</p>
                    <p class="text-[10px] text-stone-600 dark:text-slate-400">{{ $kitab->jumlah_pasal }} pasal</p>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTestament(testament) {
    // Hide all books
    document.getElementById('PL-books').classList.add('hidden');
    document.getElementById('PB-books').classList.add('hidden');
    
    // Get tab elements
    const tabPL = document.getElementById('tab-PL');
    const tabPB = document.getElementById('tab-PB');
    const isDark = document.documentElement.classList.contains('dark');
    
    // Reset both to unselected state (light mode style)
    const unselectedClasses = ['bg-gradient-to-r', 'from-forest', 'to-forest-mid', 'text-white', 'shadow-md', 'from-cyan-600', 'to-sky-600', 'dark:from-cyan-600', 'dark:to-sky-600', 'dark:text-white'];
    const unselectedDarkClasses = ['bg-gradient-to-r', 'from-cyan-50', 'to-sky-50', 'text-forest', 'shadow-sm', 'ring-1', 'ring-cyan-200/60', 'dark:from-slate-600', 'dark:to-slate-500', 'dark:text-cyan-400'];
    
    const unselectedLight = ['bg-gradient-to-r', 'from-cyan-50', 'to-sky-50', 'text-forest', 'shadow-sm', 'ring-1', 'ring-cyan-200/60'];
    const unselectedDark = ['bg-gradient-to-r', 'from-slate-600', 'to-slate-500', 'text-cyan-400'];
    
    // Remove all possible classes
    tabPL.classList.remove(...unselectedClasses, ...unselectedLight, ...unselectedDark);
    tabPB.classList.remove(...unselectedClasses, ...unselectedLight, ...unselectedDark);
    
    // Add unselected base classes
    if (isDark) {
        tabPL.classList.add('bg-gradient-to-r', 'from-slate-600', 'to-slate-500', 'text-cyan-400', 'shadow-sm', 'ring-1', 'ring-slate-500');
        tabPB.classList.add('bg-gradient-to-r', 'from-slate-600', 'to-slate-500', 'text-cyan-400', 'shadow-sm', 'ring-1', 'ring-slate-500');
    } else {
        tabPL.classList.add('bg-gradient-to-r', 'from-cyan-50', 'to-sky-50', 'text-forest', 'shadow-sm', 'ring-1', 'ring-cyan-200/60');
        tabPB.classList.add('bg-gradient-to-r', 'from-cyan-50', 'to-sky-50', 'text-forest', 'shadow-sm', 'ring-1', 'ring-cyan-200/60');
    }
    
    // Show selected books
    document.getElementById(testament + '-books').classList.remove('hidden');
    
    // Set active button style
    const activeTab = document.getElementById('tab-' + testament);
    
    // Remove all possible classes from active tab
    activeTab.classList.remove(...unselectedClasses, ...unselectedLight, ...unselectedDark);
    
    // Add active classes based on dark mode
    if (isDark) {
        activeTab.classList.add('bg-gradient-to-r', 'from-cyan-600', 'to-sky-600', 'text-white', 'shadow-md');
    } else {
        activeTab.classList.add('bg-gradient-to-r', 'from-forest', 'to-forest-mid', 'text-white', 'shadow-md');
    }
}
</script>
@endpush
