<?php

namespace Database\Seeders;

use App\Models\BloxFruit\PermanentFruitPrice;
use Illuminate\Database\Seeder;

class PermanentFruitPriceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Perm Dragon', 'harga_robux' => 5000, 'harga_beli' => 325000, 'harga_jual' => 440000],
            ['nama' => 'Perm Kitsune', 'harga_robux' => 4000, 'harga_beli' => 260000, 'harga_jual' => 352000],
            ['nama' => 'Perm Control', 'harga_robux' => 4000, 'harga_beli' => 260000, 'harga_jual' => 352000],
            ['nama' => 'Perm Yeti', 'harga_robux' => 3000, 'harga_beli' => 195000, 'harga_jual' => 264000],
            ['nama' => 'Perm Tiger', 'harga_robux' => 3000, 'harga_beli' => 195000, 'harga_jual' => 264000],
            ['nama' => 'Perm Spirit', 'harga_robux' => 2550, 'harga_beli' => 165750, 'harga_jual' => 224400],
            ['nama' => 'Perm Gas', 'harga_robux' => 2500, 'harga_beli' => 162500, 'harga_jual' => 220000],
            ['nama' => 'Perm Venom', 'harga_robux' => 2450, 'harga_beli' => 159250, 'harga_jual' => 216000],
            ['nama' => 'Perm Shadow', 'harga_robux' => 2425, 'harga_beli' => 157625, 'harga_jual' => 214000],
            ['nama' => 'Perm Dough', 'harga_robux' => 2400, 'harga_beli' => 156000, 'harga_jual' => 211000],
            ['nama' => 'Perm T-Rex', 'harga_robux' => 2350, 'harga_beli' => 152750, 'harga_jual' => 207000],
            ['nama' => 'Perm Mammoth', 'harga_robux' => 2350, 'harga_beli' => 152750, 'harga_jual' => 207000],
            ['nama' => 'Perm Gravity', 'harga_robux' => 2300, 'harga_beli' => 149500, 'harga_jual' => 203000],
            ['nama' => 'Perm Blizzard', 'harga_robux' => 2250, 'harga_beli' => 146250, 'harga_jual' => 198000],
            ['nama' => 'Perm Pain', 'harga_robux' => 2200, 'harga_beli' => 143000, 'harga_jual' => 194000],
            ['nama' => 'Perm Lightning', 'harga_robux' => 2100, 'harga_beli' => 136500, 'harga_jual' => 185000],
            ['nama' => 'Perm Portal', 'harga_robux' => 2000, 'harga_beli' => 130000, 'harga_jual' => 176000],
            ['nama' => 'Perm Phonix', 'harga_robux' => 2000, 'harga_beli' => 130000, 'harga_jual' => 176000],
            ['nama' => 'Perm Sound', 'harga_robux' => 1900, 'harga_beli' => 123500, 'harga_jual' => 167200],
            ['nama' => 'Perm Spider', 'harga_robux' => 1800, 'harga_beli' => 117000, 'harga_jual' => 158400],
            ['nama' => 'Perm Creation', 'harga_robux' => 1750, 'harga_beli' => 113750, 'harga_jual' => 154000],
            ['nama' => 'Perm Love', 'harga_robux' => 1700, 'harga_beli' => 110500, 'harga_jual' => 149600],
            ['nama' => 'Perm Buddha', 'harga_robux' => 1650, 'harga_beli' => 107250, 'harga_jual' => 146000],
            ['nama' => 'Perm Quake', 'harga_robux' => 1500, 'harga_beli' => 97500, 'harga_jual' => 132000],
            ['nama' => 'Perm Magma', 'harga_robux' => 1300, 'harga_beli' => 84500, 'harga_jual' => 115000],
            ['nama' => 'Perm Ghost', 'harga_robux' => 1275, 'harga_beli' => 82875, 'harga_jual' => 112000],
            ['nama' => 'Perm Rubber', 'harga_robux' => 1200, 'harga_beli' => 78000, 'harga_jual' => 106000],
            ['nama' => 'Perm Light', 'harga_robux' => 1100, 'harga_beli' => 71500, 'harga_jual' => 97000],
            ['nama' => 'Perm Diamond', 'harga_robux' => 1000, 'harga_beli' => 65000, 'harga_jual' => 88000],
            ['nama' => 'Perm Eagle', 'harga_robux' => 975, 'harga_beli' => 63375, 'harga_jual' => 86000],
            ['nama' => 'Perm Dark', 'harga_robux' => 950, 'harga_beli' => 61750, 'harga_jual' => 84000],
            ['nama' => 'Perm Sand', 'harga_robux' => 850, 'harga_beli' => 55250, 'harga_jual' => 75000],
            ['nama' => 'Perm Ice', 'harga_robux' => 750, 'harga_beli' => 48750, 'harga_jual' => 66000],
            ['nama' => 'Perm Flame', 'harga_robux' => 550, 'harga_beli' => 35750, 'harga_jual' => 49000],
            ['nama' => 'Perm Spike', 'harga_robux' => 380, 'harga_beli' => 24700, 'harga_jual' => 34000],
            ['nama' => 'Perm Smoke', 'harga_robux' => 250, 'harga_beli' => 16250, 'harga_jual' => 22000],
            ['nama' => 'Perm Bomb', 'harga_robux' => 220, 'harga_beli' => 14300, 'harga_jual' => 20000],
            ['nama' => 'Perm Spring', 'harga_robux' => 180, 'harga_beli' => 11700, 'harga_jual' => 16000],
            ['nama' => 'Perm Blade', 'harga_robux' => 100, 'harga_beli' => 6500, 'harga_jual' => 9000],
            ['nama' => 'Perm Spin', 'harga_robux' => 75, 'harga_beli' => 4875, 'harga_jual' => 7000],
            ['nama' => 'Perm Rocket', 'harga_robux' => 50, 'harga_beli' => 3250, 'harga_jual' => 5000],
        ];

        foreach ($data as $item) {
            PermanentFruitPrice::updateOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}
