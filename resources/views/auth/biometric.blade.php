@extends('layouts.guest')

@section('title', 'Perekaman Wajah')

@push('styles')
<style>
    .cam-preview { transform: scaleX(-1); }
    @keyframes pulse-ring { 0%, 100% { box-shadow: 0 0 0 0 rgba(201,149,42,0.4); } 50% { box-shadow: 0 0 0 14px rgba(201,149,42,0); } }
    .ring-scanning { animation: pulse-ring 2s ease-in-out infinite; }
    @keyframes scanline { 0% { top: 4%; } 100% { top: 96%; } }
    .scan-line { position: absolute; left: 8%; right: 8%; height: 2px; background: linear-gradient(90deg, transparent, rgba(245,185,66,0.9), transparent); animation: scanline 1.6s ease-in-out infinite alternate; }
</style>
@endpush

@section('content')
<div class="flex min-h-screen flex-col items-center justify-center px-6 py-10">
    <p class="rounded-full bg-gold-soft px-3 py-1 text-[11px] font-bold text-forest">Langkah 3/3 · GBI Pondok Daud</p>
    <h1 class="mt-4 text-2xl font-extrabold text-forest dark:text-cyan-400">Perekaman Biometrik Wajah</h1>
    <p class="mt-1 text-sm text-stone-500 dark:text-slate-400">FaceNet · embedding 512-d → PostgreSQL · pemindaian otomatis</p>

    @include('partials.flash')

    @error('biometric')
        <div class="mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">{{ $message }}</div>
    @enderror

    @if (!$serviceOnline)
        <div class="mt-6 max-w-md rounded-2xl bg-amber-50 p-5 text-center ring-1 ring-amber-200">
            <i class="fa-solid fa-triangle-exclamation text-2xl text-amber-600"></i>
            <p class="mt-2 text-sm font-semibold text-amber-800">Layanan FaceNet belum aktif</p>
            <p class="mt-1 text-xs text-amber-700">Jalankan <code class="rounded bg-amber-100 px-1">face-service/start.bat</code> terlebih dahulu, lalu refresh halaman ini.</p>
        </div>
    @else
        <div class="relative my-8">
            <div id="camRing" class="relative h-[280px] w-[280px] overflow-hidden rounded-full ring-4 ring-gold/40 sm:h-[320px] sm:w-[320px]">
                <video id="video" class="cam-preview h-full w-full object-cover" autoplay playsinline muted></video>
                <canvas id="canvas" class="hidden"></canvas>
                <div id="scanLine" class="scan-line hidden"></div>
            </div>
            <div class="mt-4 text-center">
                <p id="statusText" class="text-sm font-semibold text-forest dark:text-cyan-400">
                    <i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Menyiapkan kamera...
                </p>
                <div class="mx-auto mt-3 h-2 w-48 overflow-hidden rounded-full bg-stone-200 dark:bg-slate-700">
                    <div id="progressBar" class="h-full rounded-full bg-gold transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="countText" class="mt-2 text-xs text-stone-500 dark:text-slate-400">0 / {{ config('facenet.samples_required') }} sampel</p>
            </div>
        </div>

        <form id="biometricForm" action="{{ route('register.biometric.store') }}" method="POST">
            @csrf
            <div id="imagesContainer"></div>
            <div class="flex items-center justify-center gap-3">
                <button type="button" id="restartBtn"
                        class="hidden rounded-2xl bg-stone-100 px-6 py-3 text-sm font-bold text-stone-600 transition hover:bg-stone-200 dark:bg-slate-700 dark:text-slate-200">
                    <i class="fa-solid fa-rotate-right mr-2"></i> Ulangi Pindai
                </button>
            </div>
        </form>

        <p class="mt-6 max-w-md text-center text-xs text-stone-400 dark:text-slate-500">
            Posisikan wajah di tengah lingkaran dan tahan sebentar. Sistem menangkap sampel otomatis —
            gerakkan sedikit kepala (kiri/kanan) untuk variasi yang lebih baik.
        </p>
    @endif
</div>
@endsection

