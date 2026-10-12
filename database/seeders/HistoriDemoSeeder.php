<?php

namespace Database\Seeders;

use App\Models\JadwalIbadah;
use App\Models\Kehadiran;
use App\Models\Reservasi;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

/**
 * Data historis untuk menghidupkan laporan (grafik tren + breakdown metode
 * verifikasi). Aturan penting:
 *  - Kehadiran HANYA di-seed untuk ibadah yang tanggalnya SUDAH LEWAT
 *    (status "selesai"). Ibadah mendatang dibiarkan kosong kehadirannya.
 *  - Ibadah mendatang hanya diisi RESERVASI (wajar: jemaat booking tempat).
 */
class HistoriDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedExtraJemaat();

        $jemaat = User::where('role', 'jemaat')->get();
        if ($jemaat->isEmpty()) {
            return;
        }

        $this->seedRiwayatIbadah($jemaat);
        $this->seedReservasiMendatang($jemaat);
    }

    /**
     * Tambah beberapa jemaat lagi + profil lengkap, supaya dashboard &
     * rekomendasi lebih berisi.
     */
    private function seedExtraJemaat(): void
    {
        $data = [
            ['name' => 'Hana Lumban', 'email' => 'hana@email.com', 'usia' => 26, 'jk' => 'P', 'kec' => 'Kambu', 'minat' => ['musik', 'anak_muda'], 'bakat' => ['keyboard', 'vokal'], 'hari' => ['sabtu', 'minggu'], 'waktu' => 'sore'],
            ['name' => 'Markus Tobing', 'email' => 'markus@email.com', 'usia' => 31, 'jk' => 'L', 'kec' => 'Poasia', 'minat' => ['teknologi', 'multimedia'], 'bakat' => ['komputer', 'video'], 'hari' => ['minggu'], 'waktu' => 'pagi'],
            ['name' => 'Ribka Sihombing', 'email' => 'ribka@email.com', 'usia' => 24, 'jk' => 'P', 'kec' => 'Wua-Wua', 'minat' => ['sosial', 'melayani'], 'bakat' => ['organisasi', 'public_speaking'], 'hari' => ['sabtu'], 'waktu' => 'fleksibel'],
            ['name' => 'Yohanes Panjaitan', 'email' => 'yohanes@email.com', 'usia' => 38, 'jk' => 'L', 'kec' => 'Mandonga', 'minat' => ['doa', 'evangelisme'], 'bakat' => ['public_speaking'], 'hari' => ['rabu', 'minggu'], 'waktu' => 'malam'],
            ['name' => 'Sarah Manullang', 'email' => 'sarah@email.com', 'usia' => 29, 'jk' => 'P', 'kec' => 'Baruga', 'minat' => ['kreatif', 'multimedia'], 'bakat' => ['desain', 'fotografi'], 'hari' => ['sabtu', 'minggu'], 'waktu' => 'sore'],
            ['name' => 'Petrus Gultom', 'email' => 'petrus@email.com', 'usia' => 42, 'jk' => 'L', 'kec' => 'Puuwatu', 'minat' => ['administrasi', 'melayani'], 'bakat' => ['organisasi', 'komputer'], 'hari' => ['minggu'], 'waktu' => 'pagi'],
        ];

        foreach ($data as $d) {
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name' => $d['name'],
                    'password' => 'password123',
                    'role' => 'jemaat',
                    'has_biometric' => true,
                    'email_verified_at' => now(),
                    'face_embedding' => $this->mockEmbedding(),
                ]
            );

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'usia' => $d['usia'],
                    'jenis_kelamin' => $d['jk'],
                    'kecamatan' => $d['kec'],
                    'alamat' => 'Kendari',
                    'minat' => $d['minat'],
                    'bakat' => $d['bakat'],
                    'ketersediaan_hari' => $d['hari'],
                    'ketersediaan_waktu' => $d['waktu'],
                    'completed_at' => now(),
                ]
            );
        }
    }

    /**
     * Buat 6 ibadah raya mingguan yang SUDAH LEWAT (status selesai) dan isi
     * kehadirannya. Jumlah hadir dibuat bervariasi agar grafik tren menarik,
     * dengan mayoritas verifikasi via facenet (sesuai fokus sistem).
     */
    private function seedRiwayatIbadah($jemaat): void
    {
        // Jumlah hadir per minggu (6 minggu lalu -> minggu lalu).
        $polaHadir = [9, 11, 8, 12, 10, 13];

        foreach ($polaHadir as $i => $target) {
            $mingguLalu = 6 - $i; // 6,5,4,3,2,1 minggu lalu
            $tanggal = now()->subWeeks($mingguLalu)->startOfWeek()->next('Sunday');

            $jadwal = JadwalIbadah::firstOrCreate(
                ['nama_sesi' => 'Ibadah Raya Minggu', 'tanggal' => $tanggal->toDateString()],
                [
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '10:00',
                    'pembicara' => 'Pdt. Samsu Hadi',
                    'lokasi' => 'Ruang Utama GBI Pondok Daud',
                    'batas_kuota' => 200,
                    'status' => 'selesai',
                ]
            );

            // Pilih sejumlah jemaat untuk hadir di ibadah ini.
            $hadir = $jemaat->shuffle()->take(min($target, $jemaat->count()));

            foreach ($hadir as $idx => $user) {
                // ~75% facenet, sisanya manual.
                $metode = ($idx % 4 === 0) ? 'manual' : 'facenet';

                Kehadiran::firstOrCreate(
                    ['user_id' => $user->id, 'jadwal_ibadah_id' => $jadwal->id],
                    [
                        'waktu_checkin' => $tanggal->copy()->setTime(7, 30)->addMinutes($idx * 2),
                        'metode_verifikasi' => $metode,
                    ]
                );
            }
        }
    }

    /**
     * Isi reservasi untuk ibadah MENDATANG yang sudah terpublikasi.
     * Kehadiran tetap kosong (belum hari-H).
     */
    private function seedReservasiMendatang($jemaat): void
    {
        $mendatang = JadwalIbadah::where('status', 'terpublikasi')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->get();

        foreach ($mendatang as $jadwal) {
            // Isi reservasi sampai ~60% kuota, maksimal sejumlah jemaat.
            $target = (int) min($jemaat->count(), max(2, round($jadwal->batas_kuota * 0.6)));
            $peserta = $jemaat->shuffle()->take($target);

            foreach ($peserta as $user) {
                Reservasi::firstOrCreate(
                    ['user_id' => $user->id, 'jadwal_ibadah_id' => $jadwal->id],
                    ['status' => 'confirmed']
                );
            }
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
