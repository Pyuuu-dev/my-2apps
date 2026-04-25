<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis_olahraga');
            $table->integer('durasi_menit')->default(0);
            $table->integer('kalori_terbakar')->default(0);
            $table->string('intensitas')->default('sedang'); // ringan, sedang, berat
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
