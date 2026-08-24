@extends('layouts.guest')

@section('title', 'GBI Pondok Daud')

@section('content')
<div class="relative min-h-screen overflow-hidden">
    <div class="absolute -left-32 top-20 h-96 w-96 rounded-full bg-gold/10 blur-3xl"></div>
    <div class="absolute -right-20 bottom-10 h-80 w-80 rounded-full bg-forest-mid/10 blur-3xl"></div>

    <nav class="relative z-10 flex items-center justify-between px-6 py-5 lg:px-16">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest text-gold">
                <i class="fa-solid fa-cross"></i>
            </div>
            <div>
                <span class="text-lg font-extrabold text-forest">GBI Pondok Daud</span>
                <p class="text-[10px] font-medium text-stone-400">Portal Jemaat</p>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('about') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-forest transition hover:bg-forest/5 sm:px-5">Tentang Kami</a>
            <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-forest transition hover:bg-forest/5 sm:px-5">Masuk</a>
            <a href="{{ route('register') }}" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-forest/20 transition hover:bg-forest-mid sm:px-5">Daftar Jemaat</a>
        </div>
    </nav>

    <section class="relative z-10 mx-auto grid max-w-6xl items-center gap-12 px-6 pb-20 pt-8 lg:grid-cols-2 lg:px-8 lg:pt-16">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-gold-soft px-4 py-1.5 text-xs font-semibold text-forest">
                <i class="fa-solid fa-church text-gold"></i>
                GBI Pondok Daud
            </span>
            <h1 class="mt-5 text-4xl font-extrabold leading-tight text-forest lg:text-[2.75rem]">
                Sistem manajemen & presensi jemaat gereja
            </h1>
            <p class="mt-4 max-w-lg text-base leading-relaxed text-stone-500">
                Reservasi ibadah, permohonan doa, renungan harian, dan presensi berbasis Face Recognition — dipimpin oleh Pdt. Samsu Hadi, Pdt. Lisna Tappi, dan Pdt. Ficky Benyaminsz.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-2xl bg-forest px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-forest/25 transition hover:-translate-y-0.5 hover:bg-forest-mid">
                    <i class="fa-solid fa-user-plus"></i> Registrasi
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-7 py-3.5 text-sm font-bold text-forest ring-1 ring-stone-200 transition hover:-translate-y-0.5">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                </a>
                <a href="{{ route('about') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gold-soft px-7 py-3.5 text-sm font-bold text-forest transition hover:-translate-y-0.5">
                    <i class="fa-solid fa-church"></i> Tentang Kami
                </a>
            </div>
        </div>

        <div class="relative">
            <div class="pattern-dots absolute inset-0 rounded-3xl"></div>
            <div class="relative space-y-4 p-4">
                <div class="ml-8 rounded-2xl bg-white p-5 shadow-lg ring-1 ring-stone-100">
                    <p class="font-bold text-forest">Ibadah Raya — Pdt. Samsu Hadi</p>
                    <p class="text-xs text-stone-400">Minggu · 08:00 · Ruang Utama</p>
                </div>
                <div class="rounded-2xl bg-forest p-5 text-white shadow-xl">
                    <p class="text-xs uppercase tracking-wider text-white/50">Renungan</p>
                    <p class="mt-2 font-bold">"Tuhan adalah gembalaku, tidak kekurangan aku."</p>
                    <p class="mt-1 text-xs text-white/50">Mazmur 23:1</p>
                </div>
                <div class="mr-8 rounded-2xl bg-white p-5 shadow-lg ring-1 ring-stone-100">
                    <p class="font-bold text-forest">Presensi FaceNet</p>
                    <p class="text-xs text-stone-400">Vector embedding 128-d · PostgreSQL</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
