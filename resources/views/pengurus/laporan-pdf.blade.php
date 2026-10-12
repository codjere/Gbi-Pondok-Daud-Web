<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; margin: 0; }
        .header { text-align: center; border-bottom: 2px solid #0e7490; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 18px; color: #065f46; }
        .header p { margin: 2px 0; font-size: 11px; color: #6b7280; }
        .meta { width: 100%; margin-bottom: 14px; font-size: 11px; }
        .meta td { padding: 2px 4px; vertical-align: top; }
        .meta .label { color: #6b7280; width: 120px; }
        .stats { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
        .stats td { border: 1px solid #d1d5db; padding: 8px; text-align: center; }
        .stats .num { font-size: 18px; font-weight: bold; color: #065f46; }
        .stats .cap { font-size: 10px; color: #6b7280; text-transform: uppercase; }
        table.list { width: 100%; border-collapse: collapse; }
        table.list th { background: #ecfeff; border: 1px solid #d1d5db; padding: 6px; text-align: left; font-size: 10px; text-transform: uppercase; color: #374151; }
        table.list td { border: 1px solid #d1d5db; padding: 6px; }
        .badge { font-size: 10px; padding: 1px 5px; border-radius: 8px; }
        .badge-face { background: #d1fae5; color: #065f46; }
        .badge-manual { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 24px; font-size: 10px; color: #9ca3af; text-align: center; }
        .empty { text-align: center; padding: 30px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>GBI Pondok Daud</h1>
        <p>Laporan Kehadiran Jemaat &middot; Verifikasi Pengenalan Wajah (FaceNet)</p>
    </div>

    <table class="meta">
        <tr><td class="label">Sesi Ibadah</td><td>: {{ $jadwal->nama_sesi }}</td></tr>
        <tr><td class="label">Tanggal</td><td>: {{ $jadwal->tanggal->translatedFormat('l, d F Y') }}</td></tr>
        <tr><td class="label">Jam</td><td>: {{ $jadwal->jamMulaiFormatted() }} - {{ $jadwal->jamSelesaiFormatted() }} WIB</td></tr>
        <tr><td class="label">Pembicara</td><td>: {{ $jadwal->pembicara ?? '-' }}</td></tr>
        <tr><td class="label">Lokasi</td><td>: {{ $jadwal->lokasi ?? '-' }}</td></tr>
    </table>

    <table class="stats">
        <tr>
            <td><div class="num">{{ $stats['hadir'] }}</div><div class="cap">Total Hadir</div></td>
            <td><div class="num">{{ $stats['kuota'] }}</div><div class="cap">Kuota</div></td>
            <td><div class="num">{{ $stats['pct'] }}%</div><div class="cap">Tingkat Kehadiran</div></td>
            <td><div class="num">{{ $metode['facenet'] }}</div><div class="cap">Via Wajah</div></td>
            <td><div class="num">{{ $metode['manual'] }}</div><div class="cap">Manual</div></td>
        </tr>
    </table>

    @if ($kehadiran->isEmpty())
        <div class="empty">Belum ada data kehadiran untuk sesi ibadah ini.</div>
    @else
        <table class="list">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th style="width: 90px;">Check-in</th>
                    <th style="width: 90px;">Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kehadiran as $i => $h)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $h->user->name }}</td>
                        <td>{{ $h->user->email }}</td>
                        <td>{{ $h->waktu_checkin->format('H:i') }} WIB</td>
                        <td>
                            @if ($h->metode_verifikasi === 'facenet')
                                <span class="badge badge-face">Wajah</span>
                            @else
                                <span class="badge badge-manual">Manual</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dicetak {{ now()->translatedFormat('d F Y, H:i') }} WIB &middot; Sistem Informasi GBI Pondok Daud
    </div>
</body>
</html>
