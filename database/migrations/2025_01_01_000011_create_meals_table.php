<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('waktu_makan'); // sarapan, makan_siang, makan_malam, snack
            $table->string('nama_makanan');
            $table->integer('kalori')->default(0);
            $table->decimal('protein', 6, 1)->default(0);
            $table->decimal('karbohidrat', 6, 1)->default(0);
            $table->decimal('lemak', 6, 1)->default(0);
            $table->decimal('porsi', 4, 1)->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
