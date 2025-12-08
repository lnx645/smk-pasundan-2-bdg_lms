<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\TugasController;
use App\Http\Controllers\GuruMateriController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\NotifServiceController;
use App\Http\Controllers\TugasSiswaController;
use Illuminate\Support\Facades\Route;

Route::post('/fcm-cloud/save-fcm-token', [NotifServiceController::class, 'saveFcmToken'])->name('save-fcm-token');
Route::get("/send-notification", [NotifServiceController::class, 'testSend'])->name('send');
Route::get('login', LoginController::class)->name('login');
Route::post('login', [LoginController::class, 'checkLogin'])->name('login.check');
Route::get('logout', [LoginController::class, 'logout'])->name('auth.logout');

//authenticated guarded
Route::middleware('authenticated')->group(function () {
    Route::get('/', DashboardController::class)->name('home');
    Route::middleware('authenticated:siswa')->group(function () {
        Route::get('/siswa/materi', [MateriController::class, 'showMateri'])->name('siswa.materi');
        Route::get('/siswa/tugas', TugasSiswaController::class)->name('siswa.tugas');
        Route::get('/siswa/materi/{id}', [MateriController::class, 'view'])->name('siswa.materi.view');
    });
    Route::middleware('authenticated:guru')->group(function () {
        Route::get('/guru/materi/{kelas_kode?}', [GuruMateriController::class, 'materi'])->name('guru.materi');
        Route::get('/guru/materi/{kelas_kode?}/tambah-materi', [GuruMateriController::class, 'tambahMateri'])->name('guru.materi.tambah');
        Route::post('/guru/materi/{kelas_kode?}/simpan-materi', [GuruMateriController::class, 'simpanMateri'])->name('guru.materi.tambah.simpan');
        Route::delete('guru/delete-materi/{materi_id}', [GuruMateriController::class, 'deleteMateri'])->name('guru.materi.delete');
        Route::patch('guru/publish-materi', [GuruMateriController::class, 'publishMateri'])->name('guru.materi.publish');


        Route::get('guru/tugas', App\Http\Controllers\Guru\TugasController::class)->name('guru.tugas');
        Route::get('guru/{kelas_id}/tugas', [App\Http\Controllers\Guru\TugasController::class, 'index'])->name('guru.tugas.all_tugas');

        Route::get('guru/tugas/tambah', [App\Http\Controllers\Guru\TugasController::class, 'tambah'])->name('guru.tugas.tambah');
        Route::post('guru/tugas/tambah', [App\Http\Controllers\Guru\TugasController::class, 'simpanTugas'])->name('guru.tugas.simpanTugas');
        //periksa
        Route::get('guru/tugas/{id}/periksa', [App\Http\Controllers\Guru\TugasController::class, 'periksaTugas'])->name('guru.tugas.periksa');
        Route::get('guru/tugas/{id}/edit', [App\Http\Controllers\Guru\TugasController::class, 'editTugas'])->name('guru.tugas.edit');
        Route::put('/guru/tugas/{id}', [TugasController::class, 'updateTugas'])->name('guru.tugas.update');

        Route::post('guru/get-kelas-by-matpel', [App\Http\Controllers\Guru\TugasController::class, 'getKelasByMatpel'])->name('guru.get-kelas-by-matpel');
        Route::get('guru/get-siswa', [App\Http\Controllers\Guru\TugasController::class, 'getSiswa'])->name('guru.get-siswa');
    });
});
