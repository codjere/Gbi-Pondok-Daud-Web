<?php

namespace Tests\Feature;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Halaman publik ----------

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertOk();
    }

    // ---------- Register ----------

    public function test_register_creates_user_and_sends_to_otp(): void
    {
        $res = $this->post('/register', [
            'nama' => 'Test Jemaat',
            'email' => 'testjemaat@example.com',
            'status' => 'jemaat',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertRedirect(route('otp.show'));
        $this->assertDatabaseHas('users', [
            'email' => 'testjemaat@example.com',
            'role' => 'jemaat',
            'has_biometric' => false,
        ]);
        $user = User::where('email', 'testjemaat@example.com')->first();
        $this->assertNull($user->email_verified_at, 'User baru belum terverifikasi');
        $this->assertAuthenticatedAs($user);
    }

    public function test_register_rejects_pengurus_role(): void
    {
        $this->post('/register', [
            'nama' => 'Hacker',
            'email' => 'hacker@example.com',
            'status' => 'pengurus',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('status');

        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'dup@example.com']);

        $this->post('/register', [
            'nama' => 'Dup',
            'email' => 'dup@example.com',
            'status' => 'jemaat',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    public function test_register_rejects_short_password(): void
    {
        $this->post('/register', [
            'nama' => 'Shorty',
            'email' => 'shorty@example.com',
            'status' => 'jemaat',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    // ---------- Login ----------

    public function test_login_with_valid_credentials_verified_and_biometric_goes_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
            'has_biometric' => true,
        ]);

        $this->post('/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_verified_but_no_biometric_goes_to_biometric(): void
    {
        User::factory()->create([
            'email' => 'nobio@example.com',
            'password' => Hash::make('password123'),
            'has_biometric' => false,
        ]);

        $this->post('/login', [
            'email' => 'nobio@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('register.biometric'));
    }

    public function test_login_unverified_email_goes_to_otp(): void
    {
        User::factory()->unverified()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('otp.show'));
    }

    public function test_login_with_wrong_password_fails(): void
    {
        User::factory()->create([
            'email' => 'wrong@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'salahsalah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // ---------- OTP ----------

    public function test_verify_otp_with_correct_code_verifies_email(): void
    {
        $user = User::factory()->unverified()->create();
        EmailOtp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($user)
            ->post('/register/otp', ['code' => '123456'])
            ->assertRedirect(route('register.biometric')); // verified tapi belum biometrik

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verify_otp_with_wrong_code_fails(): void
    {
        $user = User::factory()->unverified()->create();
        EmailOtp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($user)
            ->post('/register/otp', ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_verify_otp_expired_fails(): void
    {
        $user = User::factory()->unverified()->create();
        EmailOtp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($user)
            ->post('/register/otp', ['code' => '123456'])
            ->assertSessionHasErrors('code');
    }

    // ---------- Biometric (butuh Http::fake untuk FaceNet) ----------

    public function test_store_biometric_success_with_face_service_up(): void
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200),
            '*/extract-embedding-batch' => Http::response([
                'embedding' => array_fill(0, 128, 0.1),
                'samples_used' => 5,
                'samples_failed' => 0,
            ], 200),
        ]);

        $user = User::factory()->create(['has_biometric' => false]);
        $images = array_fill(0, 5, 'data:image/jpeg;base64,AAAA');

        $this->actingAs($user)
            ->post('/register/biometric', ['images' => $images])
            ->assertRedirect(route('dashboard'));

        $fresh = $user->fresh();
        $this->assertTrue($fresh->has_biometric);
        $this->assertNotNull($fresh->face_embedding);
    }

    public function test_store_biometric_fails_when_service_down(): void
    {
        Http::fake([
            '*/health' => Http::response('', 500),
        ]);

        $user = User::factory()->create(['has_biometric' => false]);
        $images = array_fill(0, 5, 'data:image/jpeg;base64,AAAA');

        $this->actingAs($user)
            ->post('/register/biometric', ['images' => $images])
            ->assertSessionHasErrors('biometric');

        $this->assertFalse($user->fresh()->has_biometric);
    }

    public function test_store_biometric_requires_minimum_samples(): void
    {
        Http::fake(['*/health' => Http::response(['status' => 'ok'], 200)]);

        $user = User::factory()->create(['has_biometric' => false]);

        $this->actingAs($user)
            ->post('/register/biometric', ['images' => ['only-one']])
            ->assertSessionHasErrors('images');
    }

    // ---------- Guard / middleware ----------

    public function test_guest_redirected_to_login_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_unverified_user_blocked_from_dashboard(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('otp.show'));
    }

    public function test_jemaat_cannot_access_pengurus_area(): void
    {
        $user = User::factory()->create(['role' => 'jemaat']);
        $this->actingAs($user)->get('/pengurus/jadwal')->assertForbidden();
    }

    public function test_pengurus_can_access_pengurus_area(): void
    {
        $user = User::factory()->create(['role' => 'pengurus']);
        $this->actingAs($user)->get('/pengurus/jadwal')->assertOk();
    }

    public function test_logout_works(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/logout')->assertRedirect(route('landing'));
        $this->assertGuest();
    }
}
