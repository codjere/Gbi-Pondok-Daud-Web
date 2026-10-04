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

        // Verifikasi kredensial TANPA langsung login. OTP wajib sebagai faktor
        // kedua (2FA) sebelum benar-benar masuk.
        if (! Auth::validate($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        // Simpan sebagai "pending login" di session + ingat preferensi remember.
        $request->session()->put('pending_login_user_id', $user->id);
        $request->session()->put('pending_login_remember', $request->boolean('remember'));

        // Kirim OTP ke email lalu arahkan ke halaman verifikasi OTP.
        $this->otpService->send($user);

        return redirect()->route('otp.show')
            ->with('success', 'Kata sandi benar. Masukkan kode OTP yang dikirim ke '.$this->maskEmail($user->email).' untuk masuk.');
    }

    /**
     * FACE LOGIN — langkah 1: pindai wajah (1:N) lalu kirim OTP ke email
     * user yang terdeteksi. Dipanggil via AJAX dari halaman login (auto-scan).
     */
    public function faceLogin(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'string'],
        ]);

        if (! $this->faceService->isAvailable()) {
            return response()->json([
                'success' => false,
                'code' => 'service_down',
                'message' => 'Layanan pengenalan wajah tidak aktif. Silakan login dengan kata sandi.',
            ], 503);
        }

        $result = $this->faceService->extractEmbeddingBatch([$data['image']]);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'recognized' => false,
                'code' => 'no_face',
                'message' => 'Wajah tidak terdeteksi. Pastikan wajah terlihat jelas dan pencahayaan cukup.',
            ], 422);
        }

        $knownUsers = User::where('has_biometric', true)
            ->whereNotNull('face_embedding')
            ->get(['id', 'name', 'face_embedding'])
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'embedding' => $u->face_embedding,
            ])
            ->all();

        if (empty($knownUsers)) {
            return response()->json([
                'success' => false,
                'recognized' => false,
                'code' => 'no_registered',
                'message' => 'Belum ada pengguna dengan data wajah terdaftar.',
            ], 422);
        }

        $match = $this->faceService->findBestMatch($result['embedding'], $knownUsers);

        if ($match === null) {
            return response()->json([
                'success' => false,
                'recognized' => false,
                'code' => 'no_match',
                'message' => 'Wajah tidak dikenali. Coba lagi atau gunakan kata sandi.',
            ]);
        }

        $user = User::find($match['user_id']);
        if (! $user) {
            return response()->json([
                'success' => false,
                'recognized' => false,
                'code' => 'no_match',
                'message' => 'Data pengguna tidak ditemukan.',
            ], 422);
        }

        // Simpan user yang cocok sebagai "pending" (belum login penuh) di session,
        // lalu kirim OTP ke email-nya sebagai faktor kedua.
        $request->session()->put('face_login_user_id', $user->id);
        $this->otpService->send($user);

        return response()->json([
            'success' => true,
            'recognized' => true,
            'name' => $user->name,
            'similarity' => round($match['similarity'] * 100, 1),
            'redirect' => route('face.otp'),
            'message' => "Wajah dikenali sebagai {$user->name}. Kode OTP dikirim ke email.",
        ]);
    }

    /**
     * FACE LOGIN — langkah 2: halaman input OTP untuk penyelesaian login wajah.
     */
    public function showFaceOtp(Request $request)
    {
        $userId = $request->session()->get('face_login_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Sesi login wajah berakhir. Silakan pindai wajah kembali.');
        }

        // Email disamarkan sebagian untuk ditampilkan
        $masked = $this->maskEmail($user->email);

        return view('auth.face-otp', ['maskedEmail' => $masked]);
    }

    /**
     * FACE LOGIN — langkah 3: verifikasi OTP lalu login user.
     */
    public function verifyFaceOtp(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('face_login_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Sesi login wajah berakhir. Silakan pindai wajah kembali.');
        }

        if (! $this->otpService->verify($user, $data['code'])) {
            return back()->withErrors([
                'code' => 'Kode OTP salah atau sudah kedaluwarsa. Minta kode baru jika perlu.',
            ]);
        }

        // OTP valid -> login penuh
        Auth::login($user);
        $request->session()->forget('face_login_user_id');
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Login wajah berhasil! Selamat datang, '.$user->name.'.');
    }

    /**
     * FACE LOGIN — kirim ulang OTP untuk penyelesaian login wajah.
     */
    public function resendFaceOtp(Request $request)
    {
        $userId = $request->session()->get('face_login_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Sesi login wajah berakhir. Silakan pindai wajah kembali.');
        }

        $this->otpService->send($user);

        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($name === '' || $domain === '') {
            return $email;
        }
        $visible = mb_substr($name, 0, min(2, mb_strlen($name)));
        return $visible.str_repeat('*', max(1, mb_strlen($name) - mb_strlen($visible))).'@'.$domain;
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

    /**
     * Tentukan user & konteks OTP:
     *  - 'login'    : login password (belum auth, ada pending_login_user_id)
     *  - 'register' : verifikasi email saat registrasi (user sudah auth)
     * Return [User|null $user, string|null $context].
     */
    private function resolveOtpSubject(Request $request): array
    {
        $pendingId = $request->session()->get('pending_login_user_id');
        if ($pendingId) {
            return [User::find($pendingId), 'login'];
        }

        if (Auth::check()) {
            return [Auth::user(), 'register'];
        }

        return [null, null];
    }

    public function showOtp(Request $request)
    {
        [$user, $context] = $this->resolveOtpSubject($request);

        if (! $user) {
            return redirect()->route('login');
        }

        // Konteks registrasi: kalau email sudah terverifikasi, lanjut ke langkah berikutnya.
        if ($context === 'register' && $user->email_verified_at) {
            if (! $user->has_biometric) {
                return redirect()->route('register.biometric')
                    ->with('info', 'Email sudah terverifikasi. Silakan lengkapi perekaman wajah.');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.otp', [
            'otpContext' => $context,
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        [$user, $context] = $this->resolveOtpSubject($request);

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Sesi berakhir. Silakan masuk kembali.');
        }

        if (! $this->otpService->verify($user, $data['code'])) {
            return back()->withErrors([
                'code' => 'Kode OTP salah atau sudah kedaluwarsa. Minta kode baru jika perlu.',
            ]);
        }

        if ($context === 'login') {
            // 2FA login password: sekarang baru login penuh.
            $remember = (bool) $request->session()->pull('pending_login_remember', false);
            Auth::login($user, $remember);
            $request->session()->forget('pending_login_user_id');
            $request->session()->regenerate();

            if (! $user->has_biometric) {
                return redirect()->route('register.biometric')
                    ->with('info', 'Login berhasil. Silakan lengkapi perekaman wajah untuk akses penuh.');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Login berhasil! Selamat datang, '.$user->name.'.');
        }

        // Konteks registrasi: user sudah auth, ini verifikasi email.
        $request->session()->regenerate();

        if (! $user->has_biometric) {
            return redirect()->route('register.biometric')
                ->with('success', 'Email berhasil diverifikasi. Lanjutkan perekaman wajah.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Verifikasi berhasil! Selamat datang.');
    }

    public function resendOtp(Request $request)
    {
        [$user, $context] = $this->resolveOtpSubject($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if ($context === 'register' && $user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $this->otpService->send($user);

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
