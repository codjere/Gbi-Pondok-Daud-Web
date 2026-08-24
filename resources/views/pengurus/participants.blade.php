@extends('layouts.app')

@section('title', 'Peserta Sesi')
@section('breadcrumb', 'Pengurus / Peserta Sesi')

@section('content')
<div class="mx-auto max-w-6xl">
    @include('partials.flash')

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-forest">Peserta Sesi: {{ $jadwal->nama_sesi }}</h2>
            <p class="mt-1 text-sm text-stone-500">
                {{ $jadwal->tanggal->format('d M Y') }} · {{ $jadwal->jamMulaiFormatted() }} - {{ $jadwal->jamSelesaiFormatted() }}
            </p>
        </div>
        <a href="{{ route('pengurus.absen') }}" class="rounded-xl bg-stone-100 px-4 py-2 text-sm font-bold text-stone-600 hover:bg-stone-200">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    @if ($jadwal->deskripsi)
        <div class="mb-6 rounded-2xl bg-cream p-4 ring-1 ring-stone-200">
            <p class="text-sm font-semibold text-forest">Deskripsi:</p>
            <p class="text-sm text-stone-600">{{ $jadwal->deskripsi }}</p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Reservations --}}
        <div class="rounded-2xl bg-white ring-1 ring-stone-200/80">
            <div class="border-b border-stone-100 px-5 py-4">
                <p class="text-sm font-bold text-forest">Reservasi ({{ $reservations->count() }})</p>
                <p class="text-xs text-stone-400">Jemaat yang sudah mendaftar</p>
            </div>
            <div class="max-h-[400px] overflow-y-auto divide-y divide-stone-100">
                @forelse ($reservations as $reservasi)
                    <div class="flex items-center gap-3 px-5 py-3.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-700">
                            {{ substr($reservasi->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-semibold text-forest">{{ $reservasi->user->name }}</p>
                            <p class="text-[10px] capitalize text-stone-400">{{ $reservasi->user->role }}</p>
                        </div>
                        <span class="text-xs text-stone-400">{{ $reservasi->created_at->format('d M H:i') }}</span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-stone-400">Belum ada reservasi.</div>
                @endforelse
            </div>
        </div>

        {{-- Attendance --}}
        <div class="rounded-2xl bg-white ring-1 ring-stone-200/80">
            <div class="border-b border-stone-100 px-5 py-4">
                <p class="text-sm font-bold text-forest">Kehadiran ({{ $attendance->count() }})</p>
                <p class="text-xs text-stone-400">Jemaat yang sudah absen</p>
            </div>
            <div class="max-h-[400px] overflow-y-auto divide-y divide-stone-100">
                @forelse ($attendance as $kehadiran)
                    <div class="flex items-center gap-3 px-5 py-3.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">
                            {{ substr($kehadiran->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-semibold text-forest">{{ $kehadiran->user->name }}</p>
                            <p class="text-[10px] capitalize text-stone-400">{{ $kehadiran->user->role }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold text-stone-500">{{ $kehadiran->waktu_checkin->format('H:i') }}</span>
                            <p class="text-[10px] text-stone-400">{{ $kehadiran->metode_verifikasi }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-stone-400">Belum ada kehadiran tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Manual Attendance Form --}}
    <div class="mt-6 rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
        <p class="mb-4 text-sm font-bold text-forest">Input Absen Manual</p>
        <form action="{{ route('pengurus.absen.manual') }}" method="POST" class="grid gap-4 sm:grid-cols-3">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-semibold text-stone-500">Pilih Jemaat</label>
                <select name="user_id" required class="w-full rounded-xl bg-cream px-3 py-2.5 text-sm ring-1 ring-stone-200 outline-none">
                    <option value="">Pilih jemaat...</option>
                    @foreach ($allUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-gold px-4 py-2.5 text-sm font-bold text-forest hover:bg-gold-soft">
                    <i class="fa-solid fa-user-check mr-1"></i> Input Absen
                </button>
            </div>
        </form>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('pengurus.jadwal') }}" class="text-sm font-semibold text-forest hover:underline">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Manajemen Jadwal
        </a>
    </div>
</div>
@endsection
