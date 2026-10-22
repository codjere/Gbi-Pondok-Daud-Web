@extends('layouts.guest')

@section('title', 'GBI Pondok Daud')

@section('content')
<div class="relative overflow-hidden bg-gradient-to-br from-sky-50 via-white to-blue-50 transition-colors duration-300 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
    {{-- Background decorations: sedikit tidak simetris biar terasa "digambar tangan" --}}
    <div class="pointer-events-none absolute -left-24 top-6 h-[26rem] w-[26rem] rounded-full bg-sky-400/10 blur-3xl"></div>
    <div class="pointer-events-none absolute right-[-6rem] top-40 h-80 w-80 rotate-12 rounded-full bg-amber-300/10 blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-[28%] left-[42%] h-64 w-64 rounded-full bg-cyan-500/8 blur-3xl"></div>
    {{-- Grid halus untuk tekstur --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.4] dark:opacity-[0.15]"
         style="background-image: linear-gradient(to right, rgba(8,145,178,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(8,145,178,0.04) 1px, transparent 1px); background-size: 48px 48px;"></div>

    {{-- ===================== NAVBAR ===================== --}}
    <nav class="sticky top-0 z-40 border-b border-sky-100/70 bg-white/70 backdrop-blur-xl transition-colors duration-300 dark:border-slate-700/60 dark:bg-slate-900/70">
        <div class="mx-auto flex max-w-[100rem] items-center justify-between px-5 py-4 sm:px-8 lg:px-12">
            <a href="/" class="group flex items-center gap-3">
                <div class="relative flex h-11 w-11 items-center justify-center rounded-2xl bg-forest text-gold shadow-lg shadow-cyan-900/10 transition-transform duration-300 group-hover:-rotate-6">
                    <i class="fa-solid fa-cross"></i>
                    <span class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full bg-gold ring-2 ring-white dark:ring-slate-900"></span>
                </div>
                <div class="leading-tight">
                    <span class="block text-lg font-extrabold tracking-tight text-forest dark:text-sky-400">GBI Pondok Daud</span>
                    <span class="block text-[10px] font-medium uppercase tracking-[0.2em] text-stone-400 dark:text-slate-500">Portal Jemaat</span>
                </div>
            </a>
            <div class="flex items-center gap-2 sm:gap-3">
                <button onclick="toggleDarkMode()" class="press flex h-10 w-10 items-center justify-center rounded-xl bg-white text-forest shadow-sm ring-1 ring-sky-200/60 transition hover:ring-sky-300 dark:bg-slate-800 dark:text-sky-400 dark:ring-slate-600" title="Ganti tema">
                    <i class="fa-solid fa-moon text-sm dark:hidden"></i>
                    <i class="fa-solid fa-sun text-sm hidden dark:block"></i>
                </button>
                <a href="{{ route('login') }}" class="press hidden rounded-xl px-4 py-2.5 text-sm font-semibold text-forest transition hover:bg-sky-50 sm:inline-block dark:text-sky-400 dark:hover:bg-slate-800">Masuk</a>
                <a href="{{ route('register') }}" class="press inline-flex items-center gap-2 rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-cyan-900/15 transition hover:bg-forest-mid sm:px-5">
                    <span>Daftar Jemaat</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </nav>

    {{-- ===================== HERO ===================== --}}
    <section class="relative z-10 mx-auto grid max-w-[100rem] items-center gap-10 px-5 pb-16 pt-10 sm:px-8 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16 lg:px-12 lg:pb-24 lg:pt-16">
        <div data-reveal>
            <span class="inline-flex items-center gap-2 rounded-full border border-sky-200/70 bg-white/70 px-4 py-1.5 text-xs font-semibold text-forest shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-800/70 dark:text-sky-400">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-gold opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-gold"></span>
                </span>
                House of Restoration · Kendari
            </span>

            <h1 class="mt-6 text-4xl font-extrabold leading-[1.05] tracking-tight text-stone-800 sm:text-5xl lg:text-6xl dark:text-white">
                Rumah pemulihan,<br>
                <span class="relative inline-block">
                    <span class="relative z-10 bg-gradient-to-r from-forest via-forest-mid to-cyan-500 bg-clip-text text-transparent">satu portal jemaat.</span>
                    <span class="absolute -bottom-1 left-0 z-0 h-3 w-full -rotate-1 bg-gold/30 dark:bg-gold/20"></span>
                </span>
            </h1>

            <p class="mt-6 max-w-xl text-base leading-relaxed text-stone-600 sm:text-lg dark:text-slate-400">
                Reservasi ibadah, permohonan doa, renungan harian, sampai presensi pelayan berbasis pengenalan wajah — semua di satu tempat. Dipimpin oleh Pdt. Ficky Benyaminsz, Pdt. Lisna Tappi, dan Pdt. Samsu Hadi.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('register') }}" class="press group inline-flex items-center gap-2 rounded-2xl bg-forest px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-cyan-900/20 transition hover:bg-forest-mid">
                    <i class="fa-solid fa-user-plus transition-transform group-hover:scale-110"></i> Registrasi
                </a>
                <a href="{{ route('login') }}" class="press inline-flex items-center gap-2 rounded-2xl border border-sky-200/70 bg-white/80 px-6 py-3.5 text-sm font-semibold text-forest shadow-sm backdrop-blur transition hover:border-forest/40 hover:bg-white dark:border-slate-700 dark:bg-slate-800/80 dark:text-sky-400">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                </a>
                <a href="#tentang-kami" class="press inline-flex items-center gap-2 rounded-2xl px-5 py-3.5 text-sm font-semibold text-stone-500 transition hover:text-forest dark:text-slate-400 dark:hover:text-sky-400">
                    <i class="fa-solid fa-church text-gold"></i> Tentang Kami
                </a>
            </div>

            {{-- Mini stats: memberi bobot, bukan sekadar deret kotak --}}
            <dl class="mt-10 flex flex-wrap gap-x-8 gap-y-4 border-t border-sky-100 pt-6 dark:border-slate-700/60">
                <div>
                    <dt class="text-2xl font-extrabold text-forest dark:text-sky-400">3</dt>
                    <dd class="text-xs font-medium text-stone-500 dark:text-slate-500">Ibadah tiap pekan</dd>
                </div>
                <div>
                    <dt class="text-2xl font-extrabold text-forest dark:text-sky-400">128-d</dt>
                    <dd class="text-xs font-medium text-stone-500 dark:text-slate-500">Presensi FaceNet</dd>
                </div>
                <div>
                    <dt class="text-2xl font-extrabold text-forest dark:text-sky-400">24/7</dt>
                    <dd class="text-xs font-medium text-stone-500 dark:text-slate-500">Akses portal</dd>
                </div>
            </dl>
        </div>

        {{-- Kartu bertumpuk sedikit miring biar terasa organik --}}
        <div class="relative" data-reveal data-reveal-delay="120">
            <div class="pattern-dots absolute inset-0 -z-10 rounded-[2rem]"></div>
            <div class="relative space-y-4 p-2 sm:p-4">
                <article class="tilt-card ml-auto w-[92%] rotate-1 rounded-3xl border border-white/60 bg-white/90 p-5 shadow-xl shadow-cyan-900/10 backdrop-blur dark:border-slate-700 dark:bg-slate-800/90">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest/10 text-forest dark:bg-sky-400/10 dark:text-sky-400"><i class="fa-solid fa-people-group"></i></div>
                        <div>
                            <p class="font-bold text-forest dark:text-sky-400">Ibadah Raya — Pdt. Ficky Benyaminsz</p>
                            <p class="text-xs text-stone-500 dark:text-slate-500">Minggu · 08:00 · Ruang Utama</p>
                        </div>
                    </div>
                </article>

                <article class="tilt-card -rotate-1 rounded-3xl bg-gradient-to-br from-forest-mid to-forest p-6 text-white shadow-xl shadow-cyan-900/20 dark:from-slate-700 dark:to-slate-800">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gold-soft">Renungan hari ini</p>
                    <p class="mt-3 text-lg font-semibold leading-snug">"Tuhan adalah gembalaku, takkan kekurangan aku."</p>
                    <p class="mt-2 text-xs text-white/70">Mazmur 23:1</p>
                </article>

                <article class="tilt-card mr-auto w-[92%] rotate-1 rounded-3xl border border-gold/20 bg-white/90 p-5 shadow-xl shadow-amber-900/5 backdrop-blur dark:border-slate-700 dark:bg-slate-800/90">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold/15 text-gold"><i class="fa-solid fa-camera-retro"></i></div>
                        <div>
                            <p class="font-bold text-forest dark:text-sky-400">Presensi FaceNet</p>
                            <p class="text-xs text-stone-500 dark:text-slate-500">Vector embedding 128-d · PostgreSQL</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ===================== TENTANG KAMI ===================== --}}
    <section id="tentang-kami" class="relative z-10 scroll-mt-20">
        <div class="mx-auto max-w-[100rem] px-5 pb-10 pt-6 sm:px-8 lg:px-12" data-reveal>
            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-gold">Gereja Bethel Indonesia · Sinode GBI</p>
            <div class="mt-3 grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
                <h2 class="max-w-3xl text-3xl font-extrabold leading-tight tracking-tight text-stone-800 sm:text-4xl lg:text-5xl dark:text-white">GBI Pondok Daud Kendari</h2>
                <p class="text-lg font-semibold text-forest-mid">House of Restoration — Rumah Pemulihan</p>
            </div>
            <p class="mt-5 max-w-3xl text-base leading-relaxed text-stone-600 dark:text-slate-400">
                Gereja lokal di bawah naungan Sinode GBI yang digembalakan oleh
                <strong class="text-forest dark:text-sky-400">Pendeta Ficky Benyaminsz, M.Th.</strong>
                Kami menjadi rumah pemulihan bagi jemaat di Kota Kendari melalui ibadah, persekutuan, dan pelayanan.
            </p>

            <figure class="group relative mt-8 overflow-hidden rounded-[2rem] bg-white shadow-2xl shadow-cyan-900/10 ring-1 ring-sky-200/60 dark:ring-slate-700">
                <video class="aspect-video w-full object-cover transition-transform duration-700 group-hover:scale-[1.02]" controls playsinline poster="{{ asset('assets/about/foto-3.jpeg') }}">
                    <source src="{{ asset('assets/about/ibadah-1.mp4') }}" type="video/mp4">
                </video>
            </figure>
            <p class="mt-3 text-center text-xs text-stone-400 dark:text-slate-600">Cuplikan persekutuan jemaat GBI Pondok Daud Kendari</p>
        </div>

        {{-- Siapa kami --}}
        <div class="mx-auto grid max-w-[100rem] gap-10 px-5 py-8 sm:px-8 lg:grid-cols-2 lg:items-center lg:gap-16 lg:px-12" data-reveal>
            <div>
                <h3 class="text-2xl font-extrabold tracking-tight text-stone-800 sm:text-3xl dark:text-white">Siapa kami</h3>
                <p class="mt-4 text-base leading-relaxed text-stone-600 dark:text-slate-400">
                    GBI Pondok Daud Kendari adalah gereja lokal GBI dengan visi
                    <em class="font-semibold not-italic text-forest dark:text-sky-400">House of Restoration</em> (Rumah Pemulihan). Portal ini membantu jemaat reservasi ibadah, permohonan doa,
                    rekomendasi pelayanan &amp; COOL, serta presensi pelayan dengan pengenalan wajah.
                </p>
                <div class="mt-6 space-y-1 rounded-3xl border border-sky-100 bg-white/70 p-2 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-800/70">
                    <p class="flex items-start gap-3 rounded-2xl p-3 transition hover:bg-sky-50 dark:hover:bg-slate-700/50">
                        <i class="fa-solid fa-location-dot mt-1 w-4 text-gold"></i>
                        <span class="text-sm text-stone-600 dark:text-slate-400">Jl. Malaka, Anduonohu, Kec. Kambu, Kota Kendari, Sulawesi Tenggara 93231<br>
                        <span class="text-stone-400 dark:text-slate-600">Samping Klinik Raudha / Jl. Martandu Anduonohu</span></span>
                    </p>
                    <p class="flex items-start gap-3 rounded-2xl p-3 transition hover:bg-sky-50 dark:hover:bg-slate-700/50">
                        <i class="fa-solid fa-wheelchair mt-1 w-4 text-gold"></i>
                        <span class="text-sm text-stone-600 dark:text-slate-400">Pintu masuk dan area parkir ramah kursi roda</span>
                    </p>
                    <p class="flex items-start gap-3 rounded-2xl p-3 transition hover:bg-sky-50 dark:hover:bg-slate-700/50">
                        <i class="fa-solid fa-user-tie mt-1 w-4 text-gold"></i>
                        <span class="text-sm text-stone-600 dark:text-slate-400">Gembala: Pendeta Ficky Benyaminsz, M.Th.</span>
                    </p>
                </div>
            </div>
            <figure class="group overflow-hidden rounded-[2rem] shadow-2xl shadow-cyan-900/10 ring-1 ring-sky-200/60 dark:ring-slate-700">
                <img src="{{ asset('assets/about/foto-3.jpeg') }}" alt="Perayaan jemaat GBI Pondok Daud Kendari" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
            </figure>
        </div>

        {{-- Jadwal ibadah --}}
        <div class="mx-auto max-w-[100rem] px-5 py-12 sm:px-8 lg:px-12" data-reveal>
            <h3 class="text-2xl font-extrabold tracking-tight text-stone-800 sm:text-3xl dark:text-white">Jadwal ibadah</h3>
            <p class="mt-2 text-sm text-stone-500 dark:text-slate-500">Semua waktu dalam WITA. Perjamuan Kudus pada Ibadah Raya 1 di awal bulan.</p>
            <div class="mt-6 overflow-hidden rounded-3xl bg-white shadow-lg shadow-cyan-900/5 ring-1 ring-sky-200/60 dark:bg-slate-800 dark:ring-slate-700">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead class="bg-gradient-to-r from-forest to-forest-mid text-white dark:from-slate-700 dark:to-slate-600">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Jenis Ibadah</th>
                                <th class="px-6 py-4 font-semibold">Hari</th>
                                <th class="px-6 py-4 font-semibold">Waktu</th>
                                <th class="px-6 py-4 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-100 dark:divide-slate-700">
                            <tr class="transition hover:bg-sky-50/70 dark:hover:bg-slate-700/40">
                                <td class="px-6 py-4 font-semibold text-forest dark:text-sky-400">Ibadah Raya 1</td>
                                <td class="px-6 py-4 dark:text-slate-300">Minggu</td>
                                <td class="px-6 py-4 dark:text-slate-300">09.00 WITA</td>
                                <td class="px-6 py-4 text-stone-500 dark:text-slate-500">Umum &amp; Perjamuan Kudus (awal bulan)</td>
                            </tr>
                            <tr class="bg-gold-soft/40 transition hover:bg-gold-soft/60 dark:bg-slate-700/50 dark:hover:bg-slate-700/70">
                                <td class="px-6 py-4 font-semibold text-forest dark:text-sky-400">Ibadah Raya 2</td>
                                <td class="px-6 py-4 dark:text-slate-300">Minggu</td>
                                <td class="px-6 py-4 dark:text-slate-300">18.00 WITA</td>
                                <td class="px-6 py-4 text-stone-500 dark:text-slate-500">Umum</td>
                            </tr>
                            <tr class="transition hover:bg-sky-50/70 dark:hover:bg-slate-700/40">
                                <td class="px-6 py-4 font-semibold text-forest dark:text-sky-400">GROW</td>
                                <td class="px-6 py-4 dark:text-slate-300">Sabtu</td>
                                <td class="px-6 py-4 dark:text-slate-300">18.00 WITA</td>
                                <td class="px-6 py-4 text-stone-500 dark:text-slate-500">Ibadah Youth &amp; Remaja — Generation Revival Of Worship</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Visi/Misi --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-forest-mid via-forest to-cyan-600 text-white dark:from-slate-800 dark:via-slate-700 dark:to-slate-600">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-20 left-10 h-72 w-72 rounded-full bg-gold/10 blur-3xl"></div>
            <div class="mx-auto grid max-w-[100rem] gap-6 px-5 py-16 sm:grid-cols-3 sm:px-8 lg:px-12" data-reveal>
                <div class="tilt-card rounded-3xl bg-white/10 p-7 ring-1 ring-white/20 backdrop-blur-sm">
                    <i class="fa-solid fa-house-chimney text-2xl text-gold"></i>
                    <h3 class="mt-4 text-lg font-bold">Visi</h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/80">House of Restoration — menjadi rumah pemulihan bagi setiap jiwa di Kota Kendari.</p>
                </div>
                <div class="tilt-card rounded-3xl bg-white/10 p-7 ring-1 ring-white/20 backdrop-blur-sm">
                    <i class="fa-solid fa-bullseye text-2xl text-gold"></i>
                    <h3 class="mt-4 text-lg font-bold">Misi</h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/80">Menyembah, memuridkan, dan memulihkan melalui ibadah raya, GROW, dan kelompok sel.</p>
                </div>
                <div class="tilt-card rounded-3xl bg-white/10 p-7 ring-1 ring-white/20 backdrop-blur-sm">
                    <i class="fa-solid fa-heart text-2xl text-gold"></i>
                    <h3 class="mt-4 text-lg font-bold">Nilai</h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/80">Kasih, pemulihan, integritas, dan kebersamaan lintas generasi.</p>
                </div>
            </div>
        </div>

        {{-- Kehidupan jemaat --}}
        <div class="mx-auto max-w-[100rem] px-5 py-16 sm:px-8 lg:px-12" data-reveal>
            <h3 class="text-2xl font-extrabold tracking-tight text-stone-800 sm:text-3xl dark:text-white">Kehidupan jemaat</h3>
            <p class="mt-2 max-w-xl text-sm text-stone-500 dark:text-slate-500">Dari perayaan bersama anak-anak hingga pelayanan keluarga gereja.</p>

            <div class="mt-8 grid gap-6 md:grid-cols-2">
                <figure class="tilt-card group overflow-hidden rounded-[2rem] bg-white shadow-lg shadow-cyan-900/5 ring-1 ring-sky-200/60 dark:bg-slate-800 dark:ring-slate-700">
                    <div class="overflow-hidden">
                        <img src="{{ asset('assets/about/foto-2.jpeg') }}" alt="Anak-anak jemaat GBI Pondok Daud Kendari" class="h-72 w-full object-cover transition-transform duration-700 group-hover:scale-105 sm:h-80">
                    </div>
                    <figcaption class="p-6">
                        <p class="font-bold text-forest dark:text-sky-400">Generasi berikutnya</p>
                        <p class="mt-1 text-sm text-stone-500 dark:text-slate-500">Anak-anak dan sekolah minggu merayakan bersama dalam sukacita.</p>
                    </figcaption>
                </figure>
                <figure class="tilt-card group overflow-hidden rounded-[2rem] bg-white shadow-lg shadow-cyan-900/5 ring-1 ring-sky-200/60 dark:bg-slate-800 dark:ring-slate-700">
                    <div class="overflow-hidden">
                        <img src="{{ asset('assets/about/foto-1.jpeg') }}" alt="Pelayan GBI Pondok Daud Kendari" class="h-72 w-full object-cover transition-transform duration-700 group-hover:scale-105 sm:h-80">
                    </div>
                    <figcaption class="p-6">
                        <p class="font-bold text-forest dark:text-sky-400">Kebersamaan pelayan</p>
                        <p class="mt-1 text-sm text-stone-500 dark:text-slate-500">Tim pelayanan yang berjalan bersama di setiap musim kehidupan.</p>
                    </figcaption>
                </figure>
            </div>

            <figure class="group mt-6 overflow-hidden rounded-[2rem] bg-white shadow-2xl shadow-cyan-900/10 ring-1 ring-sky-200/60 dark:bg-slate-800 dark:ring-slate-700">
                <video class="aspect-video w-full object-cover transition-transform duration-700 group-hover:scale-[1.02]" controls playsinline poster="{{ asset('assets/about/foto-1.jpeg') }}">
                    <source src="{{ asset('assets/about/ibadah-2.mp4') }}" type="video/mp4">
                </video>
            </figure>
        </div>

        {{-- Kontak --}}
        <div class="mx-auto max-w-[100rem] px-5 pb-10 sm:px-8 lg:px-12" data-reveal>
            <div class="rounded-[2rem] border border-sky-100 bg-white/80 p-8 shadow-lg shadow-cyan-900/5 backdrop-blur lg:p-10 dark:border-slate-700 dark:bg-slate-800/80">
                <h3 class="text-xl font-extrabold tracking-tight text-stone-800 sm:text-2xl dark:text-white">Kontak &amp; media sosial</h3>
                <p class="mt-2 text-sm text-stone-500 dark:text-slate-500">Konseling, pelayanan jemaat, atau informasi hari raya.</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <a href="https://wa.me/628114034416" target="_blank" rel="noopener" class="tilt-card group rounded-2xl bg-gold-soft/60 p-5 transition hover:bg-gold-soft dark:bg-slate-700/60 dark:hover:bg-slate-700">
                        <i class="fa-brands fa-whatsapp text-xl text-gold transition-transform group-hover:scale-110"></i>
                        <p class="mt-3 text-[11px] font-semibold uppercase tracking-wide text-stone-400 dark:text-slate-500">WhatsApp</p>
                        <p class="mt-1 font-bold text-forest dark:text-sky-400">+62 811-4034-416</p>
                    </a>
                    <a href="https://instagram.com/gbi_pondokdaudkendari" target="_blank" rel="noopener" class="tilt-card group rounded-2xl bg-gold-soft/60 p-5 transition hover:bg-gold-soft dark:bg-slate-700/60 dark:hover:bg-slate-700">
                        <i class="fa-brands fa-instagram text-xl text-gold transition-transform group-hover:scale-110"></i>
                        <p class="mt-3 text-[11px] font-semibold uppercase tracking-wide text-stone-400 dark:text-slate-500">Instagram gereja</p>
                        <p class="mt-1 font-bold text-forest dark:text-sky-400">@gbi_pondokdaudkendari</p>
                    </a>
                    <a href="https://instagram.com/grow_pondokdaud" target="_blank" rel="noopener" class="tilt-card group rounded-2xl bg-gold-soft/60 p-5 transition hover:bg-gold-soft dark:bg-slate-700/60 dark:hover:bg-slate-700">
                        <i class="fa-brands fa-instagram text-xl text-gold transition-transform group-hover:scale-110"></i>
                        <p class="mt-3 text-[11px] font-semibold uppercase tracking-wide text-stone-400 dark:text-slate-500">Instagram Youth (GROW)</p>
                        <p class="mt-1 font-bold text-forest dark:text-sky-400">@grow_pondokdaud</p>
                    </a>
                </div>
            </div>
        </div>

        {{-- CTA penutup --}}
        <div class="mx-auto max-w-[100rem] px-5 pb-20 sm:px-8 lg:px-12" data-reveal>
            <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-gold-soft via-white to-cream px-8 py-14 text-center ring-1 ring-gold/30 dark:from-slate-800 dark:via-slate-800 dark:to-slate-700 dark:ring-slate-600">
                <div class="pointer-events-none absolute -left-10 -top-10 h-40 w-40 rounded-full bg-gold/20 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-10 right-0 h-52 w-52 rounded-full bg-forest/10 blur-3xl"></div>
                <h3 class="relative text-2xl font-extrabold tracking-tight text-stone-800 sm:text-3xl dark:text-white">Mari bertumbuh bersama</h3>
                <p class="relative mx-auto mt-3 max-w-lg text-sm text-stone-600 dark:text-slate-400">Daftar sebagai jemaat untuk reservasi ibadah, bergabung di COOL, dan menemukan tempat pelayanan.</p>
                <div class="relative mt-7 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('register') }}" class="press rounded-2xl bg-forest px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-cyan-900/20 transition hover:bg-forest-mid">Daftar Jemaat</a>
                    <a href="{{ route('login') }}" class="press rounded-2xl border border-sky-200/70 bg-white px-7 py-3.5 text-sm font-semibold text-forest transition hover:border-forest/40 dark:border-slate-600 dark:bg-slate-800 dark:text-sky-400">Masuk</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="border-t border-sky-200/50 bg-white/60 backdrop-blur transition-colors duration-300 dark:border-slate-700 dark:bg-slate-900/60">
        <div class="mx-auto max-w-[100rem] px-5 py-10 sm:px-8 lg:px-12">
            <div class="grid gap-8 md:grid-cols-4">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-forest text-gold">
                            <i class="fa-solid fa-cross text-sm"></i>
                        </div>
                        <span class="font-bold text-forest dark:text-sky-400">GBI Pondok Daud</span>
                    </div>
                    <p class="text-xs text-stone-500 dark:text-slate-500">House of Restoration</p>
                    <p class="text-xs text-stone-400 dark:text-slate-600">Jl. Malaka, Anduonohu, Kendari</p>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-forest dark:text-sky-400">Tautan Cepat</h4>
                    <ul class="mt-3 space-y-2">
                        <li><a href="{{ route('login') }}" class="text-xs text-stone-500 transition hover:text-forest hover:underline dark:text-slate-500 dark:hover:text-sky-400">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="text-xs text-stone-500 transition hover:text-forest hover:underline dark:text-slate-500 dark:hover:text-sky-400">Daftar Jemaat</a></li>
                        <li><a href="#tentang-kami" class="text-xs text-stone-500 transition hover:text-forest hover:underline dark:text-slate-500 dark:hover:text-sky-400">Tentang Kami</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-forest dark:text-sky-400">Kontak</h4>
                    <ul class="mt-3 space-y-2">
                        <li class="flex items-center gap-2 text-xs text-stone-500 dark:text-slate-500">
                            <i class="fa-brands fa-whatsapp text-forest dark:text-sky-400"></i>
                            <a href="https://wa.me/628114034416" target="_blank" class="transition hover:text-forest dark:hover:text-sky-400">+62 811-4034-416</a>
                        </li>
                        <li class="flex items-center gap-2 text-xs text-stone-500 dark:text-slate-500">
                            <i class="fa-brands fa-instagram text-forest dark:text-sky-400"></i>
                            <a href="https://instagram.com/gbi_pondokdaudkendari" target="_blank" class="transition hover:text-forest dark:hover:text-sky-400">@gbi_pondokdaudkendari</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-forest dark:text-sky-400">Jadwal Ibadah</h4>
                    <ul class="mt-3 space-y-2">
                        <li class="text-xs text-stone-500 dark:text-slate-500">Minggu 09:00 - Ibadah Raya 1</li>
                        <li class="text-xs text-stone-500 dark:text-slate-500">Minggu 18:00 - Ibadah Raya 2</li>
                        <li class="text-xs text-stone-500 dark:text-slate-500">Sabtu 18:00 - GROW Youth</li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 border-t border-sky-200/50 pt-6 text-center dark:border-slate-700">
                <p class="text-xs text-stone-400 dark:text-slate-600">© {{ date('Y') }} GBI Pondok Daud Kendari. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>
@endsection
