@extends('layouts.guest')

@section('title', 'Verifikasi OTP')

@section('content')
<div class="flex min-h-screen items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">
        @php
            $isLogin = ($otpContext ?? null) === 'login';
            $emailToShow = $maskedEmail ?? (auth()->check() ? auth()->user()->email : 'email Anda');
        @endphp
        <p class="text-center text-[11px] font-bold uppercase tracking-widest text-gold">
            {{ $isLogin ? 'Verifikasi Dua Langkah' : 'Langkah Verifikasi' }}
        </p>
        <h1 class="mt-3 text-center text-2xl font-extrabold text-forest dark:text-cyan-400">
            {{ $isLogin ? 'Masukkan Kode OTP' : 'Verifikasi email' }}
        </h1>
        <p class="mt-2 text-center text-sm text-stone-500 dark:text-slate-400">
            Kode OTP 6 digit dikirim ke
            <strong class="text-forest dark:text-cyan-400">{{ $emailToShow }}</strong>
        </p>

        @include('partials.flash')

        <form action="{{ route('otp.verify') }}" method="POST" id="otpForm" class="mt-8">
            @csrf
            {{-- Nilai gabungan 6 digit dikirim lewat field tersembunyi ini --}}
            <input type="hidden" name="code" id="code">

            <label class="mb-2 block text-center text-sm font-semibold text-stone-700 dark:text-slate-300">Masukkan Kode OTP</label>

            <div id="otpBoxes" class="flex justify-center gap-2 sm:gap-3" role="group" aria-label="Kode OTP 6 digit">
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
                Verifikasi &amp; Lanjutkan
            </button>
        </form>

        {{-- Kirim ulang dengan countdown --}}
        <div class="mt-4 text-center">
            <form action="{{ route('otp.resend') }}" method="POST" id="resendForm">
                @csrf
                <button type="submit" id="resendBtn"
                        class="text-sm font-semibold text-forest hover:underline disabled:cursor-not-allowed disabled:text-stone-400 disabled:no-underline dark:text-cyan-400">
                    Kirim ulang kode
                </button>
                <span id="resendTimer" class="hidden text-sm text-stone-500 dark:text-slate-400"></span>
            </form>
        </div>

        @if (config('mail.default') === 'log')
            <p class="mt-6 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-800 ring-1 ring-amber-200">
                Mode lokal: email tercatat di <code>storage/logs/laravel.log</code>. Cari baris <strong>OTP</strong> untuk membaca kode.
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

    // Submit sekali saja
    function doSubmit() {
        if (submitted) return;
        submitted = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Memverifikasi...';
        boxes.forEach(b => b.readOnly = true);
        form.requestSubmit ? form.requestSubmit() : form.submit();
    }

    // Jaga-jaga jika form disubmit lewat cara lain (tombol/enter)
    form.addEventListener('submit', (e) => {
        if (submitted) { return; }
        // biarkan submit berjalan, tapi tandai supaya tidak dobel
        submitted = true;
        submitBtn.disabled = true;
    });

    boxes.forEach((box, idx) => {
        box.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 1);
            if (e.target.value && idx < boxes.length - 1) {
                boxes[idx + 1].focus();
            }
            if (sync().length === 6) doSubmit();
        });

        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                boxes[idx - 1].focus();
                boxes[idx - 1].value = '';
                sync();
            } else if (e.key === 'ArrowLeft' && idx > 0) {
                boxes[idx - 1].focus();
            } else if (e.key === 'ArrowRight' && idx < boxes.length - 1) {
                boxes[idx + 1].focus();
            }
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

    // ===== Countdown 60 detik untuk kirim ulang =====
    const resendBtn = document.getElementById('resendBtn');
    const resendTimer = document.getElementById('resendTimer');
    const STORAGE_KEY = 'otpResendUntil';
    const COOLDOWN = 60; // detik

    function startCooldown(until) {
        localStorage.setItem(STORAGE_KEY, String(until));
        tick();
    }

    function tick() {
        const until = parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10);
        const remaining = Math.ceil((until - Date.now()) / 1000);
        if (remaining > 0) {
            resendBtn.disabled = true;
            resendBtn.classList.add('hidden');
            resendTimer.classList.remove('hidden');
            resendTimer.textContent = `Kirim ulang kode dalam ${remaining} detik`;
            setTimeout(tick, 1000);
        } else {
            resendBtn.disabled = false;
            resendBtn.classList.remove('hidden');
            resendTimer.classList.add('hidden');
            localStorage.removeItem(STORAGE_KEY);
        }
    }

    // Saat form kirim-ulang disubmit, mulai cooldown 60s (persist antar reload)
    document.getElementById('resendForm').addEventListener('submit', () => {
        startCooldown(Date.now() + COOLDOWN * 1000);
    });

    // Jika sedang halaman ini baru dimuat setelah resend, cek cooldown tersimpan
    tick();
})();
</script>
@endpush
