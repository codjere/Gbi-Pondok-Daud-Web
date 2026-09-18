<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') — GBI Pondok Daud</title>

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
        
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        #sidebar.open { transform: translateX(0); }
        #sidebar-overlay.open { opacity: 1; pointer-events: auto; }
        
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

        /* ==================== BODY BACKGROUND ====================
           Ditulis eksplisit agar light = putih bersih (bukan biru pucat) dan
           dark = gelap netral. Tidak bergantung timing kompilasi Tailwind CDN. */
        body.app-body {
            background-color: #f7f8fa !important;   /* LIGHT: putih (sedikit off-white agar kartu putih tetap menonjol) */
            background-image: none !important;
        }
        html.dark body.app-body {
            background-color: #131315 !important;   /* DARK: gelap netral */
            background-image: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.015) 0%, transparent 55%) !important;
        }

        /* ==================== LIGHT MODE: PUTIH TOTAL, TANPA JEJAK BIRU ====================
           Solusi menyeluruh & konsisten. Di LIGHT mode (html tanpa .dark), semua latar
           bergradien/berwarna cyan-sky-blue PUCAT (level 50/100) dipaksa jadi putih —
           apa pun arah gradiennya. Memakai [class~="..."] (cocok token utuh yang
           dipisah spasi) sehingga TIDAK keliru menyasar level terang seperti
           from-cyan-500 / from-cyan-100 (tombol & badge brand tetap berwarna). */

        /* 1) Latar gradien pucat (stop 50) -> putih. */
        html:not(.dark) [class~="from-cyan-50"],
        html:not(.dark) [class~="from-sky-50"],
        html:not(.dark) [class~="from-blue-50"],
        html:not(.dark) [class~="via-sky-50"],
        html:not(.dark) [class~="via-cyan-50"],
        html:not(.dark) [class~="to-sky-50"],
        html:not(.dark) [class~="to-cyan-50"],
        html:not(.dark) [class~="to-blue-50"],
        html:not(.dark) [class~="to-blue-100"] {
            background-image: none !important;
            background-color: #ffffff !important;
        }

        /* 2) Latar solid pucat -> putih. */
        html:not(.dark) [class~="bg-cyan-50"],
        html:not(.dark) [class~="bg-sky-50"],
        html:not(.dark) [class~="bg-blue-50"] {
            background-color: #ffffff !important;
            background-image: none !important;
        }

        /* 3) Latar pucat level-100 (badge kecil, lingkaran ikon, pill kategori):
              dibuat abu sangat muda netral — supaya tak ada semburat biru, tapi
              bentuk elemen kecil tetap terlihat. TIDAK menyasar level-500 (tombol). */
        html:not(.dark) [class~="from-cyan-100"],
        html:not(.dark) [class~="to-sky-100"],
        html:not(.dark) [class~="from-sky-100"],
        html:not(.dark) [class~="to-cyan-100"],
        html:not(.dark) [class~="bg-cyan-100"],
        html:not(.dark) [class~="bg-sky-100"] {
            background-image: none !important;
            background-color: #eef1f4 !important;
        }

        /* 4) Hover yang tadinya kebiruan -> abu sangat muda (netral). */
        html:not(.dark) [class~="hover:from-cyan-50"]:hover,
        html:not(.dark) [class~="hover:to-sky-50"]:hover,
        html:not(.dark) [class~="hover:from-cyan-100"]:hover,
        html:not(.dark) [class~="hover:to-sky-100"]:hover,
        html:not(.dark) [class~="hover:bg-sky-50"]:hover,
        html:not(.dark) [class~="hover:bg-cyan-50"]:hover {
            background-image: none !important;
            background-color: #f3f4f6 !important;
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
        /* Konten utama "timbul" halus saat pindah menu */
        @keyframes pageEnter {
            from { opacity: 0; transform: translateY(14px) scale(0.995); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .page-enter { animation: pageEnter 0.32s cubic-bezier(0.22, 1, 0.36, 1) both; }

        /* Fade-out saat berpindah halaman */
        body.is-leaving { opacity: 0; transition: opacity 0.12s ease; }

        /* Stagger item sidebar saat halaman dibuka */
        @keyframes navItemIn {
            from { opacity: 0; transform: translateX(-8px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .nav-stagger { animation: navItemIn 0.4s cubic-bezier(0.22, 1, 0.36, 1) both; }

        /* Hormati preferensi reduce-motion */
        @media (prefers-reduced-motion: reduce) {
            .press, .tilt-card, [data-reveal] { transition: none !important; }
            .press:hover, .press:active, .tilt-card:hover { transform: none !important; }
            [data-reveal] { opacity: 1 !important; transform: none !important; }
            .page-enter, .nav-stagger { animation: none !important; }
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
        
        // Handle session expiration gracefully
        window.addEventListener('load', function() {
            // Check for session expiration on AJAX requests
            let originalFetch = window.fetch;
            window.fetch = function(url, options) {
                return originalFetch(url, options).then(response => {
                    if (response.status === 401) {
                        window.location.href = '/login';
                    }
                    return response;
                }).catch(error => {
                    console.error('Fetch error:', error);
                    throw error;
                });
            };
        });
        
        // Clear dark mode preference on logout
        function clearDarkModePreference() {
            localStorage.removeItem('darkMode');
        }
    </script>
</head>
<body class="app-body text-stone-800 antialiased overflow-x-hidden transition-colors duration-300 dark:text-slate-100">

    @php
        $activeRoute = $activeRoute ?? (request()->route()?->getName() ?? '');
        $jemaatMenu = [
            ['label' => 'Beranda',             'icon' => 'fa-house',           'route' => 'dashboard',        'url' => route('dashboard'), 'active' => ['dashboard']],
            ['label' => 'Jadwal Ibadah',       'icon' => 'fa-calendar-days',   'route' => 'jemaat.jadwal',    'url' => route('jemaat.jadwal'), 'active' => ['jemaat.jadwal']],
            ['label' => 'Reservasi Kehadiran', 'icon' => 'fa-ticket',          'route' => 'jemaat.reservasi', 'url' => route('jemaat.reservasi'), 'active' => ['jemaat.reservasi', 'jemaat.reservasi.store']],
            ['label' => 'Permohonan Doa',      'icon' => 'fa-hands-praying',   'route' => 'jemaat.doa',       'url' => route('jemaat.doa'), 'active' => ['jemaat.doa']],
            ['label' => 'Alkitab Digital',     'icon' => 'fa-bible',           'route' => 'jemaat.alkitab',   'url' => route('jemaat.alkitab'), 'active' => ['jemaat.alkitab', 'jemaat.alkitab.baca', 'jemaat.alkitab.plans', 'jemaat.alkitab.plan', 'jemaat.alkitab.cari']],
            ['label' => 'Renungan & Warta',    'icon' => 'fa-book-open',       'route' => 'jemaat.warta',     'url' => route('jemaat.warta'), 'active' => ['jemaat.warta', 'jemaat.warta.show']],
            ['label' => 'Rekomendasi AI',      'icon' => 'fa-wand-magic-sparkles', 'route' => 'jemaat.rekomendasi', 'url' => route('jemaat.rekomendasi'), 'active' => ['jemaat.rekomendasi', 'jemaat.kuesioner']],
            ['label' => 'Asisten GBI Pondok Daud', 'icon' => 'fa-robot',           'route' => 'jemaat.chat',      'url' => route('jemaat.chat'), 'active' => ['jemaat.chat']],
        ];
        $pengurusMenu = auth()->user()->isPengurus() ? [
            ['label' => 'Kelola Jadwal',   'icon' => 'fa-calendar-plus', 'route' => 'pengurus.jadwal',   'url' => route('pengurus.jadwal')],
            ['label' => 'Daftar Hadir',    'icon' => 'fa-clipboard-user', 'route' => 'pengurus.absen',    'url' => route('pengurus.absen')],
            ['label' => 'Permohonan Doa',  'icon' => 'fa-hands-praying', 'route' => 'pengurus.permohonan-doa',      'url' => route('pengurus.permohonan-doa')],
            ['label' => 'Kelola Konten',   'icon' => 'fa-newspaper',      'route' => 'pengurus.konten',   'url' => route('pengurus.konten')],
            ['label' => 'Laporan',         'icon' => 'fa-chart-pie',      'route' => 'pengurus.laporan',  'url' => route('pengurus.laporan')],
        ] : [];
    @endphp

    {{-- Mobile Sidebar Overlay --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-[60] bg-black/60 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"
         onclick="closeSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="sidebar"
          class="fixed inset-y-0 left-0 z-[70] flex w-[280px] max-w-[85vw] flex-col bg-gradient-to-b from-forest-mid to-forest text-white -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:w-[260px] lg:max-w-none shadow-2xl lg:shadow-none dark:from-slate-800 dark:to-slate-900">
        <div class="flex items-center justify-between px-4 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gold/20 text-gold">
                    <i class="fa-solid fa-cross text-sm"></i>
                </div>
                <span class="font-semibold">GBI Pondok Daud</span>
            </a>
            <button type="button" onclick="closeSidebar()" class="flex h-9 w-9 items-center justify-center rounded-lg text-white/60 hover:bg-white/10 lg:hidden">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="sidebar-scroll flex-1 space-y-5 overflow-y-auto px-4 pb-4">
            <div>
                <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-white/35">Menu Jemaat</p>
                <div class="space-y-0.5">
                    @foreach ($jemaatMenu as $item)
                        @php $isActive = in_array($activeRoute, $item['active'] ?? [$item['route']]); @endphp
                        <a href="{{ $item['url'] }}" onclick="closeSidebar()"
                           class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-300
                                  {{ $isActive ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-0.5' }}">
                            <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-gold transition-all duration-300 {{ $isActive ? 'opacity-100' : 'opacity-0 group-hover:opacity-60' }}"></span>
                            <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-xs transition-transform duration-300 group-hover:scale-110 {{ $isActive ? 'text-gold' : 'text-white/45' }}"></i>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($pengurusMenu)
                <div>
                    <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-white/35">Menu Pengurus</p>
                    <div class="space-y-0.5">
                        @foreach ($pengurusMenu as $item)
                            @php $isActive = $activeRoute === $item['route']; @endphp
                            <a href="{{ $item['url'] }}" onclick="closeSidebar()"
                               class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-300
                                      {{ $isActive ? 'bg-gold/20 text-gold-soft' : 'text-white/65 hover:bg-white/8 hover:text-white hover:translate-x-0.5' }}">
                                <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-gold transition-all duration-300 {{ $isActive ? 'opacity-100' : 'opacity-0 group-hover:opacity-60' }}"></span>
                                <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-xs transition-transform duration-300 group-hover:scale-110 {{ $isActive ? 'text-gold' : 'text-white/40' }}"></i>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </nav>

        <div class="border-t border-white/10 px-4 py-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('jemaat.profil') }}" onclick="closeSidebar()" class="group flex min-w-0 flex-1 items-center gap-3 rounded-xl px-1 py-1 transition hover:bg-white/10" title="Lihat & edit profil">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gold/20 text-gold">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-medium text-white group-hover:underline">{{ auth()->user()->name }}</p>
                        <p class="truncate text-[10px] text-white/50">Lihat & edit profil</p>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" onsubmit="clearDarkModePreference()">
                    @csrf
                    <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg text-white/60 hover:bg-white/10" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="min-h-screen lg:ml-[260px]">
        <header class="sticky top-0 z-30 border-b border-cyan-200/60 bg-white/90 px-4 py-3 backdrop-blur-md sm:px-6 lg:px-8 lg:py-4 transition-colors duration-300 dark:bg-slate-900/90 dark:border-slate-700/50">
            <div class="flex items-center gap-3">
                <button type="button" onclick="openSidebar()" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-sky-500 text-white shadow-sm ring-1 ring-cyan-200/60 lg:hidden hover-card">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[10px] font-semibold uppercase tracking-wider text-cyan-600 sm:text-[11px] dark:text-cyan-400">@yield('breadcrumb', 'Dashboard')</p>
                    <h1 class="truncate text-sm font-bold text-forest sm:text-lg lg:text-xl dark:text-cyan-400">GBI Pondok Daud</h1>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    {{-- Dark Mode Toggle --}}
                    <button onclick="toggleDarkMode()" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-sky-500 text-white shadow-sm ring-1 ring-cyan-200/60 hover-card dark:from-slate-700 dark:to-slate-600 dark:text-yellow-400 dark:ring-slate-600" title="Toggle Dark Mode">
                        <i class="fa-solid fa-moon text-sm dark:hidden"></i>
                        <i class="fa-solid fa-sun text-sm hidden dark:block"></i>
                    </button>
                    <div class="hidden items-center gap-1.5 rounded-full bg-gradient-to-r from-cyan-100 to-sky-100 px-2.5 py-1 ring-1 ring-cyan-200/60 sm:flex dark:from-slate-700 dark:to-slate-600 dark:ring-slate-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                        <span class="text-[10px] font-semibold text-cyan-700 dark:text-cyan-400">PostgreSQL</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-enter px-4 py-6 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
        }
        
        // Sidebar functions
        function openSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
            
            // Add scale-in animation to menu items with stagger
            const menuItems = sidebar.querySelectorAll('nav a');
            menuItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'scale(0.95) translateY(10px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1) translateY(0)';
                }, 50 + (index * 30)); // Stagger with 30ms delay between items
            });
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }

        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // ==================== SIDEBAR STAGGER ON LOAD ====================
        // Item menu muncul berurutan saat halaman dibuka — terasa hidup
        window.addEventListener('DOMContentLoaded', function() {
            if (prefersReduced) return;
            const links = document.querySelectorAll('#sidebar nav a');
            links.forEach((el, i) => {
                el.classList.add('nav-stagger');
                el.style.animationDelay = (i * 45) + 'ms';
            });
        });

        // ==================== SCROLL REVEAL ====================
        (function() {
            const items = document.querySelectorAll('[data-reveal]');
            if (!items.length) return;
            if (prefersReduced || !('IntersectionObserver' in window)) {
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
        // Fade-out halus saat pindah menu supaya perpindahan tidak kaku
        (function() {
            if (prefersReduced) return;
            document.addEventListener('click', function(e) {
                const a = e.target.closest('a');
                if (!a) return;
                const href = a.getAttribute('href');
                if (!href || href.startsWith('#') || a.target === '_blank' || href.startsWith('mailto:') || href.startsWith('tel:') || a.hasAttribute('download')) return;
                let url;
                try { url = new URL(href, window.location.href); } catch (_) { return; }
                if (url.origin !== window.location.origin) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
                // abaikan link yang menuju halaman yang sama (mis. anchor internal)
                if (url.href === window.location.href) return;

                e.preventDefault();
                document.body.classList.add('is-leaving');
                setTimeout(() => { window.location.href = url.href; }, 110);
            });
            window.addEventListener('pageshow', function() {
                document.body.classList.remove('is-leaving');
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
