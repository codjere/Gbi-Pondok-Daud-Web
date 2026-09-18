@extends('layouts.app')

@section('title', 'Peserta Sesi')
@section('breadcrumb', 'Pengurus / Peserta Sesi')

@section('content')
<div class="mx-auto max-w-6xl pattern-dots">
    @include('partials.flash')

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Peserta Sesi: {{ $jadwal->nama_sesi }}</h2>
            <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">
                {{ $jadwal->tanggal->format('d M Y') }} · {{ $jadwal->jamMulaiFormatted() }} - {{ $jadwal->jamSelesaiFormatted() }}
            </p>
        </div>
        <a href="{{ route('pengurus.absen') }}" class="rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-4 py-2 text-sm font-bold text-forest ring-1 ring-cyan-200/60 hover:from-cyan-100 hover:to-sky-100 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    @if ($jadwal->deskripsi)
        <div class="mb-6 rounded-2xl bg-gradient-to-br from-cyan-50 to-sky-50 p-4 shadow-md ring-1 ring-cyan-200/60 dark:from-slate-600 dark:to-slate-700">
            <p class="text-sm font-semibold text-forest dark:text-cyan-400">Deskripsi:</p>
            <p class="text-sm text-stone-600 dark:text-slate-400">{{ $jadwal->deskripsi }}</p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Reservations --}}
        <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <div class="border-b border-cyan-200/50 px-5 py-4 dark:border-slate-600">
                <p class="text-sm font-bold text-forest dark:text-cyan-400">Reservasi ({{ $reservations->count() }})</p>
                <p class="text-xs text-stone-500 dark:text-slate-500">Jemaat yang sudah mendaftar</p>
            </div>
            <div class="max-h-[400px] overflow-y-auto divide-y divide-cyan-200/50 dark:divide-slate-600">
                @forelse ($reservations as $reservasi)
                    <div class="flex items-center gap-3 px-5 py-3.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-cyan-100 to-sky-100 text-xs font-bold text-cyan-700 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                            {{ substr($reservasi->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-semibold text-forest dark:text-cyan-400">{{ $reservasi->user->name }}</p>
                            <p class="text-[10px] capitalize text-stone-500 dark:text-slate-500">{{ $reservasi->user->role }}</p>
                        </div>
                        <span class="text-xs text-stone-500 dark:text-slate-500">{{ $reservasi->created_at->format('d M H:i') }}</span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-stone-500 dark:text-slate-500">Belum ada reservasi.</div>
                @endforelse
            </div>
        </div>

        {{-- Attendance --}}
        <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <div class="border-b border-cyan-200/50 px-5 py-4 dark:border-slate-600">
                <p class="text-sm font-bold text-forest dark:text-cyan-400">Kehadiran ({{ $attendance->count() }})</p>
                <p class="text-xs text-stone-500 dark:text-slate-500">Jemaat yang sudah absen</p>
            </div>
            <div class="max-h-[400px] overflow-y-auto divide-y divide-cyan-200/50 dark:divide-slate-600">
                @forelse ($attendance as $kehadiran)
                    <div class="flex items-center gap-3 px-5 py-3.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-green-100 text-xs font-bold text-emerald-700 dark:from-slate-600 dark:to-slate-500 dark:text-emerald-400">
                            {{ substr($kehadiran->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-semibold text-forest dark:text-cyan-400">{{ $kehadiran->user->name }}</p>
                            <p class="text-[10px] capitalize text-stone-500 dark:text-slate-500">{{ $kehadiran->user->role }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-stone-600 dark:text-slate-400">{{ $kehadiran->waktu_checkin->format('H:i') }}</span>
                            <p class="text-[10px] text-stone-500 dark:text-slate-500">{{ $kehadiran->metode_verifikasi }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-stone-500 dark:text-slate-500">Belum ada kehadiran tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Manual Attendance Form --}}
    <div class="mt-6 rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <p class="mb-4 text-sm font-bold text-forest dark:text-cyan-400">Input Absen Manual</p>
        <form action="{{ route('pengurus.absen.manual') }}" method="POST" class="grid gap-4 sm:grid-cols-3">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-semibold text-stone-600 dark:text-slate-400">Pilih Jemaat</label>
                <select name="user_id" required class="w-full rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2.5 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-white dark:ring-slate-500">
                    <option value="">Pilih jemaat...</option>
                    @foreach ($allUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-gold to-amber-500 px-4 py-2.5 text-sm font-bold text-forest shadow-md hover:shadow-lg transition hover:from-amber-400 hover:to-orange-400">
                    <i class="fa-solid fa-user-check mr-1"></i> Input Absen
                </button>
            </div>
        </form>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('pengurus.jadwal') }}" class="text-sm font-semibold text-forest hover:underline dark:text-cyan-500">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Manajemen Jadwal
        </a>
    </div>
</div>
@endsection
