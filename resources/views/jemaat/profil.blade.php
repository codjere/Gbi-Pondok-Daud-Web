@extends('layouts.app')

@section('title', 'Profil Saya')
@section('breadcrumb', 'Akun / Profil Saya')

@section('content')
<div class="mx-auto max-w-3xl">
    @include('partials.flash')

    {{-- Header kartu profil --}}
    <div class="mb-6 rounded-2xl bg-gradient-to-br from-forest via-forest-mid to-cyan-500 p-6 text-white shadow-lg dark:from-slate-700 dark:via-slate-600 dark:to-cyan-600">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 text-2xl font-extrabold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h2 class="truncate text-xl font-extrabold">{{ $user->name }}</h2>
                <p class="text-sm text-white/80">{{ $user->email }}</p>
                <div class="mt-1 flex flex-wrap gap-2">
                    <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide">{{ $user->role }}</span>
                    @if($user->has_biometric)
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-bold"><i class="fa-solid fa-check mr-1"></i>Biometrik terdaftar</span>
                    @else
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-bold"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Belum ada biometrik</span>
                    @endif
                    @if($profile->isComplete())
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-bold"><i class="fa-solid fa-user-check mr-1"></i>Profil lengkap</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('jemaat.profil.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Akun --}}
        <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <p class="mb-4 text-sm font-bold text-forest dark:text-cyan-400"><i class="fa-solid fa-id-card mr-2 text-gold"></i>Informasi Akun</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-600 dark:text-slate-400">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-600 dark:text-slate-400">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Data pribadi --}}
        <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <p class="mb-4 text-sm font-bold text-forest dark:text-cyan-400"><i class="fa-solid fa-user mr-2 text-gold"></i>Data Pribadi</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-600 dark:text-slate-400">Usia</label>
                    <input type="number" name="usia" value="{{ old('usia', $profile->usia) }}" min="10" max="100"
                           class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-600 dark:text-slate-400">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                        <option value="">— Pilih —</option>
                        <option value="L" @selected(old('jenis_kelamin', $profile->jenis_kelamin) === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('jenis_kelamin', $profile->jenis_kelamin) === 'P')>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-600 dark:text-slate-400">Wilayah / KUL Domisili</label>
                    <select name="kecamatan" class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                        <option value="">— Pilih Wilayah —</option>
                        @foreach ($kecamatanOptions as $kec)
                            <option value="{{ $kec }}" @selected(old('kecamatan', $profile->kecamatan) === $kec)>{{ $kec }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-600 dark:text-slate-400">Alamat (opsional)</label>
                    <input type="text" name="alamat" value="{{ old('alamat', $profile->alamat) }}"
                           class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                </div>
            </div>
        </div>

        {{-- Minat --}}
        <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <p class="mb-4 text-sm font-bold text-forest dark:text-cyan-400"><i class="fa-solid fa-heart mr-2 text-gold"></i>Minat Pelayanan</p>
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($minatOptions as $key => $label)
                    <label class="flex items-center gap-2 rounded-xl bg-cream px-3 py-2.5 text-sm cursor-pointer hover:ring-1 hover:ring-gold/40 dark:bg-slate-800 dark:text-slate-200">
                        <input type="checkbox" name="minat[]" value="{{ $key }}"
                               @checked(in_array($key, old('minat', $profile->minat ?? []))) class="accent-forest">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Bakat --}}
        <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <p class="mb-4 text-sm font-bold text-forest dark:text-cyan-400"><i class="fa-solid fa-star mr-2 text-gold"></i>Bakat &amp; Kemampuan</p>
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($bakatOptions as $key => $label)
                    <label class="flex items-center gap-2 rounded-xl bg-cream px-3 py-2.5 text-sm cursor-pointer hover:ring-1 hover:ring-gold/40 dark:bg-slate-800 dark:text-slate-200">
                        <input type="checkbox" name="bakat[]" value="{{ $key }}"
                               @checked(in_array($key, old('bakat', $profile->bakat ?? []))) class="accent-forest">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Ketersediaan --}}
        <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <p class="mb-4 text-sm font-bold text-forest dark:text-cyan-400"><i class="fa-solid fa-clock mr-2 text-gold"></i>Ketersediaan Waktu</p>
            <div class="mb-4 grid gap-2 sm:grid-cols-3">
                @foreach ($hariOptions as $key => $label)
                    <label class="flex items-center gap-2 rounded-xl bg-cream px-3 py-2.5 text-sm cursor-pointer dark:bg-slate-800 dark:text-slate-200">
                        <input type="checkbox" name="ketersediaan_hari[]" value="{{ $key }}"
                               @checked(in_array($key, old('ketersediaan_hari', $profile->ketersediaan_hari ?? []))) class="accent-forest">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            <select name="ketersediaan_waktu" class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                <option value="">— Pilih Waktu —</option>
                @foreach (['pagi' => 'Pagi', 'siang' => 'Siang', 'sore' => 'Sore', 'malam' => 'Malam', 'fleksibel' => 'Fleksibel'] as $k => $v)
                    <option value="{{ $k }}" @selected(old('ketersediaan_waktu', $profile->ketersediaan_waktu) === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>

        {{-- Ganti password --}}
        <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
            <p class="mb-1 text-sm font-bold text-forest dark:text-cyan-400"><i class="fa-solid fa-lock mr-2 text-gold"></i>Ganti Kata Sandi</p>
            <p class="mb-4 text-xs text-stone-500 dark:text-slate-400">Kosongkan jika tidak ingin mengubah kata sandi.</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-600 dark:text-slate-400">Kata Sandi Baru</label>
                    <input type="password" name="password" minlength="8"
                           class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-stone-600 dark:text-slate-400">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation"
                           class="w-full rounded-xl bg-cream px-4 py-3 text-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-500 dark:text-white">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="rounded-xl bg-stone-100 px-6 py-3 text-sm font-bold text-stone-600 hover:bg-stone-200 dark:bg-slate-600 dark:text-slate-200">Batal</a>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-forest to-forest-mid px-6 py-3 text-sm font-bold text-white shadow-md hover:shadow-lg transition hover:from-cyan-600 hover:to-sky-600">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
