<?php

namespace Tests\Feature;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FaceLoginTest extends TestCase
{
    use RefreshDatabase;

    private array $embedding;

    protected function setUp(): void
    {
        parent::setUp();
        $this->embedding = array_fill(0, 512, 0.1);
    }

    private function faceUser(): User
    {
        return User::factory()->create([
            'has_biometric' => true,
            'face_embedding' => $this->embedding,
        ]);
    }

    public function test_login_page_shows_face_login_button(): void
    {
        $this->get('/login')->assertOk()->assertSee('Login dengan Wajah');
    }

    public function test_face_login_matches_and_sends_otp(): void
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200),
            '*/extract-embedding-batch' => Http::response([
                'embedding' => $this->embedding,
                'samples_used' => 1,
                'samples_failed' => 0,
            ], 200),
        ]);

        $user = $this->faceUser();

        $res = $this->postJson(route('face.login'), ['image' => 'data:image/jpeg;base64,AAAA']);

        $res->assertOk()->assertJson([
            'success' => true,
            'recognized' => true,
            'name' => $user->name,
        ]);
        $res->assertJsonPath('redirect', route('face.otp'));

        // OTP tercatat & user disimpan sebagai pending di session
        $this->assertDatabaseHas('email_otps', ['user_id' => $user->id, 'consumed_at' => null]);
        $this->assertEquals($user->id, session('face_login_user_id'));
    }

    public function test_face_login_service_down_returns_503(): void
    {
        Http::fake(['*/health' => Http::response('', 500)]);
        $this->faceUser();

        $this->postJson(route('face.login'), ['image' => 'data:image/jpeg;base64,AAAA'])
            ->assertStatus(503)
            ->assertJsonPath('code', 'service_down');
    }

    public function test_face_login_no_match_returns_recognized_false(): void
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200),
            '*/extract-embedding-batch' => Http::response([
                'embedding' => array_fill(0, 512, -0.9), // sangat berbeda -> cosine rendah
                'samples_used' => 1,
                'samples_failed' => 0,
            ], 200),
        ]);
        // user dengan embedding positif
        User::factory()->create(['has_biometric' => true, 'face_embedding' => array_fill(0, 512, 0.9)]);

        $this->postJson(route('face.login'), ['image' => 'data:image/jpeg;base64,AAAA'])
            ->assertOk()
            ->assertJsonPath('recognized', false)
            ->assertJsonPath('code', 'no_match');
    }

    public function test_face_otp_page_requires_pending_session(): void
    {
        // Tanpa sesi face-login -> diarahkan kembali ke login
        $this->get(route('face.otp'))->assertRedirect(route('login'));
    }

    public function test_full_face_login_flow_logs_user_in(): void
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200),
            '*/extract-embedding-batch' => Http::response([
                'embedding' => $this->embedding, 'samples_used' => 1, 'samples_failed' => 0,
            ], 200),
        ]);

        $user = $this->faceUser();

        // Langkah 1: scan wajah -> match + OTP
        $this->postJson(route('face.login'), ['image' => 'data:image/jpeg;base64,AAAA'])->assertOk();

        // Ambil OTP asli tidak bisa (di-hash). Buat OTP diketahui langsung.
        EmailOtp::where('user_id', $user->id)->update(['consumed_at' => now()]); // batalkan yg dari service
        EmailOtp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code_hash' => Hash::make('654321'),
            'expires_at' => now()->addMinutes(10),
        ]);

        // Halaman OTP tampil
        $this->get(route('face.otp'))->assertOk()->assertSee('Wajah dikenali');

        // Langkah 2: verifikasi OTP -> login + redirect dashboard
        $this->post(route('face.otp.verify'), ['code' => '654321'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('face_login_user_id'));
    }

    public function test_face_otp_wrong_code_fails(): void
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200),
            '*/extract-embedding-batch' => Http::response([
                'embedding' => $this->embedding, 'samples_used' => 1, 'samples_failed' => 0,
            ], 200),
        ]);
        $user = $this->faceUser();
        $this->postJson(route('face.login'), ['image' => 'x'])->assertOk();

        EmailOtp::where('user_id', $user->id)->update(['consumed_at' => now()]);
        EmailOtp::create([
            'user_id' => $user->id, 'email' => $user->email,
            'code_hash' => Hash::make('111111'), 'expires_at' => now()->addMinutes(10),
        ]);

        $this->post(route('face.otp.verify'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertGuest();
    }
}
