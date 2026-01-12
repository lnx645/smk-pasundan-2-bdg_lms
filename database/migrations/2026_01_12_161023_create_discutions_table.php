<?php

use App\Models\Kelas;
use App\Models\User;
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
        Schema::create('discusions', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->enum('object_type', ['tugas', 'materi', 'forum']);
            $table->string('object_type_id')->nullable();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Kelas::class);
            $table->integer('likes')->default(0);
            $table->string('matpel_kode');
            $table->foreign('matpel_kode')->on('matpels')->references('kode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discutions');
    }
};
