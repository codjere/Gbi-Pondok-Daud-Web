@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="flex min-h-screen">
    <div class="relative hidden w-[45%] flex-col justify-between overflow-hidden bg-gradient-to-br from-forest-mid via-forest to-cyan-600 p-12 lg:flex dark:from-slate-800 dark:via-slate-700 dark:to-slate-600">
        <div class="relative">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold/20 text-gold"><i class="fa-solid fa-cross"></i></div>
                <div>
                    <span class="text-lg font-extrabold text-white">GBI Pondok Daud</span>
                    <p class="text-[10px] text-white/50">Portal Jemaat</p>
                </div>
            </div>
        </div>
        <blockquote class="relative text-2xl font-bold leading-snug text-white">
            "Datanglah kepad-Ku, semua yang letih lesu dan berbeban berat, Aku akan memberi kelegaan kepadamu."
        </blockquote>
        <p class="relative text-xs text-white/35">© 2026 GBI Pondok Daud</p>
    </div>

    <div class="auth-panel flex flex-1 items-center justify-center px-6 py-12">
        <div class="w-full max-w-[420px]">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-extrabold text-forest dark:text-cyan-400">Selamat datang kembali</h1>
                <button onclick="toggleDarkMode()" class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-sky-500 text-white shadow-sm ring-1 ring-cyan-200/60 hover-card dark:from-slate-700 dark:to-slate-600 dark:text-yellow-400 dark:ring-slate-600" title="Toggle Dark Mode">
                    <i class="fa-solid fa-moon text-sm dark:hidden"></i>
                    <i class="fa-solid fa-sun text-sm hidden dark:block"></i>
                </button>
            </div>
            <p class="text-sm text-stone-600 dark:text-slate-400">Masuk ke portal jemaat / pengurus GBI Pondok Daud</p>

            @include('partials.flash')

            {{-- Pemilih metode login --}}
            <div class="mt-6 grid grid-cols-2 gap-1 rounded-2xl bg-stone-100 p-1 dark:bg-slate-700/60">
                <button type="button" id="tabEmail"
                        class="rounded-xl px-3 py-2.5 text-sm font-bold transition">
                    <i class="fa-solid fa-envelope mr-1.5"></i> Email
                </button>
                <button type="button" id="tabFace"
                        class="rounded-xl px-3 py-2.5 text-sm font-bold transition">
                    <i class="fa-solid fa-face-viewfinder mr-1.5"></i> Wajah
                </button>
            </div>

            {{-- PANEL 1: Email & Password --}}
            <div id="panelEmail" class="mt-5">
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-300">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com"
                               class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-600 dark:text-white dark:focus:ring-cyan-500/40">
                    </div>
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-300">Kata Sandi</label>
                        <input type="password" id="password" name="password" placeholder="••••••••"
                               class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-600 dark:text-white dark:focus:ring-cyan-500/40">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-stone-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" value="1" class="rounded text-cyan-500 focus:ring-cyan-500/30 dark:bg-slate-700 dark:border-slate-600"> Ingat saya
                    </label>
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-forest to-forest-mid py-3.5 text-sm font-bold text-white shadow-md transition hover:from-cyan-600 hover:to-sky-600 hover:shadow-lg">
                        Lanjut &amp; Kirim OTP <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                    <p class="text-center text-xs text-stone-400 dark:text-slate-500"><i class="fa-solid fa-shield-halved mr-1"></i> Kode OTP akan dikirim ke email Anda untuk verifikasi.</p>
                </form>
            </div>

            {{-- PANEL 2: Quick Join Wajah --}}
            <div id="panelFace" class="mt-5 hidden">
                <div class="rounded-2xl border border-dashed border-cyan-300/70 bg-cyan-50/40 p-6 text-center dark:border-slate-600 dark:bg-slate-800/40">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-forest text-gold">
                        <i class="fa-solid fa-face-viewfinder text-xl"></i>
                    </div>
                    <p class="mt-3 text-sm font-bold text-forest dark:text-cyan-400">Quick Join dengan Wajah</p>
                    <p class="mt-1 text-xs text-stone-500 dark:text-slate-400">Kamera akan memindai wajah Anda otomatis, lalu OTP dikirim ke email untuk verifikasi.</p>
                    <button type="button" id="openFaceLogin"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-forest to-forest-mid py-3.5 text-sm font-bold text-white shadow-md transition hover:from-cyan-600 hover:to-sky-600">
                        <i class="fa-solid fa-camera"></i> Mulai Pindai Wajah
                    </button>
                </div>
            </div>

            <div class="mt-6 rounded-xl bg-white p-4 text-xs text-stone-600 shadow-sm ring-1 ring-cyan-200/60 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700">
                <p class="font-bold text-forest dark:text-cyan-400">Akun demo:</p>
                <p class="mt-1">Jemaat: <code class="bg-cyan-100 px-1.5 py-0.5 rounded dark:bg-slate-700 dark:text-cyan-400">andi@email.com</code> / <code class="bg-cyan-100 px-1.5 py-0.5 rounded dark:bg-slate-700 dark:text-cyan-400">password123</code></p>
                <p>Pengurus: <code class="bg-cyan-100 px-1.5 py-0.5 rounded dark:bg-slate-700 dark:text-cyan-400">admin@gbipondokdaud.id</code> / <code class="bg-cyan-100 px-1.5 py-0.5 rounded dark:bg-slate-700 dark:text-cyan-400">password123</code></p>
            </div>

            <p class="mt-6 text-center text-sm text-stone-600 dark:text-slate-400">
                Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-forest hover:underline dark:text-cyan-400">Daftar sekarang</a>
                · <a href="{{ route('landing') }}#tentang-kami" class="font-bold text-forest hover:underline dark:text-cyan-400">Tentang Kami</a>
            </p>
        </div>
    </div>
