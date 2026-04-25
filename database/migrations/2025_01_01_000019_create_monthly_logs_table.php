<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->string('bulan'); // format: 2025-01
            $table->decimal('berat_awal_bulan', 5, 1);
            $table->decimal('berat_akhir_bulan', 5, 1)->nullable();
            $table->decimal('berat_turun', 5, 1)->default(0);
            $table->integer('target_kalori')->default(0);
            $table->integer('avg_kalori_masuk')->default(0);
            $table->integer('avg_kalori_keluar')->default(0);
            $table->integer('total_hari_olahraga')->default(0);
            $table->integer('total_hari_catat')->default(0);
            $table->integer('konsistensi_persen')->default(0);
            $table->text('catatan')->nullable();
            $table->string('status')->default('berjalan'); // berjalan, selesai
            $table->timestamps();

            $table->unique(['diet_plan_id', 'bulan']);
        });
    }
    public function down(): void { Schema::dropIfExists('monthly_logs'); }
};
