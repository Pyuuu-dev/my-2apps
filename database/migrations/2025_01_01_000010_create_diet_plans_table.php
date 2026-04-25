<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('berat_awal', 5, 1);
            $table->decimal('berat_target', 5, 1);
            $table->decimal('berat_sekarang', 5, 1)->nullable();
            $table->integer('kalori_harian_target')->default(2000);
            $table->text('catatan')->nullable();
            $table->string('status')->default('aktif'); // aktif, selesai, berhenti
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};
