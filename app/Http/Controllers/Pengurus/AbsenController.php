<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\JadwalIbadah;
use App\Models\Kehadiran;
use App\Models\User;
use App\Services\FaceRecognitionService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AbsenController extends Controller
{
    public function __construct(
        private FaceRecognitionService $faceService
    ) {}

    public function index(): View
    {
        $jadwals = JadwalIbadah::aktif()->terbaru()->get();
        $serviceOnline = $this->faceService->isAvailable();
        $registeredCount = User::where('has_biometric', true)->count();

        return view('pengurus.absen', compact('jadwals', 'serviceOnline', 'registeredCount'));
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'string'],
            'jadwal_id' => ['required', 'exists:jadwal_ibadah,id'],
        ]);

        if (! $this->faceService->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan FaceNet tidak aktif. Jalankan face-service/start.bat terlebih dahulu.',
            ], 503);
        }

        $result = $this->faceService->extractEmbeddingBatch([$data['image']]);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak terdeteksi. Pastikan wajah terlihat jelas di kamera.',
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
                'message' => 'Belum ada pelayan/jemaat dengan data wajah terdaftar.',
            ], 422);
        }

        $match = $this->faceService->findBestMatch($result['embedding'], $knownUsers);

        if ($match === null) {
            return response()->json([
                'success' => false,
                'recognized' => false,
                'message' => 'Wajah tidak dikenali. Pastikan sudah registrasi biometrik.',
            ]);
        }

        try {
            $alreadyCheckedIn = DB::transaction(function () use ($match, $data) {
                $exists = Kehadiran::where('user_id', $match['user_id'])
                    ->where('jadwal_ibadah_id', $data['jadwal_id'])
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    return true;
                }

                Kehadiran::create([
                    'user_id' => $match['user_id'],
                    'jadwal_ibadah_id' => $data['jadwal_id'],
                    'waktu_checkin' => now(),
                    'metode_verifikasi' => 'facenet',
                ]);

                return false;
            });
        } catch (UniqueConstraintViolationException) {
            $alreadyCheckedIn = true;
        }

        if ($alreadyCheckedIn) {
            return response()->json([
                'success' => true,
                'recognized' => true,
                'already_checked_in' => true,
                'name' => $match['name'],
                'similarity' => round($match['similarity'] * 100, 1),
                'message' => "{$match['name']} sudah absen untuk sesi ini.",
            ]);
        }

        return response()->json([
            'success' => true,
            'recognized' => true,
            'already_checked_in' => false,
            'name' => $match['name'],
            'similarity' => round($match['similarity'] * 100, 1),
            'message' => "Absen berhasil! Selamat datang, {$match['name']}.",
        ]);
    }

    public function todayList(Request $request): JsonResponse
    {
        $data = $request->validate([
            'jadwal_id' => ['required', 'exists:jadwal_ibadah,id'],
        ]);

        $kehadiran = Kehadiran::with('user')
            ->where('jadwal_ibadah_id', $data['jadwal_id'])
            ->orderByDesc('waktu_checkin')
            ->get()
            ->map(fn (Kehadiran $k) => [
                'name' => $k->user->name,
                'role' => $k->user->role,
                'time' => $k->waktu_checkin->format('H:i'),
            ]);

        return response()->json(['data' => $kehadiran]);
    }

    public function manual(Request $request)
    {
        $data = $request->validate([
            'jadwal_id' => ['required', 'exists:jadwal_ibadah,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $alreadyCheckedIn = DB::transaction(function () use ($data) {
                $exists = Kehadiran::where('user_id', $data['user_id'])
                    ->where('jadwal_ibadah_id', $data['jadwal_id'])
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    return true;
                }

                Kehadiran::create([
                    'user_id' => $data['user_id'],
                    'jadwal_ibadah_id' => $data['jadwal_id'],
                    'waktu_checkin' => now(),
                    'metode_verifikasi' => 'manual',
                ]);

                return false;
            });
        } catch (UniqueConstraintViolationException) {
            $alreadyCheckedIn = true;
        }

        if ($alreadyCheckedIn) {
            return back()->with('error', 'Jemaat ini sudah absen untuk sesi ini.');
        }

        return back()->with('success', 'Absen manual berhasil ditambahkan.');
    }

    public function participants(Request $request)
    {
        $data = $request->validate([
            'jadwal_id' => ['required', 'exists:jadwal_ibadah,id'],
        ]);

        $jadwal = JadwalIbadah::findOrFail($data['jadwal_id']);

        // Get all reservations for this session
        $reservations = $jadwal->reservasi()->with('user')->get();

        // Get all attendance records for this session
        $attendance = $jadwal->kehadiran()->with('user')->get();

        // Get all users who could potentially attend
        $allUsers = User::all();

        return view('pengurus.participants', compact('jadwal', 'reservations', 'attendance', 'allUsers'));
    }
}
