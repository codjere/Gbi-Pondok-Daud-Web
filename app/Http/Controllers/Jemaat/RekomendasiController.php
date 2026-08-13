<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\CoolGroup;
use App\Models\TimPelayanan;
use App\Models\UserCoolChoice;
use App\Models\UserPelayananChoice;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekomendasiController extends Controller
{
    public function __construct(
        private RecommendationService $recommendationService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (! $profile?->isComplete()) {
            return redirect()->route('jemaat.kuesioner')
                ->with('info', 'Isi kuesioner profil terlebih dahulu untuk mendapatkan rekomendasi AI.');
        }

        $recommendations = session('recommendations');

        if (! $recommendations) {
            $recommendations = $this->recommendationService->generate($user, $profile);
        }

        $timDetails = TimPelayanan::whereIn(
            'id',
            collect($recommendations['tim_pelayanan'] ?? [])->pluck('id')
        )->get()->keyBy('id');

        $coolDetails = CoolGroup::whereIn(
            'id',
            collect($recommendations['cool_groups'] ?? [])->pluck('id')
        )->get()->keyBy('id');

        return view('jemaat.rekomendasi', compact(
            'profile', 'recommendations', 'timDetails', 'coolDetails'
        ));
    }

    public function pilihTim(Request $request)
    {
        $data = $request->validate([
            'tim_id' => ['required', 'exists:tim_pelayanan,id'],
            'score' => ['nullable', 'numeric'],
        ]);

        UserPelayananChoice::updateOrCreate(
            ['user_id' => Auth::id(), 'tim_pelayanan_id' => $data['tim_id']],
            ['score' => $data['score'] ?? null, 'source' => 'rekomendasi']
        );

        return back()->with('success', 'Pilihan tim pelayanan tersimpan!');
    }

    public function pilihCool(Request $request)
    {
        $data = $request->validate([
            'cool_id' => ['required', 'exists:cool_groups,id'],
            'score' => ['nullable', 'numeric'],
        ]);

        UserCoolChoice::updateOrCreate(
            ['user_id' => Auth::id(), 'cool_group_id' => $data['cool_id']],
            ['score' => $data['score'] ?? null, 'status' => 'tertarik']
        );

        return back()->with('success', 'Minat COOL tersimpan! Pemimpin COOL akan menghubungi Anda.');
    }
}
