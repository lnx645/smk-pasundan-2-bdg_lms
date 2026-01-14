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
use Rap2hpoutre\FastExcel\FastExcel;

class UserManagementController extends Controller
{
    /**
     * =========================================================================
     * 1. USERS MANAGEMENT (Admin / Staff)
     * =========================================================================
     */

    public function users(Request $request)
    {
        $query = User::query()->doesntHave('guru')->doesntHave('siswa');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()
            ->paginate(10)
            ->withQueryString();

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
            // Perbaikan: Ignore ID user saat validasi unique email
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

        // Cek relasi sebelum hapus
        if ($user->guru()->exists() || $user->siswa()->exists()) {
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('guru', function (Builder $qGuru) use ($search) {
                        $qGuru->where('nip', 'like', "%{$search}%");
                    });
            });
        }

        // FIX N+1: Tambahkan 'guru' di level pertama array with()
        // Ini memastikan relation guru dimuat SEBELUM accessor di User.php dijalankan
        $users = $query->with([
            'guru', // <--- PENTING
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
            'matpel_kode'    => 'nullable|exists:matpels,kode',
        ]);

        $passwordDefault = $request->nip . "-" . Carbon::now()->format("Y");

        DB::transaction(function () use ($request, $passwordDefault) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => EmailGenerator::generateEmailDomain($request->nip),
                'password' => Hash::make($passwordDefault),
            ]);

            $photoPath = null;
            if ($request->hasFile('foto')) {
                $photoPath = $request->file('foto')->store('guru-photos', 'public');
            }

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
        });

        return back()->with(['success' => "Data Guru ditambahkan. Pass: $passwordDefault"]);
    }

    public function editGuru($id)
    {
        // Eager load user untuk form edit
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
            'status'         => 'required',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'matpel_kode'    => 'nullable|exists:matpels,kode',
        ]);

        DB::transaction(function () use ($request, $guru) {
            // Update User
            $guru->user()->update(['name' => $request->name]);

            // Handle Foto
            $photoPath = $guru->foto;
            if ($request->hasFile('foto')) {
                if ($guru->foto && $guru->foto !== 'guru-default.png' && Storage::disk('public')->exists($guru->foto)) {
                    Storage::disk('public')->delete($guru->foto);
                }
                $photoPath = $request->file('foto')->store('guru-photos', 'public');
            }

            // Update Guru
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

                (new FastExcel)->import($path, function ($line) {
                    // Skip jika NIP kosong atau sudah ada
                    if (empty($line['nip'])) return null;
                    if (Guru::where('nip', $line['nip'])->exists()) return null;

                    // Validasi Kode Mapel
                    $kodeMapel = $line['kode_mapel'] ?? null;
                    if ($kodeMapel && !Matpel::where('kode', $kodeMapel)->exists()) {
                        $kodeMapel = null;
                    }

                    $nip = trim($line['nip']); // Bersihkan spasi
                    $password = $nip . '-' . Carbon::now()->format('Y');

                    $user = User::create([
                        'name'     => $line['nama_lengkap'],
                        'email'    => EmailGenerator::generateEmailDomain($nip),
                        'password' => Hash::make($password),
                    ]);

                    return Guru::create([
                        'nip'            => $nip,
                        'user_id'        => $user->id,
                        'jenis_kelamin'  => strtoupper(trim($line['jk'])),
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
        return (new FastExcel($data))->download('template_guru.xlsx');
    }


    /**
     * =========================================================================
     * 3. SISWA MANAGEMENT
     * =========================================================================
     */

    public function siswa(Request $request)
    {
        $query = User::query()->whereHas('siswa');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('siswa', function (Builder $qSiswa) use ($search) {
                        $qSiswa->where('nis', 'like', "%{$search}%");
                    });
            });
        }

        // FIX N+1: Tambahkan 'siswa' secara eksplisit
        $users = $query->with([
            'siswa',       // <--- PENTING
            'siswa.kelas'  // Load relasi nested
        ])
            ->orderBy('name', 'asc') // Urutkan berdasarkan kolom name di tabel users
            ->paginate(10) // Naikkan pagination biar tidak terlalu sering klik next
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
            'nis'           => 'required|unique:siswas,nis',
            'name'          => 'required',
            'kelas_id'      => 'required|exists:kelas,id',
            'pas_photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($request) {
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
            'name'      => 'required',
            // Perbaikan unique rule untuk NIS (abaikan NIS siswa ini sendiri)
            'nis'       => ['required', Rule::unique('siswas', 'nis')->ignore($siswa->id)],
            'kelas_id'  => 'required|exists:kelas,id',
            'pas_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($request, $siswa) {
            // Update User
            $siswa->user()->update(['name' => $request->name]);

            // Handle Photo
            if ($request->hasFile('pas_photo')) {
                if ($siswa->pas_photo && $siswa->pas_photo !== 'siswa.png' && Storage::disk('public')->exists($siswa->pas_photo)) {
                    Storage::disk('public')->delete($siswa->pas_photo);
                }
                $siswa->pas_photo = $request->file('pas_photo')->store('siswa-photos', 'public');
            }

            // Update Siswa (Gunakan property fillable di model Siswa)
            $siswa->update($request->only([
                'nis',
                'jenis_kelamin',
                'agama',
                'tahun_masuk',
                'tingkat',
                'kelas_id',
                'status'
            ]));

            // Jika Anda ingin menyimpan foto path ke database
            if ($request->hasFile('pas_photo')) {
                $siswa->save();
            }
        });

        return to_route('admin.user-management.siswa')->with('success', 'Data siswa diperbarui');
    }

    public function destroySiswa($id)
    {
        try {
            $siswa = Siswa::where('nis', $id)->firstOrFail();

            DB::transaction(function () use ($siswa) {
                // Hapus file fisik
                if ($siswa->pas_photo && $siswa->pas_photo !== 'siswa.png') {
                    Storage::disk('public')->delete($siswa->pas_photo);
                }

                // Simpan user sebelum siswa dihapus untuk dihapus belakangan
                $user = $siswa->user;

                $siswa->delete();
                if ($user) $user->delete();
            });

            return back()->with('success', 'Data siswa dan akun berhasil dihapus.');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', 'Gagal menghapus! Siswa memiliki data relasi (Nilai/Absensi dll).');
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    // --- Import Siswa ---

    public function importSiswa(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:2048']);

        try {
            DB::transaction(function () use ($request) {
                $path = $request->file('file')->getRealPath();

                (new FastExcel)->import($path, function ($line) {
                    if (empty($line['nis'])) return null;
                    if (Siswa::where('nis', $line['nis'])->exists()) return null;

                    $kelasId = null;
                    if (!empty($line['nama_kelas'])) {
                        $kelas = Kelas::where('nama', trim($line['nama_kelas']))->first();
                        $kelasId = $kelas ? $kelas->id : null;
                    }

                    $nis = trim($line['nis']);
                    $password = $nis . '-' . Carbon::now()->format('Y');

                    $user = User::create([
                        'name'     => $line['nama_lengkap'],
                        'email'    => EmailGenerator::generateEmailDomain($nis),
                        'password' => Hash::make($password),
                    ]);

                    return Siswa::create([
                        'nis'           => $nis,
                        'user_id'       => $user->id,
                        'kelas_id'      => $kelasId,
                        'jenis_kelamin' => strtoupper(trim($line['jk'])),
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
        return (new FastExcel($data))->download('template_siswa.xlsx');
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

        // Cek apakah sudah ada pengajar untuk mapel & kelas ini
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
