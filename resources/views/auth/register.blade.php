@extends('layouts.guest')

@section('title', 'Registrasi Akun')

@section('content')
<div class="flex min-h-screen">
    <div class="relative hidden w-[42%] flex-col justify-between overflow-hidden bg-gradient-to-br from-forest-mid via-forest to-cyan-600 p-12 lg:flex dark:from-slate-800 dark:via-slate-700 dark:to-slate-600">
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

    <div class="auth-panel flex flex-1 items-center justify-center px-6 py-10">
        <div class="w-full max-w-[440px]">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-extrabold text-forest dark:text-cyan-400">Buat akun baru</h1>
                <button onclick="toggleDarkMode()" class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-sky-500 text-white shadow-sm ring-1 ring-cyan-200/60 hover-card dark:from-slate-700 dark:to-slate-600 dark:text-yellow-400 dark:ring-slate-600" title="Toggle Dark Mode">
                    <i class="fa-solid fa-moon text-sm dark:hidden"></i>
                    <i class="fa-solid fa-sun text-sm hidden dark:block"></i>
                </button>
            </div>
            @include('partials.flash')

            <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="nama" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-300">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                           class="w-full rounded-xl bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-600 dark:text-white dark:focus:ring-cyan-500/40">
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-300">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-600 dark:text-white dark:focus:ring-cyan-500/40">
                </div>
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-300">Status Keanggotaan</label>
                    <select id="status" name="status" required class="w-full rounded-xl bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-600 dark:text-white dark:focus:ring-cyan-500/40">
                        <option value="jemaat" @selected(old('status', 'jemaat') === 'jemaat')>Jemaat</option>
                        <option value="pelayan" @selected(old('status') === 'pelayan')>Pelayan</option>
                    </select>
                    <p class="mt-1.5 text-xs text-stone-500 dark:text-slate-400">Pelayan bisa mendaftar di sini. Akun pengurus hanya dibuat oleh admin gereja.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-300">Kata Sandi</label>
                        <input type="password" id="password" name="password" required minlength="8"
                               class="w-full rounded-xl bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-600 dark:text-white dark:focus:ring-cyan-500/40">
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-stone-700 dark:text-slate-300">Konfirmasi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full rounded-xl bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:bg-slate-800 dark:ring-slate-600 dark:text-white dark:focus:ring-cyan-500/40">
                    </div>
                </div>
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-forest to-forest-mid py-3.5 text-sm font-bold text-white shadow-md transition hover:from-cyan-600 hover:to-sky-600 hover:shadow-lg">
                    Lanjut ke Perekaman Wajah <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-stone-600 dark:text-slate-400">
                Sudah terdaftar? <a href="{{ route('login') }}" class="font-bold text-forest hover:underline dark:text-cyan-400">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
