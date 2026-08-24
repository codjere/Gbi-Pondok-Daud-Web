@extends('layouts.guest')

@section('title', 'Verifikasi OTP')

@section('content')
<div class="flex min-h-screen items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">
        <p class="text-center text-[11px] font-bold uppercase tracking-widest text-gold">Langkah 2 dari 3</p>
        <h1 class="mt-3 text-center text-2xl font-extrabold text-forest">Verifikasi email</h1>
        <p class="mt-2 text-center text-sm text-stone-500">
            Kode OTP 6 digit dikirim ke
            <strong class="text-forest">{{ auth()->user()->email }}</strong>
        </p>

        @include('partials.flash')

        <form action="{{ route('otp.verify') }}" method="POST" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="code" class="mb-1.5 block text-sm font-semibold text-stone-700">Kode OTP</label>
                <input type="text" id="code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required
                       autocomplete="one-time-code"
                       class="w-full rounded-xl bg-white px-4 py-3 text-center text-2xl font-extrabold tracking-[0.4em] text-forest ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-forest/40"
                       placeholder="••••••">
            </div>
            <button type="submit" class="w-full rounded-xl bg-forest py-3.5 text-sm font-bold text-white hover:bg-forest-mid">
                Verifikasi & Lanjutkan
            </button>
        </form>

        <form action="{{ route('otp.resend') }}" method="POST" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm font-semibold text-forest hover:underline">Kirim ulang kode</button>
        </form>

        @if (config('mail.default') === 'log')
            <p class="mt-6 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-800 ring-1 ring-amber-200">
                Mode lokal: email tercatat di <code>storage/logs/laravel.log</code>. Cari baris <strong>OTP</strong> untuk membaca kode.
            </p>
        @endif
    </div>
</div>
@endsection
