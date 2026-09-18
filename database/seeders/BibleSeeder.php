<?php

namespace Database\Seeders;

use App\Models\Ayat;
use App\Models\Kitab;
use App\Models\Pasal;
use App\Models\ReadingPlan;
use App\Models\ReadingPlanItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BibleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Kitab (Books) - Sample beberapa kitab untuk demo
        $kitabs = [
            // Perjanjian Lama
            ['nama' => 'Kejadian', 'singkatan' => 'Kej', 'urutan' => 1, 'jumlah_pasal' => 50, 'testament' => 'PL'],
            ['nama' => 'Keluaran', 'singkatan' => 'Kel', 'urutan' => 2, 'jumlah_pasal' => 40, 'testament' => 'PL'],
            ['nama' => 'Imamat', 'singkatan' => 'Im', 'urutan' => 3, 'jumlah_pasal' => 27, 'testament' => 'PL'],
            ['nama' => 'Bilangan', 'singkatan' => 'Bil', 'urutan' => 4, 'jumlah_pasal' => 36, 'testament' => 'PL'],
            ['nama' => 'Ulangan', 'singkatan' => 'Ul', 'urutan' => 5, 'jumlah_pasal' => 34, 'testament' => 'PL'],
            ['nama' => 'Yosua', 'singkatan' => 'Yos', 'urutan' => 6, 'jumlah_pasal' => 24, 'testament' => 'PL'],
            ['nama' => 'Hakim-hakim', 'singkatan' => 'Hak', 'urutan' => 7, 'jumlah_pasal' => 21, 'testament' => 'PL'],
            ['nama' => 'Rut', 'singkatan' => 'Rut', 'urutan' => 8, 'jumlah_pasal' => 4, 'testament' => 'PL'],
            ['nama' => '1 Samuel', 'singkatan' => '1Sam', 'urutan' => 9, 'jumlah_pasal' => 31, 'testament' => 'PL'],
            ['nama' => '2 Samuel', 'singkatan' => '2Sam', 'urutan' => 10, 'jumlah_pasal' => 24, 'testament' => 'PL'],
            ['nama' => 'Mazmur', 'singkatan' => 'Mazm', 'urutan' => 19, 'jumlah_pasal' => 150, 'testament' => 'PL'],
            ['nama' => 'Amsal', 'singkatan' => 'Ams', 'urutan' => 20, 'jumlah_pasal' => 31, 'testament' => 'PL'],
            ['nama' => 'Yesaya', 'singkatan' => 'Yes', 'urutan' => 23, 'jumlah_pasal' => 66, 'testament' => 'PL'],
            ['nama' => 'Yeremia', 'singkatan' => 'Yer', 'urutan' => 24, 'jumlah_pasal' => 52, 'testament' => 'PL'],
            ['nama' => 'Daniel', 'singkatan' => 'Dan', 'urutan' => 27, 'jumlah_pasal' => 12, 'testament' => 'PL'],
            ['nama' => 'Hosea', 'singkatan' => 'Hos', 'urutan' => 28, 'jumlah_pasal' => 14, 'testament' => 'PL'],
            ['nama' => 'Yoel', 'singkatan' => 'Yoel', 'urutan' => 29, 'jumlah_pasal' => 3, 'testament' => 'PL'],
            ['nama' => 'Amos', 'singkatan' => 'Am', 'urutan' => 30, 'jumlah_pasal' => 9, 'testament' => 'PL'],
            ['nama' => 'Obaja', 'singkatan' => 'Ob', 'urutan' => 31, 'jumlah_pasal' => 1, 'testament' => 'PL'],
            ['nama' => 'Yona', 'singkatan' => 'Yon', 'urutan' => 32, 'jumlah_pasal' => 4, 'testament' => 'PL'],
            ['nama' => 'Mikha', 'singkatan' => 'Mik', 'urutan' => 33, 'jumlah_pasal' => 7, 'testament' => 'PL'],
            ['nama' => 'Nahum', 'singkatan' => 'Nah', 'urutan' => 34, 'jumlah_pasal' => 3, 'testament' => 'PL'],
            ['nama' => 'Habakuk', 'singkatan' => 'Hab', 'urutan' => 35, 'jumlah_pasal' => 3, 'testament' => 'PL'],
            ['nama' => 'Zefanya', 'singkatan' => 'Zef', 'urutan' => 36, 'jumlah_pasal' => 3, 'testament' => 'PL'],
            ['nama' => 'Haggai', 'singkatan' => 'Hag', 'urutan' => 37, 'jumlah_pasal' => 2, 'testament' => 'PL'],
            ['nama' => 'Zakharia', 'singkatan' => 'Zakh', 'urutan' => 38, 'jumlah_pasal' => 14, 'testament' => 'PL'],
            ['nama' => 'Maleakhi', 'singkatan' => 'Mal', 'urutan' => 39, 'jumlah_pasal' => 4, 'testament' => 'PL'],

            // Perjanjian Baru
            ['nama' => 'Matius', 'singkatan' => 'Mat', 'urutan' => 40, 'jumlah_pasal' => 28, 'testament' => 'PB'],
            ['nama' => 'Markus', 'singkatan' => 'Mrk', 'urutan' => 41, 'jumlah_pasal' => 16, 'testament' => 'PB'],
            ['nama' => 'Lukas', 'singkatan' => 'Luk', 'urutan' => 42, 'jumlah_pasal' => 24, 'testament' => 'PB'],
            ['nama' => 'Yohanes', 'singkatan' => 'Yoh', 'urutan' => 43, 'jumlah_pasal' => 21, 'testament' => 'PB'],
            ['nama' => 'Kisah Para Rasul', 'singkatan' => 'Kis', 'urutan' => 44, 'jumlah_pasal' => 28, 'testament' => 'PB'],
            ['nama' => 'Roma', 'singkatan' => 'Rom', 'urutan' => 45, 'jumlah_pasal' => 16, 'testament' => 'PB'],
            ['nama' => '1 Korintus', 'singkatan' => '1Kor', 'urutan' => 46, 'jumlah_pasal' => 16, 'testament' => 'PB'],
            ['nama' => '2 Korintus', 'singkatan' => '2Kor', 'urutan' => 47, 'jumlah_pasal' => 13, 'testament' => 'PB'],
            ['nama' => 'Galatia', 'singkatan' => 'Gal', 'urutan' => 48, 'jumlah_pasal' => 6, 'testament' => 'PB'],
            ['nama' => 'Efesus', 'singkatan' => 'Ef', 'urutan' => 49, 'jumlah_pasal' => 6, 'testament' => 'PB'],
            ['nama' => 'Filipi', 'singkatan' => 'Fil', 'urutan' => 50, 'jumlah_pasal' => 4, 'testament' => 'PB'],
            ['nama' => 'Kolose', 'singkatan' => 'Kol', 'urutan' => 51, 'jumlah_pasal' => 4, 'testament' => 'PB'],
            ['nama' => '1 Tesalonika', 'singkatan' => '1Tes', 'urutan' => 52, 'jumlah_pasal' => 5, 'testament' => 'PB'],
            ['nama' => '2 Tesalonika', 'singkatan' => '2Tes', 'urutan' => 53, 'jumlah_pasal' => 3, 'testament' => 'PB'],
            ['nama' => '1 Timotius', 'singkatan' => '1Tim', 'urutan' => 54, 'jumlah_pasal' => 6, 'testament' => 'PB'],
            ['nama' => '2 Timotius', 'singkatan' => '2Tim', 'urutan' => 55, 'jumlah_pasal' => 4, 'testament' => 'PB'],
            ['nama' => 'Titus', 'singkatan' => 'Tit', 'urutan' => 56, 'jumlah_pasal' => 3, 'testament' => 'PB'],
            ['nama' => 'Filemon', 'singkatan' => 'Flm', 'urutan' => 57, 'jumlah_pasal' => 1, 'testament' => 'PB'],
            ['nama' => 'Ibrani', 'singkatan' => 'Ibr', 'urutan' => 58, 'jumlah_pasal' => 13, 'testament' => 'PB'],
            ['nama' => 'Yakobus', 'singkatan' => 'Yak', 'urutan' => 59, 'jumlah_pasal' => 5, 'testament' => 'PB'],
            ['nama' => '1 Petrus', 'singkatan' => '1Pet', 'urutan' => 60, 'jumlah_pasal' => 5, 'testament' => 'PB'],
            ['nama' => '2 Petrus', 'singkatan' => '2Pet', 'urutan' => 61, 'jumlah_pasal' => 3, 'testament' => 'PB'],
            ['nama' => '1 Yohanes', 'singkatan' => '1Yoh', 'urutan' => 62, 'jumlah_pasal' => 5, 'testament' => 'PB'],
            ['nama' => '2 Yohanes', 'singkatan' => '2Yoh', 'urutan' => 63, 'jumlah_pasal' => 1, 'testament' => 'PB'],
            ['nama' => '3 Yohanes', 'singkatan' => '3Yoh', 'urutan' => 64, 'jumlah_pasal' => 1, 'testament' => 'PB'],
            ['nama' => 'Yudas', 'singkatan' => 'Yud', 'urutan' => 65, 'jumlah_pasal' => 1, 'testament' => 'PB'],
            ['nama' => 'Wahyu', 'singkatan' => 'Why', 'urutan' => 66, 'jumlah_pasal' => 22, 'testament' => 'PB'],
        ];

        foreach ($kitabs as $kitab) {
            Kitab::create($kitab);
        }

        $this->command->info('Kitab berhasil di-seed.');

        // 2. Seed Pasal dan Ayat - Sample untuk beberapa pasal pertama
        $this->seedSamplePasalDanAyat();

        // 3. Seed Reading Plans
        $this->seedReadingPlans();

        $this->command->info('Bible Seeder selesai!');
    }

    private function seedSamplePasalDanAyat()
    {
        // Sample data untuk Kejadian 1
        $kejadian = Kitab::where('singkatan', 'Kej')->first();
        $pasal1 = Pasal::create([
            'kitab_id' => $kejadian->id,
            'nomor_pasal' => 1,
            'jumlah_ayat' => 31,
        ]);

        $ayatKejadian1 = [
            "Pada mulanya Allah menciptakan langit dan bumi.",
            "Bumi belum berbentuk dan kosong; gelap gulita menutupi samudra raya, dan Roh Allah melayang-layang di atas permukaan air.",
            "Berfirmanlah Allah: 'Jadilah terang!' Lalu terang itu jadi.",
            "Allah melihat bahwa terang itu baik, lalu Allah memisahkan terang itu dari gelap.",
            "Dan Allah menamai terang itu siang, dan gelap itu malam. Jadilah petang dan jadilah pagi, itulah hari pertama.",
            "Berfirmanlah Allah: 'Jadilah suatu cakrawala di tengah segala air untuk memisahkan air dari air.'",
            "Maka Allah menjadikan cakrawala dan Ia memisahkan air yang ada di bawah cakrawala itu dari air yang ada di atasnya. Dan jadilah demikian.",
            "Lalu Allah menamai cakrawala itu langit. Jadilah petang dan jadilah pagi, itulah hari kedua.",
            "Berfirmanlah Allah: 'Berkumpullah air yang di bawah langit pada satu tempat, sehingga tampak yang kering.' Lalu jadilah demikian.",
            "Lalu Allah menamai yang kering itu bumi, dan kumpulan air itu Ia namai laut. Allah melihat bahwa yang demikian itu baik.",
            "Berfirmanlah Allah: 'Hijaulah bumi oleh tanaman-tanaman yang benih yang mengandung benihnya dan oleh pohon-pohonan yang buahnya mengandung benihnya menurut jenisnya.' Lalu jadilah demikian.",
            "Bumi mengeluarkan tanaman-tanaman dan pohon-pohonan yang mengandung benihnya menurut jenisnya. Allah melihat bahwa yang demikian itu baik.",
            "Jadilah petang dan jadilah pagi, itulah hari ketiga.",
            "Berfirmanlah Allah: 'Jadilah cahaya-cahaya di cakrawala untuk memisahkan siang dari malam, dan hendaklah cahaya-cahaya itu menjadi tanda untuk menentukan waktu, hari dan tahun.",
            "Dan cahaya-cahaya itu harus menjadi cahaya di cakrawala dan menerangi bumi.' Lalu jadilah demikian.",
            "Allah membuat dua cahaya besar: cahaya yang besar untuk menguasai siang dan cahaya yang kecil untuk menguasai malam, demikian juga bintang-bintang.",
            "Allah menaruh cahaya-cahaya itu di cakrawala untuk menerangi bumi",
            "dan untuk menguasai siang dan malam serta untuk memisahkan terang dari gelap. Allah melihat bahwa yang demikian itu baik.",
            "Jadilah petang dan jadilah pagi, itulah hari keempat.",
            "Berfirmanlah Allah: 'Hendaklah air memuakkan oleh segala jenis makhluk hidup yang bergerak, dan hendaklah terbang burung-burung di atas bumi di cakrawala langit.'",
            "Maka Allah menciptakan binatang-binatang laut yang besar dan segala jenis makhluk hidup yang bergerak, yang memuakkan air, dan segala jenis burung yang terbang. Allah melihat bahwa yang demikian itu baik.",
            "Allah memberkati semuanya itu, firman-Nya: 'Berkembang biaklah dan bertambah banyaklah; penuhilah air di laut, dan hendaklah burung-burung bertambah banyak di bumi.'",
            "Jadilah petang dan jadilah pagi, itulah hari kelima.",
            "Berfirmanlah Allah: 'Hendaklah bumi mengeluarkan segala jenis makhluk hidup, ternak dan binatang melata dan segala jenis binatang liar.' Lalu jadilah demikian.",
            "Allah menjadikan segala jenis binatang liar dan segala jenis ternak dan segala jenis binatang melata di muka bumi. Allah melihat bahwa yang demikian itu baik.",
            "Berfirmanlah Allah: 'Baiklah Kita menjadikan manusia menurut gambar dan rupa Kita, supaya mereka berkuasa atas ikan-ikan di laut dan burung-burung di udara dan atas ternak dan atas seluruh bumi dan atas segala binatang melata yang merayap di bumi.'",
            "Maka Allah menciptakan manusia itu menurut gambar-Nya, laki-laki dan perempuan diciptakan-Nya mereka.",
            "Allah memberkati mereka, lalu Allah berfirman kepada mereka: 'Beranakcuculah dan bertambah banyak; penuhilah bumi dan taklukkanlah itu; berkuasalah atas ikan-ikan di laut dan burung-burung di udara dan atas segala binatang yang merayap di bumi.'",
            "Berfirmanlah Allah: 'Lihatlah, Aku memberikan kepadamu segala tumbuhan yang berbiji di seluruh bumi dan segala pohon-pohonan yang buahnya berbiji; itulah akan menjadi makananmu.",
            "Dan kepada segala binatang di bumi dan segala burung di udara dan segala yang merayap di bumi, yang bernyawa, Kuberikan segala tumbuh-tumbuhan hijau menjadi makanan.' Dan jadilah demikian.",
            "Maka Allah melihat segala yang telah dibuat-Nya itu, sungguh amat baik. Jadilah petang dan jadilah pagi, itulah hari keenam.",
        ];

        foreach ($ayatKejadian1 as $index => $isi) {
            Ayat::create([
                'pasal_id' => $pasal1->id,
                'nomor_ayat' => $index + 1,
                'isi_ayat' => $isi,
            ]);
        }

        // Sample untuk Kejadian 2
        $pasal2 = Pasal::create([
            'kitab_id' => $kejadian->id,
            'nomor_pasal' => 2,
            'jumlah_ayat' => 25,
        ]);

        $ayatKejadian2 = [
            "Demikianlah diselesaikan langit dan bumi dan segala isinya.",
            "Pada hari ketujuh Allah mengakhiri pekerjaan-Nya yang telah dibuat-Nya itu, lalu Ia beristirahat pada hari ketujuh dari segala pekerjaan yang telah dibuat-Nya itu.",
            "Lalu Allah memberkati hari ketujuh itu dan menguduskannya, karena pada hari itulah Ia beristirahat dari segala pekerjaan yang telah dibuat-Nya itu ketika menciptakan.",
            "Itulah sejarah langit dan bumi pada waktu diciptakan. Pada waktu TUHAN Allah menjadikan bumi dan langit,",
            "belum ada semak-semak di bumi, belum ada tumbuh-tumbuhan di padang, sebab TUHAN Allah belum menurunkan hujan ke bumi dan belum ada manusia untuk mengusahakan tanah itu,",
            "tetapi ada kabut yang naik dari bumi dan membasahi seluruh muka bumi.",
            "Maka TUHAN Allah membentuk manusia itu dari debu tanah dan menghembuskan nafas hidup ke dalam hidungnya; demikianlah manusia itu menjadi makhluk yang hidup.",
            "Lalu TUHAN Allah menanam suatu taman di Eden, di sebelah timur, dan Ia menempatkan manusia yang dibentuk-Nya itu di dalamnya.",
            "Lalu dari tanah itu TUHAN Allah membuat tumbuh-tumbuhan beraneka ragam yang elik untuk dipandang dan baik untuk dimakan, dan pohon kehidupan di tengah taman itu, serta pohon pengetahuan yang baik dan jahat.",
            "Ada sungai yang mengalir dari Eden untuk membasahi taman itu, dan dari situ sungai itu bercabang menjadi empat.",
            "Yang pertama, sungai itu bernama Pison; itu mengalir mengelilingi seluruh tanah Hawila, tempat ada emas.",
            "Dan emas di negeri itu baik; di situ ada damar bedolah dan batu permata.",
            "Sungai yang kedua bernama Gihon; itu mengalir mengelilingi seluruh tanah Kus.",
            "Sungai yang ketiga bernama Tigris; itu mengalir di sebelah timur Asyur. Sungai yang keempat ialah Efrat.",
            "Lalu TUHAN Allah mengambil manusia itu dan menempatkan dia dalam taman Eden untuk mengusahakan dan memelihara taman itu.",
            "Lalu TUHAN Allah memberi perintah ini kepada manusia: 'Segala pohon di taman ini boleh kaumakan buahnya dengan bebas,",
            "tetapi pohon pengetahuan yang baik dan jahat itu, buahnya jangan kaumakan, sebab pada hari engkau memakannya, engkau pasti mati.'",
            "Berfirmanlah TUHAN Allah: 'Tidak baik kalau manusia itu seorang diri saja. Aku akan menjadikan penolong baginya yang sepadan dengan dia.'",
            "Lalu TUHAN Allah membentuk dari tanah segala binatang hutan dan segala burung di udara, lalu Ia membawa mereka kepada manusia itu untuk melihat, bagaimana ia menamai mereka; dan apa saja yang manusia itu namakannya kepada makhluk yang hidup, itulah namanya.",
            "Maka manusia itu memberi nama kepada segala ternak dan kepada burung-burung di udara dan kepada segala binatang hutan; tetapi baginya sendiri tidak didapatinya penolong yang sepadan dengan dia.",
            "Lalu TUHAN Allah membuat manusia itu tidur nyenyak; ketika ia tidur, TUHAN Allah mengambil salah satu rusuk dari padanya, lalu menutup tempat itu dengan daging.",
            "Dari rusuk yang telah diambil TUHAN Allah dari manusia itu, dibangun-Nyalah seorang perempuan, lalu Ia membawanya kepada manusia itu.",
            "Lalu berkatalah manusia itu: 'Inilah akhirnya tulang dari tulangku dan daging dari dagingku. Perempuan ini akan dipanggil istri, sebab diambil dari laki-laki.'",
            "Sebab itu seorang laki-laki akan meninggalkan ayahnya dan ibunya dan bersatu dengan isterinya, sehingga keduanya menjadi satu daging.",
            "Keduanya telanjang, manusia dan isterinya itu, tetapi mereka tidak merasa malu.",
        ];

        foreach ($ayatKejadian2 as $index => $isi) {
            Ayat::create([
                'pasal_id' => $pasal2->id,
                'nomor_ayat' => $index + 1,
                'isi_ayat' => $isi,
            ]);
        }

        // Sample untuk Mazmur 23
        $mazmur = Kitab::where('singkatan', 'Mazm')->first();
        $pasalMazmur23 = Pasal::create([
            'kitab_id' => $mazmur->id,
            'nomor_pasal' => 23,
            'jumlah_ayat' => 6,
        ]);

        $ayatMazmur23 = [
            "TUHAN adalah gembalaku, aku tidak akan kekurangan.",
            "Ia membaringkan aku di padang yang rumput hijau, Ia membimbing aku ke air yang tenang.",
            "Ia menyegarkan jiwaku. Ia menuntun aku di jalan kebenaran karena nama-Nya.",
            "Sekalipun aku berjalan lembah kekelaman, aku tidak takut bahaya, sebab Engkau besertaku; gada-Mu dan tongkat-Mu, itulah yang menghibur aku.",
            "Engkau menyediakan hidangan bagiku di hadapan lawanku; Engkau mengurapi kepalaku dengan minyak; pialaku berlimpah-limpah.",
            "Kebaikan dan kasih setia akan mengikuti aku seumur hidupku, dan aku akan diam dalam rumah TUHAN selama-lamanya.",
        ];

        foreach ($ayatMazmur23 as $index => $isi) {
            Ayat::create([
                'pasal_id' => $pasalMazmur23->id,
                'nomor_ayat' => $index + 1,
                'isi_ayat' => $isi,
            ]);
        }

        // Sample untuk Yohanes 3:16 (PB)
        $yohanes = Kitab::where('singkatan', 'Yoh')->first();
        $pasalYohanes3 = Pasal::create([
            'kitab_id' => $yohanes->id,
            'nomor_pasal' => 3,
            'jumlah_ayat' => 36,
        ]);

        // Sample ayat 16-21 saja untuk demo
        $ayatYohanes3_16_21 = [
            null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, // 1-15
            "Karena begitu besar kasih Allah akan dunia ini, sehingga Ia telah mengaruniakan Anak-Nya yang tunggal, supaya setiap orang yang percaya kepada-Nya tidak binasa, melainkan beroleh hidup yang kekal.",
            "Sebab Allah mengutus Anak-Nya ke dalam dunia bukan untuk menghakimi dunia, melainkan untuk menyelamatkannya oleh Dia.",
            "Barangsiapa percaya kepada-Nya, ia tidak akan dihukum; barangsiapa tidak percaya, ia sudah dihukum, sebab ia tidak percaya dalam nama Anak Tunggal Allah.",
            "Dan inilah hukuman itu: Terang telah datang ke dalam dunia, tetapi manusia lebih mengasihi kegelapan dari pada terang, sebab perbuatan-perbuatan mereka jahat.",
            "Sebab setiap orang yang berbuat jahat membenci terang dan tidak datang kepada terang, supaya perbuatan-perbuatannya itu tidak dihukum.",
            "Tapi barangsiapa melakukan yang benar, ia datang kepada terang, supaya nyata, bahwa perbuatan-perbuatannya dikerjakan dalam Allah.",
        ];

        foreach ($ayatYohanes3_16_21 as $index => $isi) {
            if ($isi !== null) {
                Ayat::create([
                    'pasal_id' => $pasalYohanes3->id,
                    'nomor_ayat' => $index + 1,
                    'isi_ayat' => $isi,
                ]);
            }
        }

        $this->command->info('Sample pasal dan ayat berhasil di-seed.');
    }

    private function seedReadingPlans()
    {
        // Plan 1 Tahun Baca Alkitab — ritme santai (~3 pasal/hari).
        $plan1Tahun = ReadingPlan::create([
            'nama' => '1 Tahun Baca Alkitab',
            'deskripsi' => 'Baca seluruh Alkitab dalam 1 tahun dengan ritme santai sekitar 3 pasal per hari.',
            'durasi_hari' => 365,
            'aktif' => true,
        ]);
        $this->generateReadingPlanItems($plan1Tahun, 3, 30);

        // Plan 6 Bulan Baca Alkitab — ritme lebih padat (~6 pasal/hari) untuk
        // mengejar target khatam dalam 6 bulan.
        $plan6Bulan = ReadingPlan::create([
            'nama' => '6 Bulan Baca Alkitab',
            'deskripsi' => 'Baca seluruh Alkitab dalam 6 bulan dengan ritme lebih intensif sekitar 6 pasal per hari.',
            'durasi_hari' => 180,
            'aktif' => true,
        ]);
        $this->generateReadingPlanItems($plan6Bulan, 6, 30);

        $this->command->info('Reading Plans berhasil di-seed.');
    }

    /**
     * Generate jadwal bacaan harian secara berurutan mengikuti urutan kitab.
     *
     * @param  \App\Models\ReadingPlan  $plan
     * @param  int  $pasalPerHari  jumlah pasal yang dibaca tiap hari (menentukan kepadatan)
     * @param  int  $jumlahHari    berapa hari pertama yang di-generate (contoh jadwal)
     */
    private function generateReadingPlanItems($plan, int $pasalPerHari, int $jumlahHari): void
    {
        // Daftar kitab berurutan beserta jumlah pasalnya (dipakai untuk membagi bacaan).
        $kitabs = Kitab::orderBy('urutan')->get(['id', 'jumlah_pasal']);
        if ($kitabs->isEmpty()) {
            return;
        }

        // "Pointer" posisi baca: index kitab & nomor pasal saat ini.
        $kitabIndex = 0;
        $pasalSaatIni = 1;

        for ($hari = 1; $hari <= $jumlahHari; $hari++) {
            $bacaan = [];
            $sisaPasalHariIni = $pasalPerHari;

            while ($sisaPasalHariIni > 0 && $kitabIndex < $kitabs->count()) {
                $kitab = $kitabs[$kitabIndex];
                $mulai = $pasalSaatIni;
                $selesai = min($kitab->jumlah_pasal, $mulai + $sisaPasalHariIni - 1);

                $bacaan[] = [
                    'kitab_id' => $kitab->id,
                    'pasal_mulai' => $mulai,
                    'pasal_selesai' => $selesai,
                ];

                $terpakai = $selesai - $mulai + 1;
                $sisaPasalHariIni -= $terpakai;

                if ($selesai >= $kitab->jumlah_pasal) {
                    // Lanjut ke kitab berikutnya
                    $kitabIndex++;
                    $pasalSaatIni = 1;
                } else {
                    $pasalSaatIni = $selesai + 1;
                }
            }

            if (empty($bacaan)) {
                break; // seluruh kitab sudah terbagi
            }

            ReadingPlanItem::create([
                'reading_plan_id' => $plan->id,
                'hari_ke' => $hari,
                'bacaan' => $bacaan,
            ]);
        }
    }
}
