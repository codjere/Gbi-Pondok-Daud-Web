<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EmailOtpService;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(
        private FaceRecognitionService $faceService,
        private EmailOtpService $otpService
    ) {}
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Step 1: Check email verification
        if (! $user->email_verified_at) {
            $this->otpService->send($user);
            return redirect()->route('otp.show')
                ->with('success', 'Silakan verifikasi email Anda dengan kode OTP yang dikirim ke '.$user->email);
        }

        // Step 2: Check biometric registration
        if (! $user->has_biometric) {
            return redirect()->route('register.biometric')
                ->with('info', 'Silakan lengkapi perekaman wajah untuk akses penuh.');
        }

        // All checks passed - go to dashboard
        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'status' => ['required', 'in:jemaat,pelayan'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'status.in' => 'Pilih Jemaat atau Pelayan. Akun pengurus tidak bisa dibuat lewat pendaftaran publik.',
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain atau masuk dengan akun yang ada.',
        ]);

        $user = User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['status'],
            'has_biometric' => false,
        ]);

        Auth::login($user);
        $this->otpService->send($user);

        return redirect()->route('otp.show')
            ->with('success', 'Akun dibuat. Masukkan kode OTP yang dikirim ke email Anda.');
    }

    public function showOtp()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // If already verified, redirect to appropriate next step
        if ($user->email_verified_at) {
            if (! $user->has_biometric) {
                return redirect()->route('register.biometric')
                    ->with('info', 'Email sudah terverifikasi. Silakan lengkapi perekaman wajah.');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (! $this->otpService->verify(Auth::user(), $data['code'])) {
            return back()->withErrors([
                'code' => 'Kode OTP salah atau sudah kedaluwarsa. Minta kode baru jika perlu.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // After OTP verification, check if biometric is needed
        if (! $user->has_biometric) {
            return redirect()->route('register.biometric')
                ->with('success', 'Email berhasil diverifikasi. Lanjutkan perekaman wajah.');
        }

        // All complete - go to dashboard
        return redirect()->route('dashboard')
            ->with('success', 'Login berhasil! Selamat datang.');
    }

    public function resendOtp()
    {
        if (Auth::user()->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $this->otpService->send(Auth::user());

        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }

    public function showBiometric()
    {
        $user = Auth::user();

        if ($user->has_biometric) {
            return redirect()->route('dashboard');
        }

        $serviceOnline = $this->faceService->isAvailable();

        return view('auth.biometric', compact('serviceOnline'));
    }

    public function storeBiometric(Request $request)
    {
        $user = Auth::user();

        if ($user->has_biometric) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'images' => ['required', 'array', 'min:'.config('facenet.samples_required')],
            'images.*' => ['required', 'string'],
        ]);

        if (! $this->faceService->isAvailable()) {
            return back()->withErrors([
                'biometric' => 'Layanan FaceNet tidak aktif. Jalankan face-service/start.bat terlebih dahulu.',
            ]);
        }

        $result = $this->faceService->extractEmbeddingBatch($data['images']);

        if ($result === null) {
            return back()->withErrors([
                'biometric' => 'Gagal mengekstrak wajah. Pastikan wajah terlihat jelas dan coba lagi.',
            ]);
        }

        $user->update([
            'face_embedding' => $result['embedding'],
            'has_biometric' => true,
        ]);

        return redirect()->route('dashboard')
            ->with('success', "Registrasi berhasil! Vektor wajah FaceNet ({$result['samples_used']} sampel) tersimpan.");
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('landing')
            ->with('success', 'Anda telah logout.');
    }
}
