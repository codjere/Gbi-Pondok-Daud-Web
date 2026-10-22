<?php

namespace Database\Seeders;

use App\Models\KontenWarta;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Menambah data dummy JEMAAT (beserta profil kuesioner) dan WARTA
 * untuk keperluan pengujian / pengumpulan data.
 *
 * Aman dijalankan berulang: memakai firstOrCreate / updateOrCreate
 * berbasis email atau judul sehingga tidak menduplikasi data.
 *
 * Catatan: face_embedding diisi mock (vektor acak 128 dimensi) hanya
 * agar data konsisten; ini bukan wajah asli dan tidak dipakai untuk
 * pencocokan nyata.
 */
class DummyWartaJemaatSeeder extends Seeder
{
    public function run(): void
    {
        $pengurus = User::where('role', 'pengurus')->first();
        if (! $pengurus) {
            $this->command?->warn('DummyWartaJemaatSeeder: user pengurus tidak ditemukan, lewati warta.');
        }

        // ─── 1. Jemaat baru + profil kuesioner lengkap ───
        $jemaatBaru = [
            ['name' => 'Yohana Pardede', 'email' => 'yohana@email.com', 'usia' => 26, 'jk' => 'P', 'kec' => 'Kambu', 'minat' => ['musik', 'anak_muda'], 'bakat' => ['vokal', 'keyboard'], 'hari' => ['sabtu', 'minggu'], 'waktu' => 'sore'],
            ['name' => 'Markus Tarigan', 'email' => 'markus@email.com', 'usia' => 31, 'jk' => 'L', 'kec' => 'Poasia', 'minat' => ['teknologi', 'multimedia'], 'bakat' => ['komputer', 'video'], 'hari' => ['minggu'], 'waktu' => 'pagi'],
            ['name' => 'Priska Manurung', 'email' => 'priska@email.com', 'usia' => 23, 'jk' => 'P', 'kec' => 'Mandonga', 'minat' => ['kreatif', 'sosial'], 'bakat' => ['desain', 'fotografi'], 'hari' => ['jumat', 'sabtu'], 'waktu' => 'malam'],
            ['name' => 'Yakobus Sinaga', 'email' => 'yakobus@email.com', 'usia' => 40, 'jk' => 'L', 'kec' => 'Baruga', 'minat' => ['doa', 'melayani'], 'bakat' => ['public_speaking', 'organisasi'], 'hari' => ['rabu', 'minggu'], 'waktu' => 'fleksibel'],
            ['name' => 'Hana Situngkir', 'email' => 'hana@email.com', 'usia' => 20, 'jk' => 'P', 'kec' => 'Wua-Wua', 'minat' => ['anak_muda', 'evangelisme'], 'bakat' => ['vokal', 'public_speaking'], 'hari' => ['sabtu', 'minggu'], 'waktu' => 'sore'],
            ['name' => 'Benyamin Hutasoit', 'email' => 'benyamin@email.com', 'usia' => 35, 'jk' => 'L', 'kec' => 'Kadia', 'minat' => ['musik', 'melayani'], 'bakat' => ['gitar', 'drum'], 'hari' => ['minggu'], 'waktu' => 'pagi'],
            ['name' => 'Sarah Panjaitan', 'email' => 'sarah@email.com', 'usia' => 29, 'jk' => 'P', 'kec' => 'Puuwatu', 'minat' => ['administrasi', 'sosial'], 'bakat' => ['organisasi', 'komputer'], 'hari' => ['selasa', 'kamis'], 'waktu' => 'pagi'],
            ['name' => 'Elia Simbolon', 'email' => 'elia@email.com', 'usia' => 24, 'jk' => 'L', 'kec' => 'Abeli', 'minat' => ['multimedia', 'kreatif'], 'bakat' => ['fotografi', 'desain'], 'hari' => ['sabtu', 'minggu'], 'waktu' => 'fleksibel'],
        ];

        $dibuatJemaat = 0;
        foreach ($jemaatBaru as $j) {
            $user = User::firstOrCreate(
                ['email' => $j['email']],
                [
                    'name' => $j['name'],
                    'password' => 'password123',
                    'role' => 'jemaat',
                    'has_biometric' => true,
                    'email_verified_at' => now(),
                    'face_embedding' => $this->mockEmbedding(),
                ]
            );
            if ($user->wasRecentlyCreated) {
                $dibuatJemaat++;
            }

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'usia' => $j['usia'],
                    'jenis_kelamin' => $j['jk'],
                    'kecamatan' => $j['kec'],
                    'alamat' => 'Kendari',
                    'minat' => $j['minat'],
                    'bakat' => $j['bakat'],
                    'ketersediaan_hari' => $j['hari'],
                    'ketersediaan_waktu' => $j['waktu'],
                    'completed_at' => now(),
                ]
            );
        }

        // ─── 2. Konten warta tambahan ───
        $wartas = [
            [
                'kategori' => 'Renungan Harian',
                'judul' => 'Damai Sejahtera yang Melampaui Akal',
                'isi' => "Filipi 4:7 berbicara tentang damai sejahtera Allah yang melampaui segala akal, yang memelihara hati dan pikiran kita.\n\nDi tengah kesibukan dan kekhawatiran, firman ini mengingatkan jemaat GBI Pondok Daud untuk menyerahkan setiap beban kepada Tuhan melalui doa dan ucapan syukur.",
            ],
            [
                'kategori' => 'Pengumuman',
                'judul' => 'Jadwal Latihan Paduan Suara Natal 2026',
                'isi' => "Tim musik GBI Pondok Daud mengundang jemaat yang terbeban melayani dalam Paduan Suara Natal 2026.\n\nLatihan perdana dimulai awal November setiap Sabtu sore di Ruang Utama. Pendaftaran dibuka melalui pemimpin tim pujian.",
            ],
            [
                'kategori' => 'Berita Kegiatan',
                'judul' => 'Retret Keluarga GBI Pondok Daud Sukses Digelar',
                'isi' => "Puji Tuhan, retret keluarga tahun ini berlangsung penuh sukacita. Puluhan keluarga mengikuti sesi pembinaan rumah tangga yang dibawakan Pdt. Ficky Benyaminsz.\n\nTerima kasih untuk setiap panitia dan jemaat yang telah ambil bagian.",
            ],
            [
                'kategori' => 'Renungan Harian',
                'judul' => 'Setia dalam Perkara Kecil',
                'isi' => "Lukas 16:10 mengajar kita bahwa siapa setia dalam perkara kecil, ia setia juga dalam perkara besar.\n\nPdt. Lisna Tappi mengajak jemaat untuk menghargai setiap tugas pelayanan, sekecil apa pun, sebagai bentuk kesetiaan kepada Tuhan.",
            ],
            [
                'kategori' => 'Pengumuman',
                'judul' => 'Persiapan Ibadah Akhir Tahun 31 Desember',
                'isi' => "Ibadah Akhir Tahun akan diselenggarakan pada 31 Desember 2026 di Ruang Utama GBI Pondok Daud.\n\nMari bersama-sama mengucap syukur atas kebaikan Tuhan sepanjang tahun. Detail jam ibadah akan diumumkan menyusul.",
            ],
        ];

        $dibuatWarta = 0;
        if ($pengurus) {
            foreach ($wartas as $i => $w) {
                $konten = KontenWarta::firstOrCreate(
                    ['judul' => $w['judul']],
                    [
                        'user_id' => $pengurus->id,
                        'kategori' => $w['kategori'],
                        'slug' => Str::slug($w['judul']).'-'.Str::random(5),
                        'isi' => $w['isi'],
                        'published_at' => now()->subDays($i),
                    ]
                );
                if ($konten->wasRecentlyCreated) {
                    $dibuatWarta++;
                }
            }
        }

        $this->command?->info("DummyWartaJemaatSeeder: {$dibuatJemaat} jemaat baru, {$dibuatWarta} warta baru dibuat.");
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