</div>

{{-- ===================== MODAL LOGIN WAJAH ===================== --}}
<div id="faceModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 p-4 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl dark:bg-slate-800">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-extrabold text-forest dark:text-cyan-400">Login dengan Wajah</h3>
            <button type="button" id="closeFaceLogin" class="flex h-8 w-8 items-center justify-center rounded-lg text-stone-400 hover:bg-stone-100 dark:hover:bg-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="relative mx-auto my-5 h-[240px] w-[240px]">
            <div id="faceRing" class="relative h-full w-full overflow-hidden rounded-full ring-4 ring-stone-300">
                <video id="faceVideo" class="h-full w-full object-cover" style="transform: scaleX(-1);" autoplay playsinline muted></video>
                <canvas id="faceCanvas" class="hidden"></canvas>
            </div>
        </div>

        <p id="faceStatus" class="text-sm font-semibold text-forest dark:text-cyan-400">
            <i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Menyiapkan kamera...
        </p>

        {{-- Fallback ke password setelah beberapa kali gagal --}}
        <div id="faceFallback" class="mt-4 hidden rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-800 ring-1 ring-amber-200">
            Wajah sulit dikenali. Anda bisa
            <button type="button" id="fallbackToPassword" class="font-bold underline">gunakan kata sandi</button>
            atau
            <button type="button" id="retryFace" class="font-bold underline">coba pindai lagi</button>.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ===== Tab switching: Email vs Wajah =====
(function () {
    const tabEmail = document.getElementById('tabEmail');
    const tabFace = document.getElementById('tabFace');
    const panelEmail = document.getElementById('panelEmail');
    const panelFace = document.getElementById('panelFace');
    const active = 'bg-white text-forest shadow-sm dark:bg-slate-800 dark:text-cyan-400';
    const inactive = 'text-stone-500 dark:text-slate-400';

    function activate(which) {
        const emailOn = which === 'email';
        panelEmail.classList.toggle('hidden', !emailOn);
        panelFace.classList.toggle('hidden', emailOn);
        tabEmail.className = 'rounded-xl px-3 py-2.5 text-sm font-bold transition ' + (emailOn ? active : inactive);
        tabFace.className = 'rounded-xl px-3 py-2.5 text-sm font-bold transition ' + (emailOn ? inactive : active);
    }
    tabEmail.addEventListener('click', () => activate('email'));
    tabFace.addEventListener('click', () => activate('face'));
    activate('email'); // default
})();

