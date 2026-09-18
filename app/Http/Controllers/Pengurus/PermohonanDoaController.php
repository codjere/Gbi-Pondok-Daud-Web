<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\PermohonanDoa;
use Illuminate\Http\Request;

class PermohonanDoaController extends Controller
{
    public function index()
    {
        $permohonanDoa = PermohonanDoa::with('user')
            ->orderByDesc('created_at')
            ->get();

        return view('pengurus.permohonan-doa', compact('permohonanDoa'));
    }

    public function updateStatus(Request $request, PermohonanDoa $permohonanDoa)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,diproses,selesai'],
        ]);

        $permohonanDoa->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Status permohonan doa berhasil diperbarui.');
    }

    public function destroy(PermohonanDoa $permohonanDoa)
    {
        $permohonanDoa->delete();

        return back()->with('success', 'Permohonan doa berhasil dihapus.');
    }
}
