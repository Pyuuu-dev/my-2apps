<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fruit_skins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blox_fruit_id')->constrained('blox_fruits')->cascadeOnDelete();
            $table->string('nama_skin');
            $table->bigInteger('harga')->default(0);
            $table->string('gambar')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fruit_skins'); }
};
