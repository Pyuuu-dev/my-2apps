<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercise_database', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori')->default('kardio'); // kardio, kekuatan, fleksibilitas, hiit
            $table->string('intensitas')->default('sedang'); // ringan, sedang, berat
            $table->integer('kalori_per_menit')->default(5); // rata-rata kalori terbakar per menit
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('exercise_database'); }
};
