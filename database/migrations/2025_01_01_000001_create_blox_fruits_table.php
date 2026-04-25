<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blox_fruits', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tipe')->default('Natural');
            $table->string('rarity')->default('Common');
            $table->bigInteger('harga_beli')->default(0);
            $table->bigInteger('harga_jual')->default(0);
            $table->string('gambar')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('blox_fruits'); }
};
