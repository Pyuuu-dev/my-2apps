<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permanent_fruit_prices', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->bigInteger('harga_robux')->default(0);
            $table->bigInteger('harga_beli')->default(0);
            $table->bigInteger('harga_jual')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permanent_fruit_prices');
    }
};
