<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('joki_categories', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('icon')->nullable();
            $table->integer('urutan')->default(99);
            $table->boolean('aktif')->default(true);
            $table->string('slug')->nullable()->unique();
            $table->timestamps();
        });

        // Seed 13 kategori existing supaya data joki_services lama tetap kompatibel.
        $defaults = [
            ['key' => 'level',          'label' => 'Joki Level',        'icon' => '⚔️'],
            ['key' => 'belly_fragment', 'label' => 'Belly & Fragment',  'icon' => '💰'],
            ['key' => 'mastery',        'label' => 'Mastery',           'icon' => '🔥'],
            ['key' => 'fighting_style', 'label' => 'Fighting Style V2', 'icon' => '🥋'],
            ['key' => 'sword',          'label' => 'Get Sword',         'icon' => '🗡️'],
            ['key' => 'gun',            'label' => 'Get Gun',           'icon' => '🔫'],
            ['key' => 'race',           'label' => 'Up & Get Race',     'icon' => '🧬'],
            ['key' => 'boss_raid',      'label' => 'Boss Raid',         'icon' => '👹'],
            ['key' => 'haki',           'label' => 'Haki Legendary',    'icon' => '✨'],
            ['key' => 'instinct',       'label' => 'Instinct',          'icon' => '👁️'],
            ['key' => 'awaken',         'label' => 'Awaken Fruit',      'icon' => '🍎'],
            ['key' => 'material',       'label' => 'Material',          'icon' => '📦'],
            ['key' => 'lainnya',        'label' => 'Lainnya',           'icon' => '📝'],
        ];

        $now = now();
        foreach ($defaults as $i => $row) {
            DB::table('joki_categories')->insert([
                'key'        => $row['key'],
                'label'      => $row['label'],
                'icon'       => $row['icon'],
                'urutan'     => $i + 1,
                'aktif'      => true,
                'slug'       => Str::slug($row['label']) ?: $row['key'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('joki_categories');
    }
};
