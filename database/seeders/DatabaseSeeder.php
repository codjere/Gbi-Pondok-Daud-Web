<?php

namespace Database\Seeders;

use App\Models\JadwalIbadah;
use App\Models\Kehadiran;
use App\Models\KontenWarta;
use App\Models\PermohonanDoa;
use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AiSeeder::class);
        $this->call(BibleSeeder::class);

        $pendeta = [
            'Pdt. Ficky Benyaminsz',
            'Pdt. Lisna Tappi',
            'Pdt. Samsu Hadi',
        ];

        $pengurus = User::create([
            'name' => 'Admin GBI Pondok Daud',
            'email' => 'admin@gbipondokdaud.id',
            'password' => 'password123',
            'role' => 'pengurus',
            'has_biometric' => true,
            'email_verified_at' => now(),
            'face_embedding' => $this->mockEmbedding(),
        ]);

        $jemaatList = [
            ['name' => 'Andi Raharjo', 'email' => 'andi@email.com'],
            ['name' => 'Maria Sitorus', 'email' => 'maria@email.com'],
            ['name' => 'Budi Santoso', 'email' => 'budi@email.com'],
            ['name' => 'Grace Tanuwijaya', 'email' => 'grace@email.com'],
            ['name' => 'Daniel Hutapea', 'email' => 'daniel@email.com'],
            ['name' => 'Ruth Simanjuntak', 'email' => 'ruth@email.com'],
            ['name' => 'Samuel Wijaya', 'email' => 'samuel@email.com'],
            ['name' => 'Debora Pangaribuan', 'email' => 'debora@email.com'],
        ];

        $jemaatUsers = collect($jemaatList)->map(fn ($j) => User::create([
            'name' => $j['name'],
            'email' => $j['email'],
            'password' => 'password123',
            'role' => 'jemaat',
            'has_biometric' => true,
            'email_verified_at' => now(),
            'face_embedding' => $this->mockEmbedding(),
        ]));

        $jadwals = [
            [
                'nama_sesi' => 'Ibadah Raya 1',
                'tanggal' => now()->next('Sunday')->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'pembicara' => $pendeta[0],
                'lokasi' => 'Ruang Utama GBI Pondok Daud',
                'batas_kuota' => 200,
                'status' => 'terpublikasi',
            ],
            [
                'nama_sesi' => 'Ibadah Raya 2',
                'tanggal' => now()->next('Sunday')->toDateString(),
                'jam_mulai' => '17:00',
                'jam_selesai' => '19:00',
                'pembicara' => $pendeta[1],
                'lokasi' => 'Ruang Utama GBI Pondok Daud',
                'batas_kuota' => 8,
                'status' => 'terpublikasi',
            ],
            [
                'nama_sesi' => 'Ibadah Malam',
                'tanggal' => now()->next('Sunday')->toDateString(),
                'jam_mulai' => '19:30',
                'jam_selesai' => '21:00',
                'pembicara' => $pendeta[2],
                'lokasi' => 'Ruang Utama GBI Pondok Daud',
                'batas_kuota' => 8,
                'status' => 'terpublikasi',
            ],
            [
                'nama_sesi' => 'Doa Syafaat',
                'tanggal' => now()->next('Wednesday')->toDateString(),
                'jam_mulai' => '19:00',
                'jam_selesai' => '20:30',
                'pembicara' => 'Tim Pendoa GBI Pondok Daud',
                'lokasi' => 'Ruang Doa',
                'batas_kuota' => 50,
                'status' => 'terpublikasi',
            ],
            [
                'nama_sesi' => 'Ibadah Raya 3',
                'tanggal' => now()->subWeek()->next('Sunday')->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'pembicara' => $pendeta[0],
                'lokasi' => 'Ruang Utama GBI Pondok Daud',
                'batas_kuota' => 200,
                'status' => 'selesai',
            ],
        ];

        $jadwalModels = collect($jadwals)->map(fn ($j) => JadwalIbadah::create($j));

        // CATATAN: Reservasi & kehadiran TIDAK di-seed sebagai data dummy.
        // Keduanya harus terisi hanya melalui aksi nyata (reservasi jemaat &
        // check-in/absen pada hari-H), supaya daftar kehadiran tidak "sudah
        // terisi" secara otomatis.

        PermohonanDoa::create([
            'user_id' => $jemaatUsers[0]->id,
            'pokok_doa' => 'Mohan doa untuk kesembuhan ibu saya yang sedang dirawat di rumah sakit.',
            'is_anonymous' => false,
            'status' => 'pending',
        ]);

        PermohonanDoa::create([
            'user_id' => $jemaatUsers[1]->id,
            'pokok_doa' => 'Ucapan syukur atas pekerjaan baru yang Tuhan berikan.',
            'is_anonymous' => true,
            'status' => 'pending',
        ]);

        $kontens = [
            [
                'kategori' => 'Renungan Harian',
                'judul' => 'Tuhan Adalah Gembala ku, Tidak Kekurangan Aku',
                'isi' => "Dalam kehidupan yang penuh tantangan, Mazmur 23 mengingatkan kita bahwa Tuhan memimpin jalan kita ke tempat yang hijau dan memulihkan jiwa kita.\n\nKetika kita merasa letih dan berbeban berat, gembala yang baik tidak pernah meninggalkan domba-domba-Nya. Pdt. Ficky Benyaminsz mengajak jemaat GBI Pondok Daud untuk percaya bahwa di setiap musim kehidupan, Tuhan hadir sebagai gembala yang setia.\n\n\"Tuhan adalah gembalaku, tidak kekurangan aku.\" — Mazmur 23:1",
            ],
            [
                'kategori' => 'Berita Kegiatan',
                'judul' => 'Konferensi Pemuda GBI Pondok Daud 2026 Resmi Dibuka',
                'isi' => "GBI Pondok Daud dengan sukacita mengumumkan pembukaan Konferensi Pemuda 2026. Acara ini akan dipimpin oleh Pdt. Samsu Hadi dan dihadiri ratusan pemuda dari seluruh jemaat.\n\nPendaftaran dibuka melalui portal GBI Pondok Daud. Jangan lewatkan sesi worship dan seminar rohani yang telah disiapkan.",
            ],
            [
                'kategori' => 'Renungan Harian',
                'judul' => 'Kasih yang Mengampuni',
                'isi' => "Pdt. Lisna Tappi berbagi renungan tentang kasih karunia yang mengampuni. Tuhan tidak menghitung kesalahan kita, melainkan memanggil kita untuk hidup dalam kebebasan dan sukacita.\n\nMari jadikan setiap hari sebagai kesempatan untuk mengampuni dan menerima ampunan-Nya.",
            ],
            [
                'kategori' => 'Pengumuman',
                'judul' => 'Perubahan Jam Ibadah Doa Rabu',
                'isi' => "Diberitahukan kepada seluruh jemaat GBI Pondok Daud bahwa mulai minggu depan, ibadah doa syafaat hari Rabu dimulai pukul 19:00 WIB di Ruang Doa.\n\nTim pendoa siap melayani permohonan doa jemaat.",
            ],
        ];

        foreach ($kontens as $i => $k) {
            KontenWarta::create([
                'user_id' => $pengurus->id,
                'kategori' => $k['kategori'],
                'judul' => $k['judul'],
                'slug' => Str::slug($k['judul']).'-'.Str::random(4),
                'isi' => $k['isi'],
                'published_at' => now()->subDays($i),
            ]);
        }

        $this->call(ExtraDemoSeeder::class);
        $this->call(HistoriDemoSeeder::class);
    }

    private function mockEmbedding(): array
    {
        $embedding = [];
        for ($i = 0; $i < 128; $i++) {
            $embedding[] = round(mt_rand(-1000, 1000) / 1000, 6);
        }

        return $embedding;
    }
}