(function () {
    const FACE_LOGIN_URL = @json(route('face.login'));
    const CSRF = @json(csrf_token());
    const MAX_FAILS = 5;         // batas gagal sebelum sarankan fallback
    const INTERVAL_MS = 1500;    // jeda antar percobaan scan

    const openBtn = document.getElementById('openFaceLogin');
    const modal = document.getElementById('faceModal');
    const closeBtn = document.getElementById('closeFaceLogin');
    const video = document.getElementById('faceVideo');
    const canvas = document.getElementById('faceCanvas');
    const ring = document.getElementById('faceRing');
    const statusEl = document.getElementById('faceStatus');
    const fallbackBox = document.getElementById('faceFallback');
    const fallbackToPassword = document.getElementById('fallbackToPassword');
    const retryFace = document.getElementById('retryFace');

    let stream = null;
    let timer = null;
    let processing = false;
    let fails = 0;
    let done = false;

    function setStatus(html, color) {
        statusEl.innerHTML = html;
        statusEl.className = 'text-sm font-semibold ' + (color || 'text-forest dark:text-cyan-400');
    }
    function setRing(state) {
        ring.className = 'relative h-full w-full overflow-hidden rounded-full ring-4';
        const map = { idle: 'ring-stone-300', scanning: 'ring-gold/60', ok: 'ring-emerald-500', err: 'ring-red-400' };
        ring.classList.add(map[state] || map.idle);
    }
    function frame() {
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        return canvas.toDataURL('image/jpeg', 0.82);
    }

    async function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        fallbackBox.classList.add('hidden');
        fails = 0; done = false;
        setStatus('<i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Menyiapkan kamera...');
        setRing('idle');
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } });
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                setStatus('<i class="fa-solid fa-magnifying-glass mr-1"></i> Memindai wajah — posisikan di tengah', 'text-amber-600');
                setRing('scanning');
                timer = setInterval(scanOnce, INTERVAL_MS);
                scanOnce();
            };
        } catch (err) {
            setRing('err');
            setStatus('<i class="fa-solid fa-triangle-exclamation mr-1"></i> Kamera tidak dapat diakses. Gunakan kata sandi.', 'text-red-600');
            fallbackBox.classList.remove('hidden');
        }
    }

    function stopCamera() {
        if (timer) { clearInterval(timer); timer = null; }
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    }
    function closeModal() {
        stopCamera();
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function scanOnce() {
        if (processing || done) return;
        processing = true;
        try {
            const res = await fetch(FACE_LOGIN_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ image: frame() })
            });
            const data = await res.json().catch(() => ({}));

            if (data.success && data.recognized) {
                done = true;
                clearInterval(timer);
                setRing('ok');
                setStatus('<i class="fa-solid fa-circle-check mr-1"></i> ' + data.name + ' dikenali (' + data.similarity + '%). Mengirim OTP...', 'text-emerald-600');
                stopCamera();
                setTimeout(() => { window.location.href = data.redirect; }, 900);
                return;
            }

            if (data.code === 'service_down' || data.code === 'no_registered') {
                clearInterval(timer);
                setRing('err');
                setStatus('<i class="fa-solid fa-triangle-exclamation mr-1"></i> ' + (data.message || 'Layanan tidak tersedia.'), 'text-red-600');
                fallbackBox.classList.remove('hidden');
                processing = false;
                return;
            }

            // no_face / no_match
            fails++;
            if (fails >= MAX_FAILS) {
                clearInterval(timer);
                setRing('err');
                setStatus('<i class="fa-solid fa-face-frown mr-1"></i> Wajah belum dikenali setelah beberapa percobaan.', 'text-red-600');
                fallbackBox.classList.remove('hidden');
            } else {
                setRing('scanning');
                setStatus('<i class="fa-solid fa-magnifying-glass mr-1"></i> ' + (data.message || 'Mencari kecocokan...') + ' (' + fails + '/' + MAX_FAILS + ')', 'text-amber-600');
            }
        } catch (e) {
            setStatus('<i class="fa-solid fa-triangle-exclamation mr-1"></i> Gagal menghubungi server.', 'text-red-600');
        }
        processing = false;
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    fallbackToPassword.addEventListener('click', () => { closeModal(); document.getElementById('email').focus(); });
    retryFace.addEventListener('click', () => { closeModal(); openModal(); });
    // Tutup modal saat klik area gelap
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
})();
</script>
@endpush
