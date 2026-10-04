@extends('layouts.guest')

@section('title', 'Verifikasi OTP Login Wajah')

@section('content')
<div class="flex min-h-screen items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">
        <div class="mb-2 flex justify-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-forest text-gold">
                <i class="fa-solid fa-shield-halved text-lg"></i>
            </div>
        </div>
        <p class="text-center text-[11px] font-bold uppercase tracking-widest text-gold">Verifikasi Dua Langkah</p>
        <h1 class="mt-2 text-center text-2xl font-extrabold text-forest dark:text-cyan-400">Wajah dikenali</h1>
        <p class="mt-2 text-center text-sm text-stone-500 dark:text-slate-400">
            Untuk keamanan, masukkan kode OTP 6 digit yang dikirim ke
            <strong class="text-forest dark:text-cyan-400">{{ $maskedEmail }}</strong>
        </p>

        @include('partials.flash')

        <form action="{{ route('face.otp.verify') }}" method="POST" id="otpForm" class="mt-8">
            @csrf
            <input type="hidden" name="code" id="code">

            <label class="mb-2 block text-center text-sm font-semibold text-stone-700 dark:text-slate-300">Kode OTP</label>

            <div id="otpBoxes" class="flex justify-center gap-2 sm:gap-3">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code"
                           data-otp-index="{{ $i }}"
                           class="otp-digit h-14 w-12 rounded-xl bg-white text-center text-2xl font-extrabold text-forest ring-1 ring-stone-200 outline-none transition focus:ring-2 focus:ring-forest/50 sm:h-16 sm:w-14 dark:bg-slate-800 dark:text-cyan-400 dark:ring-slate-600"
                           @if($i === 0) autofocus @endif>
                @endfor
            </div>

            @error('code')
                <p class="mt-3 text-center text-sm text-red-500">{{ $message }}</p>
            @enderror

            <button type="submit" id="otpSubmit"
                    class="mt-6 w-full rounded-xl bg-forest py-3.5 text-sm font-bold text-white transition hover:bg-forest-mid disabled:cursor-not-allowed disabled:opacity-50">
                Masuk
            </button>
        </form>

        <div class="mt-4 text-center">
            <form action="{{ route('face.otp.resend') }}" method="POST" id="resendForm">
                @csrf
                <button type="submit" id="resendBtn"
                        class="text-sm font-semibold text-forest hover:underline disabled:cursor-not-allowed disabled:text-stone-400 disabled:no-underline dark:text-cyan-400">
                    Kirim ulang kode
                </button>
                <span id="resendTimer" class="hidden text-sm text-stone-500 dark:text-slate-400"></span>
            </form>
        </div>

        <p class="mt-6 text-center text-sm">
            <a href="{{ route('login') }}" class="font-semibold text-stone-500 hover:text-forest dark:text-slate-400">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke login
            </a>
        </p>

        @if (config('mail.default') === 'log')
            <p class="mt-6 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-800 ring-1 ring-amber-200">
                Mode lokal: kode OTP tercatat di <code>storage/logs/laravel.log</code> (cari baris <strong>OTP</strong>).
            </p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const boxes = Array.from(document.querySelectorAll('.otp-digit'));
    const hidden = document.getElementById('code');
    const form = document.getElementById('otpForm');
    const submitBtn = document.getElementById('otpSubmit');
    let submitted = false; // cegah submit ganda (penyebab error 419)

    function sync() {
        const code = boxes.map(b => b.value).join('');
        hidden.value = code;
        submitBtn.disabled = code.length !== 6 || submitted;
        return code;
    }

    function doSubmit() {
        if (submitted) return;
        submitted = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Memverifikasi...';
        boxes.forEach(b => b.readOnly = true);
        form.requestSubmit ? form.requestSubmit() : form.submit();
    }

    form.addEventListener('submit', () => {
        if (submitted) return;
        submitted = true;
        submitBtn.disabled = true;
    });

    boxes.forEach((box, idx) => {
        box.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 1);
            if (e.target.value && idx < boxes.length - 1) boxes[idx + 1].focus();
            if (sync().length === 6) doSubmit();
        });
        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                boxes[idx - 1].focus(); boxes[idx - 1].value = ''; sync();
            } else if (e.key === 'ArrowLeft' && idx > 0) { boxes[idx - 1].focus(); }
            else if (e.key === 'ArrowRight' && idx < boxes.length - 1) { boxes[idx + 1].focus(); }
        });
        box.addEventListener('paste', (e) => {
            e.preventDefault();
            const digits = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            if (!digits) return;
            digits.split('').forEach((d, i) => { if (boxes[i]) boxes[i].value = d; });
            boxes[Math.min(digits.length, boxes.length - 1)].focus();
            if (sync().length === 6) doSubmit();
        });
    });
    sync();

    // Countdown 60s resend
    const resendBtn = document.getElementById('resendBtn');
    const resendTimer = document.getElementById('resendTimer');
    const KEY = 'faceOtpResendUntil';
    const COOLDOWN = 60;
    function tick() {
        const until = parseInt(localStorage.getItem(KEY) || '0', 10);
        const remaining = Math.ceil((until - Date.now()) / 1000);
        if (remaining > 0) {
            resendBtn.disabled = true; resendBtn.classList.add('hidden');
            resendTimer.classList.remove('hidden');
            resendTimer.textContent = `Kirim ulang kode dalam ${remaining} detik`;
            setTimeout(tick, 1000);
        } else {
            resendBtn.disabled = false; resendBtn.classList.remove('hidden');
            resendTimer.classList.add('hidden'); localStorage.removeItem(KEY);
        }
    }
    document.getElementById('resendForm').addEventListener('submit', () => {
        localStorage.setItem(KEY, String(Date.now() + COOLDOWN * 1000)); tick();
    });
    tick();
})();
</script>
@endpush
