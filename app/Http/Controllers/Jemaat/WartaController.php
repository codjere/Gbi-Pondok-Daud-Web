<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\KontenWarta;

class WartaController extends Controller
{
    public function index()
    {
        $artikels = KontenWarta::published()->latest('published_at')->get();
        $featured = $artikels->first();

        return view('jemaat.warta', compact('artikels', 'featured'));
    }

    public function show(KontenWarta $warta)
    {
        if (! $warta->published_at || $warta->published_at->isFuture()) {
            abort(404);
        }

        return view('jemaat.warta-show', compact('warta'));
    }
}
