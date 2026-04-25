<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN, so recreate
        Schema::create('permanent_fruit_stocks_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('storage_account_id');
            $table->unsignedBigInteger('blox_fruit_id')->nullable();
            $table->unsignedBigInteger('permanent_fruit_price_id')->nullable();
            $table->bigInteger('harga_robux')->default(0);
            $table->bigInteger('harga_idr')->default(0);
            $table->integer('jumlah')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Copy data
        DB::statement('INSERT INTO permanent_fruit_stocks_new SELECT * FROM permanent_fruit_stocks');

        Schema::drop('permanent_fruit_stocks');
        Schema::rename('permanent_fruit_stocks_new', 'permanent_fruit_stocks');
    }

    public function down(): void {}
};
