<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::prefix('user-management')->name('user-management.')->group(function () {
    Route::get('', [UserManagementController::class, 'index'])->name('index');
    Route::get('siswa', [UserManagementController::class, 'siswa'])->name('siswa');
    Route::get('tambah', [UserManagementController::class, 'tambahSiswa'])->name('tambah-siswa');
    Route::post('tambah', [UserManagementController::class, 'simpanSiswa'])->name('simpan-siswa');
    Route::get('edit-siswa/{id}', [UserManagementController::class, 'editSiswa'])->name('edit-siswa');
    Route::post('edit-siswa/{id}', [UserManagementController::class, 'updateSiswa'])->name('update-siswa');


    #GURU
    Route::get('guru', [UserManagementController::class, 'guru'])->name('guru');
    Route::get('tambah-guru', [UserManagementController::class, 'tambahGuru'])->name('tambahGuru');
    Route::post('tambah-guru', [UserManagementController::class, 'simpanGuru'])->name('simpanGuru');
    Route::post('guru-add-matpel', [UserManagementController::class, 'addMatpelToGuru'])->name('guru-add-matpel');
    Route::get('edit-guru/{id}', [UserManagementController::class, 'editGuru'])->name('editGuru');
    Route::post('edit-guru/{id}', [UserManagementController::class, 'updateGuru'])->name('updateGuru');
});
