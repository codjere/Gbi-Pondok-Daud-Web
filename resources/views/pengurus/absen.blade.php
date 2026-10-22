@extends('layouts.app')

@section('title', 'Absen Pelayan')
@section('breadcrumb', 'Pengurus / Absensi Face Recognition')

@push('styles')
<style>
    .cam-preview { transform: scaleX(-1); }
    @keyframes scan-line { 0% { top: 10%; } 100% { top: 90%; } }
    .scan-line { animation: scan-line 2s ease-in-out infinite alternate; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-6xl pattern-dots">
    @include('partials.flash')

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Absensi Pelayan — Face Recognition</h2>
            <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">Verifikasi kehadiran pelayan/jemaat menggunakan FaceNet + Cosine Similarity</p>
        </div>
        <div class="flex items-center gap-2">
            @if ($serviceOnline)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-emerald-50 to-green-50 px-3 py-1.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 dark:from-slate-600 dark:to-slate-500 dark:text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> FaceNet Aktif
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-red-50 to-orange-50 px-3 py-1.5 text-xs font-bold text-red-700 ring-1 ring-red-200 dark:from-slate-600 dark:to-slate-500 dark:text-red-400">
                    <span class="h-2 w-2 rounded-full bg-red-500"></span> Service Offline
                </span>
            @endif
            <span class="rounded-full bg-gradient-to-r from-gold-soft to-amber-100 px-3 py-1.5 text-xs font-bold text-forest dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">{{ $registeredCount }} wajah terdaftar</span>
        </div>
    </div>

    @if (!$serviceOnline)
        <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 p-8 text-center shadow-md ring-1 ring-amber-200 dark:from-slate-600 dark:to-slate-700">
            <i class="fa-solid fa-server text-3xl text-amber-600 dark:text-amber-400"></i>
            <p class="mt-3 text-sm font-semibold text-amber-800 dark:text-amber-300">Layanan FaceNet belum berjalan</p>
            <p class="mt-1 text-xs text-amber-700 dark:text-amber-400">Buka terminal dan jalankan: <code class="rounded bg-gradient-to-r from-amber-100 to-orange-100 px-2 py-0.5 dark:from-slate-500 dark:to-slate-600">face-service\start.bat</code></p>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-5">
            {{-- Kamera --}}
            <div class="lg:col-span-3">
                <div class="overflow-hidden rounded-2xl bg-stone-900 ring-1 ring-stone-200/80">
                    <div class="relative aspect-video bg-stone-800">
                        <video id="video" class="cam-preview h-full w-full object-cover" autoplay playsinline muted></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        <div class="scan-line absolute left-4 right-4 h-[2px] rounded-full bg-gold/70 shadow-[0_0_12px_rgba(201,149,42,0.8)]"></div>
                        <div id="overlay" class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 transition-opacity pointer-events-none">
                            <div id="overlayContent" class="rounded-2xl px-8 py-6 text-center"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between bg-stone-900 px-5 py-3">
                        <p id="scanStatus" class="text-xs text-white/70">Menyiapkan kamera...</p>
                        <button type="button" id="toggleScan" class="rounded-lg bg-gold px-4 py-1.5 text-xs font-bold text-forest">
                            Mulai Scan
                        </button>
                    </div>
                    <div data-camera-select-wrap class="hidden items-center gap-2 bg-stone-800 px-5 py-2.5">
                        <label for="cameraSelect" class="text-[11px] font-semibold text-white/50"><i class="fa-solid fa-camera mr-1"></i>Kamera</label>
                        <select id="cameraSelect" class="flex-1 rounded-lg bg-stone-700 px-2.5 py-1.5 text-xs text-white outline-none ring-1 ring-white/10"></select>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl bg-white p-4 ring-1 ring-stone-200/80">
                    <label class="mb-1.5 block text-xs font-semibold text-stone-500">Sesi Ibadah</label>
                    <select id="jadwalSelect" class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
                        @forelse ($jadwals as $j)
                            <option value="{{ $j->id }}">
                                {{ $j->nama_sesi }} — {{ $j->tanggal->format('d M Y') }} ({{ substr($j->jam_mulai, 0, 5) }})
                            </option>
                        @empty
                            <option value="">— Belum ada jadwal aktif —</option>
                        @endforelse
                    </select>
                </div>

                {{-- Manual Attendance Form --}}
                <div class="mt-4 rounded-2xl bg-white p-4 ring-1 ring-stone-200/80">
                    <p class="mb-3 text-sm font-bold text-forest">Absen Manual</p>
                    <form action="{{ route('pengurus.absen.manual') }}" method="POST" class="grid gap-3 sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="jadwal_id" id="manualJadwalId">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-stone-500">Jemaat</label>
                            <select name="user_id" required class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
                                <option value="">Pilih jemaat...</option>
                                @foreach (App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full rounded-xl bg-gold px-4 py-2.5 text-sm font-bold text-forest hover:bg-gold-soft">
                                <i class="fa-solid fa-user-check mr-1"></i> Input Absen Manual
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Log absen --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl bg-white ring-1 ring-stone-200/80">
                    <div class="border-b border-stone-100 px-5 py-4">
                        <p class="text-sm font-bold text-forest">Log Absensi Hari Ini</p>
                        <p class="text-xs text-stone-400">Threshold: {{ config('facenet.threshold') * 100 }}% cosine similarity</p>
                    </div>
                    <div id="absenLog" class="max-h-[420px] overflow-y-auto divide-y divide-stone-100">
                        <div class="p-6 text-center text-xs text-stone-400">Belum ada absensi tercatat.</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
@if ($serviceOnline)
<script src="{{ asset('js/camera-select.js') }}"></script>
<script>
(function () {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const toggleBtn = document.getElementById('toggleScan');
    const scanStatus = document.getElementById('scanStatus');
    const cameraSelect = document.getElementById('cameraSelect');
    const jadwalSelect = document.getElementById('jadwalSelect');
    const absenLog = document.getElementById('absenLog');
    const overlay = document.getElementById('overlay');
    const overlayContent = document.getElementById('overlayContent');

    const VERIFY_URL = @json(route('pengurus.absen.verify'));
    const LIST_URL = @json(route('pengurus.absen.list'));
    const CSRF = @json(csrf_token());

    let scanning = false;
    let scanInterval = null;
    let processing = false;
    let cooldown = false;

    const camera = window.CameraSelect.create({
        video: video,
        select: cameraSelect,
        onReady: function (stream, info) {
            if (info && info.usedFallback) {
                scanStatus.textContent = 'Kamera utama tidak tersedia — memakai DroidCam. Pilih sesi lalu klik Mulai Scan.';
            } else {
                scanStatus.textContent = 'Kamera siap. Pilih sesi ibadah lalu klik Mulai Scan.';
            }
        },
        onError: function (err, message) {
            scanStatus.textContent = message;
        },
    });

    function initCamera() {
        camera.start();
    }

    function showOverlay(html, bgClass) {
        overlayContent.innerHTML = html;
        overlayContent.className = 'rounded-2xl px-8 py-6 text-center ' + bgClass;
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        setTimeout(() => overlay.classList.replace('opacity-100', 'opacity-0'), 2500);
    }

    function captureFrame() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0);
        return canvas.toDataURL('image/jpeg', 0.80);
    }

    async function verifyFace() {
        if (processing || cooldown) return;
        const jadwalId = jadwalSelect.value;
        if (!jadwalId) return;

        processing = true;
        scanStatus.textContent = 'Menganalisis wajah...';

        try {
            const res = await fetch(VERIFY_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ image: captureFrame(), jadwal_id: parseInt(jadwalId) })
            });
            const data = await res.json();

            if (data.recognized) {
                const isNew = !data.already_checked_in;
                const icon = isNew ? 'fa-circle-check' : 'fa-circle-info';
                const color = isNew ? 'text-emerald-400' : 'text-amber-400';
                showOverlay(
                    `<i class="fa-solid ${icon} text-5xl ${color}"></i>
                     <p class="mt-3 text-lg font-bold text-white">${data.name}</p>
                     <p class="text-sm text-white/70">${data.similarity}% match · ${data.message}</p>`,
                    'bg-stone-900/90'
                );
                scanStatus.textContent = data.message;
                if (isNew) {
                    cooldown = true;
                    setTimeout(() => { cooldown = false; }, 3000);
                    refreshList();
                }
            } else {
                scanStatus.textContent = data.message || 'Wajah tidak dikenali.';
            }
        } catch (err) {
            scanStatus.textContent = 'Gagal menghubungi server.';
        }

        processing = false;
    }

    async function refreshList() {
        const jadwalId = jadwalSelect.value;
        if (!jadwalId) return;

        try {
            const res = await fetch(LIST_URL + '?jadwal_id=' + jadwalId, {
                headers: { 'Accept': 'application/json' }
            });
            const json = await res.json();

            if (json.data.length === 0) {
                absenLog.innerHTML = '<div class="p-6 text-center text-xs text-stone-400">Belum ada absensi tercatat.</div>';
                return;
            }

            absenLog.innerHTML = json.data.map((item, i) => `
                <div class="flex items-center gap-3 px-5 py-3.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">${i + 1}</div>
                    <div class="flex-1 min-w-0">
                        <p class="truncate text-sm font-semibold text-forest">${item.name}</p>
                        <p class="text-[10px] capitalize text-stone-400">${item.role}</p>
                    </div>
                    <span class="text-xs font-semibold text-stone-500">${item.time}</span>
                </div>
            `).join('');
        } catch (err) { /* silent */ }
    }

    toggleBtn.addEventListener('click', () => {
        scanning = !scanning;
        if (scanning) {
            toggleBtn.textContent = 'Hentikan Scan';
            toggleBtn.classList.replace('bg-gold', 'bg-red-500');
            toggleBtn.classList.add('text-white');
            scanStatus.textContent = 'Scanning aktif — arahkan wajah ke kamera...';
            scanInterval = setInterval(verifyFace, 2000);
            verifyFace();
        } else {
            toggleBtn.textContent = 'Mulai Scan';
            toggleBtn.classList.replace('bg-red-500', 'bg-gold');
            toggleBtn.classList.remove('text-white');
            scanStatus.textContent = 'Scan dihentikan.';
            clearInterval(scanInterval);
        }
    });

    jadwalSelect.addEventListener('change', () => {
        document.getElementById('manualJadwalId').value = jadwalSelect.value;
        refreshList();
    });
    initCamera();
    refreshList();
})();
</script>
@endif
@endpush
