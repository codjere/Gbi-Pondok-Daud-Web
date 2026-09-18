<?php

namespace App\Http\Controllers\Jemaat;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        return view('jemaat.profil', [
            'user' => $user,
            'profile' => $profile,
            'minatOptions' => $this->minatOptions(),
            'bakatOptions' => $this->bakatOptions(),
            'hariOptions' => $this->hariOptions(),
            'kecamatanOptions' => $this->kecamatanOptions(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'usia' => ['nullable', 'integer', 'min:10', 'max:100'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'kecamatan' => ['nullable', 'in:'.implode(',', $this->kecamatanOptions())],
            'alamat' => ['nullable', 'string', 'max:255'],
            'minat' => ['nullable', 'array'],
            'minat.*' => ['in:'.implode(',', array_keys($this->minatOptions()))],
            'bakat' => ['nullable', 'array'],
            'bakat.*' => ['in:'.implode(',', array_keys($this->bakatOptions()))],
            'ketersediaan_hari' => ['nullable', 'array'],
            'ketersediaan_hari.*' => ['in:'.implode(',', array_keys($this->hariOptions()))],
            'ketersediaan_waktu' => ['nullable', 'in:pagi,siang,sore,malam,fleksibel'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // Update akun (nama & email; password opsional)
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        // Update / buat profil. Pertahankan status completed_at bila sudah ada.
        $profile = $user->profile;
        $profileData = [
            'usia' => $data['usia'] ?? null,
            'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
            'kecamatan' => $data['kecamatan'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'minat' => $data['minat'] ?? [],
            'bakat' => $data['bakat'] ?? [],
            'ketersediaan_hari' => $data['ketersediaan_hari'] ?? [],
            'ketersediaan_waktu' => $data['ketersediaan_waktu'] ?? null,
        ];

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('jemaat.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    private function minatOptions(): array
    {
        return [
            'musik' => 'Musik & Worship',
            'multimedia' => 'Multimedia & Teknologi',
            'teknologi' => 'Teknologi IT',
            'melayani' => 'Melayani Jemaat',
            'anak_muda' => 'Pelayanan Anak Muda / Youth',
            'doa' => 'Doa & Intercessory',
            'evangelisme' => 'Evangelisme & Outreach',
            'sosial' => 'Pelayanan Sosial',
            'administrasi' => 'Administrasi & Organisasi',
            'kreatif' => 'Seni & Kreatif',
        ];
    }

    private function bakatOptions(): array
    {
        return [
            'vokal' => 'Vokal / Nyanyi',
            'gitar' => 'Gitar',
            'keyboard' => 'Keyboard / Piano',
            'drum' => 'Drum',
            'fotografi' => 'Fotografi',
            'video' => 'Videografi / Live Stream',
            'desain' => 'Desain Grafis',
            'public_speaking' => 'Public Speaking / MC',
            'organisasi' => 'Kepemimpinan & Organisasi',
            'komputer' => 'Komputer / Programming',
        ];
    }

    private function hariOptions(): array
    {
        return [
            'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
            'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu',
        ];
    }

    private function kecamatanOptions(): array
    {
        // Wilayah/KUL GBI Pondok Daud di Kendari, Sulawesi Tenggara.
        return [
            'Onembute', 'Ponggolaka', 'Anggoya', 'Lepo-Lepo',
            'Kambu', 'Poasia', 'Kadia', 'Wua-Wua',
            'Mandonga', 'Baruga', 'Puuwatu', 'Kendari Barat',
        ];
    }
}
