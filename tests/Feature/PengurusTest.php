<?php

namespace Tests\Feature;

use App\Models\JadwalIbadah;
use App\Models\Kehadiran;
use App\Models\KontenWarta;
use App\Models\PermohonanDoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengurusTest extends TestCase
{
    use RefreshDatabase;

    private function pengurus(): User
    {
        return User::factory()->create(['role' => 'pengurus', 'has_biometric' => true]);
    }

    private function jemaat(array $overrides = []): User
    {
        return User::factory()->create(array_merge(['role' => 'jemaat', 'has_biometric' => true], $overrides));
    }

    private function makeJadwal(array $overrides = []): JadwalIbadah
    {
        return JadwalIbadah::create(array_merge([
            'nama_sesi' => 'Ibadah Test',
            'tanggal' => now()->addDays(3)->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'pembicara' => 'Pdt. Test',
            'lokasi' => 'Ruang Utama',
            'batas_kuota' => 100,
            'status' => 'terpublikasi',
        ], $overrides));
    }

    // ---------- Jadwal (pengurus) ----------

    public function test_jadwal_index_loads(): void
    {
        $this->actingAs($this->pengurus())->get(route('pengurus.jadwal'))->assertOk();
    }

    public function test_jadwal_store_creates_published_schedule(): void
    {
        $this->actingAs($this->pengurus())->post(route('pengurus.jadwal.store'), [
            'nama_sesi' => 'Ibadah Baru',
            'tanggal' => now()->addWeek()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'pembicara' => 'Pdt. A',
            'batas_kuota' => 150,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('jadwal_ibadah', [
            'nama_sesi' => 'Ibadah Baru',
            'status' => 'terpublikasi',
            'batas_kuota' => 150,
        ]);
    }

    public function test_jadwal_store_rejects_end_before_start(): void
    {
        $this->actingAs($this->pengurus())->post(route('pengurus.jadwal.store'), [
            'nama_sesi' => 'Salah Jam',
            'tanggal' => now()->addWeek()->toDateString(),
            'jam_mulai' => '11:00',
            'jam_selesai' => '09:00',
            'pembicara' => 'Pdt. A',
            'batas_kuota' => 50,
        ])->assertSessionHasErrors('jam_selesai');
    }

    public function test_jadwal_destroy(): void
    {
        $jadwal = $this->makeJadwal();
        $this->actingAs($this->pengurus())
            ->delete(route('pengurus.jadwal.destroy', $jadwal))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('jadwal_ibadah', ['id' => $jadwal->id]);
    }

    // ---------- Konten (pengurus) ----------

    public function test_konten_index_loads(): void
    {
        $this->actingAs($this->pengurus())->get(route('pengurus.konten'))->assertOk();
    }

    public function test_konten_store(): void
    {
        $this->actingAs($this->pengurus())->post(route('pengurus.konten.store'), [
            'kategori' => 'Pengumuman',
            'judul' => 'Ibadah Natal',
            'isi' => 'Ibadah Natal akan diadakan pada tanggal 25 Desember.',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('konten_warta', ['judul' => 'Ibadah Natal', 'kategori' => 'Pengumuman']);
    }

    public function test_konten_store_with_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->pengurus())->post(route('pengurus.konten.store'), [
            'kategori' => 'Renungan Harian',
            'judul' => 'Renungan Bergambar',
            'isi' => 'Isi renungan yang cukup panjang untuk lolos validasi minimal.',
            'gambar' => UploadedFile::fake()->image('warta.jpg', 400, 300),
        ])->assertSessionHas('success');

        $konten = KontenWarta::where('judul', 'Renungan Bergambar')->first();
        $this->assertNotNull($konten);
        $this->assertNotNull($konten->gambar);
        $this->assertStringStartsWith('warta/', $konten->gambar);
        Storage::disk('public')->assertExists($konten->gambar);
    }

    public function test_konten_store_rejects_non_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->pengurus())->post(route('pengurus.konten.store'), [
            'kategori' => 'Pengumuman',
            'judul' => 'Dokumen Bukan Gambar',
            'isi' => 'Isi konten yang cukup panjang untuk lolos validasi minimal.',
            'gambar' => UploadedFile::fake()->create('file.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('gambar');
    }

    public function test_warta_detail_shows_image(): void
    {
        $warta = KontenWarta::create([
            'user_id' => $this->pengurus()->id,
            'kategori' => 'Renungan Harian',
            'judul' => 'Renungan Tampil Gambar',
            'isi' => 'Isi renungan untuk menguji tampilan gambar pada halaman detail.',
            'gambar' => 'warta/contoh.jpg',
            'published_at' => now()->subDay(),
        ]);

        $this->actingAs($this->jemaat())
            ->get(route('jemaat.warta.show', $warta->slug))
            ->assertOk()
            ->assertSee('warta/contoh.jpg');
    }

    public function test_konten_destroy_removes_image_file(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('hapus.jpg')->store('warta', 'public');

        $konten = KontenWarta::create([
            'user_id' => $this->pengurus()->id,
            'kategori' => 'Pengumuman',
            'judul' => 'Konten Bergambar Dihapus',
            'isi' => 'Konten ini beserta gambarnya akan dihapus dari sistem.',
            'gambar' => $path,
            'published_at' => now(),
        ]);

        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->pengurus())
            ->delete(route('pengurus.konten.destroy', $konten))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('konten_warta', ['id' => $konten->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_konten_store_rejects_bad_category(): void
    {
        $this->actingAs($this->pengurus())->post(route('pengurus.konten.store'), [
            'kategori' => 'KategoriNgawur',
            'judul' => 'Judul',
            'isi' => 'Isi konten yang cukup panjang untuk lolos min.',
        ])->assertSessionHasErrors('kategori');
    }

    public function test_konten_destroy(): void
    {
        $konten = KontenWarta::create([
            'user_id' => $this->pengurus()->id,
            'kategori' => 'Pengumuman',
            'judul' => 'Akan Dihapus',
            'isi' => 'Konten ini akan segera dihapus dari sistem.',
            'published_at' => now(),
        ]);

        $this->actingAs($this->pengurus())
            ->delete(route('pengurus.konten.destroy', $konten))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('konten_warta', ['id' => $konten->id]);
    }

    // ---------- Laporan ----------

    public function test_laporan_loads(): void
    {
        $this->makeJadwal();
        $this->actingAs($this->pengurus())->get(route('pengurus.laporan'))->assertOk();
    }

    public function test_laporan_with_jadwal_filter(): void
    {
        $jadwal = $this->makeJadwal();
        $this->actingAs($this->pengurus())
            ->get(route('pengurus.laporan', ['jadwal_id' => $jadwal->id]))
            ->assertOk();
    }

    public function test_laporan_shows_verification_breakdown(): void
    {
        $jadwal = $this->makeJadwal();
        Kehadiran::create(['user_id' => $this->jemaat()->id, 'jadwal_ibadah_id' => $jadwal->id, 'waktu_checkin' => now(), 'metode_verifikasi' => 'facenet']);
        Kehadiran::create(['user_id' => $this->jemaat()->id, 'jadwal_ibadah_id' => $jadwal->id, 'waktu_checkin' => now(), 'metode_verifikasi' => 'manual']);

        $this->actingAs($this->pengurus())
            ->get(route('pengurus.laporan', ['jadwal_id' => $jadwal->id]))
            ->assertOk()
            ->assertSee('Metode Verifikasi')
            ->assertSee('Tren Kehadiran');
    }

    public function test_laporan_export_pdf(): void
    {
        $jadwal = $this->makeJadwal();
        Kehadiran::create(['user_id' => $this->jemaat()->id, 'jadwal_ibadah_id' => $jadwal->id, 'waktu_checkin' => now(), 'metode_verifikasi' => 'facenet']);

        $res = $this->actingAs($this->pengurus())
            ->get(route('pengurus.laporan.pdf', ['jadwal_id' => $jadwal->id]));

        $res->assertOk();
        $this->assertEquals('application/pdf', $res->headers->get('content-type'));
    }

    public function test_laporan_export_excel(): void
    {
        $jadwal = $this->makeJadwal();
        Kehadiran::create(['user_id' => $this->jemaat()->id, 'jadwal_ibadah_id' => $jadwal->id, 'waktu_checkin' => now(), 'metode_verifikasi' => 'manual']);

        $res = $this->actingAs($this->pengurus())
            ->get(route('pengurus.laporan.excel', ['jadwal_id' => $jadwal->id]));

        $res->assertOk();
        $this->assertStringContainsString('spreadsheet', $res->headers->get('content-type'));
    }

    // ---------- Absen ----------

    public function test_absen_index_loads(): void
    {
        Http::fake(['*/health' => Http::response('', 500)]); // service dianggap offline
        $this->actingAs($this->pengurus())->get(route('pengurus.absen'))->assertOk();
    }

    public function test_absen_manual_success(): void
    {
        $jadwal = $this->makeJadwal();
        $jemaat = $this->jemaat();

        $this->actingAs($this->pengurus())->post(route('pengurus.absen.manual'), [
            'jadwal_id' => $jadwal->id,
            'user_id' => $jemaat->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('kehadiran', [
            'user_id' => $jemaat->id,
            'jadwal_ibadah_id' => $jadwal->id,
            'metode_verifikasi' => 'manual',
        ]);
    }

    public function test_absen_manual_duplicate_blocked(): void
    {
        $jadwal = $this->makeJadwal();
        $jemaat = $this->jemaat();
        Kehadiran::create([
            'user_id' => $jemaat->id,
            'jadwal_ibadah_id' => $jadwal->id,
            'waktu_checkin' => now(),
            'metode_verifikasi' => 'manual',
        ]);

        $this->actingAs($this->pengurus())->post(route('pengurus.absen.manual'), [
            'jadwal_id' => $jadwal->id,
            'user_id' => $jemaat->id,
        ])->assertSessionHas('error');

        $this->assertEquals(1, Kehadiran::where('user_id', $jemaat->id)->count());
    }

    public function test_absen_today_list_json(): void
    {
        $jadwal = $this->makeJadwal();
        $jemaat = $this->jemaat();
        Kehadiran::create([
            'user_id' => $jemaat->id,
            'jadwal_ibadah_id' => $jadwal->id,
            'waktu_checkin' => now(),
            'metode_verifikasi' => 'manual',
        ]);

        $this->actingAs($this->pengurus())
            ->getJson(route('pengurus.absen.list', ['jadwal_id' => $jadwal->id]))
            ->assertOk()
            ->assertJsonStructure(['data' => [['name', 'role', 'time']]]);
    }

    public function test_absen_verify_service_down_returns_503(): void
    {
        Http::fake(['*/health' => Http::response('', 500)]);
        $jadwal = $this->makeJadwal();

        $this->actingAs($this->pengurus())
            ->postJson(route('pengurus.absen.verify'), [
                'image' => 'data:image/jpeg;base64,AAAA',
                'jadwal_id' => $jadwal->id,
            ])
            ->assertStatus(503);
    }

    public function test_absen_verify_recognizes_and_records(): void
    {
        // Embedding yang cocok persis -> cosine similarity = 1.0 > threshold 0.60
        $embedding = array_fill(0, 128, 0.1);

        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200),
            '*/extract-embedding-batch' => Http::response([
                'embedding' => $embedding,
                'samples_used' => 1,
                'samples_failed' => 0,
            ], 200),
        ]);

        $jadwal = $this->makeJadwal();
        $jemaat = $this->jemaat(['face_embedding' => $embedding]);

        $res = $this->actingAs($this->pengurus())
            ->postJson(route('pengurus.absen.verify'), [
                'image' => 'data:image/jpeg;base64,AAAA',
                'jadwal_id' => $jadwal->id,
            ]);

        $res->assertOk()->assertJson([
            'success' => true,
            'recognized' => true,
            'name' => $jemaat->name,
        ]);

        $this->assertDatabaseHas('kehadiran', [
            'user_id' => $jemaat->id,
            'jadwal_ibadah_id' => $jadwal->id,
            'metode_verifikasi' => 'facenet',
        ]);
    }

    public function test_absen_participants_loads(): void
    {
        $jadwal = $this->makeJadwal();
        $this->actingAs($this->pengurus())
            ->get(route('pengurus.absen.participants', ['jadwal_id' => $jadwal->id]))
            ->assertOk();
    }

    // ---------- Permohonan Doa (pengurus) ----------

    public function test_permohonan_doa_index_loads(): void
    {
        $this->actingAs($this->pengurus())->get(route('pengurus.permohonan-doa'))->assertOk();
    }

    public function test_permohonan_doa_update_status(): void
    {
        $jemaat = $this->jemaat();
        $doa = PermohonanDoa::create([
            'user_id' => $jemaat->id,
            'pokok_doa' => 'Doa untuk pekerjaan baru yang sedang dilamar.',
            'is_anonymous' => false,
            'status' => 'pending',
        ]);

        $this->actingAs($this->pengurus())
            ->put(route('pengurus.permohonan-doa.update', $doa), ['status' => 'selesai'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('permohonan_doa', ['id' => $doa->id, 'status' => 'selesai']);
    }

    public function test_permohonan_doa_destroy(): void
    {
        $jemaat = $this->jemaat();
        $doa = PermohonanDoa::create([
            'user_id' => $jemaat->id,
            'pokok_doa' => 'Doa yang akan dihapus oleh pengurus.',
            'is_anonymous' => false,
            'status' => 'pending',
        ]);

        $this->actingAs($this->pengurus())
            ->delete(route('pengurus.permohonan-doa.destroy', $doa))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('permohonan_doa', ['id' => $doa->id]);
    }
}
