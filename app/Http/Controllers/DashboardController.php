<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $pending_tugas = [];
        $user = $request->user();
        if ($user->role === 'siswa') {
            $kelasId = $user->siswa->kelas_id; // Asumsi relasi user->siswa->kelas

            $pending_tugas = Tugas::where(function ($q) use ($kelasId, $user) {
                $q->where('receiver_type', 'class_id')->whereJsonContains('receiver_type_id', $kelasId)
                    ->orWhere('receiver_type', 'siswa_id')->whereJsonContains('receiver_type_id', $user->id);
            })
                ->whereDoesntHave('jawaban', function ($q) use ($user) {
                    $q->where('answered_by_id', $user->id);
                })
                ->where('deadline', '>', now())
                ->with('matpel')
                ->orderBy('deadline', 'asc')
                ->limit(5)
                ->get();
        }

        return inertia('home', ['pending_tugas' => $pending_tugas]);
    }
}
