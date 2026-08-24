@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="flex min-h-screen">
    <div class="relative hidden w-[45%] flex-col justify-between overflow-hidden bg-forest p-12 lg:flex">
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

    <div class="flex flex-1 items-center justify-center px-6 py-12">
        <div class="w-full max-w-[420px]">
            <h1 class="text-2xl font-extrabold text-forest">Selamat datang kembali</h1>
            <p class="mt-1.5 text-sm text-stone-500">Masuk ke portal jemaat / pengurus GBI Pondok Daud</p>

            @include('partials.flash')

            <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-stone-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com"
                           class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40">
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-stone-700">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="••••••••"
                           class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm shadow-sm ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40">
                </div>
                <label class="flex items-center gap-2 text-sm text-stone-500">
                    <input type="checkbox" name="remember" value="1" class="rounded text-forest focus:ring-forest/30"> Ingat saya
                </label>
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-forest py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-forest-mid">
                    Masuk
                </button>
            </form>

            <div class="mt-6 rounded-xl bg-cream p-4 text-xs text-stone-500">
                <p class="font-bold text-forest">Akun demo:</p>
                <p class="mt-1">Jemaat: <code>andi@email.com</code> / <code>password123</code></p>
                <p>Pengurus: <code>admin@gbipondokdaud.id</code> / <code>password123</code></p>
            </div>

            <p class="mt-6 text-center text-sm text-stone-500">
                Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-forest hover:underline">Daftar sekarang</a>
                · <a href="{{ route('about') }}" class="font-bold text-forest hover:underline">Tentang Kami</a>
            </p>
        </div>
    </div>
</div>
@endsection
