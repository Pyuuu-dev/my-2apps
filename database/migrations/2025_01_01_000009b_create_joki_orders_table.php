<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('joki_orders', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->string('kontak')->nullable();
            $table->string('jenis_joki');
            $table->text('detail_pesanan')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->string('status')->default('antrian');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('joki_orders'); }
};
