@extends('layouts.app')

@section('title', 'Rekomendasi AI')
@section('breadcrumb', 'Jemaat / Rekomendasi AI')

@section('content')
<div class="mx-auto max-w-3xl">
    @include('partials.flash')

    <div class="mb-7">
        <p class="rounded-full bg-gold-soft px-3 py-1 text-[11px] font-bold text-forest inline-block">AI Recommendation System</p>
        <h2 class="mt-3 text-xl font-extrabold text-forest">Kuesioner Profil Jemaat</h2>
        <p class="mt-1 text-sm text-stone-500">Isi profil Anda — AI akan merekomendasikan tim pelayanan & COOL terdekat</p>
    </div>

    <form action="{{ route('jemaat.kuesioner.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
            <p class="mb-4 text-sm font-bold text-forest"><i class="fa-solid fa-user mr-2 text-gold"></i>Data Pribadi</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-500">Usia</label>
                    <input type="number" name="usia" value="{{ old('usia', $profile->usia) }}" min="15" max="80" required
                           class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-stone-200 outline-none">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-500">Jenis Kelamin</label>
                    <select name="jenis_kelamin" required class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-stone-200 outline-none">
                        <option value="L" @selected(old('jenis_kelamin', $profile->jenis_kelamin) === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('jenis_kelamin', $profile->jenis_kelamin) === 'P')>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-500">Kecamatan Domisili</label>
                    <select name="kecamatan" required class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-stone-200 outline-none">
                        <option value="">— Pilih Kecamatan —</option>
                        @foreach ($kecamatanOptions as $kec)
                            <option value="{{ $kec }}" @selected(old('kecamatan', $profile->kecamatan) === $kec)>{{ $kec }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-500">Alamat (opsional)</label>
                    <input type="text" name="alamat" value="{{ old('alamat', $profile->alamat) }}"
                           class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-stone-200 outline-none">
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
            <p class="mb-4 text-sm font-bold text-forest"><i class="fa-solid fa-heart mr-2 text-gold"></i>Minat Pelayanan</p>
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($minatOptions as $key => $label)
                    <label class="flex items-center gap-2 rounded-xl bg-cream px-3 py-2.5 text-sm cursor-pointer hover:ring-1 hover:ring-gold/40">
                        <input type="checkbox" name="minat[]" value="{{ $key }}"
                               @checked(in_array($key, old('minat', $profile->minat ?? []))) class="accent-forest">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
            <p class="mb-4 text-sm font-bold text-forest"><i class="fa-solid fa-star mr-2 text-gold"></i>Bakat & Kemampuan</p>
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($bakatOptions as $key => $label)
                    <label class="flex items-center gap-2 rounded-xl bg-cream px-3 py-2.5 text-sm cursor-pointer hover:ring-1 hover:ring-gold/40">
                        <input type="checkbox" name="bakat[]" value="{{ $key }}"
                               @checked(in_array($key, old('bakat', $profile->bakat ?? []))) class="accent-forest">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 ring-1 ring-stone-200/80">
            <p class="mb-4 text-sm font-bold text-forest"><i class="fa-solid fa-clock mr-2 text-gold"></i>Ketersediaan Waktu</p>
            <div class="grid gap-2 sm:grid-cols-3 mb-4">
                @foreach ($hariOptions as $key => $label)
                    <label class="flex items-center gap-2 rounded-xl bg-cream px-3 py-2.5 text-sm cursor-pointer">
                        <input type="checkbox" name="ketersediaan_hari[]" value="{{ $key }}"
                               @checked(in_array($key, old('ketersediaan_hari', $profile->ketersediaan_hari ?? []))) class="accent-forest">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            <select name="ketersediaan_waktu" required class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-stone-200 outline-none">
                @foreach (['pagi' => 'Pagi', 'siang' => 'Siang', 'sore' => 'Sore', 'malam' => 'Malam', 'fleksibel' => 'Fleksibel'] as $k => $v)
                    <option value="{{ $k }}" @selected(old('ketersediaan_waktu', $profile->ketersediaan_waktu) === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-forest py-4 text-sm font-bold text-white shadow-lg hover:bg-forest-mid">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Analisis AI & Lihat Rekomendasi
        </button>
    </form>
</div>
@endsection
