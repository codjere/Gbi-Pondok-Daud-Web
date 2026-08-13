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
        @theme {
            --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            --color-cream: #FAF7F2;
            --color-forest: #1B4332;
            --color-forest-mid: #2D6A4F;
            --color-gold: #C9952A;
            --color-gold-soft: #F5E6C8;
            --color-warm: #E07A5F;
        }
    </style>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        #sidebar.open { transform: translateX(0); }
        #sidebar-overlay.open { opacity: 1; pointer-events: auto; }
    </style>

    @stack('styles')
</head>
<body class="bg-cream text-stone-800 antialiased overflow-x-hidden">

    @php
        $activeRoute = $activeRoute ?? (request()->route()?->getName() ?? '');
        $jemaatMenu = [
            ['label' => 'Beranda',             'icon' => 'fa-house',           'route' => 'dashboard',        'url' => route('dashboard')],
            ['label' => 'Jadwal Ibadah',       'icon' => 'fa-calendar-days',   'route' => 'jemaat.jadwal',    'url' => route('jemaat.jadwal')],
            ['label' => 'Reservasi Kehadiran', 'icon' => 'fa-ticket',          'route' => 'jemaat.reservasi', 'url' => route('jemaat.reservasi')],
            ['label' => 'Permohonan Doa',      'icon' => 'fa-hands-praying',   'route' => 'jemaat.doa',       'url' => route('jemaat.doa')],
            ['label' => 'Renungan & Warta',    'icon' => 'fa-book-open',       'route' => 'jemaat.warta',     'url' => route('jemaat.warta')],
            ['label' => 'Kuesioner Profil',    'icon' => 'fa-clipboard-list',  'route' => 'jemaat.kuesioner', 'url' => route('jemaat.kuesioner')],
            ['label' => 'Rekomendasi AI',      'icon' => 'fa-wand-magic-sparkles', 'route' => 'jemaat.rekomendasi', 'url' => route('jemaat.rekomendasi')],
            ['label' => 'Asisten GBI Pondok Daud', 'icon' => 'fa-robot',           'route' => 'jemaat.chat',      'url' => route('jemaat.chat')],
        ];
        $pengurusMenu = auth()->user()->isPengurus() ? [
            ['label' => 'Absen Pelayan',     'icon' => 'fa-camera',        'route' => 'pengurus.absen',   'url' => route('pengurus.absen')],
            ['label' => 'Kelola Jadwal',     'icon' => 'fa-calendar-plus', 'route' => 'pengurus.jadwal',  'url' => route('pengurus.jadwal')],
            ['label' => 'Konten & Warta',    'icon' => 'fa-pen-to-square', 'route' => 'pengurus.konten',  'url' => route('pengurus.konten')],
            ['label' => 'Laporan Kehadiran','icon' => 'fa-file-lines',    'route' => 'pengurus.laporan', 'url' => route('pengurus.laporan')],
        ] : [];
    @endphp

    {{-- Overlay mobile --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden" onclick="closeSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-[280px] max-w-[85vw] flex-col bg-forest text-white -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:w-[260px] lg:max-w-none">
        <div class="flex items-center justify-between px-5 pt-6 pb-4 lg:px-6 lg:pt-7">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gold/20 ring-1 ring-gold/30">
                    <i class="fa-solid fa-cross text-gold"></i>
                </div>
                <div>
                    <p class="text-sm font-extrabold leading-none">GBI Pondok Daud</p>
                    <p class="mt-0.5 text-[10px] text-white/50">Portal Jemaat</p>
                </div>
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
                        <a href="{{ $item['url'] }}" onclick="closeSidebar()"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all
                                  {{ $activeRoute === $item['route'] ? 'bg-white/15 text-white' : 'text-white/65 hover:bg-white/8 hover:text-white' }}">
                            <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-xs {{ $activeRoute === $item['route'] ? 'text-gold' : 'text-white/40' }}"></i>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if (count($pengurusMenu))
            <div>
                <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-white/35">Panel Pengurus</p>
                <div class="space-y-0.5">
                    @foreach ($pengurusMenu as $item)
                        <a href="{{ $item['url'] }}" onclick="closeSidebar()"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all
                                  {{ $activeRoute === $item['route'] ? 'bg-gold/20 text-gold-soft' : 'text-white/65 hover:bg-white/8 hover:text-white' }}">
                            <i class="fa-solid {{ $item['icon'] }} w-4 text-center text-xs {{ $activeRoute === $item['route'] ? 'text-gold' : 'text-white/40' }}"></i>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3 rounded-xl bg-white/8 px-3 py-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gold/25 text-sm font-bold text-gold">
                    {{ auth()->user()->initials() }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[10px] capitalize text-white/45">{{ auth()->user()->role }} · aktif</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-white/40 transition hover:text-white" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex min-h-screen flex-col lg:pl-[260px]">

        <header class="sticky top-0 z-30 border-b border-stone-200/80 bg-cream/95 px-4 py-3 backdrop-blur-md sm:px-6 lg:px-8 lg:py-4">
            <div class="flex items-center gap-3">
                <button type="button" onclick="openSidebar()" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-forest shadow-sm ring-1 ring-stone-200/80 lg:hidden">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[10px] font-semibold uppercase tracking-wider text-stone-400 sm:text-[11px]">@yield('breadcrumb', 'Dashboard')</p>
                    <h1 class="truncate text-sm font-bold text-forest sm:text-lg lg:text-xl">GBI Pondok Daud</h1>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <div class="hidden items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 ring-1 ring-emerald-200/60 sm:flex">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span class="text-[10px] font-semibold text-emerald-800 sm:text-xs">PostgreSQL</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-5 sm:px-6 sm:py-7 lg:px-8">
            @yield('content')
        </main>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebar-overlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebar-overlay').classList.remove('open');
            document.body.style.overflow = '';
        }
    </script>

    @stack('scripts')
</body>
</html>
