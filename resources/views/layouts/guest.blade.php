<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'GBI Pondok Daud')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <style type="text/tailwindcss">
        /* Aktifkan dark mode berbasis class (.dark) alih-alih prefers-color-scheme.
           Tanpa ini, semua utility dark:* di Tailwind v4 hanya merespon setting OS,
           sehingga tombol toggle tidak konsisten dengan class .dark pada <html>. */
        @custom-variant dark (&:where(.dark, .dark *));

        @theme {
            --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            --color-cream: #F0F9FF;
            --color-forest: #0891B2;
            --color-forest-mid: #06B6D4;
            --color-gold: #F59E0B;
            --color-gold-soft: #FEF3C7;
            --color-warm: #FB923C;

            /* ==================== PALET DARK MUTED ====================
               Gelap sungguhan, hangat tapi peredam (low saturation). Kunci agar
               tidak terasa "AI": warna diredam, kontras halus, aksen tenang.
               Karena SELURUH utility dark:*-slate-* di semua view merujuk variabel
               ini, satu perubahan di sini otomatis mewarnai ulang tema gelap. */
            --color-slate-950: #0a0a09;
            --color-slate-900: #111110;   /* base background — near-black warm */
            --color-slate-800: #1a1a18;   /* surface / card — charcoal muted */
            --color-slate-700: #242422;   /* raised / border */
            --color-slate-600: #33322f;   /* hover / border kuat */
            --color-slate-500: #4a4844;   /* divider / teks redup */
            --color-slate-400: #7c7972;   /* teks sekunder */
            --color-slate-300: #a8a49c;   /* teks body di dark */
            --color-slate-200: #cbc7bf;
        }
    </style>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* ==================== COLOR SCHEME LOCK ==================== */
        :root {
            color-scheme: light;
        }
        
        .dark {
            color-scheme: dark;
        }
        
        /* ==================== CUSTOM COLORS ==================== */
        /* Warna brand kustom (bukan warna Tailwind bawaan) tetap dikunci
           agar konsisten di light mode. */
        .text-forest { color: #0891B2; }
        .text-forest-mid { color: #06B6D4; }
        .text-gold { color: #F59E0B; }
        .text-gold-soft { color: #FEF3C7; }
        .bg-forest { background-color: #0891B2; }
        .bg-forest-mid { background-color: #06B6D4; }
        .bg-gold { background-color: #F59E0B; }
        .bg-gold-soft { background-color: #FEF3C7; }
        .bg-cream { background-color: #F0F9FF; }

        /* ==================== DARK MODE FALLBACK ====================
           Semua selector dibungkus :where(.dark) sehingga spesifisitasnya
           setara satu class (0,1,0). Ini penting: bila sebuah elemen sudah
           punya utility dark:* eksplisit (mis. `bg-white dark:bg-slate-700`),
           utility tersebut yang menang. Aturan di bawah hanya berlaku sebagai
           fallback untuk elemen yang TIDAK menyertakan pasangan dark:* sendiri,
           sehingga tetap ikut berubah saat class .dark aktif. */

        /* Aksen dark diredam: tan/khaki tenang, bukan emas berkilau. Kalem &
           manusiawi. */
        :where(.dark) .text-forest { color: #b39a70; }
        :where(.dark) .text-forest-mid { color: #c4ad84; }
        :where(.dark) .text-gold { color: #bfa678; }
        :where(.dark) .bg-forest { background-color: #242422; }
        :where(.dark) .bg-forest-mid { background-color: #1a1a18; }
        :where(.dark) .bg-gold-soft { background-color: #242422; }
        :where(.dark) .bg-cream { background-color: #242422; }

        /* Teks */
        :where(.dark) .text-stone-800 { color: #e7e3da; }
        :where(.dark) .text-stone-600 { color: #b9b4aa; }
        :where(.dark) .text-stone-500 { color: #8a867d; }
        :where(.dark) .text-stone-400 { color: #6a675f; }
        :where(.dark) .text-cyan-400 { color: #b39a70; }
        :where(.dark) .text-cyan-500 { color: #c4ad84; }
        :where(.dark) .text-cyan-600 { color: #b39a70; }
        :where(.dark) .text-cyan-700 { color: #b39a70; }
        :where(.dark) .text-sky-400 { color: #c4ad84; }
        :where(.dark) .text-sky-500 { color: #c4ad84; }
        :where(.dark) .text-sky-700 { color: #b39a70; }
        :where(.dark) .text-amber-400 { color: #bfa678; }
        :where(.dark) .text-blue-400 { color: #8f9a92; }
        :where(.dark) .text-emerald-600 { color: #7fa588; }

        /* Latar — card charcoal muted, menonjol halus dari background near-black. */
        :where(.dark) .bg-white { background-color: #1a1a18; color: #e7e3da; }
        :where(.dark) .bg-stone-100 { background-color: #242422; }
        :where(.dark) .bg-amber-100 { background-color: #242422; }
        :where(.dark) .bg-blue-100 { background-color: #242422; }
        :where(.dark) .bg-indigo-100 { background-color: #242422; }
        :where(.dark) .bg-emerald-100 { background-color: #242422; }

        /* Ring / border — tipis & tenang supaya batas card halus, tidak mencolok. */
        :where(.dark) .ring-stone-200\/60 { --tw-ring-color: rgba(124, 121, 114, 0.22); }
        :where(.dark) .border-stone-200\/60 { border-color: rgba(124, 121, 114, 0.22); }
        :where(.dark) .ring-cyan-200\/60 { --tw-ring-color: rgba(179, 154, 112, 0.18); }
        :where(.dark) .border-cyan-200\/50 { border-color: rgba(179, 154, 112, 0.16); }
        :where(.dark) .ring-sky-200\/60 { --tw-ring-color: rgba(179, 154, 112, 0.18); }
        :where(.dark) .border-sky-200\/50 { border-color: rgba(179, 154, 112, 0.16); }
        :where(.dark) .ring-slate-500 { --tw-ring-color: rgba(74, 72, 68, 0.5); }

        /* Gradient fallback: hanya untuk gradient yang tidak punya pasangan
           dark:from-/dark:to- sendiri. */
        :where(.dark) .from-cyan-50 { background-color: #1a1a18; }
        :where(.dark) .via-sky-50 { background-color: #1a1a18; }
        :where(.dark) .to-blue-100 { background-color: #1a1a18; }
        :where(.dark) .from-gold-soft { background-color: #1a1a18; }
        :where(.dark) .to-sky-50 { background-color: #1a1a18; }
        :where(.dark) .from-forest-mid { background-color: #242422; }
        :where(.dark) .to-forest { background-color: #1a1a18; }
        :where(.dark) .from-forest { background-color: #242422; }
        :where(.dark) .via-forest-mid { background-color: #1a1a18; }

        /* Gradient brand utama (header kartu, tombol) di dark mode:
           nyaris solid charcoal — halus, tidak "berwarna/AI". */
        :where(.dark) .bg-gradient-to-r.from-forest,
        :where(.dark) .bg-gradient-to-br.from-forest,
        :where(.dark) .bg-gradient-to-b.from-forest {
            background-image: linear-gradient(to right, #2b2a27, #201f1d) !important;
        }
        :where(.dark) .bg-gradient-to-br.from-forest {
            background-image: linear-gradient(to bottom right, #2b2a27, #1a1a18) !important;
        }
        :where(.dark) .bg-gradient-to-b.from-forest-mid {
            background-image: linear-gradient(to bottom, #2b2a27, #1c1c1a) !important;
        }
        
        /* ==================== PATTERN DOTS ==================== */
        /* Titik dekoratif TRANSPARAN — menyatu dengan background halaman,
           tidak membentuk kotak/panel di sekitar konten (baik light/dark). */
        .pattern-dots {
            background-image: radial-gradient(circle, rgba(8, 145, 178, 0.10) 1px, transparent 1px);
            background-size: 22px 22px;
        }
        
        .dark .pattern-dots {
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }

        /* Background dark dibuat rata/flat — tanpa glow berwarna yang bikin
           kesan "AI". Hanya sentuhan sangat halus supaya tidak mati total. */
        .dark body {
            background-image: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.015) 0%, transparent 55%) !important;
        }

        /* ==================== AUTH PANEL ====================
           Panel form login/register. Ditulis sebagai CSS eksplisit (bukan hanya
           utility Tailwind) agar pergantian light/dark PASTI bekerja dan tidak
           bergantung timing kompilasi Tailwind CDN. */
        .auth-panel {
            background-color: #ffffff;              /* LIGHT: putih bersih */
            background-image: none;
            transition: background-color 0.3s ease;
        }
        .dark .auth-panel {
            background-color: #131315;              /* DARK: gelap netral */
            background-image: linear-gradient(to bottom right, #1a1a18, #131315);
        }
        
        /* ==================== HOVER EFFECTS ==================== */
        .hover-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        
        /* Button animations */
        .btn-primary {
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        /* ==================== ANIMATIONS ==================== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .scale-in {
            animation: scaleIn 0.3s ease-out forwards;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-up {
            animation: slideUp 0.4s ease-out forwards;
        }

        /* ==================== MICRO-INTERACTIONS ==================== */
        /* Efek "ditekan" pada tombol/link — terasa taktil, bukan flat */
        .press { transition: transform 0.15s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease; }
        .press:hover { transform: translateY(-2px); }
        .press:active { transform: translateY(0) scale(0.97); }

        /* Kartu yang "timbul" saat disentuh */
        .tilt-card { transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease; will-change: transform; }
        .tilt-card:hover { transform: translateY(-6px); box-shadow: 0 24px 45px -18px rgba(8, 66, 91, 0.35); }

        /* ==================== SCROLL REVEAL ==================== */
        /* Elemen muncul dengan efek naik + fade saat masuk viewport */
        [data-reveal] {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1), transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
            will-change: opacity, transform;
        }
        [data-reveal].is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ==================== PAGE ENTER TRANSITION ==================== */
        /* Seluruh konten "timbul" halus saat halaman dibuka */
        @keyframes pageEnter {
            from { opacity: 0; transform: translateY(14px) scale(0.995); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .page-enter { animation: pageEnter 0.32s cubic-bezier(0.22, 1, 0.36, 1) both; }

        /* Fade-out saat berpindah halaman */
        body.is-leaving { opacity: 0; transition: opacity 0.12s ease; }

        /* Hormati preferensi reduce-motion */
        @media (prefers-reduced-motion: reduce) {
            .press, .tilt-card, [data-reveal] { transition: none !important; }
            .press:hover, .press:active, .tilt-card:hover { transform: none !important; }
            [data-reveal] { opacity: 1 !important; transform: none !important; }
            .page-enter { animation: none !important; }
            body.is-leaving { opacity: 1 !important; }
        }
    </style>

    @stack('styles')
    
    <script>
        // Terapkan tema SEBELUM render untuk mencegah kedip.
        // DETERMINISTIK: hanya ikut preferensi tersimpan. Default = LIGHT.
        // Tidak ikut prefers-color-scheme OS supaya tema tidak berubah sendiri
        // saat pindah menu.
        (function() {
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="bg-gradient-to-br from-cyan-50 via-sky-50 to-blue-100 text-stone-800 antialiased overflow-x-hidden transition-colors duration-300 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-slate-100"
      style="background-image: radial-gradient(circle at 20% 30%, rgba(6, 182, 212, 0.08) 0%, transparent 50%), radial-gradient(circle at 80% 70%, rgba(14, 165, 233, 0.08) 0%, transparent 50%);">
    <div class="page-enter">
        @yield('content')
    </div>
    
    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
        }

        // ==================== SCROLL REVEAL ====================
        (function() {
            const items = document.querySelectorAll('[data-reveal]');
            if (!items.length) return;

            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce || !('IntersectionObserver' in window)) {
                items.forEach(el => el.classList.add('is-visible'));
                return;
            }

            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const delay = parseInt(entry.target.getAttribute('data-reveal-delay') || '0', 10);
                        setTimeout(() => entry.target.classList.add('is-visible'), delay);
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

            items.forEach(el => io.observe(el));
        })();

        // ==================== PAGE LEAVE TRANSITION ====================
        // Saat klik link internal, fade-out dulu supaya perpindahan tidak kaku
        (function() {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce) return;

            document.addEventListener('click', function(e) {
                const a = e.target.closest('a');
                if (!a) return;
                const href = a.getAttribute('href');
                if (!href || href.startsWith('#') || a.target === '_blank' || href.startsWith('mailto:') || href.startsWith('tel:') || a.hasAttribute('download')) return;
                // hanya untuk navigasi same-origin
                let url;
                try { url = new URL(href, window.location.href); } catch (_) { return; }
                if (url.origin !== window.location.origin) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;

                e.preventDefault();
                document.body.classList.add('is-leaving');
                setTimeout(() => { window.location.href = url.href; }, 110);
            });

            // Jika kembali via tombol back (bfcache), pastikan konten terlihat lagi
            window.addEventListener('pageshow', function() {
                document.body.classList.remove('is-leaving');
            });
        })();
    </script>
    
    @stack('scripts')
</body>
</html>
