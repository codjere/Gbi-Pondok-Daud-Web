<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\PermohonanDoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermohonanDoaController extends Controller
{
    public function index()
    {
        return view('jemaat.doa');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pokok' => ['required', 'string', 'min:10'],
            'anonim' => ['required', 'in:0,1'],
        ]);

        PermohonanDoa::create([
            'user_id' => Auth::id(),
            'pokok_doa' => $data['pokok'],
            'is_anonymous' => (bool) $data['anonim'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permohonan doa Anda telah terkirim dan akan didoakan oleh tim pendoa syafaat.');
    }
}
