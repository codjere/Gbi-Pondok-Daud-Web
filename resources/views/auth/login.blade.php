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

            <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-5">
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
                    Masuk
                </button>
            </form>

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
@endsection
