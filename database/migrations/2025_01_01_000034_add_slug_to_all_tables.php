<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    private array $tables = [
        'storage_accounts' => 'nama_akun',
        'blox_fruits' => 'nama',
        'fruit_skins' => 'nama_skin',
        'gamepasses' => 'nama',
        'permanent_fruit_prices' => 'nama',
        'joki_services' => 'nama',
        'joki_orders' => 'nama_pelanggan',
        'account_stocks' => 'judul',
        'profit_records' => null, // use random
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $sourceCol) {
            if (!Schema::hasColumn($table, 'slug')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('slug')->nullable();
                });
            }

            // Generate slugs for existing rows
            $rows = DB::table($table)->get();
            $usedSlugs = [];
            foreach ($rows as $row) {
                if ($sourceCol === null) {
                    $base = Str::random(8);
                } else {
                    $val = $row->$sourceCol ?? '';
                    $base = Str::slug($val);
                    if (empty($base)) $base = 'item-' . $row->id;
                }

                $slug = $base;
                $i = 1;
                while (in_array($slug, $usedSlugs)) {
                    $slug = $base . '-' . $i++;
                }
                $usedSlugs[] = $slug;

                DB::table($table)->where('id', $row->id)->update(['slug' => $slug]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $sourceCol) {
            if (Schema::hasColumn($table, 'slug')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('slug');
                });
            }
        }
    }
};
