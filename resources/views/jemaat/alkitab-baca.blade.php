@extends('layouts.app')

@section('title', $pasal->namaLengkap())
@section('breadcrumb', 'Jemaat / Alkitab / ' . $pasal->namaLengkap())

@section('content')
<div class="mx-auto max-w-4xl">
    @include('partials.flash')

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-forest">{{ $pasal->namaLengkap() }}</h2>
            <p class="mt-1 text-sm text-stone-500">{{ $kitab->nama }} - Pasal {{ $pasal->nomor_pasal }}</p>
        </div>
        <div class="flex gap-2">
            {{-- Navigation Buttons --}}
            @if($pasal->nomor_pasal > 1)
                <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => $pasal->nomor_pasal - 1]) }}" 
                   class="rounded-xl bg-stone-100 px-4 py-2 text-sm font-bold text-stone-600 hover:bg-stone-200">
                    <i class="fa-solid fa-chevron-left mr-1"></i> Pasal {{ $pasal->nomor_pasal - 1 }}
                </a>
            @endif
            @if($pasal->nomor_pasal < $kitab->jumlah_pasal)
                <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => $pasal->nomor_pasal + 1]) }}" 
                   class="rounded-xl bg-forest px-4 py-2 text-sm font-bold text-white hover:bg-forest-mid">
                    Pasal {{ $pasal->nomor_pasal + 1 }} <i class="fa-solid fa-chevron-right ml-1"></i>
                </a>
            @endif
        </div>
    </div>

    {{-- Text-to-Speech Controls --}}
    <div class="mb-6 rounded-2xl bg-white p-4 ring-1 ring-stone-200/80">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-forest">Audio Pembacaan</h3>
                <p class="text-xs text-stone-500">Dengarkan firman Tuhan dibacakan</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="toggleSpeech()" id="speechBtn" 
                        class="rounded-xl bg-gold px-4 py-2 text-sm font-bold text-forest hover:bg-gold-soft">
                    <i class="fa-solid fa-play mr-1" id="speechIcon"></i> <span id="speechText">Mulai Baca</span>
                </button>
                <button onclick="stopSpeech()" 
                        class="rounded-xl bg-red-50 px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-100">
                    <i class="fa-solid fa-stop mr-1"></i> Stop
                </button>
            </div>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold text-stone-500">Kecepatan Suara</label>
            <input type="range" id="speechRate" min="0.5" max="2" step="0.1" value="1" 
                   class="w-full" onchange="updateSpeechRate()">
            <div class="flex justify-between text-[10px] text-stone-400">
                <span>Lambat</span>
                <span id="rateValue">1x</span>
                <span>Cepat</span>
            </div>
        </div>
    </div>

    {{-- Bible Content --}}
    <div class="rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
        <div id="bibleContent" class="space-y-4">
            @foreach ($ayats as $ayat)
                <div class="flex gap-3 p-3 rounded-lg hover:bg-cream/50">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gold-soft text-xs font-bold text-forest">
                        {{ $ayat->nomor_ayat }}
                    </span>
                    <p class="flex-1 text-sm text-stone-700 leading-relaxed">{{ $ayat->isi_ayat }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Mark as Complete Button --}}
    @if(auth()->check())
        <div class="mt-6 text-center">
            <button onclick="markAsComplete()" id="completeBtn"
                    class="rounded-xl bg-emerald-50 px-6 py-3 text-sm font-bold text-emerald-700 hover:bg-emerald-100">
                <i class="fa-solid fa-check mr-1"></i> Tandai Selesai Dibaca
            </button>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
let speechSynthesis = window.speechSynthesis;
let speechUtterance = null;
let isSpeaking = false;
let speechRate = 1;

// Get all verse text for reading
function getBibleText() {
    const verses = document.querySelectorAll('#bibleContent p');
    let text = '';
    verses.forEach((verse, index) => {
        text += `Ayat ${index + 1}. ${verse.textContent} `;
    });
    return text;
}

function toggleSpeech() {
    if (isSpeaking) {
        speechSynthesis.pause();
        isSpeaking = false;
        updateSpeechButton(false);
    } else {
        if (speechSynthesis.paused) {
            speechSynthesis.resume();
            isSpeaking = true;
            updateSpeechButton(true);
        } else {
            startSpeech();
        }
    }
}

function startSpeech() {
    if (!speechSynthesis) {
        alert('Browser Anda tidak mendukung fitur text-to-speech');
        return;
    }

    // Cancel any ongoing speech
    speechSynthesis.cancel();

    const text = getBibleText();
    speechUtterance = new SpeechSynthesisUtterance(text);
    
    // Set language to Indonesian
    speechUtterance.lang = 'id-ID';
    speechUtterance.rate = speechRate;
    speechUtterance.pitch = 1;

    // Events
    speechUtterance.onstart = () => {
        isSpeaking = true;
        updateSpeechButton(true);
    };

    speechUtterance.onend = () => {
        isSpeaking = false;
        updateSpeechButton(false);
    };

    speechUtterance.onerror = () => {
        isSpeaking = false;
        updateSpeechButton(false);
    };

    speechSynthesis.speak(speechUtterance);
}

function stopSpeech() {
    speechSynthesis.cancel();
    isSpeaking = false;
    updateSpeechButton(false);
}

function updateSpeechRate() {
    speechRate = parseFloat(document.getElementById('speechRate').value);
    document.getElementById('rateValue').textContent = speechRate + 'x';
    
    if (isSpeaking) {
        stopSpeech();
        startSpeech();
    }
}

function updateSpeechButton(speaking) {
    const btn = document.getElementById('speechBtn');
    const icon = document.getElementById('speechIcon');
    const text = document.getElementById('speechText');
    
    if (speaking) {
        icon.className = 'fa-solid fa-pause mr-1';
        text.textContent = 'Pause';
        btn.classList.remove('bg-gold', 'text-forest');
        btn.classList.add('bg-amber-500', 'text-white');
    } else {
        icon.className = 'fa-solid fa-play mr-1';
        text.textContent = 'Lanjut Baca';
        btn.classList.remove('bg-amber-500', 'text-white');
        btn.classList.add('bg-gold', 'text-forest');
    }
}

function markAsComplete() {
    const pasalId = {{ $pasal->id }};
    
    fetch('{{ route("jemaat.alkitab.selesai-baca") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            pasal_id: pasalId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById('completeBtn');
            btn.innerHTML = '<i class="fa-solid fa-check-circle mr-1"></i> Sudah Selesai';
            btn.classList.remove('bg-emerald-50', 'text-emerald-700', 'hover:bg-emerald-100');
            btn.classList.add('bg-emerald-500', 'text-white', 'cursor-default');
            btn.disabled = true;
            
            // Show notification
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan progress.');
    });
}

// Cleanup when leaving page
window.addEventListener('beforeunload', () => {
    if (speechSynthesis) {
        speechSynthesis.cancel();
    }
});
</script>
@endpush
