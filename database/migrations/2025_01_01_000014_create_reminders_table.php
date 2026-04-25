<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan')->nullable();
            $table->time('waktu');
            $table->string('hari_aktif')->default('setiap_hari'); // setiap_hari, senin-jumat, custom
            $table->string('tipe')->default('makan'); // makan, olahraga, minum, timbang, tidur
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
