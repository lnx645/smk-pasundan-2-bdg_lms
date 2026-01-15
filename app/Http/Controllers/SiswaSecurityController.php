<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SiswaSecurityController extends Controller
{
    public function __construct() {}
    public function __invoke()
    {
        return inertia('siswa/user/keamanan/index');
    }
    public function index()
    {
        return inertia('auth/reset-password', []);
    }
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required', // Wajib konfirmasi password
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // 1. Verifikasi Password user saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini salah.',
            ]);
        }

        // 2. Simpan Pertanyaan & Jawaban (di-Hash)
        $user->security_question = $request->question;
        // PENTING: Lowercase dulu sebelum hash agar tidak sensitif huruf kapital
        $user->security_answer = Hash::make(strtolower(trim($request->answer)));

        $user->save();

        return back()->with('success', 'Pertanyaan keamanan berhasil disimpan!');
    }
    // Tahap 1: Cek NIS (Query User berdasarkan relasi Siswa)
    public function checkNisn(Request $request)
    {
        // Validasi input
        $request->validate(['nis' => 'required']);

        // CARI USER DIMANA RELASI SISWA MEMILIKI NIS TERSEBUT
        $user = User::whereHas('siswa', function (Builder $query) use ($request) {
            $query->where('nis', $request->nis);
        })->first();

        // Cek apakah User ketemu
        if (!$user) {
            return response()->json(['message' => 'NIS tidak ditemukan dalam sistem.'], 404);
        }

        // Cek apakah User sudah set Security Question (kolom ada di tabel users)
        if (!$user->security_question) {
            return response()->json(['message' => 'Akun ini belum mengatur Pertanyaan Keamanan. Silahkan isi halaman keamanan sesuai dengan panduan yang diberikan'], 400);
        }

        return response()->json([
            'status' => 'success',
            'question' => $user->security_question,
            'nis' => $request->nis // Kembalikan NIS untuk step selanjutnya
        ]);
    }

    // Tahap 2: Verifikasi Jawaban & Ganti Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'nis' => 'required', // Konsisten pakai 'nis'
            'answer' => 'required',
            'new_password' => 'required|min:6'
        ]);

        // CARI USER LAGI BERDASARKAN NIS DI TABEL SISWA
        $user = User::whereHas('siswa', function (Builder $query) use ($request) {
            $query->where('nis', $request->nis);
        })->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak valid.'], 404);
        }

        // Cek Jawaban (Case Insensitive)
        $inputAnswer = strtolower(trim($request->answer));

        // Pastikan kolom security_answer ada di tabel users
        if (!Hash::check($inputAnswer, $user->security_answer)) {
            return response()->json(['message' => 'Jawaban salah! Coba lagi.'], 401);
        }

        // Update Password di tabel users
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah! Silakan login.']);
    }
}
