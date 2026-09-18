<?php

namespace Database\Seeders;

use App\Models\CoolGroup;
use App\Models\FaqItem;
use App\Models\TimPelayanan;
use Illuminate\Database\Seeder;

class AiSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            [
                'nama' => 'Praise & Worship',
                'slug' => 'praise-worship',
                'deskripsi' => 'Tim musik ibadah yang melayani dalam pujian dan penyembahan setiap ibadah raya.',
                'minat_tags' => ['musik', 'melayani', 'doa'],
                'bakat_tags' => ['vokal', 'gitar', 'keyboard', 'drum'],
                'min_usia' => 17, 'max_usia' => 45,
                'hari_pelayanan' => ['sabtu', 'minggu'],
                'icon' => 'fa-music',
            ],
            [
                'nama' => 'Multimedia',
                'slug' => 'multimedia',
                'deskripsi' => 'Tim teknologi gereja: live streaming, sound system, proyektor, dan dokumentasi ibadah.',
                'minat_tags' => ['multimedia', 'teknologi', 'kreatif'],
                'bakat_tags' => ['fotografi', 'video', 'desain', 'komputer'],
                'min_usia' => 18, 'max_usia' => 50,
                'hari_pelayanan' => ['sabtu', 'minggu'],
                'icon' => 'fa-video',
            ],
            [
                'nama' => 'Usher & Hospitality',
                'slug' => 'usher',
                'deskripsi' => 'Tim penyambut jemaat, pengatur tempat duduk, dan pelayanan tamu di setiap ibadah.',
                'minat_tags' => ['melayani', 'sosial', 'administrasi'],
                'bakat_tags' => ['public_speaking', 'organisasi'],
                'min_usia' => 17, 'max_usia' => 60,
                'hari_pelayanan' => ['minggu'],
                'icon' => 'fa-door-open',
            ],
            [
                'nama' => 'Youth / Pemuda',
                'slug' => 'youth',
                'deskripsi' => 'Pelayanan khusus generasi muda: ibadah pemuda, retreat, dan kegiatan outreach.',
                'minat_tags' => ['anak_muda', 'evangelisme', 'musik', 'kreatif'],
                'bakat_tags' => ['vokal', 'gitar', 'public_speaking', 'organisasi'],
                'min_usia' => 15, 'max_usia' => 30,
                'hari_pelayanan' => ['sabtu', 'minggu'],
                'icon' => 'fa-fire',
            ],
            [
                'nama' => 'Tim Doa & Intercessory',
                'slug' => 'doa',
                'deskripsi' => 'Tim pendoa yang melayani ibadah doa syafaat dan mendoakan permohonan jemaat.',
                'minat_tags' => ['doa', 'melayani', 'evangelisme'],
                'bakat_tags' => ['public_speaking', 'organisasi'],
                'min_usia' => 20, 'max_usia' => 65,
                'hari_pelayanan' => ['rabu', 'minggu'],
                'icon' => 'fa-hands-praying',
            ],
            [
                'nama' => 'Tim Anak (Sunday School)',
                'slug' => 'anak',
                'deskripsi' => 'Pelayanan pengajaran Alkitab dan aktivitas kreatif untuk anak-anak jemaat.',
                'minat_tags' => ['melayani', 'sosial', 'kreatif'],
                'bakat_tags' => ['public_speaking', 'desain', 'organisasi'],
                'min_usia' => 18, 'max_usia' => 55,
                'hari_pelayanan' => ['minggu'],
                'icon' => 'fa-child',
            ],
        ];

        foreach ($teams as $t) {
            TimPelayanan::updateOrCreate(['slug' => $t['slug']], $t);
        }

        // COOL berbasis KUL (wilayah) GBI Pondok Daud Kendari, Sulawesi Tenggara.
        // Waktu memakai WITA (Kendari).
        $cools = [
            ['nama' => 'COOL Onembute — Rehoboth', 'pemimpin' => 'Bpk. Andi Wijaya', 'kecamatan' => 'Onembute', 'lokasi' => 'Sekretariat GBI Pondok Daud, Onembute (Pusat)', 'jadwal' => 'Selasa 19:00 WITA', 'min_usia' => 20, 'max_usia' => 45, 'tema_minat' => ['doa', 'melayani'], 'kapasitas' => 15, 'terisi' => 8],
            ['nama' => 'COOL Ponggolaka — Bethel', 'pemimpin' => 'Ibu Maria Sitorus', 'kecamatan' => 'Ponggolaka', 'lokasi' => 'Jl. Ponggolaka, Kendari', 'jadwal' => 'Rabu 19:30 WITA', 'min_usia' => 25, 'max_usia' => 55, 'tema_minat' => ['musik', 'doa'], 'kapasitas' => 12, 'terisi' => 10],
            ['nama' => 'COOL Anggoya — Efrata', 'pemimpin' => 'Bpk. Samuel Hutapea', 'kecamatan' => 'Anggoya', 'lokasi' => 'Jl. Anggoya, Kendari', 'jadwal' => 'Kamis 19:00 WITA', 'min_usia' => 18, 'max_usia' => 40, 'tema_minat' => ['anak_muda', 'evangelisme'], 'kapasitas' => 15, 'terisi' => 6],
            ['nama' => 'COOL Lepo-Lepo — Nazareth', 'pemimpin' => 'Ibu Grace Tanuwijaya', 'kecamatan' => 'Lepo-Lepo', 'lokasi' => 'Jl. Lepo-Lepo, Kendari', 'jadwal' => 'Jumat 19:00 WITA', 'min_usia' => 22, 'max_usia' => 50, 'tema_minat' => ['sosial', 'melayani'], 'kapasitas' => 12, 'terisi' => 5],
            ['nama' => 'COOL Anggoya — Salem (Youth)', 'pemimpin' => 'Bpk. Daniel Pangaribuan', 'kecamatan' => 'Anggoya', 'lokasi' => 'Jl. Anggoya Raya, Kendari', 'jadwal' => 'Sabtu 16:00 WITA', 'min_usia' => 17, 'max_usia' => 35, 'tema_minat' => ['anak_muda', 'musik', 'kreatif'], 'kapasitas' => 15, 'terisi' => 12],
            ['nama' => 'COOL Onembute — Sion', 'pemimpin' => 'Ibu Debora Simanjuntak', 'kecamatan' => 'Onembute', 'lokasi' => 'Jl. Onembute, Kendari', 'jadwal' => 'Minggu 13:00 WITA', 'min_usia' => 20, 'max_usia' => 50, 'tema_minat' => ['doa', 'administrasi'], 'kapasitas' => 10, 'terisi' => 7],
        ];

        foreach ($cools as $c) {
            CoolGroup::updateOrCreate(['nama' => $c['nama']], $c);
        }

        $faqs = [
            ['kategori' => 'pembaptisan', 'pertanyaan' => 'Bagaimana cara mendaftar pembaptisan?', 'jawaban' => 'Untuk mendaftar pembaptisan di GBI Pondok Daud: (1) Ikuti Kelas KRK (Kelas Regenerasi Kejadian), (2) Konsultasi dengan pendeta jemaat, (3) Daftar ke sekretariat gereja atau melalui portal GBI Pondok Daud. Pembaptisan dilaksanakan setelah kelas KRK selesai.', 'keywords' => ['pembaptisan', 'baptis', 'krk', 'daftar']],
            ['kategori' => 'pernikahan', 'pertanyaan' => 'Apa syarat pendaftaran pernikahan gereja?', 'jawaban' => 'Syarat pernikahan GBI Pondok Daud: (1) Kedua mempelai sudah dibaptis selam, (2) Mengikuti konseling pra-nikah minimal 3 sesi, (3) Menyerahkan dokumen ke sekretariat minimal 1 bulan sebelum pernikahan.', 'keywords' => ['pernikahan', 'nikah', 'menikah', 'konseling']],
            ['kategori' => 'jadwal', 'pertanyaan' => 'Kapan ibadah pemuda dilaksanakan?', 'jawaban' => 'Ibadah pemuda GBI Pondok Daud biasanya dilaksanakan setiap Sabtu malam pukul 18:00 WIB. Cek jadwal terbaru di menu Jadwal Ibadah GBI Pondok Daud.', 'keywords' => ['pemuda', 'youth', 'ibadah', 'jadwal', 'sabtu']],
            ['kategori' => 'cool', 'pertanyaan' => 'Apa itu COOL dan bagaimana bergabung?', 'jawaban' => 'COOL (Community Of Love) adalah kelompok sel GBI Pondok Daud berbasis wilayah/KUL yang berkumpul rutin untuk persekutuan, firman, dan doa. COOL tersebar di wilayah Kendari seperti Onembute, Ponggolaka, Anggoya, dan Lepo-Lepo. Buka menu Rekomendasi AI untuk mendapat rekomendasi COOL terdekat sesuai domisili Anda.', 'keywords' => ['cool', 'kul', 'sel', 'kelompok', 'komunitas', 'wilayah']],
            ['kategori' => 'pelayanan', 'pertanyaan' => 'Bagaimana cara mendaftar tim pelayanan?', 'jawaban' => 'Buka menu Rekomendasi AI di GBI Pondok Daud. Sistem AI akan merekomendasikan tim pelayanan yang cocok berdasarkan minat, bakat, dan ketersediaan waktu Anda. Tim meliputi: Praise & Worship, Multimedia, Usher, Youth, Doa, dan Tim Anak.', 'keywords' => ['pelayanan', 'tim', 'melayani', 'volunteer']],
            ['kategori' => 'doa', 'pertanyaan' => 'Bagaimana mengirim permohonan doa?', 'jawaban' => 'Anda bisa mengirim permohonan doa melalui menu Permohonan Doa di GBI Pondok Daud. Tim pendoa GBI Pondok Daud akan mendoakan permohonan Anda. Permohonan bisa dikirim secara anonim jika diinginkan.', 'keywords' => ['doa', 'permohonan', 'shalom']],
        ];

        foreach ($faqs as $f) {
            FaqItem::updateOrCreate(['pertanyaan' => $f['pertanyaan']], $f);
        }
    }
}
