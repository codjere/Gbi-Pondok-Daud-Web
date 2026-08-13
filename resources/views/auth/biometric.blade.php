@extends('layouts.guest')

@section('title', 'Perekaman Wajah')

@push('styles')
<style>
    .cam-preview { transform: scaleX(-1); }
    @keyframes pulse-ring { 0%, 100% { box-shadow: 0 0 0 0 rgba(201,149,42,0.4); } 50% { box-shadow: 0 0 0 14px rgba(201,149,42,0); } }
    .cam-active { animation: pulse-ring 2s ease-in-out infinite; }
</style>
@endpush

@section('content')
<div class="flex min-h-screen flex-col items-center justify-center px-6 py-10">
    <p class="rounded-full bg-gold-soft px-3 py-1 text-[11px] font-bold text-forest">Langkah 2/2 · GBI Pondok Daud</p>
    <h1 class="mt-4 text-2xl font-extrabold text-forest">Perekaman Biometrik Wajah</h1>
    <p class="mt-1 text-sm text-stone-500">FaceNet · embedding 512-d → PostgreSQL</p>

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
            <div id="camRing" class="relative h-[280px] w-[280px] overflow-hidden rounded-full ring-4 ring-gold/40 sm:h-[320px] sm:w-[320px] cam-active">
                <video id="video" class="cam-preview h-full w-full object-cover" autoplay playsinline muted></video>
                <canvas id="canvas" class="hidden"></canvas>
            </div>
            <div class="mt-4 text-center">
                <p id="statusText" class="text-sm font-semibold text-forest">Menyiapkan kamera...</p>
                <div class="mx-auto mt-3 h-2 w-48 overflow-hidden rounded-full bg-stone-200">
                    <div id="progressBar" class="h-full rounded-full bg-gold transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="countText" class="mt-2 text-xs text-stone-500">0 / {{ config('facenet.samples_required') }} foto</p>
            </div>
        </div>

        <form id="biometricForm" action="{{ route('register.biometric.store') }}" method="POST">
            @csrf
            <div id="imagesContainer"></div>
            <button type="button" id="captureBtn" disabled
                    class="rounded-2xl bg-forest px-8 py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-forest-mid disabled:cursor-not-allowed disabled:opacity-50">
                <i class="fa-solid fa-camera mr-2"></i> Ambil Foto Sampel
            </button>
            <button type="submit" id="submitBtn" disabled
                    class="ml-3 rounded-2xl bg-gold px-8 py-3.5 text-sm font-bold text-forest shadow-lg transition hover:bg-gold/90 disabled:cursor-not-allowed disabled:opacity-50">
                <i class="fa-solid fa-check mr-2"></i> Simpan & Selesai
            </button>
        </form>
    @endif
</div>
@endsection

@push('scripts')
@if ($serviceOnline)
<script>
(function () {
    const REQUIRED = {{ config('facenet.samples_required') }};
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const captureBtn = document.getElementById('captureBtn');
    const submitBtn = document.getElementById('submitBtn');
    const statusText = document.getElementById('statusText');
    const countText = document.getElementById('countText');
    const progressBar = document.getElementById('progressBar');
    const imagesContainer = document.getElementById('imagesContainer');
    const captured = [];

    async function initCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
            });
            video.srcObject = stream;
            statusText.textContent = 'Kamera siap. Arahkan wajah ke kamera lalu ambil foto.';
            captureBtn.disabled = false;
        } catch (err) {
            statusText.textContent = 'Kamera tidak dapat diakses. Izinkan akses kamera di browser.';
            console.error(err);
        }
    }

    captureBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        captured.push(dataUrl);

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'images[]';
        input.value = dataUrl;
        imagesContainer.appendChild(input);

        const pct = Math.round((captured.length / REQUIRED) * 100);
        progressBar.style.width = pct + '%';
        countText.textContent = captured.length + ' / ' + REQUIRED + ' foto';
        statusText.textContent = captured.length >= REQUIRED
            ? 'Cukup! Klik "Simpan & Selesai" untuk menyelesaikan registrasi.'
            : 'Foto ' + captured.length + ' tersimpan. Ubah sedikit posisi wajah, lalu ambil lagi.';

        if (captured.length >= REQUIRED) {
            submitBtn.disabled = false;
            captureBtn.disabled = true;
        }
    });

    document.getElementById('biometricForm').addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses FaceNet...';
    });

    initCamera();
})();
</script>
@endif
@endpush
