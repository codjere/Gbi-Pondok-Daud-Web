@extends('layouts.guest')

@section('title', 'Tentang Kami — GBI Pondok Daud Kendari')

@section('content')
<div class="min-h-screen">
    <nav class="relative z-10 flex items-center justify-between px-6 py-5 lg:px-16">
        <a href="{{ route('landing') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest text-gold">
                <i class="fa-solid fa-cross"></i>
            </div>
            <div>
                <span class="text-lg font-extrabold text-forest">GBI Pondok Daud</span>
                <p class="text-[10px] font-medium text-stone-400">Kendari · House of Restoration</p>
            </div>
        </a>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('about') }}" class="hidden rounded-xl bg-forest/8 px-4 py-2.5 text-sm font-semibold text-forest sm:inline-flex">Tentang Kami</a>
            <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-forest transition hover:bg-forest/5 sm:px-5">Masuk</a>
            <a href="{{ route('register') }}" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-forest/20 transition hover:bg-forest-mid sm:px-5">Daftar</a>
        </div>
    </nav>

    <section class="relative mx-auto max-w-6xl px-6 pb-12 pt-4 lg:px-8">
        <p class="text-[11px] font-bold uppercase tracking-widest text-gold">Gereja Bethel Indonesia · Sinode GBI</p>
        <h1 class="mt-2 text-3xl font-extrabold leading-tight text-forest sm:text-4xl lg:text-5xl">GBI Pondok Daud Kendari</h1>
        <p class="mt-3 text-lg font-semibold text-forest-mid">House of Restoration — Rumah Pemulihan</p>
        <p class="mt-4 max-w-2xl text-base leading-relaxed text-stone-500">
            Gereja lokal di bawah naungan Sinode GBI yang digembalakan oleh
            <strong>Pendeta Ficky Benyaminsz, M.Th.</strong>
            Kami menjadi rumah pemulihan bagi jemaat di Kota Kendari melalui ibadah, persekutuan, dan pelayanan.
        </p>

        <div class="mt-8 overflow-hidden rounded-3xl bg-stone-900 shadow-xl ring-1 ring-stone-200/80">
            <video class="aspect-video w-full object-cover" controls playsinline poster="{{ asset('assets/about/foto-3.jpeg') }}">
                <source src="{{ asset('assets/about/ibadah-1.mp4') }}" type="video/mp4">
            </video>
        </div>
        <p class="mt-3 text-center text-xs text-stone-400">Cuplikan persekutuan jemaat GBI Pondok Daud Kendari</p>
    </section>

    <section class="mx-auto grid max-w-6xl gap-10 px-6 py-6 lg:grid-cols-2 lg:items-start lg:px-8">
        <div>
            <h2 class="text-2xl font-extrabold text-forest">Siapa kami</h2>
            <p class="mt-4 text-sm leading-relaxed text-stone-600">
                GBI (Gereja Bethel Indonesia) Pondok Daud Kendari adalah salah satu gereja lokal GBI dengan visi
                <em>House of Restoration</em> (Rumah Pemulihan). Portal ini membantu jemaat reservasi ibadah, permohonan doa,
                rekomendasi pelayanan &amp; COOL, serta presensi pelayan dengan pengenalan wajah.
            </p>
            <div class="mt-6 space-y-3 rounded-2xl bg-white p-5 text-sm ring-1 ring-stone-200/80">
                <p class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 w-4 text-gold"></i>
                    <span>Jl. Malaka, Anduonohu, Kec. Kambu, Kota Kendari, Sulawesi Tenggara 93231<br>
                    <span class="text-stone-400">Samping Klinik Raudha / Jl. Martandu Anduonohu</span></span>
                </p>
                <p class="flex gap-3"><i class="fa-solid fa-wheelchair mt-1 w-4 text-gold"></i>
                    <span>Pintu masuk dan area parkir ramah kursi roda</span>
                </p>
                <p class="flex gap-3"><i class="fa-solid fa-user-tie mt-1 w-4 text-gold"></i>
                    <span>Gembala: Pendeta Ficky Benyaminsz, M.Th.</span>
                </p>
            </div>
        </div>
        <div class="overflow-hidden rounded-3xl shadow-lg ring-1 ring-stone-200/80">
            <img src="{{ asset('assets/about/foto-3.jpeg') }}" alt="Perayaan jemaat GBI Pondok Daud Kendari" class="h-full w-full object-cover">
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-12 lg:px-8">
        <h2 class="text-2xl font-extrabold text-forest">Jadwal ibadah</h2>
        <p class="mt-2 text-sm text-stone-500">Semua waktu dalam WITA. Perjamuan Kudus pada Ibadah Raya 1 di awal bulan.</p>
        <div class="mt-6 overflow-hidden rounded-2xl bg-white ring-1 ring-stone-200/80">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-forest text-white">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Jenis Ibadah</th>
                            <th class="px-5 py-3 font-semibold">Hari</th>
                            <th class="px-5 py-3 font-semibold">Waktu</th>
                            <th class="px-5 py-3 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <tr>
                            <td class="px-5 py-3.5 font-semibold text-forest">Ibadah Raya 1</td>
                            <td class="px-5 py-3.5">Minggu</td>
                            <td class="px-5 py-3.5">09.00 WITA</td>
                            <td class="px-5 py-3.5 text-stone-500">Umum &amp; Perjamuan Kudus (awal bulan)</td>
                        </tr>
                        <tr class="bg-cream/60">
                            <td class="px-5 py-3.5 font-semibold text-forest">Ibadah Raya 2</td>
                            <td class="px-5 py-3.5">Minggu</td>
                            <td class="px-5 py-3.5">18.00 WITA</td>
                            <td class="px-5 py-3.5 text-stone-500">Umum</td>
                        </tr>
                        <tr>
                            <td class="px-5 py-3.5 font-semibold text-forest">GROW</td>
                            <td class="px-5 py-3.5">Sabtu</td>
                            <td class="px-5 py-3.5">18.00 WITA</td>
                            <td class="px-5 py-3.5 text-stone-500">Ibadah Youth &amp; Remaja — Generation Revival Of Worship</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="bg-forest text-white">
        <div class="mx-auto grid max-w-6xl gap-6 px-6 py-14 sm:grid-cols-3 lg:px-8">
            <div class="rounded-2xl bg-white/8 p-6 ring-1 ring-white/10">
                <i class="fa-solid fa-house-chimney text-gold"></i>
                <h3 class="mt-3 font-bold">Visi</h3>
                <p class="mt-2 text-sm leading-relaxed text-white/70">House of Restoration — menjadi rumah pemulihan bagi setiap jiwa di Kota Kendari.</p>
            </div>
            <div class="rounded-2xl bg-white/8 p-6 ring-1 ring-white/10">
                <i class="fa-solid fa-bullseye text-gold"></i>
                <h3 class="mt-3 font-bold">Misi</h3>
                <p class="mt-2 text-sm leading-relaxed text-white/70">Menyembah, memuridkan, dan memulihkan melalui ibadah raya, GROW, dan kelompok sel.</p>
            </div>
            <div class="rounded-2xl bg-white/8 p-6 ring-1 ring-white/10">
                <i class="fa-solid fa-heart text-gold"></i>
                <h3 class="mt-3 font-bold">Nilai</h3>
                <p class="mt-2 text-sm leading-relaxed text-white/70">Kasih, pemulihan, integritas, dan kebersamaan lintas generasi.</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-14 lg:px-8">
        <h2 class="text-2xl font-extrabold text-forest">Kehidupan jemaat</h2>
        <p class="mt-2 max-w-xl text-sm text-stone-500">Dari perayaan bersama anak-anak hingga pelayanan keluarga gereja.</p>

        <div class="mt-8 grid gap-5 md:grid-cols-2">
            <figure class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-stone-200/80">
                <img src="{{ asset('assets/about/foto-2.jpeg') }}" alt="Anak-anak jemaat GBI Pondok Daud Kendari" class="h-72 w-full object-cover sm:h-80">
                <figcaption class="p-5">
                    <p class="font-bold text-forest">Generasi berikutnya</p>
                    <p class="mt-1 text-sm text-stone-500">Anak-anak dan sekolah minggu merayakan bersama dalam sukacita.</p>
                </figcaption>
            </figure>
            <figure class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-stone-200/80">
                <img src="{{ asset('assets/about/foto-1.jpeg') }}" alt="Pelayan GBI Pondok Daud Kendari" class="h-72 w-full object-cover sm:h-80">
                <figcaption class="p-5">
                    <p class="font-bold text-forest">Kebersamaan pelayan</p>
                    <p class="mt-1 text-sm text-stone-500">Tim pelayanan yang berjalan bersama di setiap musim kehidupan.</p>
                </figcaption>
            </figure>
        </div>

        <div class="mt-5 overflow-hidden rounded-3xl bg-stone-900 shadow-xl ring-1 ring-stone-200/80">
            <video class="aspect-video w-full object-cover" controls playsinline poster="{{ asset('assets/about/foto-1.jpeg') }}">
                <source src="{{ asset('assets/about/ibadah-2.mp4') }}" type="video/mp4">
            </video>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-8 lg:px-8">
        <div class="rounded-3xl bg-white p-8 ring-1 ring-stone-200/80">
            <h2 class="text-xl font-extrabold text-forest">Kontak &amp; media sosial</h2>
            <p class="mt-2 text-sm text-stone-500">Konseling, pelayanan jemaat, atau informasi hari raya.</p>
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <a href="https://wa.me/628114034416" target="_blank" rel="noopener" class="rounded-2xl bg-cream p-4 hover:ring-1 hover:ring-gold/40">
                    <i class="fa-brands fa-whatsapp text-lg text-gold"></i>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-stone-400">WhatsApp</p>
                    <p class="mt-1 font-bold text-forest">+62 811-4034-416</p>
                </a>
                <a href="https://instagram.com/gbi_pondokdaudkendari" target="_blank" rel="noopener" class="rounded-2xl bg-cream p-4 hover:ring-1 hover:ring-gold/40">
                    <i class="fa-brands fa-instagram text-lg text-gold"></i>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-stone-400">Instagram gereja</p>
                    <p class="mt-1 font-bold text-forest">@gbi_pondokdaudkendari</p>
                </a>
                <a href="https://instagram.com/grow_pondokdaud" target="_blank" rel="noopener" class="rounded-2xl bg-cream p-4 hover:ring-1 hover:ring-gold/40">
                    <i class="fa-brands fa-instagram text-lg text-gold"></i>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-stone-400">Instagram Youth (GROW)</p>
                    <p class="mt-1 font-bold text-forest">@grow_pondokdaud</p>
                </a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-16 lg:px-8">
        <div class="rounded-3xl bg-gold-soft px-8 py-10 text-center">
            <h2 class="text-2xl font-extrabold text-forest">Mari bertumbuh bersama</h2>
            <p class="mx-auto mt-2 max-w-lg text-sm text-stone-600">Daftar sebagai jemaat untuk reservasi ibadah, bergabung di COOL, dan menemukan tempat pelayanan.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="rounded-2xl bg-forest px-7 py-3 text-sm font-bold text-white shadow-lg">Daftar Jemaat</a>
                <a href="{{ route('landing') }}" class="rounded-2xl bg-white px-7 py-3 text-sm font-bold text-forest ring-1 ring-stone-200">Kembali ke Beranda</a>
            </div>
        </div>
        <p class="mt-8 text-center text-xs text-stone-400">© {{ date('Y') }} GBI Pondok Daud Kendari</p>
    </section>
</div>
@endsection
