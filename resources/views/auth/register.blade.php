@extends('layouts.guest')

@section('title', 'Registrasi Akun')

@section('content')
<div class="flex min-h-screen">
    <div class="relative hidden w-[42%] flex-col justify-between overflow-hidden bg-forest p-12 lg:flex">
        <a href="{{ route('landing') }}" class="relative flex items-center gap-2 text-white/70 hover:text-white">
            <i class="fa-solid fa-arrow-left text-sm"></i><span class="text-sm">Kembali</span>
        </a>
        <div class="relative space-y-5">
            <p class="text-xs font-bold uppercase tracking-widest text-gold">Langkah 1 dari 2</p>
            <h2 class="text-3xl font-extrabold leading-tight text-white">Daftarkan keanggotaan GBI Pondok Daud</h2>
            <p class="text-sm text-white/55">Setelah validasi, lanjut ke perekaman wajah FaceNet.</p>
        </div>
        <p class="relative text-xs text-white/30">Data disimpan ke PostgreSQL</p>
    </div>

    <div class="flex flex-1 items-center justify-center px-6 py-10">
        <div class="w-full max-w-[440px]">
            <h1 class="text-2xl font-extrabold text-forest">Buat akun baru</h1>
            @include('partials.flash')

            <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="nama" class="mb-1.5 block text-sm font-semibold text-stone-700">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                           class="w-full rounded-xl bg-white px-4 py-3 text-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40">
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-stone-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl bg-white px-4 py-3 text-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40">
                </div>
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-semibold text-stone-700">Status Keanggotaan</label>
                    <select id="status" name="status" required class="w-full rounded-xl bg-white px-4 py-3 text-sm ring-1 ring-stone-200 outline-none">
                        <option value="jemaat" @selected(old('status') === 'jemaat')>Jemaat</option>
                        <option value="pelayan" @selected(old('status') === 'pelayan')>Pelayan</option>
                        <option value="pengurus" @selected(old('status') === 'pengurus')>Pengurus</option>
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-stone-700">Kata Sandi</label>
                        <input type="password" id="password" name="password" required minlength="8"
                               class="w-full rounded-xl bg-white px-4 py-3 text-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40">
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-stone-700">Konfirmasi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full rounded-xl bg-white px-4 py-3 text-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40">
                    </div>
                </div>
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-forest py-3.5 text-sm font-bold text-white transition hover:bg-forest-mid">
                    Lanjut ke Perekaman Wajah <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-stone-500">
                Sudah terdaftar? <a href="{{ route('login') }}" class="font-bold text-forest hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
