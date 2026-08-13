<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(
        private FaceRecognitionService $faceService
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

        if (! Auth::user()->has_biometric) {
            return redirect()->route('register.biometric');
        }

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
            'status' => ['required', 'in:jemaat,pengurus,pelayan'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['status'],
            'has_biometric' => false,
        ]);

        Auth::login($user);

        return redirect()->route('register.biometric')
            ->with('success', 'Data profil tersimpan. Lanjutkan perekaman wajah.');
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
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
