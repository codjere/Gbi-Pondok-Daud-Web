<?php

namespace Database\Seeders;

use App\Models\JadwalIbadah;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Mengisi jadwal ibadah rutin dari hari ini sampai akhir Desember 2026.
 *
 * Tujuan: menyediakan data jadwal yang cukup banyak untuk keperluan
 * pengujian / pengumpulan data (responden bisa melihat & melakukan
 * reservasi pada banyak sesi ke depan).
 *
 * Aman dijalankan berulang: memakai firstOrCreate berbasis
 * (nama_sesi, tanggal) sehingga tidak menduplikasi data.
 *
 * Reservasi & kehadiran TIDAK diisi di sini — mengikuti konvensi
 * proyek, keduanya hanya terisi lewat aksi nyata pada hari-H.
 */
class JadwalSampaiDesemberSeeder extends Seeder
{
    public function run(): void
    {
        $pendeta = [
            'Pdt. Ficky Benyaminsz',
            'Pdt. Lisna Tappi',
            'Pdt. Samsu Hadi',
        ];

        // Template jadwal mingguan: dayOfWeek (0=Minggu ... 6=Sabtu).
        $templates = [
            // Minggu — tiga sesi ibadah raya
            ['dow' => 0, 'nama_sesi' => 'Ibadah Raya 1', 'jam_mulai' => '08:00', 'jam_selesai' => '10:00', 'pembicara' => $pendeta[0], 'lokasi' => 'Ruang Utama GBI Pondok Daud', 'batas_kuota' => 200],
            ['dow' => 0, 'nama_sesi' => 'Ibadah Raya 2', 'jam_mulai' => '17:00', 'jam_selesai' => '19:00', 'pembicara' => $pendeta[1], 'lokasi' => 'Ruang Utama GBI Pondok Daud', 'batas_kuota' => 200],
            ['dow' => 0, 'nama_sesi' => 'Ibadah Malam', 'jam_mulai' => '19:30', 'jam_selesai' => '21:00', 'pembicara' => $pendeta[2], 'lokasi' => 'Ruang Utama GBI Pondok Daud', 'batas_kuota' => 150],
            // Selasa — persekutuan doa pagi
            ['dow' => 2, 'nama_sesi' => 'Persekutuan Doa Pagi', 'jam_mulai' => '05:30', 'jam_selesai' => '06:30', 'pembicara' => 'Tim Pendoa GBI Pondok Daud', 'lokasi' => 'Ruang Doa', 'batas_kuota' => 40],
            // Rabu — doa syafaat
            ['dow' => 3, 'nama_sesi' => 'Doa Syafaat', 'jam_mulai' => '19:00', 'jam_selesai' => '20:30', 'pembicara' => 'Tim Pendoa GBI Pondok Daud', 'lokasi' => 'Ruang Doa', 'batas_kuota' => 50],
            // Kamis — ibadah kaum wanita
            ['dow' => 4, 'nama_sesi' => 'Ibadah Kaum Wanita', 'jam_mulai' => '10:00', 'jam_selesai' => '12:00', 'pembicara' => $pendeta[1], 'lokasi' => 'Ruang Utama GBI Pondok Daud', 'batas_kuota' => 60],
            // Sabtu — ibadah pemuda
            ['dow' => 6, 'nama_sesi' => 'Ibadah Pemuda', 'jam_mulai' => '18:00', 'jam_selesai' => '20:00', 'pembicara' => $pendeta[2], 'lokasi' => 'Aula Pemuda GBI Pondok Daud', 'batas_kuota' => 80],
        ];

        $start = CarbonImmutable::today();
        $end = CarbonImmutable::create($start->year, 12, 31);

        // Jika hari ini sudah lewat Desember tahun berjalan, amankan ke
        // 31 Desember tahun berikutnya agar rentang tetap valid.
        if ($end->lessThan($start)) {
            $end = $end->addYear();
        }

        $dibuat = 0;
        $dilewati = 0;

        for ($tanggal = $start; $tanggal->lessThanOrEqualTo($end); $tanggal = $tanggal->addDay()) {
            foreach ($templates as $t) {
                if ($tanggal->dayOfWeek !== $t['dow']) {
                    continue;
                }

                $jadwal = JadwalIbadah::firstOrCreate(
                    [
                        'nama_sesi' => $t['nama_sesi'],
                        'tanggal' => $tanggal->toDateString(),
                    ],
                    [
                        'jam_mulai' => $t['jam_mulai'],
                        'jam_selesai' => $t['jam_selesai'],
                        'pembicara' => $t['pembicara'],
                        'lokasi' => $t['lokasi'],
                        'batas_kuota' => $t['batas_kuota'],
                        'status' => 'terpublikasi',
                    ]
                );

                $jadwal->wasRecentlyCreated ? $dibuat++ : $dilewati++;
            }
        }

        $this->command?->info("JadwalSampaiDesemberSeeder: {$dibuat} jadwal dibuat, {$dilewati} dilewati (sudah ada).");
    }
}
