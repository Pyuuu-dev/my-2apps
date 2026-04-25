<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('level')->nullable();
            $table->text('daftar_buah')->nullable();
            $table->text('daftar_gamepass')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->string('status')->default('tersedia');
            $table->text('keterangan')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('account_stocks'); }
};
