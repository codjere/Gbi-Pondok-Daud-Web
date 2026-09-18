@extends('layouts.app')

@section('title', $plan->nama)
@section('breadcrumb', 'Jemaat / Alkitab / ' . $plan->nama)

@section('content')
<div class="mx-auto max-w-5xl">
    @include('partials.flash')

    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-forest">{{ $plan->nama }}</h2>
        <p class="mt-1 text-sm text-stone-500">{{ $plan->deskripsi }}</p>
    </div>

    {{-- Progress Overview --}}
    <div class="mb-6 rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-bold text-forest">Progress Anda</h3>
            <span class="text-lg font-bold text-emerald-600">{{ $progress->persentaseProgress() }}%</span>
        </div>
        <div class="h-3 overflow-hidden rounded-full bg-stone-200">
            <div class="h-full rounded-full bg-emerald-500 transition-all" 
                 style="width: {{ $progress->persentaseProgress() }}%"></div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-2xl font-bold text-forest">{{ $progress->hari_terakhir }}</p>
                <p class="text-xs text-stone-500">Hari Terakhir</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-forest">{{ $plan->durasi_hari }}</p>
                <p class="text-xs text-stone-500">Total Hari</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-forest">{{ $plan->durasi_hari - $progress->hari_terakhir }}</p>
                <p class="text-xs text-stone-500">Sisa Hari</p>
            </div>
        </div>
    </div>

    {{-- Today's Reading --}}
    @if($bacaanHariIni)
        <div class="mb-6 rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-forest">Bacaan Hari Ini</h3>
                <span class="rounded-full bg-gold-soft px-3 py-1 text-xs font-bold text-gold">
                    Hari ke-{{ $hariBerikutnya }}
                </span>
            </div>
            
            <div class="mb-4">
                <p class="text-lg font-semibold text-forest">{{ $bacaanHariIni->deskripsiBacaan() }}</p>
            </div>

            {{-- Reading Links --}}
            <div class="mb-4 space-y-2">
                @foreach($bacaanHariIni->bacaan as $bacaan)
                    @php
                        $kitab = \App\Models\Kitab::find($bacaan['kitab_id']);
                    @endphp
                    @if($kitab)
                        @if($bacaan['pasal_mulai'] == $bacaan['pasal_selesai'])
                            <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => $bacaan['pasal_mulai']]) }}" 
                               class="flex items-center gap-3 rounded-xl bg-cream p-3 hover:bg-gold-soft transition-colors">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gold text-sm font-bold text-forest">
                                    {{ $bacaan['pasal_mulai'] }}
                                </div>
                                <div>
                                    <p class="font-semibold text-forest">{{ $kitab->nama }} {{ $bacaan['pasal_mulai'] }}</p>
                                    <p class="text-xs text-stone-500">{{ $kitab->singkatan }} {{ $bacaan['pasal_mulai'] }}</p>
                                </div>
                                <i class="fa-solid fa-chevron-right ml-auto text-stone-400"></i>
                            </a>
                        @else
                            {{-- Multiple chapters --}}
                            <div class="rounded-xl bg-cream p-3">
                                <p class="font-semibold text-forest">{{ $kitab->nama }} {{ $bacaan['pasal_mulai'] }}-{{ $bacaan['pasal_selesai'] }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @for($i = $bacaan['pasal_mulai']; $i <= $bacaan['pasal_selesai']; $i++)
                                        <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => $i]) }}" 
                                           class="rounded-lg bg-white px-3 py-1 text-xs font-semibold text-forest hover:bg-gold-soft">
                                            {{ $i }}
                                        </a>
                                    @endfor
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>

            {{-- Mark as Complete Button --}}
            <button onclick="markTodayComplete()" id="completeBtn"
                    class="w-full rounded-xl bg-emerald-50 py-3 text-sm font-bold text-emerald-700 hover:bg-emerald-100">
                <i class="fa-solid fa-check mr-1"></i> Tandai Hari Ini Selesai
            </button>
        </div>
    @elseif($planSelesai)
        {{-- Plan benar-benar selesai --}}
        <div class="mb-6 rounded-2xl bg-emerald-50 p-6 text-center ring-1 ring-emerald-200">
            <i class="fa-solid fa-trophy text-3xl text-emerald-600"></i>
            <h3 class="mt-3 text-lg font-bold text-emerald-800">Selamat! 🎉</h3>
            <p class="mt-1 text-sm text-emerald-700">Anda telah menyelesaikan seluruh rencana bacaan ini!</p>
            <p class="mt-2 text-xs text-emerald-600">
                Selesai pada: {{ $progress->tanggal_selesai ? $progress->tanggal_selesai->format('d M Y') : 'Hari ini' }}
            </p>
        </div>
    @else
        {{-- Plan belum selesai, tetapi jadwal bacaan hari ini belum tersedia --}}
        <div class="mb-6 rounded-2xl bg-amber-50 p-6 text-center ring-1 ring-amber-200">
            <i class="fa-solid fa-hourglass-half text-3xl text-amber-500"></i>
            <h3 class="mt-3 text-lg font-bold text-amber-800">Bacaan Hari ke-{{ $progress->hari_terakhir + 1 }} Belum Tersedia</h3>
            <p class="mt-1 text-sm text-amber-700">Jadwal bacaan untuk hari ini belum disiapkan pengurus. Rencana ini <strong>belum selesai</strong> — silakan cek kembali nanti atau lanjutkan membaca kitab secara mandiri.</p>
            <a href="{{ route('jemaat.alkitab') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-forest px-5 py-2.5 text-xs font-bold text-white hover:bg-forest-mid">
                <i class="fa-solid fa-book-bible"></i> Baca Alkitab
            </a>
        </div>
    @endif

    <div class="text-center">
        <a href="{{ route('jemaat.alkitab.plans') }}" class="text-sm font-semibold text-forest hover:underline">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Rencana Bacaan
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markTodayComplete() {
    const planId = {{ $plan->id }};
    
    fetch('{{ route("jemaat.alkitab.selesai-harian") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            reading_plan_id: planId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan progress.');
    });
}
</script>
@endpush
