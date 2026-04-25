<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('langkah_kaki')->default(0);
            $table->decimal('jarak_km', 6, 2)->default(0);
            $table->integer('kalori_terbakar')->default(0);
            $table->decimal('berat_badan', 5, 1)->nullable();
            $table->integer('jam_tidur')->default(0);
            $table->integer('air_minum_ml')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};
