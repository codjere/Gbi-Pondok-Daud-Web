<?php

namespace App\Exports;

use App\Models\JadwalIbadah;
use App\Models\Kehadiran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KehadiranExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private JadwalIbadah $jadwal) {}

    public function collection()
    {
        return Kehadiran::with('user')
            ->where('jadwal_ibadah_id', $this->jadwal->id)
            ->orderBy('waktu_checkin')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama', 'Email', 'Peran', 'Waktu Check-in', 'Metode Verifikasi'];
    }

    /** @param Kehadiran $row */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->user?->name ?? '-',
            $row->user?->email ?? '-',
            ucfirst($row->user?->role ?? '-'),
            $row->waktu_checkin?->format('d/m/Y H:i').' WIB',
            $row->metode_verifikasi === 'facenet' ? 'Pengenalan Wajah' : 'Manual',
        ];
    }
}
