<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gamepass_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_account_id')->constrained('storage_accounts')->cascadeOnDelete();
            $table->foreignId('gamepass_id')->constrained('gamepasses')->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['storage_account_id', 'gamepass_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('gamepass_stocks'); }
};
