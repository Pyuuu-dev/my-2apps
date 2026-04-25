<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('skin_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_account_id')->constrained('storage_accounts')->cascadeOnDelete();
            $table->foreignId('fruit_skin_id')->constrained('fruit_skins')->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['storage_account_id', 'fruit_skin_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('skin_stocks'); }
};
