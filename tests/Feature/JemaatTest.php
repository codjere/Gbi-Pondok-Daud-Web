<?php

namespace Tests\Feature;

use App\Models\Ayat;
use App\Models\CoolGroup;
use App\Models\JadwalIbadah;
use App\Models\Kitab;
use App\Models\KontenWarta;
use App\Models\Pasal;
use App\Models\PermohonanDoa;
use App\Models\ReadingPlan;
use App\Models\Reservasi;
use App\Models\TimPelayanan;
use App\Models\User;
use Database\Seeders\AiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JemaatTest extends TestCase
{
    use RefreshDatabase;

    private function jemaat(): User
    {
        return User::factory()->create(['role' => 'jemaat', 'has_biometric' => true]);
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

    // ---------- Dashboard ----------

    public function test_dashboard_loads_for_jemaat(): void
    {
        $this->actingAs($this->jemaat())->get('/dashboard')->assertOk();
    }

    // ---------- Jadwal ----------

    public function test_jadwal_page_loads(): void
    {
        $this->makeJadwal();
        $this->actingAs($this->jemaat())->get(route('jemaat.jadwal'))->assertOk();
    }

    // ---------- Reservasi ----------

    public function test_reservasi_page_loads(): void
    {
        $this->actingAs($this->jemaat())->get(route('jemaat.reservasi'))->assertOk();
    }

    public function test_reservasi_success(): void
    {
        $user = $this->jemaat();
        $jadwal = $this->makeJadwal();

        $this->actingAs($user)
            ->post(route('jemaat.reservasi.store', $jadwal))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservasi', [
            'user_id' => $user->id,
            'jadwal_ibadah_id' => $jadwal->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_reservasi_duplicate_blocked(): void
    {
        $user = $this->jemaat();
        $jadwal = $this->makeJadwal();
        Reservasi::create(['user_id' => $user->id, 'jadwal_ibadah_id' => $jadwal->id, 'status' => 'confirmed']);

        $this->actingAs($user)
            ->post(route('jemaat.reservasi.store', $jadwal))
            ->assertSessionHas('error');

        $this->assertEquals(1, Reservasi::where('user_id', $user->id)->where('jadwal_ibadah_id', $jadwal->id)->count());
    }

    public function test_reservasi_full_quota_blocked(): void
    {
        $jadwal = $this->makeJadwal(['batas_kuota' => 1]);
        // Isi kuota dengan user lain
        $other = $this->jemaat();
        Reservasi::create(['user_id' => $other->id, 'jadwal_ibadah_id' => $jadwal->id, 'status' => 'confirmed']);

        $user = $this->jemaat();
        $this->actingAs($user)
            ->post(route('jemaat.reservasi.store', $jadwal))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('reservasi', ['user_id' => $user->id, 'jadwal_ibadah_id' => $jadwal->id]);
    }

    // ---------- Permohonan Doa ----------

    public function test_doa_page_loads(): void
    {
        $this->actingAs($this->jemaat())->get(route('jemaat.doa'))->assertOk();
    }

    public function test_doa_submit_success(): void
    {
        $user = $this->jemaat();
        $this->actingAs($user)->post(route('jemaat.doa.store'), [
            'pokok' => 'Mohon doa untuk kesehatan keluarga kami.',
            'anonim' => '0',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('permohonan_doa', [
            'user_id' => $user->id,
            'is_anonymous' => false,
            'status' => 'pending',
        ]);
    }

    public function test_doa_submit_too_short_rejected(): void
    {
        $this->actingAs($this->jemaat())->post(route('jemaat.doa.store'), [
            'pokok' => 'pendek',
            'anonim' => '0',
        ])->assertSessionHasErrors('pokok');
    }

    // ---------- Warta ----------

    public function test_warta_index_loads(): void
    {
        $pengurus = User::factory()->create(['role' => 'pengurus']);
        KontenWarta::create([
            'user_id' => $pengurus->id,
            'kategori' => 'Renungan Harian',
            'judul' => 'Renungan Uji',
            'isi' => 'Isi renungan yang cukup panjang untuk uji.',
            'published_at' => now()->subDay(),
        ]);

        $this->actingAs($this->jemaat())->get(route('jemaat.warta'))->assertOk()->assertSee('Renungan Uji');
    }

    public function test_warta_show_published(): void
    {
        $pengurus = User::factory()->create(['role' => 'pengurus']);
        $warta = KontenWarta::create([
            'user_id' => $pengurus->id,
            'kategori' => 'Pengumuman',
            'judul' => 'Pengumuman Penting',
            'isi' => 'Isi pengumuman yang panjang untuk uji tampil.',
            'published_at' => now()->subDay(),
        ]);

        $this->actingAs($this->jemaat())->get(route('jemaat.warta.show', $warta->slug))->assertOk();
    }

    public function test_warta_show_unpublished_returns_404(): void
    {
        $pengurus = User::factory()->create(['role' => 'pengurus']);
        $warta = KontenWarta::create([
            'user_id' => $pengurus->id,
            'kategori' => 'Pengumuman',
            'judul' => 'Draft Belum Terbit',
            'isi' => 'Isi draft yang belum dipublikasikan sama sekali.',
            'published_at' => null,
        ]);

        $this->actingAs($this->jemaat())->get(route('jemaat.warta.show', $warta->slug))->assertNotFound();
    }

    // ---------- Kuesioner + Rekomendasi (AI service di-fake ke fallback) ----------

    public function test_kuesioner_page_loads(): void
    {
        $this->actingAs($this->jemaat())->get(route('jemaat.kuesioner'))->assertOk();
    }

    public function test_kuesioner_submit_creates_profile_and_redirects(): void
    {
        // Paksa AI service dianggap down -> pakai fallback lokal, tidak butuh koneksi
        Http::fake(['*' => Http::response('', 500)]);
        $this->seed(AiSeeder::class);

        $user = $this->jemaat();
        $this->actingAs($user)->post(route('jemaat.kuesioner.store'), [
            'usia' => 25,
            'jenis_kelamin' => 'L',
            'kecamatan' => 'Onembute',
            'minat' => ['musik', 'teknologi'],
            'bakat' => ['vokal', 'komputer'],
            'ketersediaan_hari' => ['minggu', 'sabtu'],
            'ketersediaan_waktu' => 'fleksibel',
        ])->assertRedirect(route('jemaat.rekomendasi'));

        $this->assertDatabaseHas('user_profiles', ['user_id' => $user->id]);
        $this->assertNotNull($user->profile->completed_at);
    }

    public function test_kuesioner_validation_rejects_bad_input(): void
    {
        $this->actingAs($this->jemaat())->post(route('jemaat.kuesioner.store'), [
            'usia' => 5, // di bawah min 15
            'jenis_kelamin' => 'X',
        ])->assertSessionHasErrors(['usia', 'jenis_kelamin', 'kecamatan', 'minat', 'bakat']);
    }

    public function test_rekomendasi_redirects_to_kuesioner_if_profile_incomplete(): void
    {
        $this->actingAs($this->jemaat())->get(route('jemaat.rekomendasi'))
            ->assertRedirect(route('jemaat.kuesioner'));
    }

    public function test_pilih_tim_saves_choice(): void
    {
        $this->seed(AiSeeder::class);
        $tim = TimPelayanan::first();
        $user = $this->jemaat();

        $this->actingAs($user)->post(route('jemaat.rekomendasi.tim'), [
            'tim_id' => $tim->id,
            'score' => 0.9,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('user_pelayanan_choices', [
            'user_id' => $user->id,
            'tim_pelayanan_id' => $tim->id,
        ]);
    }

    public function test_pilih_cool_saves_choice(): void
    {
        $this->seed(AiSeeder::class);
        $cool = CoolGroup::first();
        $user = $this->jemaat();

        $this->actingAs($user)->post(route('jemaat.rekomendasi.cool'), [
            'cool_id' => $cool->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('user_cool_choices', [
            'user_id' => $user->id,
            'cool_group_id' => $cool->id,
        ]);
    }

    // ---------- Chatbot (AI service di-fake) ----------

    public function test_chatbot_page_loads(): void
    {
        Http::fake(['*/health' => Http::response('', 500)]);
        $this->actingAs($this->jemaat())->get(route('jemaat.chat'))->assertOk();
    }

    public function test_chatbot_send_returns_answer_via_fake_service(): void
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200),
            '*/chat' => Http::response([
                'answer' => 'Ibadah Raya hari Minggu pukul 08:00.',
                'sources' => [],
                'intent' => 'jadwal',
            ], 200),
        ]);

        $user = $this->jemaat();
        $res = $this->actingAs($user)->postJson(route('jemaat.chat.send'), [
            'message' => 'Kapan jadwal ibadah?',
        ]);

        $res->assertOk()->assertJson(['success' => true, 'answer' => 'Ibadah Raya hari Minggu pukul 08:00.']);
        $this->assertDatabaseHas('chat_messages', ['role' => 'user', 'content' => 'Kapan jadwal ibadah?']);
        $this->assertDatabaseHas('chat_messages', ['role' => 'assistant']);
    }

    public function test_chatbot_send_validation(): void
    {
        $this->actingAs($this->jemaat())->postJson(route('jemaat.chat.send'), ['message' => ''])
            ->assertStatus(422);
    }

    // ---------- Alkitab ----------

    private function seedBibleMinimal(): array
    {
        $kitab = Kitab::create([
            'nama' => 'Kejadian', 'singkatan' => 'Kej', 'urutan' => 1,
            'jumlah_pasal' => 1, 'testament' => 'PL',
        ]);
        $pasal = Pasal::create(['kitab_id' => $kitab->id, 'nomor_pasal' => 1, 'jumlah_ayat' => 2]);
        Ayat::create(['pasal_id' => $pasal->id, 'nomor_ayat' => 1, 'isi_ayat' => 'Pada mulanya Allah menciptakan langit dan bumi.']);
        Ayat::create(['pasal_id' => $pasal->id, 'nomor_ayat' => 2, 'isi_ayat' => 'Bumi belum berbentuk dan kosong.']);

        return [$kitab, $pasal];
    }

    public function test_alkitab_index_loads(): void
    {
        $this->seedBibleMinimal();
        $this->actingAs($this->jemaat())->get(route('jemaat.alkitab'))->assertOk();
    }

    public function test_alkitab_baca_pasal_loads_and_records_history(): void
    {
        [$kitab, $pasal] = $this->seedBibleMinimal();
        $user = $this->jemaat();

        $this->actingAs($user)
            ->get(route('jemaat.alkitab.baca', ['kitabId' => $kitab->id, 'nomorPasal' => 1]))
            ->assertOk()
            ->assertSee('Pada mulanya');

        $this->assertDatabaseHas('user_reading_history', ['user_id' => $user->id, 'pasal_id' => $pasal->id]);
    }

    public function test_alkitab_cari_finds_verse(): void
    {
        $this->seedBibleMinimal();
        $this->actingAs($this->jemaat())
            ->get(route('jemaat.alkitab.cari', ['kata_kunci' => 'langit']))
            ->assertOk()
            ->assertSee('menciptakan langit');
    }

    public function test_alkitab_mulai_plan(): void
    {
        $plan = ReadingPlan::create(['nama' => 'Plan 7 Hari', 'deskripsi' => 'Uji', 'durasi_hari' => 7, 'aktif' => true]);
        $user = $this->jemaat();

        $this->actingAs($user)->post(route('jemaat.alkitab.mulai-plan'), ['reading_plan_id' => $plan->id])
            ->assertRedirect(route('jemaat.alkitab.plan', $plan->id));

        $this->assertDatabaseHas('user_reading_progress', ['user_id' => $user->id, 'reading_plan_id' => $plan->id]);
    }
}
