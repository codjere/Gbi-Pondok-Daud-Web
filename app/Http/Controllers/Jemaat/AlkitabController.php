<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\Ayat;
use App\Models\Kitab;
use App\Models\Pasal;
use App\Models\ReadingPlan;
use App\Models\ReadingPlanItem;
use App\Models\UserReadingHistory;
use App\Models\UserReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlkitabController extends Controller
{
    /**
     * HALAMAN UTAMA ALKITAB
     * Menampilkan daftar kitab dan fitur-fitur Alkitab
     */
    public function index()
    {
        // Ambil semua kitab yang sudah ada
        $kitabs = Kitab::urutan()->get();

        // Ambil semua reading plan yang aktif
        $readingPlans = ReadingPlan::aktif()->get();

        // Cek apakah user punya progress bacaan
        $userProgress = Auth::check()
            ? Auth::user()->readingProgress()->with('readingPlan')->get()
            : collect();

        return view('jemaat.alkitab', compact('kitabs', 'readingPlans', 'userProgress'));
    }

    /**
     * HALAMAN BACA PASAL
     * Menampilkan isi pasal dengan ayat-ayatnya
     */
    public function bacaPasal($kitabId, $nomorPasal)
    {
        // Cari kitab berdasarkan ID
        $kitab = Kitab::findOrFail($kitabId);

        $nomorPasal = (int) $nomorPasal;

        // Validasi nomor pasal masih dalam rentang kitab.
        if ($nomorPasal < 1 || $nomorPasal > $kitab->jumlah_pasal) {
            abort(404, 'Pasal tidak ditemukan.');
        }

        // Cari pasal. Jika record-nya belum ada di database (data Alkitab belum
        // lengkap), buat record pasal secara on-the-fly supaya halaman tetap
        // tampil (tidak kosong / 404) — ayatnya bisa kosong dan ditangani view.
        $pasal = Pasal::firstOrCreate(
            ['kitab_id' => $kitab->id, 'nomor_pasal' => $nomorPasal],
            ['jumlah_ayat' => 0]
        );

        // Ambil semua ayat dalam pasal ini, diurutkan berdasarkan nomor
        $ayats = $pasal->ayat;

        // Navigasi pasal sebelumnya / berikutnya dalam kitab yang sama
        $pasalSebelumnya = $nomorPasal > 1 ? $nomorPasal - 1 : null;
        $pasalBerikutnya = $nomorPasal < $kitab->jumlah_pasal ? $nomorPasal + 1 : null;

        // Track jika user login - catat history bacaan
        if (Auth::check()) {
            UserReadingHistory::create([
                'user_id' => Auth::id(),
                'pasal_id' => $pasal->id,
                'waktu_baca' => now(),
                'selesai_baca' => false, // Belum selesai dibaca
            ]);
        }

        return view('jemaat.alkitab-baca', compact('kitab', 'pasal', 'ayats', 'pasalSebelumnya', 'pasalBerikutnya'));
    }

    /**
     * SELESAI BACA PASAL
     * Dipanggil ketika user selesai membaca pasal
     */
    public function selesaiBaca(Request $request)
    {
        $data = $request->validate([
            'pasal_id' => ['required', 'exists:pasal,id'],
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        // Update history bacaan terakhir menjadi selesai
        $history = UserReadingHistory::where('user_id', Auth::id())
            ->where('pasal_id', $data['pasal_id'])
            ->latest()
            ->first();

        if ($history) {
            $history->update(['selesai_baca' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Progress bacaan tersimpan.']);
    }

    /**
     * HALAMAN READING PLAN
     * Menampilkan plan bacaan yang tersedia
     */
    public function readingPlans()
    {
        $readingPlans = ReadingPlan::aktif()->get();

        // Progress user untuk setiap plan
        $userProgress = Auth::check()
            ? Auth::user()->readingProgress()->with('readingPlan')->get()->keyBy('reading_plan_id')
            : collect();

        return view('jemaat.alkitab-plans', compact('readingPlans', 'userProgress'));
    }

    /**
     * MULAI READING PLAN
     * User memulai plan bacaan baru
     */
    public function mulaiPlan(Request $request)
    {
        $data = $request->validate([
            'reading_plan_id' => ['required', 'exists:reading_plans,id'],
        ]);

        if (!Auth::check()) {
            return back()->with('error', 'Silakan login terlebih dahulu untuk memulai reading plan.');
        }

        $user = Auth::user();
        $plan = ReadingPlan::findOrFail($data['reading_plan_id']);

        // Cek apakah user sudah punya progress untuk plan ini
        $existingProgress = UserReadingProgress::where('user_id', $user->id)
            ->where('reading_plan_id', $plan->id)
            ->first();

        if ($existingProgress) {
            // Sudah pernah mulai -> langsung arahkan ke halaman detail plannya
            // (bukan sekadar kembali) supaya tombol terasa responsif.
            return redirect()->route('jemaat.alkitab.plan', $plan->id)
                ->with('info', 'Anda sudah memulai reading plan ini. Lanjutkan bacaan Anda.');
        }

        // Buat progress baru
        UserReadingProgress::create([
            'user_id' => $user->id,
            'reading_plan_id' => $plan->id,
            'hari_terakhir' => 0,
            'tanggal_mulai' => now(),
            'selesai' => false,
        ]);

        return redirect()->route('jemaat.alkitab.plan', $plan->id)
            ->with('success', 'Selamat memulai reading plan ' . $plan->nama . '!');
    }

    /**
     * HALAMAN DETAIL READING PLAN
     * Menampilkan jadwal bacaan hari ini dan progress
     */
    public function detailPlan($planId)
    {
        $plan = ReadingPlan::findOrFail($planId);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Cari progress user untuk plan ini
        $progress = UserReadingProgress::where('user_id', $user->id)
            ->where('reading_plan_id', $plan->id)
            ->first();

        if (!$progress) {
            return redirect()->route('jemaat.alkitab.plans')
                ->with('error', 'Anda belum memulai reading plan ini.');
        }

        // Hitung hari berikutnya yang harus dibaca
        $hariBerikutnya = $progress->hari_terakhir + 1;

        // Ambil item bacaan untuk hari berikutnya
        $bacaanHariIni = ReadingPlanItem::where('reading_plan_id', $plan->id)
            ->where('hari_ke', $hariBerikutnya)
            ->first();

        // Apakah plan BENAR-BENAR sudah selesai? Hanya bila ditandai selesai
        // ATAU hari yang sudah dibaca sudah mencapai/melewati durasi plan.
        // (Jangan menganggap "selesai" hanya karena item bacaan hari itu belum ada.)
        $planSelesai = $progress->selesai || $progress->hari_terakhir >= $plan->durasi_hari;

        // Apakah bacaan untuk hari ini belum tersedia di database (plan belum
        // dilengkapi jadwal harian), padahal plan belum selesai.
        $bacaanBelumTersedia = ! $planSelesai && ! $bacaanHariIni;

        if ($planSelesai) {
            $hariBerikutnya = null;
        }

        return view('jemaat.alkitab-plan-detail', compact(
            'plan', 'progress', 'bacaanHariIni', 'hariBerikutnya', 'planSelesai', 'bacaanBelumTersedia'
        ));
    }

    /**
     * SELESAI BACAAN HARI INI
     * Menandai bahwa user sudah selesai bacaan hari ini
     */
    public function selesaiHarian(Request $request)
    {
        $data = $request->validate([
            'reading_plan_id' => ['required', 'exists:reading_plans,id'],
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        $user = Auth::user();
        $plan = ReadingPlan::findOrFail($data['reading_plan_id']);

        // Update progress
        $progress = UserReadingProgress::where('user_id', $user->id)
            ->where('reading_plan_id', $plan->id)
            ->first();

        if ($progress) {
            $progress->increment('hari_terakhir');

            // Cek apakah sudah selesai semua
            if ($progress->hari_terakhir >= $plan->durasi_hari) {
                $progress->update([
                    'selesai' => true,
                    'tanggal_selesai' => now(),
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Progress hari ini tersimpan!']);
    }

    /**
     * PENCARIAN AYAT
     * Mencari ayat berdasarkan kata kunci
     */
    public function cari(Request $request)
    {
        $data = $request->validate([
            'kata_kunci' => ['required', 'min:2'],
        ]);

        $kataKunci = $data['kata_kunci'];

        // Cari ayat yang mengandung kata kunci
        $ayats = Ayat::where('isi_ayat', 'like', '%' . $kataKunci . '%')
            ->with('pasal.kitab')
            ->limit(50) // Batasi hasil 50 ayat
            ->get();

        return view('jemaat.alkitab-cari', compact('ayats', 'kataKunci'));
    }
}
