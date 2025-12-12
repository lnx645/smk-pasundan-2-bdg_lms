<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/',[HomeController::class,'index'])->name('index');
Route::prefix('user-management')->name('user-management.')->group(function(){
    Route::get('',[UserManagementController::class,'index'])->name('index');
    Route::get('siswa',[UserManagementController::class,'siswa'])->name('siswa');
    Route::get('tambah',[UserManagementController::class,'tambahSiswa'])->name('tambah-siswa');
});