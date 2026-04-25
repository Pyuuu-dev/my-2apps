<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\StorageAccount;

class BloxFruitSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================================
        //  MASTER BUAH
        // =============================================================
        $fruits = [
            // Common
            ['nama' => 'Rocket',    'tipe' => 'Natural',   'rarity' => 'Common',    'harga_beli' => 5000,     'harga_jual' => 2500],
            ['nama' => 'Spin',      'tipe' => 'Natural',   'rarity' => 'Common',    'harga_beli' => 7500,     'harga_jual' => 3750],
            ['nama' => 'Blade',     'tipe' => 'Natural',   'rarity' => 'Common',    'harga_beli' => 30000,    'harga_jual' => 15000],
            ['nama' => 'Bomb',      'tipe' => 'Natural',   'rarity' => 'Common',    'harga_beli' => 5000,     'harga_jual' => 2500],
            ['nama' => 'Spike',     'tipe' => 'Natural',   'rarity' => 'Common',    'harga_beli' => 7500,     'harga_jual' => 3750],
            ['nama' => 'Spring',    'tipe' => 'Natural',   'rarity' => 'Common',    'harga_beli' => 60000,    'harga_jual' => 30000],
            ['nama' => 'Smoke',     'tipe' => 'Elemental', 'rarity' => 'Common',    'harga_beli' => 100000,   'harga_jual' => 50000],

            // Uncommon
            ['nama' => 'Flame',     'tipe' => 'Elemental', 'rarity' => 'Uncommon',  'harga_beli' => 250000,   'harga_jual' => 125000],
            ['nama' => 'Ice',       'tipe' => 'Elemental', 'rarity' => 'Uncommon',  'harga_beli' => 350000,   'harga_jual' => 175000],
            ['nama' => 'Sand',      'tipe' => 'Elemental', 'rarity' => 'Uncommon',  'harga_beli' => 420000,   'harga_jual' => 210000],
            ['nama' => 'Dark',      'tipe' => 'Elemental', 'rarity' => 'Uncommon',  'harga_beli' => 500000,   'harga_jual' => 250000],
            ['nama' => 'Diamond',   'tipe' => 'Natural',   'rarity' => 'Uncommon',  'harga_beli' => 600000,   'harga_jual' => 300000],
            ['nama' => 'Eagle',     'tipe' => 'Beast',     'rarity' => 'Uncommon',  'harga_beli' => 300000,   'harga_jual' => 150000],

            // Rare
            ['nama' => 'Love',      'tipe' => 'Natural',   'rarity' => 'Rare',      'harga_beli' => 700000,   'harga_jual' => 350000],
            ['nama' => 'Rubber',    'tipe' => 'Natural',   'rarity' => 'Rare',      'harga_beli' => 750000,   'harga_jual' => 375000],
            ['nama' => 'Ghost',     'tipe' => 'Natural',   'rarity' => 'Rare',      'harga_beli' => 940000,   'harga_jual' => 470000],
            ['nama' => 'Light',     'tipe' => 'Elemental', 'rarity' => 'Rare',      'harga_beli' => 650000,   'harga_jual' => 325000],
            ['nama' => 'Magma',     'tipe' => 'Elemental', 'rarity' => 'Rare',      'harga_beli' => 850000,   'harga_jual' => 425000],
            ['nama' => 'Phoenix',   'tipe' => 'Beast',     'rarity' => 'Rare',      'harga_beli' => 1800000,  'harga_jual' => 900000],

            // Legendary
            ['nama' => 'Quake',     'tipe' => 'Natural',   'rarity' => 'Legendary', 'harga_beli' => 1000000,  'harga_jual' => 500000],
            ['nama' => 'Buddha',    'tipe' => 'Natural',   'rarity' => 'Legendary', 'harga_beli' => 1200000,  'harga_jual' => 600000],
            ['nama' => 'Spider',    'tipe' => 'Beast',     'rarity' => 'Legendary', 'harga_beli' => 1500000,  'harga_jual' => 750000],
            ['nama' => 'Sound',     'tipe' => 'Elemental', 'rarity' => 'Legendary', 'harga_beli' => 1700000,  'harga_jual' => 850000],
            ['nama' => 'Portal',    'tipe' => 'Elemental', 'rarity' => 'Legendary', 'harga_beli' => 1900000,  'harga_jual' => 950000],
            ['nama' => 'Lightning', 'tipe' => 'Elemental', 'rarity' => 'Legendary', 'harga_beli' => 2100000,  'harga_jual' => 1050000],
            ['nama' => 'Pain',      'tipe' => 'Natural',   'rarity' => 'Legendary', 'harga_beli' => 2300000,  'harga_jual' => 1150000],
            ['nama' => 'Blizzard',  'tipe' => 'Elemental', 'rarity' => 'Legendary', 'harga_beli' => 2400000,  'harga_jual' => 1200000],
            ['nama' => 'Gravity',   'tipe' => 'Natural',   'rarity' => 'Legendary', 'harga_beli' => 2500000,  'harga_jual' => 1250000],
            ['nama' => 'Control',   'tipe' => 'Natural',   'rarity' => 'Legendary', 'harga_beli' => 3200000,  'harga_jual' => 1600000],
            ['nama' => 'Dragon',    'tipe' => 'Beast',     'rarity' => 'Legendary', 'harga_beli' => 3500000,  'harga_jual' => 1750000],

            // Mythical
            ['nama' => 'T-Rex',         'tipe' => 'Beast',     'rarity' => 'Mythical', 'harga_beli' => 2700000,  'harga_jual' => 1350000],
            ['nama' => 'Dough',         'tipe' => 'Elemental', 'rarity' => 'Mythical', 'harga_beli' => 2800000,  'harga_jual' => 1400000],
            ['nama' => 'Shadow',        'tipe' => 'Elemental', 'rarity' => 'Mythical', 'harga_beli' => 2900000,  'harga_jual' => 1450000],
            ['nama' => 'Venom',         'tipe' => 'Elemental', 'rarity' => 'Mythical', 'harga_beli' => 3000000,  'harga_jual' => 1500000],
            ['nama' => 'Creation',      'tipe' => 'Natural',   'rarity' => 'Mythical', 'harga_beli' => 3200000,  'harga_jual' => 1600000],
            ['nama' => 'Spirit',        'tipe' => 'Natural',   'rarity' => 'Mythical', 'harga_beli' => 3400000,  'harga_jual' => 1700000],
            ['nama' => 'Gas',           'tipe' => 'Elemental', 'rarity' => 'Mythical', 'harga_beli' => 4200000,  'harga_jual' => 2100000],
            ['nama' => 'Tiger',         'tipe' => 'Beast',     'rarity' => 'Mythical', 'harga_beli' => 4500000,  'harga_jual' => 2250000],
            ['nama' => 'Leopard',       'tipe' => 'Beast',     'rarity' => 'Mythical', 'harga_beli' => 5000000,  'harga_jual' => 2500000],
            ['nama' => 'Mammoth',       'tipe' => 'Beast',     'rarity' => 'Mythical', 'harga_beli' => 5000000,  'harga_jual' => 2500000],
            ['nama' => 'Yeti',          'tipe' => 'Beast',     'rarity' => 'Mythical', 'harga_beli' => 5500000,  'harga_jual' => 2750000],
            ['nama' => 'Kitsune',       'tipe' => 'Beast',     'rarity' => 'Mythical', 'harga_beli' => 8000000,  'harga_jual' => 4000000],
            // Dragon variants (fruit, bukan skin)
            ['nama' => 'Dragon West',   'tipe' => 'Beast',     'rarity' => 'Legendary', 'harga_beli' => 3500000,  'harga_jual' => 1750000],
            ['nama' => 'Dragon East',   'tipe' => 'Beast',     'rarity' => 'Legendary', 'harga_beli' => 3500000,  'harga_jual' => 1750000],
        ];

        foreach ($fruits as $f) {
            BloxFruit::updateOrCreate(['nama' => $f['nama']], array_merge($f, ['aktif' => true]));
        }
        $this->command->info('Seeded ' . count($fruits) . ' buah.');

        // =============================================================
        //  MASTER SKIN BUAH
        // =============================================================
        $skins = [
            // Dragon skins
            ['fruit' => 'Dragon',    'nama_skin' => 'Dragon Ember West'],
            // Kitsune skins
            ['fruit' => 'Kitsune',   'nama_skin' => 'Galaxy Kitsune'],
            ['fruit' => 'Kitsune',   'nama_skin' => 'Empyrean Kitsune'],
            // Yeti skins
            ['fruit' => 'Yeti',      'nama_skin' => 'Fiend Yeti'],
            ['fruit' => 'Yeti',      'nama_skin' => 'Werewolf'],
            // Portal skins
            ['fruit' => 'Portal',    'nama_skin' => 'Divine Portal'],
            // Lightning skins
            ['fruit' => 'Lightning', 'nama_skin' => 'Purple Lightning'],
            ['fruit' => 'Lightning', 'nama_skin' => 'Green Lightning'],
            // Pain skins
            ['fruit' => 'Pain',      'nama_skin' => 'Torment Pain'],
            // Diamond skins
            ['fruit' => 'Diamond',   'nama_skin' => 'Topaz Diamond'],
            ['fruit' => 'Diamond',   'nama_skin' => 'Emerald Diamond'],
            ['fruit' => 'Diamond',   'nama_skin' => 'Ruby Diamond'],
            // Eagle skins
            ['fruit' => 'Eagle',     'nama_skin' => 'Glacier Eagle'],
            // Bomb skins
            ['fruit' => 'Bomb',      'nama_skin' => 'Celebration Bomb'],
        ];

        foreach ($skins as $s) {
            $fruit = BloxFruit::where('nama', $s['fruit'])->first();
            if ($fruit) {
                FruitSkin::updateOrCreate(
                    ['blox_fruit_id' => $fruit->id, 'nama_skin' => $s['nama_skin']],
                    ['aktif' => true]
                );
            }
        }
        $this->command->info('Seeded ' . count($skins) . ' skin.');

        // =============================================================
        //  MASTER GAMEPASS
        // =============================================================
        $gamepasses = [
            ['nama' => '2x Mastery',     'deskripsi' => 'Double mastery EXP untuk semua fighting style dan buah.'],
            ['nama' => '2x Money',       'deskripsi' => 'Double uang dari semua sumber.'],
            ['nama' => '+1 Storage',     'deskripsi' => 'Tambahan 1 slot penyimpanan buah.'],
            ['nama' => '2x Drop',        'deskripsi' => 'Double drop rate dari semua musuh.'],
            ['nama' => 'Fast Boat',      'deskripsi' => 'Kapal dengan kecepatan lebih tinggi.'],
            ['nama' => 'Darkblade',      'deskripsi' => 'Pedang legendary Darkblade (Yoru).'],
            ['nama' => 'Fruit Notifier', 'deskripsi' => 'Notifikasi saat buah spawn di server.'],
        ];

        foreach ($gamepasses as $gp) {
            Gamepass::updateOrCreate(['nama' => $gp['nama']], array_merge($gp, ['aktif' => true]));
        }
        $this->command->info('Seeded ' . count($gamepasses) . ' gamepass.');

        // =============================================================
        //  AKUN STORAGE
        // =============================================================
        $accounts = [
            ['nama_akun' => 'CHROME 7',  'username' => 'pyuuuu_p'],
            ['nama_akun' => 'EDGE',      'username' => 'LDCaster'],
            ['nama_akun' => 'EDGE',      'username' => 'hanh1112009ngu'],
            ['nama_akun' => 'CHROME 1',  'username' => 'eidenis26'],
            ['nama_akun' => 'CHROME 1',  'username' => 'Nevimann'],
            ['nama_akun' => 'CHROME 1',  'username' => 'Famgod3333'],
            ['nama_akun' => 'CHROME 1',  'username' => 'plsineedthisfruit'],
            ['nama_akun' => 'CHROME 1',  'username' => 'bnjeb2623b382'],
            ['nama_akun' => 'CHROME 2',  'username' => 'Hotjilla'],
            ['nama_akun' => 'CHROME 2',  'username' => 'kittyboy123145'],
            ['nama_akun' => 'CHROME 2',  'username' => 'Bfgt566'],
            ['nama_akun' => 'CHROME 3',  'username' => 'AKOSIABNOY_16'],
            ['nama_akun' => 'CHROME 3',  'username' => 'Kirklandwat5313'],
            ['nama_akun' => 'CHROME 3',  'username' => 'HongThuyo2225'],
            ['nama_akun' => 'CHROME 3',  'username' => 'Newbie214xo'],
            ['nama_akun' => 'CHROME 4',  'username' => 'Rayan12mg'],
            ['nama_akun' => 'CHROME 4',  'username' => 'Blziin_x'],
            ['nama_akun' => 'CHROME 4',  'username' => 'iambadinsgb'],
            ['nama_akun' => 'CHROME 4',  'username' => 'phantomendbringer'],
            ['nama_akun' => 'CHROME 4',  'username' => 'soalbreker'],
            ['nama_akun' => 'CHROME 3',  'username' => 'Damianayals'],
            ['nama_akun' => 'CHROME 5',  'username' => 'Toitle22'],
            ['nama_akun' => 'CHROME 2',  'username' => 'Eevee9normal'],
            ['nama_akun' => 'CHROME 2',  'username' => 'royality012345'],
        ];

        foreach ($accounts as $acc) {
            StorageAccount::updateOrCreate(
                ['username' => $acc['username']],
                array_merge($acc, ['aktif' => true])
            );
        }
        $this->command->info('Seeded ' . count($accounts) . ' akun storage.');
    }
}
