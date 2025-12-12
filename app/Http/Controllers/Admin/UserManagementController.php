<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\EmailGenerator;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Matpel;
use App\Models\Pengajaran;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function editSiswa(string $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        $kelasList = Kelas::select('id', 'nama', 'tingkat')->get();
        return Inertia::render('admin/user-management/siswa/edit', [
            'siswa'     => $siswa,
            'kelasList' => $kelasList
        ]);
    }
    public function updateSiswa(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'name'          => 'required',
            'nis'           => ['required'],
            'agama'         => 'required',
            'tahun_masuk'   => 'required',
            'tingkat'       => 'required',
            'kelas_id'      => 'required',
            'status'        => 'required',
            'pas_photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($request, $siswa) {
            $siswa->user->update([
                'name' => $request->name
            ]);

            if ($request->hasFile('pas_photo')) {
                if ($siswa->pas_photo && $siswa->pas_photo !== 'siswa.png' && Storage::disk('public')->exists($siswa->pas_photo)) {
                    Storage::disk('public')->delete($siswa->pas_photo);
                }

                $photoPath = $request->file('pas_photo')->store('siswa-photos', 'public');
                $siswa->pas_photo = $photoPath;
            }

            $siswa->update([
                'nis'           => $request->nis,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama'         => $request->agama,
                'tahun_masuk'   => $request->tahun_masuk,
                'tingkat'       => $request->tingkat,
                'kelas_id'      => $request->kelas_id,
                'status'        => $request->status,
            ]);
        });

        return to_route('admin.user-management.siswa')->with('success', 'Data siswa berhasil diperbarui');
    }
    public function tambahGuru()
    {
        return inertia('admin/user-management/guru/tambah');
    }
    public function index()
    {
        return inertia('admin/user-management/index');
    }
    public function simpanGuru(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nip'            => 'required|unique:gurus,nip|numeric', // NIP harus unik di tabel gurus
            'name'           => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:L,P',
            'status'         => 'required',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // name field di vue adalah 'foto'
            'gelar_depan'    => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
        ]);

        $user = DB::transaction(function () use ($request) {

            $emailGenerated = EmailGenerator::generateEmailDomain($request->nip);
            $password = $request->nip . "-" . Carbon::now()->format("Y");

            $userData = [
                'name'     => $request->name,
                'email'    => $emailGenerated,
                'password' => Hash::make($password), // Disarankan di-hash
            ];

            $user = User::create($userData);

            $photoPath = null;
            if ($request->hasFile('foto')) {
                // Simpan ke storage public/guru-photos
                $photoPath = $request->file('foto')->store('guru-photos', 'public');
            }

            // Create Data Guru
            Guru::create([
                'nip'            => $request->nip,
                'user_id'        => $user->id,
                'gelar_depan'    => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'status'         => $request->status, // Aktif/Nonaktif
                'foto'           => $photoPath,       // Sesuaikan nama kolom di DB (misal: pas_photo atau foto)
            ]);

            return $user;
        });

        // 3. Return Response
        if ($user) {
            return back()->with([
                'success' => "Data Guru berhasil ditambahkan. Password default: " . $request->nip . "-" . Carbon::now()->format("Y")
            ]);
        }

        return back()->withErrors([
            'message' => "Gagal menambahkan data guru."
        ]);
    }
    public function simpanSiswa(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis', // Asumsi NIS jadi basis unique
            'pas_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maks 2MB
            'name' => 'required',
            'agama' => 'required',
            "tahun_masuk" => ['required'],
            'tingkat' => ['required'],
            'kelas_id' => ['required'],
            'status' => ['required'],
        ]);
        $user =   DB::transaction(function () use ($request) {
            $emailGenerated = EmailGenerator::generateEmailDomain($request->nis);
            $password = $request->nis . "-" . Carbon::now()->format("Y");
            $user = [
                'name' => $request->name,
                'email' => $emailGenerated,
                'password' => $password,
            ];
            $user =  User::create($user);
            $photoPath = 'siswa.png';
            if ($request->hasFile('pas_photo')) {
                $photoPath = $request->file('pas_photo')->store('siswa-photos', 'public');
            }
            return Siswa::create([
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'tahun_masuk' => $request->tahun_masuk,
                'tingkat' => $request->tingkat,
                'status' => strtolower($request->status),
                'user_id' => $user->id,
                'pas_photo' => $photoPath,
                'nis' => $request->nis,
                'kelas_id' => $request->kelas_id,
            ]);
        });
        if ($user) {
            return back()->with([
                'success' => "Data berhasil di tambahkan"
            ]);
        }
        return back()->withErrors([
            'success' => "Data berhasil di tambahkan"
        ]);
    }
    public function updateGuru(Request $request, string $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nip'            => ['required', 'numeric', Rule::unique('gurus', 'nip')->ignore($guru->nip, 'nip')],
            'name'           => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:L,P',
            'status'         => 'required|in:aktif,nonaktif',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Foto bersifat opsional saat update
            'gelar_depan'    => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $guru) {
            $guru->user->update([
                'name' => $request->name,
            ]);

            $photoPath = $guru->foto;

            if ($request->hasFile('foto')) {
                if ($guru->foto && $guru->foto !== 'guru-default.png' && Storage::disk('public')->exists($guru->foto)) {
                    Storage::disk('public')->delete($guru->foto);
                }
                $photoPath = $request->file('foto')->store('guru-photos', 'public');
            }

            $guru->update([
                'nip'            => $request->nip,
                // 'user_id' tidak perlu diupdate
                'gelar_depan'    => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'status'         => $request->status,
                'foto'           => $photoPath,
            ]);
        });

        return back()->with([
            'success' => "Data Guru {$request->name} berhasil diperbarui."
        ]);
    }
    public function editGuru(string $id)
    {
        $guru = Guru::with('user')->findOrFail($id);

        return inertia('admin/user-management/guru/edit', [
            'guru' => $guru
        ]);
    }
    public function siswa()
    {
        $user = User::whereHas('siswa')->paginate(5);
        return inertia('admin/user-management/siswa/index', ['users' => $user]);
    }
    public function tambahSiswa()
    {
        $kelas = Kelas::all();

        return inertia('admin/user-management/siswa/tambah', ['kelasList' => $kelas]);
    }

    public function guru()
    {
        $user = User::query()->whereHas('guru')
            ->with(['guru.matpels', 'guru.pengajarans.kelas', 'guru.pengajarans.matpel'])
            ->paginate(4);
        $kelas = Kelas::all();
        $matpels = Matpel::all();
        return inertia('admin/user-management/guru/index', ['users' => $user, 'kelas' => $kelas, 'matpels' => $matpels]);
    }
    public function addMatpelToGuru(Request $request)
    {
        // 1. Validasi Input Basic
        $request->validate([
            'guru_nip'    => 'required|exists:gurus,nip',
            'matpel_kode' => 'required|exists:matpels,kode',
            'kelas_id'    => 'required|exists:kelas,id',
        ]);
        $existing = Pengajaran::with(['guru.user'])
            ->where('matpel_kode', $request->matpel_kode)
            ->where('kelas_id', $request->kelas_id)
            ->first();
        if ($existing) {
            if ($existing->guru_nip == $request->guru_nip) {
                return back()->withErrors([
                    'message' => 'Guru ini sudah terdaftar mengajar mata pelajaran tersebut di kelas ini.'
                ]);
            }
            $namaGuruLain = $existing->guru->user->nama ?? $existing->guru->nama ?? 'Guru Lain';
            return back()->withErrors([
                'message' => "Gagal! Mata pelajaran ini di kelas tersebut sudah diampu oleh guru: {$namaGuruLain}."
            ]);
        }
        Pengajaran::create([
            'guru_nip'    => $request->guru_nip,
            'matpel_kode' => $request->matpel_kode,
            'kelas_id'    => $request->kelas_id,
        ]);
        return back()->with('success', 'Penugasan berhasil ditambahkan.');
    }
}
