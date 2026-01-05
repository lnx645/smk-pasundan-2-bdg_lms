<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\JawabanController;
use App\Http\Controllers\Guru\NilaiController; // Pastikan ini sesuai
use App\Http\Controllers\Guru\TugasController;
use App\Http\Controllers\GuruMateriController;
use App\Http\Controllers\GuruQuizController;
use App\Http\Controllers\GuruQuizQuestionController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\NotifServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizSiswaController;
use App\Http\Controllers\TugasSiswaController;
use Illuminate\Support\Facades\Route;

Route::post('/fcm-cloud/save-fcm-token', [NotifServiceController::class, 'saveFcmToken'])->name('save-fcm-token');
Route::get("/send-notification", [NotifServiceController::class, 'testSend'])->name('send');
Route::get('login', LoginController::class)->name('login');
Route::post('login', [LoginController::class, 'checkLogin'])->name('login.check');
Route::get('logout', [LoginController::class, 'logout'])->name('auth.logout');
//authenticated guarded
Route::middleware('authenticated')->group(function () {

    Route::get('/profile', ProfileController::class)->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');


    Route::prefix('admin')->middleware('authenticated:admin')->name('admin.')->group(base_path('routes/admin.php'));
    Route::get('/', DashboardController::class)->name('home');
    // --- SISWA ROUTES ---
    Route::middleware('authenticated:siswa')->group(function () {
        Route::get('/siswa/materi', [MateriController::class, 'showMateri'])->name('siswa.materi');
        Route::get('/siswa/tugas', [TugasSiswaController::class, 'showTugas'])->name('siswa.tugas');
        Route::get('/siswa/materi/{id}', [MateriController::class, 'view'])->name('siswa.materi.view');

        Route::get('siswa/tugas/{id}/kerjakan', [TugasSiswaController::class, 'kerjakan'])->name('siswa.tugas.kerjakan');
        Route::put('siswa/tugas/{id}/kerjakan', [TugasSiswaController::class, 'kerjakanSimpan'])->name('siswa.tugas.kerjakan.kerjakanSimpan');
        Route::delete('siswa/tugas/{id}/kerjakan', [TugasSiswaController::class, 'batalkanPengumpulan'])->name('siswa.tugas.kerjakan.batalkanPengumpulan');

        Route::get('/quiz', [QuizSiswaController::class, 'index'])->name('siswa.quiz.index');
        Route::get('/quiz/{quiz}', [QuizSiswaController::class, 'show'])->name('siswa.quiz.show');
        Route::post('/quiz/{quiz}/start', [QuizSiswaController::class, 'start'])->name('siswa.quiz.start');
        Route::get('/quiz/work/{attempt}', [QuizSiswaController::class, 'work'])->name('siswa.quiz.work');
        Route::post('/quiz/{attempt}/submit', [QuizSiswaController::class, 'submit'])->name('siswa.quiz.submit');
        Route::get('/quiz/result/{attempt}', [QuizSiswaController::class, 'result'])->name('siswa.quiz.result');
    });

    // --- GURU ROUTES ---
    Route::middleware('authenticated:guru')->group(function () {



        Route::prefix('guru')->name('guru.')->group(function () {
            Route::get('/quiz', [GuruQuizController::class, 'index'])->name('quiz.index');
            Route::get('/quiz/create', [GuruQuizController::class, 'create'])->name('quiz.create');
            Route::post('/quiz', [GuruQuizController::class, 'store'])->name('quiz.store');
            Route::get('/quiz/{quiz}/edit', [GuruQuizController::class, 'edit'])->name('quiz.edit');
            Route::put('/quiz/{quiz}', [GuruQuizController::class, 'update'])->name('quiz.update');
            Route::delete('/quiz/{quiz}', [GuruQuizController::class, 'destroy'])->name('quiz.destroy');
            Route::get('/quiz/{quiz}/result', [GuruQuizController::class, 'result'])->name('quiz.result');
            Route::get('/quiz/{quiz}/questions', [GuruQuizQuestionController::class, 'index'])
                ->name('quiz.questions.index');
            Route::get('/quiz/{quiz}/questions', [GuruQuizQuestionController::class, 'index'])
                ->name('quiz.questions.index');

            // Simpan soal baru
            Route::post('/quiz/{quiz}/questions', [GuruQuizQuestionController::class, 'store'])
                ->name('quiz.questions.store');

            // Hapus soal (Perhatikan parameter binding-nya ke 'question')
            Route::delete('/quiz/questions/{question}', [GuruQuizQuestionController::class, 'destroy'])
                ->name('quiz.questions.destroy');
        });


        // Materi
        Route::get('/guru/materi/{kelas_kode?}', [GuruMateriController::class, 'materi'])->name('guru.materi');
        Route::get('/guru/materi/{kelas_kode?}/tambah-materi', [GuruMateriController::class, 'tambahMateri'])->name('guru.materi.tambah');
        Route::post('/guru/materi/{kelas_kode?}/simpan-materi', [GuruMateriController::class, 'simpanMateri'])->name('guru.materi.tambah.simpan');
        Route::delete('guru/delete-materi/{materi_id}', [GuruMateriController::class, 'deleteMateri'])->name('guru.materi.delete');
        Route::patch('guru/publish-materi', [GuruMateriController::class, 'publishMateri'])->name('guru.materi.publish');

        // Tugas
        Route::get('guru/tugas', App\Http\Controllers\Guru\TugasController::class)->name('guru.tugas');
        Route::get('guru/{kelas_id}/tugas', [App\Http\Controllers\Guru\TugasController::class, 'index'])->name('guru.tugas.all_tugas');
        Route::get('guru/tugas/tambah', [App\Http\Controllers\Guru\TugasController::class, 'tambah'])->name('guru.tugas.tambah');
        Route::post('guru/tugas/tambah', [App\Http\Controllers\Guru\TugasController::class, 'simpanTugas'])->name('guru.tugas.simpanTugas');

        // Periksa & Edit Tugas
        Route::get('guru/tugas/{id}/periksa', [App\Http\Controllers\Guru\TugasController::class, 'periksaTugas'])->name('guru.tugas.periksa');
        Route::get('guru/tugas/{id}/edit', [App\Http\Controllers\Guru\TugasController::class, 'editTugas'])->name('guru.tugas.edit');
        Route::put('/guru/tugas/{id}', [TugasController::class, 'updateTugas'])->name('guru.tugas.update');
        Route::delete('guru/tugas/{id}', [TugasController::class, 'deleteTugas'])->name('guru.tugas.delete');
        Route::get('guru/tugas/{id}/{jawaban_id}', JawabanController::class)->name('guru.tugas.lihat_jawaban');

        // Helpers (Ajax)
        Route::post('guru/get-kelas-by-matpel', [App\Http\Controllers\Guru\TugasController::class, 'getKelasByMatpel'])->name('guru.get-kelas-by-matpel');
        Route::get('guru/get-siswa', [App\Http\Controllers\Guru\TugasController::class, 'getSiswa'])->name('guru.get-siswa');

        // --- NILAI (Updated) ---
        // Route untuk menyimpan nilai baru
        Route::post('guru/nilai/simpan', [NilaiController::class, 'simpan'])->name('guru.nilai.simpan');

        // Route untuk halaman kelola nilai (sekaligus filter/fetch data)
        Route::get('guru/nilai', [NilaiController::class, 'kelola_nilai'])->name('guru.nilai.kelola');
        Route::get('guru/nilai/with-filter', [NilaiController::class, 'index'])->name('guru.nilai.filter');
        Route::put('guru/nilai/{id}', [NilaiController::class, 'update'])->name('guru.nilai.update');

        // [BARU] Route untuk hapus nilai
        Route::delete('guru/nilai/{id}', [NilaiController::class, 'destroy'])->name('guru.nilai.delete');
    });
});
