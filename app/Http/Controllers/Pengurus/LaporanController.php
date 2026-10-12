<?php

namespace App\Http\Controllers\Pengurus;

use App\Exports\KehadiranExport;
use App\Http\Controllers\Controller;
use App\Models\JadwalIbadah;
use App\Models\Kehadiran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildReport($request);

        return view('pengurus.laporan', $data);
    }

    /**
     * Export daftar hadir satu sesi ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $data = $this->buildReport($request);

        abort_unless($data['jadwal'], 404, 'Sesi ibadah tidak ditemukan.');

        $pdf = Pdf::loadView('pengurus.laporan-pdf', $data)->setPaper('a4', 'portrait');

        $filename = 'laporan-kehadiran-'.$data['jadwal']->tanggal->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export daftar hadir satu sesi ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $data = $this->buildReport($request);

        abort_unless($data['jadwal'], 404, 'Sesi ibadah tidak ditemukan.');

        $filename = 'laporan-kehadiran-'.$data['jadwal']->tanggal->format('Y-m-d').'.xlsx';

        return Excel::download(new KehadiranExport($data['jadwal']), $filename);
    }

    /**
     * Susun seluruh data laporan: sesi terpilih, statistik, breakdown metode,
     * dan tren kehadiran lintas sesi. Dipakai bersama oleh index & export.
     */
    private function buildReport(Request $request): array
    {
        $jadwals = JadwalIbadah::orderByDesc('tanggal')->get();

        $selectedId = $request->validate([
            'jadwal_id' => ['nullable', 'integer', 'exists:jadwal_ibadah,id'],
        ])['jadwal_id'] ?? $jadwals->first()?->id;

        $jadwal = $selectedId ? JadwalIbadah::find($selectedId) : null;

        $kehadiran = collect();
        $stats = ['hadir' => 0, 'kuota' => 0, 'pct' => 0];
        $metode = ['facenet' => 0, 'manual' => 0, 'facenet_pct' => 0, 'manual_pct' => 0];

        if ($jadwal) {
            $kehadiran = Kehadiran::with('user')
                ->where('jadwal_ibadah_id', $jadwal->id)
                ->orderBy('waktu_checkin')
                ->get();

            $stats['hadir'] = $kehadiran->count();
            $stats['kuota'] = $jadwal->batas_kuota;
            $stats['pct'] = $jadwal->batas_kuota > 0
                ? (int) round(($stats['hadir'] / $jadwal->batas_kuota) * 100)
                : 0;

            // Breakdown metode verifikasi untuk sesi ini.
            $metode['facenet'] = $kehadiran->where('metode_verifikasi', 'facenet')->count();
            $metode['manual'] = $kehadiran->where('metode_verifikasi', 'manual')->count();
            $total = max(1, $stats['hadir']);
            $metode['facenet_pct'] = (int) round(($metode['facenet'] / $total) * 100);
            $metode['manual_pct'] = (int) round(($metode['manual'] / $total) * 100);
        }

        // Tren kehadiran: 10 sesi terakhir (urut lama -> baru untuk grafik garis).
        $trenSource = JadwalIbadah::withCount('kehadiran')
            ->orderByDesc('tanggal')
            ->take(10)
            ->get()
            ->sortBy('tanggal')
            ->values();

        $tren = [
            'labels' => $trenSource->map(fn ($j) => $j->tanggal->format('d M'))->all(),
            'data' => $trenSource->map(fn ($j) => $j->kehadiran_count)->all(),
            'sesi' => $trenSource->map(fn ($j) => $j->nama_sesi)->all(),
        ];

        return compact('jadwals', 'jadwal', 'kehadiran', 'stats', 'metode', 'tren');
    }
}