@push('scripts')
@if ($serviceOnline)
<script>
(function () {
    const REQUIRED = {{ (int) config('facenet.samples_required') }};
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const camRing = document.getElementById('camRing');
    const scanLine = document.getElementById('scanLine');
    const statusText = document.getElementById('statusText');
    const countText = document.getElementById('countText');
    const progressBar = document.getElementById('progressBar');
    const imagesContainer = document.getElementById('imagesContainer');
    const restartBtn = document.getElementById('restartBtn');
    const form = document.getElementById('biometricForm');

    const captured = [];
    let scanTimer = null;
    let faceDetector = null;
    let stableFrames = 0;          // berapa siklus berturut wajah terdeteksi
    const STABLE_NEEDED = 2;       // butuh stabil ~2 siklus sebelum ambil
    const INTERVAL_MS = 700;       // jeda antar pengecekan
    let finished = false;

    function setStatus(html, colorClass) {
        statusText.innerHTML = html;
        statusText.className = 'text-sm font-semibold ' + (colorClass || 'text-forest dark:text-cyan-400');
    }
    function setRing(state) {
        // state: idle | scanning | detected | success | error
        camRing.className = 'relative h-[280px] w-[280px] overflow-hidden rounded-full ring-4 sm:h-[320px] sm:w-[320px]';
        const map = {
            idle: 'ring-stone-300',
            scanning: 'ring-gold/50 ring-scanning',
            detected: 'ring-emerald-400',
            success: 'ring-emerald-500',
            error: 'ring-red-400',
        };
        camRing.classList.add(...(map[state] || map.idle).split(' '));
    }

    function captureFrame() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0);
        return canvas.toDataURL('image/jpeg', 0.85);
    }

    function addSample() {
        const dataUrl = captureFrame();
        captured.push(dataUrl);

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'images[]';
        input.value = dataUrl;
        imagesContainer.appendChild(input);

        const pct = Math.round((captured.length / REQUIRED) * 100);
        progressBar.style.width = pct + '%';
        countText.textContent = captured.length + ' / ' + REQUIRED + ' sampel';
    }

    async function detectFacePresent() {
        // Jika FaceDetector tersedia -> deteksi wajah nyata (indikator liveness).
        if (faceDetector) {
            try {
                const faces = await faceDetector.detect(video);
                return faces && faces.length > 0;
            } catch (_) {
                return true; // fallback: anggap ada
            }
        }
        // Fallback: tidak ada deteksi -> anggap wajah ada (ambil berbasis waktu).
        return true;
    }

    async function scanTick() {
        if (finished) return;

        const present = await detectFacePresent();

        if (present) {
            stableFrames++;
            if (stableFrames >= STABLE_NEEDED) {
                setRing('detected');
                setStatus('<i class="fa-solid fa-camera mr-1"></i> Menangkap sampel ' + (captured.length + 1) + '...', 'text-emerald-600');
                addSample();
                stableFrames = 0;

                if (captured.length >= REQUIRED) {
                    finishAndSubmit();
                    return;
                }
                setStatus('<i class="fa-solid fa-face-smile mr-1"></i> Bagus! Gerakkan sedikit kepala untuk variasi...', 'text-forest dark:text-cyan-400');
            } else {
                setRing('detected');
                setStatus('<i class="fa-solid fa-face-viewfinder mr-1"></i> Wajah terdeteksi, tahan posisi...', 'text-emerald-600');
            }
        } else {
            stableFrames = 0;
            setRing('scanning');
            setStatus('<i class="fa-solid fa-magnifying-glass mr-1"></i> Mendeteksi wajah — posisikan di tengah & pencahayaan cukup', 'text-amber-600');
        }
    }

    function finishAndSubmit() {
        finished = true;
        clearInterval(scanTimer);
        scanLine.classList.add('hidden');
        setRing('success');
        progressBar.style.width = '100%';
        setStatus('<i class="fa-solid fa-circle-check mr-1"></i> Sampel lengkap! Memproses FaceNet...', 'text-emerald-600');
        // Submit form (server yang ekstraksi embedding & simpan)
        form.requestSubmit ? form.requestSubmit() : form.submit();
    }

    function startScanning() {
        finished = false;
        captured.length = 0;
        imagesContainer.innerHTML = '';
        stableFrames = 0;
        progressBar.style.width = '0%';
        countText.textContent = '0 / ' + REQUIRED + ' sampel';
        restartBtn.classList.add('hidden');
        scanLine.classList.remove('hidden');
        setRing('scanning');
        setStatus('<i class="fa-solid fa-magnifying-glass mr-1"></i> Mendeteksi wajah...', 'text-amber-600');
        scanTimer = setInterval(scanTick, INTERVAL_MS);
    }

    async function initCamera() {
        setStatus('<i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Menyiapkan kamera...');
        setRing('idle');
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
            });
            video.srcObject = stream;

            // Coba aktifkan FaceDetector API bila didukung browser
            if ('FaceDetector' in window) {
                try { faceDetector = new window.FaceDetector({ fastMode: true, maxDetectedFaces: 1 }); } catch (_) { faceDetector = null; }
            }

            video.onloadedmetadata = () => {
                setStatus('<i class="fa-solid fa-face-viewfinder mr-1"></i> Kamera siap. Pemindaian otomatis dimulai...', 'text-forest dark:text-cyan-400');
                setTimeout(startScanning, 600);
            };
        } catch (err) {
            setRing('error');
            setStatus('<i class="fa-solid fa-triangle-exclamation mr-1"></i> Kamera tidak dapat diakses. Izinkan akses kamera di browser.', 'text-red-600');
            console.error(err);
        }
    }

    restartBtn.addEventListener('click', startScanning);

    form.addEventListener('submit', () => {
        setStatus('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses FaceNet & menyimpan...', 'text-emerald-600');
    });

    initCamera();
})();
</script>
@endif
@endpush
