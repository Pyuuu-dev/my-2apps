<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permanent_fruit_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_account_id')->constrained('storage_accounts')->cascadeOnDelete();
            $table->foreignId('blox_fruit_id')->constrained('blox_fruits')->cascadeOnDelete();
            $table->bigInteger('harga_robux')->default(0);
            $table->bigInteger('harga_idr')->default(0);
            $table->integer('jumlah')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['storage_account_id', 'blox_fruit_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('permanent_fruit_stocks'); }
};
