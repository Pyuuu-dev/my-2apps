<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_database', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori')->default('umum'); // sarapan, makan_utama, snack, minuman, buah, umum
            $table->integer('kalori')->default(0); // per porsi
            $table->decimal('protein', 6, 1)->default(0);
            $table->decimal('karbohidrat', 6, 1)->default(0);
            $table->decimal('lemak', 6, 1)->default(0);
            $table->string('satuan_porsi')->default('1 porsi'); // 1 porsi, 1 piring, 1 gelas, 100g
            $table->decimal('berat_gram', 6, 1)->default(100); // berat per porsi dalam gram
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('food_database'); }
};
