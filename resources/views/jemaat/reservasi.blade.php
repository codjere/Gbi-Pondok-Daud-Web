@extends('layouts.app')

@section('title', 'Reservasi Ibadah')
@section('breadcrumb', 'Jemaat / Reservasi Kehadiran')

@section('content')
<div class="mx-auto max-w-5xl">
    @include('partials.flash')

    <div class="mb-7">
        <h2 class="text-xl font-extrabold text-forest">Reservasi Kehadiran Ibadah</h2>
        <p class="mt-1 text-sm text-stone-500">Amankan kuota sebelum hari-H · diverifikasi kamera FaceNet</p>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($jadwals as $jadwal)
            @php
                $terisi = $jadwal->jumlahTerisi();
                $pct = $jadwal->batas_kuota > 0 ? ($terisi / $jadwal->batas_kuota) * 100 : 0;
                $status = $jadwal->statusKuota();
                $sudah = in_array($jadwal->id, $reservasiUser);
                $headerClass = match(true) {
                    $status === 'penuh' => 'bg-stone-300 text-stone-600',
                    $status === 'hampir' => 'bg-gradient-to-r from-amber-600 to-amber-500 text-white',
                    default => 'bg-gradient-to-r from-forest to-forest-mid text-white',
                };
            @endphp
            <article class="flex flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-stone-200/80 {{ $status === 'penuh' ? 'opacity-80' : '' }}">
                <div class="px-5 py-4 {{ $headerClass }}">
                    <h3 class="text-lg font-extrabold">{{ $jadwal->nama_sesi }}</h3>
                    <p class="text-xs opacity-80">{{ $jadwal->tanggal->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <dl class="space-y-2 text-sm text-stone-500">
                        <div class="flex justify-between"><dt>Waktu</dt><dd class="font-semibold text-stone-700">{{ $jadwal->jamMulaiFormatted() }} — {{ $jadwal->jamSelesaiFormatted() }}</dd></div>
                        <div class="flex justify-between"><dt>Lokasi</dt><dd class="font-semibold text-stone-700">{{ $jadwal->lokasi }}</dd></div>
                        <div class="flex justify-between"><dt>Pembicara</dt><dd class="font-semibold text-stone-700 text-right text-xs">{{ $jadwal->pembicara }}</dd></div>
                    </dl>
                    <div class="mt-4">
                        <div class="mb-1.5 flex justify-between text-xs">
                            <span class="text-stone-400">Kuota</span>
                            <span class="font-bold {{ $status === 'penuh' ? 'text-red-500' : 'text-forest' }}">{{ $terisi }} / {{ $jadwal->batas_kuota }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-stone-100">
                            <div class="h-full rounded-full {{ $status === 'penuh' ? 'bg-red-400' : ($status === 'hampir' ? 'bg-amber-500' : 'bg-forest-mid') }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    @if ($status === 'penuh')
                        <div class="mt-4 rounded-xl bg-red-50 px-3 py-2 text-xs text-red-700">
                            Mohon maaf, kuota untuk sesi ini telah penuh.
                        </div>
                        <button disabled class="mt-3 w-full cursor-not-allowed rounded-xl bg-stone-200 py-3 text-sm font-bold text-stone-400">Kuota Penuh</button>
                    @elseif ($sudah)
                        <button disabled class="mt-5 w-full rounded-xl bg-blue-50 py-3 text-sm font-bold text-blue-700">Anda Sudah Terdaftar</button>
                    @else
                        <form action="{{ route('jemaat.reservasi.store', $jadwal) }}" method="POST" class="mt-5">
                            @csrf
                            <button class="w-full rounded-xl bg-forest py-3 text-sm font-bold text-white hover:bg-forest-mid">
                                <i class="fa-solid fa-ticket mr-1"></i> Reservasi Kehadiran
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <p class="col-span-full text-center text-stone-500">Belum ada jadwal ibadah aktif.</p>
        @endforelse
    </div>
</div>
@endsection
