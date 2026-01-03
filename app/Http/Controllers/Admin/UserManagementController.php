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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel as Rap2hpoutreFastExcel;

class UserManagementController extends Controller
{
    /**
     * =========================================================================
     * 1. USERS MANAGEMENT (Admin / Staff)
     * =========================================================================
     */

    public function users(Request $request)
    {
        // Ambil user murni (bukan guru & bukan siswa)
        $query = User::query()->doesntHave('guru')->doesntHave('siswa');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return inertia('admin/user-management/users/index', [
            'users'   => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    public function createUser()
    {
        return inertia('admin/user-management/users/tambah');
    }

    public function simpanUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Admin/Staff berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return inertia('admin/user-management/users/edit', ['user' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Data Admin/Staff berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->guru || $user->siswa) {
            return back()->with('error', 'User terhubung dengan data Guru/Siswa. Hapus lewat menu terkait.');
        }

        $user->delete();

        return back()->with('success', 'Admin/Staff berhasil dihapus.');
    }


    /**
     * =========================================================================
     * 2. GURU MANAGEMENT
     * =========================================================================
     */

    public function guru(Request $request)
    {
        $query = User::query()->whereHas('guru');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('guru', function (Builder $qGuru) use ($search) {
                        $qGuru->where('nip', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->with([
            'guru.matpels',
            'guru.pengajarans.kelas',
            'guru.spesialisMatpel',
            'guru.pengajarans.matpel'
        ])
            ->latest()
            ->paginate(4)
            ->withQueryString();

        return inertia('admin/user-management/guru/index', [
            'users'   => $users,
            'kelas'   => Kelas::all(),
            'matpels' => Matpel::all(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function tambahGuru()
    {
        return inertia('admin/user-management/guru/tambah', [
            'matpels' => Matpel::select('kode', 'nama')->orderBy('nama')->get(),
        ]);
    }

    public function simpanGuru(Request $request)
    {
        $request->validate([
            'nip'            => 'required|unique:gurus,nip|numeric',
            'name'           => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:L,P',
            'status'         => 'required',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gelar_depan'    => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'matpel_kode'    => 'nullable|exists:matpels,kode',
        ]);

        $user = DB::transaction(function () use ($request) {
            $emailGenerated = EmailGenerator::generateEmailDomain($request->nip);
            $passwordRaw = $request->nip . "-" . Carbon::now()->format("Y");

            $user = User::create([
                'name'     => $request->name,
                'email'    => $emailGenerated,
                'password' => Hash::make($passwordRaw),
            ]);

            $photoPath = $request->hasFile('foto')
                ? $request->file('foto')->store('guru-photos', 'public')
                : null;

            Guru::create([
                'nip'            => $request->nip,
                'user_id'        => $user->id,
                'gelar_depan'    => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'status'         => strtolower($request->status),
                'foto'           => $photoPath,
                'matpel_kode'    => $request->matpel_kode,
            ]);

            return $user;
        });

        $passwordDefault = $request->nip . "-" . Carbon::now()->format("Y");
        return back()->with(['success' => "Data Guru ditambahkan. Pass: $passwordDefault"]);
    }

    public function editGuru($id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        $matpels = Matpel::select('kode', 'nama')->orderBy('nama')->get();

        return inertia('admin/user-management/guru/edit', [
            'guru'    => $guru,
            'matpels' => $matpels,
        ]);
    }

    public function updateGuru(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nip'            => ['required', 'numeric', Rule::unique('gurus', 'nip')->ignore($guru->nip, 'nip')],
            'name'           => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:L,P',
            'status'         => 'required|in:aktif,nonaktif,Aktif,Nonaktif',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gelar_depan'    => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'matpel_kode'    => 'nullable|exists:matpels,kode',
        ]);

        DB::transaction(function () use ($request, $guru) {
            $guru->user->update(['name' => $request->name]);

            $photoPath = $guru->foto;
            if ($request->hasFile('foto')) {
                if ($guru->foto && $guru->foto !== 'guru-default.png' && Storage::disk('public')->exists($guru->foto)) {
                    Storage::disk('public')->delete($guru->foto);
                }
                $photoPath = $request->file('foto')->store('guru-photos', 'public');
            }

            $guru->update([
                'nip'            => $request->nip,
                'gelar_depan'    => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'status'         => strtolower($request->status),
                'foto'           => $photoPath,
                'matpel_kode'    => $request->matpel_kode,
            ]);
        });

        return back()->with(['success' => "Data Guru {$request->name} diperbarui."]);
    }

    // --- Import Guru ---

    public function importGuru(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:2048']);

        try {
            DB::transaction(function () use ($request) {
                $path = $request->file('file')->getRealPath();

                (new Rap2hpoutreFastExcel())->import($path, function ($line) {
                    if (empty($line['nip'])) return null;
                    if (Guru::where('nip', $line['nip'])->exists()) return null;

                    // Validasi Kode Mapel (Set NULL jika tidak valid)
                    $kodeMapel = $line['kode_mapel'] ?? null;
                    if ($kodeMapel && !Matpel::where('kode', $kodeMapel)->exists()) {
                        $kodeMapel = null;
                    }

                    $nip = $line['nip'];
                    $password = $nip . '-' . Carbon::now()->format('Y');

                    $user = User::create([
                        'name'     => $line['nama_lengkap'],
                        'email'    => $nip . '@guru.sekolah.id',
                        'password' => Hash::make($password),
                    ]);

                    return Guru::create([
                        'nip'            => $nip,
                        'user_id'        => $user->id,
                        'jenis_kelamin'  => strtoupper($line['jk']),
                        'status'         => strtolower($line['status'] ?? 'aktif'),
                        'gelar_depan'    => $line['gelar_depan'] ?? null,
                        'gelar_belakang' => $line['gelar_belakang'] ?? null,
                        'matpel_kode'    => $kodeMapel,
                        'foto'           => null,
                    ]);
                });
            });

            return back()->with('success', 'Data Guru berhasil diimport.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Gagal Import: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplateGuru()
    {
        $data = [[
            'nip' => '19800101',
            'nama_lengkap' => 'Budi Santoso',
            'jk' => 'L',
            'status' => 'aktif',
            'gelar_depan' => 'Drs.',
            'gelar_belakang' => 'M.Pd',
            'kode_mapel' => 'MTK'
        ]];
        return (new Rap2hpoutreFastExcel($data))->download('template_guru.xlsx');
    }


    /**
     * =========================================================================
     * 3. SISWA MANAGEMENT
     * =========================================================================
     */

    public function siswa(Request $request)
    {
        $query = User::query()->whereHas('siswa');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('siswa', function (Builder $qSiswa) use ($search) {
                        $qSiswa->where('nis', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->with(['siswa.kelas'])
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return inertia('admin/user-management/siswa/index', [
            'users'   => $users,
            'filters' => $request->only(['search']),
        ]);
    }
    public function tambahSiswa()
    {
        return inertia('admin/user-management/siswa/tambah', ['kelasList' => Kelas::all()]);
    }

    public function simpanSiswa(Request $request)
    {
        $request->validate([
            'nis'         => 'required|unique:siswas,nis',
            'pas_photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name'        => 'required',
            'agama'       => 'required',
            'tahun_masuk' => 'required',
            'tingkat'     => 'required',
            'kelas_id'    => 'required',
            'status'      => 'required',
        ]);

        $user = DB::transaction(function () use ($request) {
            $email = EmailGenerator::generateEmailDomain($request->nis);
            $password = $request->nis . "-" . Carbon::now()->format("Y");

            $user = User::create([
                'name'     => $request->name,
                'email'    => $email,
                'password' => Hash::make($password),
            ]);

            $photoPath = 'siswa.png';
            if ($request->hasFile('pas_photo')) {
                $photoPath = $request->file('pas_photo')->store('siswa-photos', 'public');
            }

            Siswa::create([
                'user_id'       => $user->id,
                'nis'           => $request->nis,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama'         => $request->agama,
                'tahun_masuk'   => $request->tahun_masuk,
                'tingkat'       => $request->tingkat,
                'status'        => strtolower($request->status),
                'pas_photo'     => $photoPath,
                'kelas_id'      => $request->kelas_id,
            ]);

            return $user;
        });

        return back()->with(['success' => "Data Siswa berhasil ditambahkan"]);
    }

    public function editSiswa($id)
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
            'name'        => 'required',
            'nis'         => 'required',
            'agama'       => 'required',
            'tahun_masuk' => 'required',
            'tingkat'     => 'required',
            'kelas_id'    => 'required',
            'status'      => 'required',
            'pas_photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($request, $siswa) {
            $siswa->user->update(['name' => $request->name]);

            if ($request->hasFile('pas_photo')) {
                if ($siswa->pas_photo && $siswa->pas_photo !== 'siswa.png' && Storage::disk('public')->exists($siswa->pas_photo)) {
                    Storage::disk('public')->delete($siswa->pas_photo);
                }
                $siswa->pas_photo = $request->file('pas_photo')->store('siswa-photos', 'public');
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

        return to_route('admin.user-management.siswa')->with('success', 'Data siswa diperbarui');
    }

    public function destroySiswa($id)
    {
        try {
            $siswa = Siswa::where('nis', $id)->firstOrFail();
            $user = $siswa->user;

            if ($siswa->pas_photo && $siswa->pas_photo !== 'siswa.png') {
                Storage::disk('public')->delete($siswa->pas_photo);
            }

            $siswa->delete();
            if ($user) $user->delete();

            return back()->with('success', 'Data siswa dan akun berhasil dihapus.');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', 'Gagal menghapus! Siswa memiliki data terkait.');
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // --- Import Siswa ---

    public function importSiswa(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:2048']);

        try {
            DB::transaction(function () use ($request) {
                $path = $request->file('file')->getRealPath();

                (new Rap2hpoutreFastExcel())->import($path, function ($line) {
                    if (empty($line['nis'])) return null;
                    if (Siswa::where('nis', $line['nis'])->exists()) return null;

                    // Cari ID Kelas by Nama
                    $kelasId = null;
                    if (!empty($line['nama_kelas'])) {
                        $kelas = Kelas::where('nama', $line['nama_kelas'])->first();
                        $kelasId = $kelas ? $kelas->id : null;
                    }

                    $nis = $line['nis'];
                    $email = EmailGenerator::generateEmailDomain($nis);
                    $password = $nis . '-' . Carbon::now()->format('Y');

                    $user = User::create([
                        'name'     => $line['nama_lengkap'],
                        'email'    => $email,
                        'password' => Hash::make($password),
                    ]);

                    return Siswa::create([
                        'nis'           => $nis,
                        'user_id'       => $user->id,
                        'kelas_id'      => $kelasId,
                        'jenis_kelamin' => strtoupper($line['jk']),
                        'agama'         => $line['agama'] ?? 'Islam',
                        'tahun_masuk'   => $line['tahun_masuk'] ?? date('Y'),
                        'tingkat'       => $line['tingkat'] ?? 10,
                        'status'        => strtolower($line['status'] ?? 'aktif'),
                        'pas_photo'     => 'siswa.png',
                    ]);
                });
            });

            return back()->with('success', 'Data Siswa berhasil diimport.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Gagal Import: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplateSiswa()
    {
        $data = [[
            'nis' => '23241001',
            'nama_lengkap' => 'Ahmad Siswa',
            'jk' => 'L',
            'agama' => 'Islam',
            'tahun_masuk' => '2024',
            'tingkat' => '10',
            'nama_kelas' => 'X RPL 1',
            'status' => 'aktif'
        ]];
        return (new Rap2hpoutreFastExcel($data))->download('template_siswa.xlsx');
    }


    /**
     * =========================================================================
     * 4. PENUGASAN & LAINNYA
     * =========================================================================
     */

    public function index()
    {
        return inertia('admin/user-management/index');
    }

    public function addMatpelToGuru(Request $request)
    {
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
                return back()->withErrors(['message' => 'Guru ini sudah mengajar mapel tsb di kelas ini.']);
            }
            $namaGuruLain = $existing->guru->user->name ?? 'Guru Lain';
            return back()->withErrors(['message' => "Gagal! Mapel ini sudah diampu oleh: {$namaGuruLain}."]);
        }

        Pengajaran::create([
            'guru_nip'    => $request->guru_nip,
            'matpel_kode' => $request->matpel_kode,
            'kelas_id'    => $request->kelas_id,
        ]);

        return back()->with('success', 'Penugasan berhasil ditambahkan.');
    }
}
