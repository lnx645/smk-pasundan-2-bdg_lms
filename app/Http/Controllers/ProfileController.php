<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('profile/index', [
            'user' => $request->user(),
            'status' => session('status'),
        ]);
    }

    // Update Nama, Email, dan Foto
    public function update(Request $request)
    {
        $user = $request->user();

        // Pastikan relasi siswa dimuat jika belum
        // (Opsional, tapi membantu memastikan data tersedia)
        if (!$user->relationLoaded('siswa')) {
            $user->load('siswa');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // 1. Update Data User (Tabel Users)
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        $user->save();

        // 2. Update Foto (Tabel Siswa)
        if ($request->hasFile('photo')) {
            // Cek apakah user ini benar-benar punya data siswa
            if ($user->siswa) {

                // Hapus foto lama jika ada
                if ($user->siswa->pas_photo) {
                    // Gunakan disk 'public' karena saat store pakai 'public'
                    Storage::disk('public')->delete($user->siswa->pas_photo);
                }

                // Simpan foto baru
                $fotoPath = $request->file('photo')->store('profile-photos', 'public');

                // Update kolom di tabel siswa
                $user->siswa()->update([
                    'pas_photo' => $fotoPath
                ]);
            }
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    // Khusus Update Password
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
