@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('breadcrumb', 'Pengurus / Laporan & Rekapitulasi')

@section('content')
<div class="mx-auto max-w-6xl pattern-dots">
    @include('partials.flash')

    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Laporan Evaluasi Kehadiran</h2>
        <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">Rekapitulasi kehadiran jemaat GBI Pondok Daud · verifikasi FaceNet</p>
    </div>

    <form method="GET" action="{{ route('pengurus.laporan') }}" class="mb-6 flex flex-wrap gap-3 rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <div class="flex-1 min-w-[200px]">
            <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Pilih Sesi Ibadah</label>
            <select name="jadwal_id" class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
                @foreach ($jadwals as $j)
                    <option value="{{ $j->id }}" @selected($jadwal?->id === $j->id)>
                        {{ $j->nama_sesi }} — {{ $j->tanggal->format('d M Y') }} ({{ substr($j->jam_mulai, 0, 5) }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-xl bg-gradient-to-r from-forest to-forest-mid px-5 py-2.5 text-sm font-bold text-white shadow-md hover:shadow-lg transition hover:from-cyan-600 hover:to-sky-600">Tampilkan</button>
        </div>
    </form>

    @if ($jadwal)
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
                <p class="text-xs font-semibold text-stone-500 dark:text-slate-500">Total Hadir</p>
                <p class="mt-1 text-3xl font-extrabold text-forest dark:text-cyan-400">{{ $stats['hadir'] }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
                <p class="text-xs font-semibold text-stone-500 dark:text-slate-500">Kuota Ruangan</p>
                <p class="mt-1 text-3xl font-extrabold text-forest dark:text-cyan-400">{{ $stats['kuota'] }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
                <p class="text-xs font-semibold text-stone-500 dark:text-slate-500">Tingkat Kehadiran</p>
                <p class="mt-1 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $stats['pct'] }}%</p>
            </div>
        </div>

        {{-- Grafik tren + breakdown metode verifikasi --}}
        <div class="mb-6 grid gap-4 lg:grid-cols-3">
            {{-- Tren kehadiran --}}
            <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600 lg:col-span-2">
                <p class="mb-3 text-sm font-bold text-forest dark:text-cyan-400"><i class="fa-solid fa-chart-line mr-2 text-gold"></i>Tren Kehadiran (10 Sesi Terakhir)</p>
                <div class="h-56">
                    <canvas id="trenChart"></canvas>
                </div>
            </div>

            {{-- Breakdown metode verifikasi --}}
            <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
                <p class="mb-3 text-sm font-bold text-forest dark:text-cyan-400"><i class="fa-solid fa-shield-halved mr-2 text-gold"></i>Metode Verifikasi</p>
                <div class="mx-auto h-40 w-40">
                    <canvas id="metodeChart"></canvas>
                </div>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-stone-600 dark:text-slate-300"><span class="inline-block h-3 w-3 rounded-full bg-emerald-500"></span>Pengenalan Wajah</span>
                        <span class="font-bold text-forest dark:text-cyan-400">{{ $metode['facenet'] }} <span class="text-xs text-stone-400">({{ $metode['facenet_pct'] }}%)</span></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-stone-600 dark:text-slate-300"><span class="inline-block h-3 w-3 rounded-full bg-amber-400"></span>Manual</span>
                        <span class="font-bold text-forest dark:text-cyan-400">{{ $metode['manual'] }} <span class="text-xs text-stone-400">({{ $metode['manual_pct'] }}%)</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <div class="flex items-center justify-between border-b border-cyan-200/50 px-5 py-4 dark:border-slate-600">
                <p class="text-sm font-bold text-forest dark:text-cyan-400">Daftar Hadir — {{ $jadwal->nama_sesi }}</p>
                @if ($kehadiran->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('pengurus.laporan.pdf', ['jadwal_id' => $jadwal->id]) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-rose-500 to-red-500 px-4 py-2 text-xs font-bold text-white shadow-md hover:shadow-lg transition hover:from-rose-400 hover:to-red-400">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>
                        <a href="{{ route('pengurus.laporan.excel', ['jadwal_id' => $jadwal->id]) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:shadow-lg transition hover:from-emerald-500 hover:to-green-500">
                            <i class="fa-solid fa-file-excel"></i> Excel
                        </a>
                    </div>
                @else
                    <button disabled class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-stone-100 to-slate-100 px-4 py-2 text-xs font-bold text-stone-400 cursor-not-allowed dark:from-slate-600 dark:to-slate-500">
                        <i class="fa-solid fa-download"></i> Belum ada data
                    </button>
                @endif
            </div>

            @if ($kehadiran->isEmpty())
                <div class="p-10 text-center text-sm text-stone-600 dark:text-slate-400">
                    Belum ada data kehadiran untuk sesi ibadah ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-cyan-200/50 bg-gradient-to-r from-cyan-50 to-sky-50 dark:border-slate-600 dark:from-slate-600 dark:to-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">No</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Nama</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Status</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Email</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Waktu Check-in</th>
                                <th class="px-5 py-3 text-xs font-bold uppercase text-stone-600 dark:text-slate-400">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cyan-200/50 dark:divide-slate-600">
                            @foreach ($kehadiran as $i => $h)
                                <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-sky-50 dark:hover:from-slate-600 dark:hover:to-slate-700">
                                    <td class="px-5 py-3.5 text-stone-500 dark:text-slate-500">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-forest dark:text-cyan-400">{{ $h->user->name }}</td>
                                    <td class="px-5 py-3.5"><span class="rounded-full bg-gradient-to-r from-gold-soft to-amber-100 px-2 py-0.5 text-[10px] font-bold capitalize text-forest dark:from-slate-600 dark:to-slate-500 dark:text-amber-400">{{ $h->user->role }}</span></td>
                                    <td class="px-5 py-3.5 text-stone-600 dark:text-slate-400">{{ $h->user->email }}</td>
                                    <td class="px-5 py-3.5 text-stone-600 dark:text-slate-400">{{ $h->waktu_checkin->format('H:i') }} WIB</td>
                                    <td class="px-5 py-3.5">
                                        @if ($h->metode_verifikasi === 'facenet')
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400"><i class="fa-solid fa-face-smile mr-1"></i>Wajah</span>
                                        @else
                                            <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-400"><i class="fa-solid fa-pen mr-1"></i>Manual</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148,163,184,0.15)' : 'rgba(0,0,0,0.06)';
    const tickColor = isDark ? '#cbd5e1' : '#475569';

    // ===== Tren kehadiran (line) =====
    const trenEl = document.getElementById('trenChart');
    if (trenEl) {
        new Chart(trenEl, {
            type: 'line',
            data: {
                labels: @json($tren['labels']),
                datasets: [{
                    label: 'Jumlah Hadir',
                    data: @json($tren['data']),
                    borderColor: '#0e9488',
                    backgroundColor: 'rgba(14,148,136,0.15)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0e9488',
                    pointRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: tickColor, precision: 0 }, grid: { color: gridColor } },
                    x: { ticks: { color: tickColor }, grid: { display: false } },
                },
            },
        });
    }

    // ===== Metode verifikasi (doughnut) =====
    const metodeEl = document.getElementById('metodeChart');
    if (metodeEl) {
        const face = {{ $metode['facenet'] }};
        const manual = {{ $metode['manual'] }};
        new Chart(metodeEl, {
            type: 'doughnut',
            data: {
                labels: ['Pengenalan Wajah', 'Manual'],
                datasets: [{
                    data: (face + manual) === 0 ? [1, 0] : [face, manual],
                    backgroundColor: ['#10b981', '#fbbf24'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { display: false } },
            },
        });
    }
})();
</script>
@endpush
