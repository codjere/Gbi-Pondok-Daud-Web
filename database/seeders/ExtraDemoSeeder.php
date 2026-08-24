<?php

namespace Database\Seeders;

use App\Models\CoolGroup;
use App\Models\FaqItem;
use App\Models\JadwalIbadah;
use App\Models\Kehadiran;
use App\Models\KontenWarta;
use App\Models\PermohonanDoa;
use App\Models\Reservasi;
use App\Models\TimPelayanan;
use App\Models\User;
use App\Models\UserCoolChoice;
use App\Models\UserPelayananChoice;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExtraDemoSeeder extends Seeder
{
    public function run(): void
    {
        $pengurus = User::where('role', 'pengurus')->first();
        if (! $pengurus) {
            return;
        }

        foreach ([
            ['name' => 'Yosef Nababan', 'email' => 'yosef@email.com'],
            ['name' => 'Esther Lumbantobing', 'email' => 'esther@email.com'],
            ['name' => 'Paulus Situmorang', 'email' => 'paulus@email.com'],
            ['name' => 'Lydia Ginting', 'email' => 'lydia@email.com'],
            ['name' => 'Timothy Siahaan', 'email' => 'timothy@email.com'],
        ] as $j) {
            User::firstOrCreate(
                ['email' => $j['email']],
                [
                    'name' => $j['name'],
                    'password' => 'password123',
                    'role' => 'jemaat',
                    'has_biometric' => true,
                    'face_embedding' => $this->mockEmbedding(),
                ]
            );
        }

        $pelayan = User::firstOrCreate(
            ['email' => 'pelayan@gbipondokdaud.id'],
            [
                'name' => 'Pelayan Multimedia',
                'password' => 'password123',
                'role' => 'pelayan',
                'has_biometric' => true,
                'face_embedding' => $this->mockEmbedding(),
            ]
        );

        $pendeta = ['Pdt. Samsu Hadi', 'Pdt. Lisna Tappi', 'Pdt. Ficky Benyaminsz'];

        $extraJadwals = [
            [
                'nama_sesi' => 'Ibadah Pemuda',
                'tanggal' => now()->next('Saturday')->toDateString(),
                'jam_mulai' => '18:00',
                'jam_selesai' => '20:00',
                'pembicara' => $pendeta[2],
                'lokasi' => 'Aula Pemuda GBI Pondok Daud',
                'batas_kuota' => 80,
                'status' => 'terpublikasi',
            ],
            [
                'nama_sesi' => 'Ibadah Kaum Wanita',
                'tanggal' => now()->next('Thursday')->toDateString(),
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'pembicara' => $pendeta[1],
                'lokasi' => 'Ruang Utama GBI Pondok Daud',
                'batas_kuota' => 60,
                'status' => 'terpublikasi',
            ],
            [
                'nama_sesi' => 'Persekutuan Doa Pagi',
                'tanggal' => now()->next('Tuesday')->toDateString(),
                'jam_mulai' => '05:30',
                'jam_selesai' => '06:30',
                'pembicara' => 'Tim Pendoa GBI Pondok Daud',
                'lokasi' => 'Ruang Doa',
                'batas_kuota' => 40,
                'status' => 'terpublikasi',
            ],
        ];

        $jadwalModels = collect($extraJadwals)->map(
            fn (array $j) => JadwalIbadah::firstOrCreate(
                ['nama_sesi' => $j['nama_sesi'], 'tanggal' => $j['tanggal']],
                $j
            )
        );

        $allJemaat = User::where('role', 'jemaat')->get();
        $ibadahPemuda = $jadwalModels[0];

        foreach ($allJemaat->take(6) as $user) {
            Reservasi::firstOrCreate([
                'user_id' => $user->id,
                'jadwal_ibadah_id' => $ibadahPemuda->id,
            ], ['status' => 'confirmed']);
        }

        $doaSamples = [
            ['pokok_doa' => 'Doakan keluarga kami yang sedang menghadapi ujian ekonomi, agar Tuhan membuka jalan pekerjaan.', 'is_anonymous' => false, 'status' => 'pending'],
            ['pokok_doa' => 'Mohon doa untuk pelayanan misi ke daerah pedalaman bulan depan.', 'is_anonymous' => false, 'status' => 'didoakan'],
            ['pokok_doa' => 'Ucapan syukur atas kesembuhan anak kami. Kiranya iman kami terus dikuatkan.', 'is_anonymous' => true, 'status' => 'pending'],
            ['pokok_doa' => 'Doakan persiapan pernikahan kami di GBI Pondok Daud bulan Oktober.', 'is_anonymous' => false, 'status' => 'pending'],
        ];

        if ($allJemaat->isNotEmpty()) {
            foreach ($doaSamples as $i => $doa) {
                $user = $allJemaat[$i % $allJemaat->count()];
                PermohonanDoa::firstOrCreate(
                    ['user_id' => $user->id, 'pokok_doa' => $doa['pokok_doa']],
                    $doa
                );
            }
        }

        foreach ([
            [
                'kategori' => 'Pengumuman',
                'judul' => 'Pendaftaran Kelas KRK Gelombang 2 Dibuka',
                'isi' => "Sekretariat GBI Pondok Daud membuka pendaftaran Kelas Regenerasi Kejadian (KRK) gelombang 2. Kelas ini merupakan syarat pembaptisan selam.\n\nPendaftaran melalui portal atau datang langsung ke sekretariat setiap hari kerja pukul 09.00–16.00 WIB.",
            ],
            [
                'kategori' => 'Berita Kegiatan',
                'judul' => 'Bakti Sosial COOL se-Medan di Panti Asuhan',
                'isi' => "Seluruh COOL GBI Pondok Daud merencanakan bakti sosial bersama di panti asuhan. Kegiatan meliputi pembagian sembako, permainan anak, dan ibadah singkat.\n\nInformasi lebih lanjut akan diumumkan pemimpin COOL masing-masing.",
            ],
            [
                'kategori' => 'Renungan Harian',
                'judul' => 'Berdiri Teguh di Tengah Badai',
                'isi' => "Pdt. Ficky Benyaminsz mengajak jemaat merenungkan Matius 7:24-25. Rumah yang dibangun di atas batu tidak rubuh ketika hujan dan angin datang.\n\nMarilah kita membangun iman di atas firman, bukan perasaan semata.",
            ],
        ] as $i => $k) {
            KontenWarta::firstOrCreate(
                ['judul' => $k['judul']],
                [
                    'user_id' => $pengurus->id,
                    'kategori' => $k['kategori'],
                    'slug' => Str::slug($k['judul']).'-'.Str::random(4),
                    'isi' => $k['isi'],
                    'published_at' => now()->subHours($i + 2),
                ]
            );
        }

        foreach ([
            [
                'kategori' => 'jadwal',
                'pertanyaan' => 'Di mana alamat GBI Pondok Daud?',
                'jawaban' => 'GBI Pondok Daud melayani jemaat di Medan. Untuk alamat lengkap dan denah parkir, silakan hubungi sekretariat gereja atau cek pengumuman terbaru di menu Warta.',
                'keywords' => ['alamat', 'lokasi', 'gereja', 'dimana'],
            ],
            [
                'kategori' => 'doa',
                'pertanyaan' => 'Apakah permohonan doa bisa dikirim tanpa nama?',
                'jawaban' => 'Ya. Saat mengisi formulir Permohonan Doa, pilih opsi anonim. Tim pendoa tetap mendoakan tanpa menampilkan identitas Anda di catatan publik.',
                'keywords' => ['anonim', 'rahasia', 'nama', 'doa'],
            ],
        ] as $f) {
            FaqItem::updateOrCreate(['pertanyaan' => $f['pertanyaan']], $f);
        }

        foreach ([
            ['email' => 'andi@email.com', 'usia' => 28, 'jenis_kelamin' => 'L', 'kecamatan' => 'Medan Kota', 'minat' => ['musik', 'doa'], 'bakat' => ['gitar', 'vokal'], 'hari' => ['sabtu', 'minggu'], 'waktu' => 'malam'],
            ['email' => 'maria@email.com', 'usia' => 34, 'jenis_kelamin' => 'P', 'kecamatan' => 'Medan Area', 'minat' => ['melayani', 'sosial'], 'bakat' => ['organisasi', 'public_speaking'], 'hari' => ['minggu'], 'waktu' => 'pagi'],
            ['email' => 'esther@email.com', 'usia' => 22, 'jenis_kelamin' => 'P', 'kecamatan' => 'Medan Sunggal', 'minat' => ['anak_muda', 'musik', 'kreatif'], 'bakat' => ['vokal', 'desain'], 'hari' => ['sabtu', 'minggu'], 'waktu' => 'sore'],
            ['email' => 'timothy@email.com', 'usia' => 19, 'jenis_kelamin' => 'L', 'kecamatan' => 'Medan Timur', 'minat' => ['multimedia', 'teknologi'], 'bakat' => ['video', 'komputer'], 'hari' => ['sabtu', 'minggu'], 'waktu' => 'fleksibel'],
        ] as $p) {
            $user = User::where('email', $p['email'])->first();
            if (! $user) {
                continue;
            }

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'usia' => $p['usia'],
                    'jenis_kelamin' => $p['jenis_kelamin'],
                    'kecamatan' => $p['kecamatan'],
                    'alamat' => 'Medan',
                    'minat' => $p['minat'],
                    'bakat' => $p['bakat'],
                    'ketersediaan_hari' => $p['hari'],
                    'ketersediaan_waktu' => $p['waktu'],
                    'completed_at' => now(),
                ]
            );
        }

        $worship = TimPelayanan::where('slug', 'praise-worship')->first();
        $multimedia = TimPelayanan::where('slug', 'multimedia')->first();
        $coolSunggal = CoolGroup::where('kecamatan', 'Medan Sunggal')->first();

        $andi = User::where('email', 'andi@email.com')->first();
        if ($andi && $worship) {
            UserPelayananChoice::firstOrCreate(
                ['user_id' => $andi->id, 'tim_pelayanan_id' => $worship->id],
                ['score' => 0.91, 'source' => 'rekomendasi']
            );
        }

        $timothy = User::where('email', 'timothy@email.com')->first();
        if ($timothy && $multimedia) {
            UserPelayananChoice::firstOrCreate(
                ['user_id' => $timothy->id, 'tim_pelayanan_id' => $multimedia->id],
                ['score' => 0.88, 'source' => 'rekomendasi']
            );
        }

        $esther = User::where('email', 'esther@email.com')->first();
        if ($esther && $coolSunggal) {
            UserCoolChoice::firstOrCreate(
                ['user_id' => $esther->id, 'cool_group_id' => $coolSunggal->id],
                ['score' => 0.86, 'status' => 'tertarik']
            );
        }

        $pastJadwal = JadwalIbadah::where('status', 'selesai')->orderByDesc('tanggal')->first();
        if ($pastJadwal && $pelayan) {
            Kehadiran::firstOrCreate(
                ['user_id' => $pelayan->id, 'jadwal_ibadah_id' => $pastJadwal->id],
                [
                    'waktu_checkin' => $pastJadwal->tanggal->copy()->setTime(7, 40),
                    'metode_verifikasi' => 'facenet',
                ]
            );
        }
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
