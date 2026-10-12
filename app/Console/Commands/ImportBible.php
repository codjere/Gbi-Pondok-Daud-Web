<?php

namespace App\Console\Commands;

use App\Models\Ayat;
use App\Models\Kitab;
use App\Models\Pasal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportBible extends Command
{
    protected $signature = 'bible:import
        {--source= : URL file VPL (default: AYT SABDA)}
        {--file= : Path lokal file VPL (jika sudah diunduh)}';

    protected $description = 'Impor teks Alkitab lengkap (AYT - Alkitab Yang Terbuka, SABDA) ke tabel kitab/pasal/ayat';

    private const VPL_URL = 'https://raw.githubusercontent.com/sabdacode/ayt/main/vpl/ayt.txt';

    /**
     * Pemetaan kode USFM (3 huruf) -> [urutan, nama Indonesia, singkatan].
     * Mengikuti daftar resmi AYT (id_books.json).
     */
    private const BOOKS = [
        'GEN' => [1, 'Kejadian', 'Kej'],        'EXO' => [2, 'Keluaran', 'Kel'],
        'LEV' => [3, 'Imamat', 'Im'],           'NUM' => [4, 'Bilangan', 'Bil'],
        'DEU' => [5, 'Ulangan', 'Ul'],          'JOS' => [6, 'Yosua', 'Yos'],
        'JDG' => [7, 'Hakim-hakim', 'Hak'],     'RUT' => [8, 'Rut', 'Rut'],
        '1SA' => [9, '1 Samuel', '1Sam'],       '2SA' => [10, '2 Samuel', '2Sam'],
        '1KI' => [11, '1 Raja-raja', '1Raj'],   '2KI' => [12, '2 Raja-raja', '2Raj'],
        '1CH' => [13, '1 Tawarikh', '1Taw'],    '2CH' => [14, '2 Tawarikh', '2Taw'],
        'EZR' => [15, 'Ezra', 'Ezr'],           'NEH' => [16, 'Nehemia', 'Neh'],
        'EST' => [17, 'Ester', 'Est'],          'JOB' => [18, 'Ayub', 'Ayb'],
        'PSA' => [19, 'Mazmur', 'Mzm'],         'PRO' => [20, 'Amsal', 'Ams'],
        'ECC' => [21, 'Pengkhotbah', 'Pkh'],    'SOL' => [22, 'Kidung Agung', 'Kid'],
        'ISA' => [23, 'Yesaya', 'Yes'],         'JER' => [24, 'Yeremia', 'Yer'],
        'LAM' => [25, 'Ratapan', 'Rat'],        'EZE' => [26, 'Yehezkiel', 'Yeh'],
        'DAN' => [27, 'Daniel', 'Dan'],         'HOS' => [28, 'Hosea', 'Hos'],
        'JOE' => [29, 'Yoel', 'Yl'],            'AMO' => [30, 'Amos', 'Am'],
        'OBA' => [31, 'Obaja', 'Ob'],           'JON' => [32, 'Yunus', 'Yun'],
        'MIC' => [33, 'Mikha', 'Mi'],           'NAH' => [34, 'Nahum', 'Nah'],
        'HAB' => [35, 'Habakuk', 'Hab'],        'ZEP' => [36, 'Zefanya', 'Zef'],
        'HAG' => [37, 'Hagai', 'Hag'],          'ZEC' => [38, 'Zakharia', 'Za'],
        'MAL' => [39, 'Maleakhi', 'Mal'],       'MAT' => [40, 'Matius', 'Mat'],
        'MAR' => [41, 'Markus', 'Mrk'],         'LUK' => [42, 'Lukas', 'Luk'],
        'JOH' => [43, 'Yohanes', 'Yoh'],        'ACT' => [44, 'Kisah Para Rasul', 'Kis'],
        'ROM' => [45, 'Roma', 'Rm'],            '1CO' => [46, '1 Korintus', '1Kor'],
        '2CO' => [47, '2 Korintus', '2Kor'],    'GAL' => [48, 'Galatia', 'Gal'],
        'EPH' => [49, 'Efesus', 'Ef'],          'PHI' => [50, 'Filipi', 'Flp'],
        'COL' => [51, 'Kolose', 'Kol'],         '1TH' => [52, '1 Tesalonika', '1Tes'],
        '2TH' => [53, '2 Tesalonika', '2Tes'],  '1TI' => [54, '1 Timotius', '1Tim'],
        '2TI' => [55, '2 Timotius', '2Tim'],    'TIT' => [56, 'Titus', 'Tit'],
        'PHM' => [57, 'Filemon', 'Flm'],        'HEB' => [58, 'Ibrani', 'Ibr'],
        'JAM' => [59, 'Yakobus', 'Yak'],        '1PE' => [60, '1 Petrus', '1Ptr'],
        '2PE' => [61, '2 Petrus', '2Ptr'],      '1JO' => [62, '1 Yohanes', '1Yoh'],
        '2JO' => [63, '2 Yohanes', '2Yoh'],     '3JO' => [64, '3 Yohanes', '3Yoh'],
        'JUD' => [65, 'Yudas', 'Yud'],          'REV' => [66, 'Wahyu', 'Why'],
    ];

    public function handle(): int
    {
        // 1) Ambil data VPL (satu ayat per baris: "GEN 1:1 teks")
        $raw = $this->loadSource();
        if ($raw === null) {
            $this->error('Gagal memuat data Alkitab.');
            return self::FAILURE;
        }

        $this->info('Mem-parsing teks Alkitab...');

        // 2) Parse menjadi struktur: [USFM => [pasal => [ayat => teks]]]
        $data = [];
        $totalAyat = 0;
        foreach (preg_split('/\r\n|\n|\r/', $raw) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            // Format: KODE PASAL:AYAT teks....
            if (! preg_match('/^([A-Z0-9]{3})\s+(\d+):(\d+)\s+(.*)$/u', $line, $m)) {
                continue;
            }
            [, $code, $chapter, $verse, $text] = $m;
            if (! isset(self::BOOKS[$code])) {
                continue; // lewati kode yang tidak dikenal (mis. judul/heading)
            }
            $data[$code][(int) $chapter][(int) $verse] = trim($text);
            $totalAyat++;
        }

        if ($totalAyat === 0) {
            $this->error('Tidak ada ayat yang berhasil diparse. Format sumber mungkin berubah.');
            return self::FAILURE;
        }

        $this->info("Berhasil parse {$totalAyat} ayat dari ".count($data).' kitab.');

        // 3) Simpan ke database (per kitab) dalam transaksi, mempertahankan kitab_id
        //    yang sudah ada (dicocokkan via "urutan") agar reading plan tidak rusak.
        $bar = $this->output->createProgressBar(count(self::BOOKS));
        $bar->start();

        foreach (self::BOOKS as $code => [$urutan, $nama, $singkatan]) {
            $chapters = $data[$code] ?? null;
            if (! $chapters) {
                $bar->advance();
                continue;
            }
            ksort($chapters);
            $jumlahPasal = count($chapters);
            $testament = $urutan <= 39 ? 'PL' : 'PB';

            DB::transaction(function () use ($urutan, $nama, $singkatan, $jumlahPasal, $testament, $chapters) {
                // Cocokkan kitab berdasarkan urutan -> pertahankan id (reading plan aman)
                $kitab = Kitab::updateOrCreate(
                    ['urutan' => $urutan],
                    [
                        'nama' => $nama,
                        'singkatan' => $singkatan,
                        'jumlah_pasal' => $jumlahPasal,
                        'testament' => $testament,
                    ]
                );

                // Hapus pasal lama kitab ini (cascade -> hapus ayat), lalu isi ulang
                Pasal::where('kitab_id', $kitab->id)->delete();

                foreach ($chapters as $nomorPasal => $verses) {
                    ksort($verses);
                    $pasal = Pasal::create([
                        'kitab_id' => $kitab->id,
                        'nomor_pasal' => $nomorPasal,
                        'jumlah_ayat' => count($verses),
                    ]);

                    $rows = [];
                    $now = now();
                    foreach ($verses as $nomorAyat => $teks) {
                        $rows[] = [
                            'pasal_id' => $pasal->id,
                            'nomor_ayat' => $nomorAyat,
                            'isi_ayat' => $teks,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    // Insert massal per pasal (lebih cepat daripada per ayat)
                    foreach (array_chunk($rows, 200) as $chunk) {
                        Ayat::insert($chunk);
                    }
                }
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Impor selesai.');
        $this->table(
            ['Kitab', 'Pasal', 'Ayat'],
            [[Kitab::count(), Pasal::count(), Ayat::count()]]
        );

        return self::SUCCESS;
    }

    private function loadSource(): ?string
    {
        $file = $this->option('file');
        if ($file) {
            if (! is_file($file)) {
                $this->error("File tidak ditemukan: {$file}");
                return null;
            }
            $this->info("Membaca dari file lokal: {$file}");
            return file_get_contents($file) ?: null;
        }

        $url = $this->option('source') ?: self::VPL_URL;
        $this->info("Mengunduh data Alkitab (AYT) dari: {$url}");

        try {
            $res = Http::timeout(120)->get($url);
            if (! $res->successful()) {
                $this->error('Unduhan gagal, HTTP '.$res->status());
                return null;
            }
            return $res->body();
        } catch (\Throwable $e) {
            $this->error('Gagal mengunduh: '.$e->getMessage());
            return null;
        }
    }
}
