<?php

use App\Models\Kelas;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('duration');
            $table->double('passing_grade');
            $table->integer('max_attempts')->default(1);
            $table->boolean('is_randomized')->default(true);
            $table->char('access_code', 15)->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('start_date');
            $table->integer('end_date');
            //relasi
            $table->string('matpel_kode');
            $table->foreign('matpel_kode')->on('matpels')->references('kode');
            $table->foreignIdFor(Kelas::class)->constrained('kelas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
