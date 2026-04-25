<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profit_records', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kategori'); // fruit, skin, gamepass, permanent, joki, lainnya
            $table->string('keterangan')->nullable();
            $table->bigInteger('modal')->default(0);
            $table->bigInteger('pendapatan')->default(0);
            $table->bigInteger('keuntungan')->default(0);
            $table->string('metode_bayar')->nullable(); // dana, gopay, shopeepay, seabank, bank_kalsel, bri, qris, cash
            $table->timestamps();
        });

        Schema::create('wallet_balances', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->bigInteger('dana')->default(0);
            $table->bigInteger('gopay')->default(0);
            $table->bigInteger('shopeepay')->default(0);
            $table->bigInteger('seabank')->default(0);
            $table->bigInteger('bank_kalsel')->default(0);
            $table->bigInteger('bri')->default(0);
            $table->bigInteger('qris')->default(0);
            $table->bigInteger('cash')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_balances');
        Schema::dropIfExists('profit_records');
    }
};
