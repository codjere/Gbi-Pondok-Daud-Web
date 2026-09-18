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
        @if ($ayats->isEmpty())
            {{-- Teks pasal belum tersedia di database --}}
            <div class="py-10 text-center">
                <i class="fa-solid fa-book-bible text-3xl text-stone-300"></i>
                <h3 class="mt-3 text-base font-bold text-forest">Teks {{ $kitab->nama }} {{ $pasal->nomor_pasal }} Belum Tersedia</h3>
                <p class="mx-auto mt-2 max-w-md text-sm text-stone-500">
                    Isi ayat untuk pasal ini belum dimuat ke sistem. Silakan lanjut ke pasal lain yang sudah tersedia,
                    atau hubungi pengurus untuk melengkapi teks Alkitab.
                </p>
                <div class="mt-5 flex flex-wrap justify-center gap-2">
                    @if($pasalSebelumnya)
                        <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => $pasalSebelumnya]) }}"
                           class="rounded-xl bg-stone-100 px-4 py-2 text-sm font-bold text-stone-600 hover:bg-stone-200">
                            <i class="fa-solid fa-chevron-left mr-1"></i> Pasal {{ $pasalSebelumnya }}
                        </a>
                    @endif
                    @if($pasalBerikutnya)
                        <a href="{{ route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => $pasalBerikutnya]) }}"
                           class="rounded-xl bg-forest px-4 py-2 text-sm font-bold text-white hover:bg-forest-mid">
                            Pasal {{ $pasalBerikutnya }} <i class="fa-solid fa-chevron-right ml-1"></i>
                        </a>
                    @endif
                    <a href="{{ route('jemaat.alkitab') }}"
                       class="rounded-xl bg-gold-soft px-4 py-2 text-sm font-bold text-forest hover:bg-gold/30">
                        <i class="fa-solid fa-list mr-1"></i> Daftar Kitab
                    </a>
                </div>
            </div>
        @else
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
        @endif
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
// Initialize speech synthesis
let speechSynthesis = window.speechSynthesis;
let speechUtterance = null;
let isSpeaking = false;
let speechRate = 1;
let currentChunkIndex = 0;
let textChunks = [];
let isPaused = false;

// Check browser support
if (!('speechSynthesis' in window)) {
    console.error('Browser tidak mendukung Web Speech API');
    document.getElementById('speechBtn').disabled = true;
    document.getElementById('speechBtn').textContent = 'Tidak Didukung';
}

// Split text into smaller chunks to avoid interruption
function splitTextIntoChunks(text, maxChunkLength = 200) {
    const chunks = [];
    const sentences = text.split(/[.!?]+/);
    let currentChunk = '';
    
    sentences.forEach(sentence => {
        if (sentence.trim()) {
            const sentenceWithPunctuation = sentence.trim() + '. ';
            if ((currentChunk + sentenceWithPunctuation).length <= maxChunkLength) {
                currentChunk += sentenceWithPunctuation;
            } else {
                if (currentChunk) {
                    chunks.push(currentChunk.trim());
                }
                currentChunk = sentenceWithPunctuation;
            }
        }
    });
    
    if (currentChunk) {
        chunks.push(currentChunk.trim());
    }
    
    return chunks;
}

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
    if (!speechSynthesis) {
        alert('Browser Anda tidak mendukung fitur text-to-speech');
        return;
    }
    
    if (isSpeaking) {
        if (isPaused) {
            speechSynthesis.resume();
            isPaused = false;
            updateSpeechButton(true);
        } else {
            speechSynthesis.pause();
            isPaused = true;
            updateSpeechButton(false);
        }
    } else {
        startSpeech();
    }
}

function startSpeech() {
    if (!speechSynthesis) {
        alert('Browser Anda tidak mendukung fitur text-to-speech');
        return;
    }

    // Cancel any ongoing speech
    speechSynthesis.cancel();
    isPaused = false;
    
    // Get text and split into chunks
    const text = getBibleText();
    
    if (!text || text.trim() === '') {
        alert('Tidak ada teks untuk dibacakan');
        return;
    }
    
    console.log('Memulai pembacaan dengan chunking...');
    
    // Split text into chunks
    textChunks = splitTextIntoChunks(text, 150);
    currentChunkIndex = 0;
    
    console.log(`Total chunks: ${textChunks.length}`);
    
    // Start speaking first chunk
    speakNextChunk();
}

function speakNextChunk() {
    if (currentChunkIndex >= textChunks.length) {
        console.log('Semua chunks selesai');
        isSpeaking = false;
        updateSpeechButton(false);
        return;
    }
    
    const chunk = textChunks[currentChunkIndex];
    console.log(`Membacakan chunk ${currentChunkIndex + 1}/${textChunks.length}:`, chunk.substring(0, 30) + '...');
    
    speechUtterance = new SpeechSynthesisUtterance(chunk);
    
    // Settings
    speechUtterance.lang = 'id-ID';
    speechUtterance.rate = speechRate;
    speechUtterance.pitch = 1;
    speechUtterance.volume = 1;

    // Events
    speechUtterance.onstart = () => {
        if (currentChunkIndex === 0) {
            console.log('Pembacaan dimulai');
            isSpeaking = true;
            updateSpeechButton(true);
        }
    };

    speechUtterance.onend = () => {
        console.log(`Chunk ${currentChunkIndex + 1} selesai`);
        currentChunkIndex++;
        
        // Small delay before next chunk
        setTimeout(() => {
            if (isSpeaking && !isPaused) {
                speakNextChunk();
            }
        }, 100);
    };

    speechUtterance.onerror = (event) => {
        console.error('Speech error pada chunk', currentChunkIndex + 1, ':', event.error);
        
        // Try to continue to next chunk even if current one fails
        if (event.error === 'interrupted' || event.error === 'canceled') {
            console.log('Error interrupt/cancel, lanjut ke chunk berikutnya');
            currentChunkIndex++;
            setTimeout(() => {
                if (isSpeaking && !isPaused) {
                    speakNextChunk();
                }
            }, 200);
        } else {
            isSpeaking = false;
            updateSpeechButton(false);
            alert('Terjadi kesalahan saat pembacaan: ' + event.error);
        }
    };

    speechSynthesis.speak(speechUtterance);
}

function stopSpeech() {
    if (speechSynthesis) {
        speechSynthesis.cancel();
        isSpeaking = false;
        isPaused = false;
        currentChunkIndex = 0;
        updateSpeechButton(false);
        console.log('Pembacaan dihentikan');
    }
}

function updateSpeechRate() {
    speechRate = parseFloat(document.getElementById('speechRate').value);
    document.getElementById('rateValue').textContent = speechRate + 'x';
    
    if (isSpeaking) {
        // Need to restart with new rate
        const wasPaused = isPaused;
        stopSpeech();
        setTimeout(() => {
            startSpeech();
            if (wasPaused) {
                speechSynthesis.pause();
                isPaused = true;
                updateSpeechButton(false);
            }
        }, 200);
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
        text.textContent = 'Mulai Baca';
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    console.log('Halaman dimuat, text-to-speech siap');
    
    if (speechSynthesis) {
        console.log('Web Speech API tersedia');
    } else {
        console.error('Web Speech API tidak tersedia');
    }
});
</script>
@endpush
