<?php

namespace Database\Seeders;

use App\Models\BloxFruit\JokiService;
use Illuminate\Database\Seeder;

class JokiServiceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Joki Level
            ['kategori' => 'level', 'nama' => '100 Level Sea 1', 'harga' => 4000],
            ['kategori' => 'level', 'nama' => '100 Level Sea 2 / Sea 3', 'harga' => 5000],

            // Belly & Fragment
            ['kategori' => 'belly_fragment', 'nama' => '1M Belly Sea 1', 'harga' => 5000],
            ['kategori' => 'belly_fragment', 'nama' => '1M Belly Sea 2 / Sea 3', 'harga' => 4000],
            ['kategori' => 'belly_fragment', 'nama' => '1K Fragment', 'harga' => 1000],

            // Mastery
            ['kategori' => 'mastery', 'nama' => '100 Mastery FS & Sword', 'harga' => 4000],
            ['kategori' => 'mastery', 'nama' => '100 Mastery Gun & Blox Fruit', 'harga' => 5000],

            // Fighting Style V2
            ['kategori' => 'fighting_style', 'nama' => 'Death Step', 'harga' => 15000],
            ['kategori' => 'fighting_style', 'nama' => 'Electric Claw', 'harga' => 15000],
            ['kategori' => 'fighting_style', 'nama' => 'Sharkman Karate', 'harga' => 15000],
            ['kategori' => 'fighting_style', 'nama' => 'Dragon Talon', 'harga' => 20000],
            ['kategori' => 'fighting_style', 'nama' => 'God Human', 'harga' => 40000, 'keterangan' => 'Full'],

            // Sword
            ['kategori' => 'sword', 'nama' => 'Legendary Sword', 'harga' => 10000, 'keterangan' => 'per 1'],
            ['kategori' => 'sword', 'nama' => 'Yama', 'harga' => 20000, 'keterangan' => 'per 1'],

            // Gun
            ['kategori' => 'gun', 'nama' => 'Skull Guitar (Tanpa Bahan)', 'harga' => 25000],
            ['kategori' => 'gun', 'nama' => 'Skull Guitar (Ready Bahan)', 'harga' => 15000],
            ['kategori' => 'gun', 'nama' => 'Kabucha', 'harga' => 5000],

            // Race
            ['kategori' => 'race', 'nama' => 'Race V2', 'harga' => 5000],
            ['kategori' => 'race', 'nama' => 'Race V3 (Mink, Shark, Human)', 'harga' => 10000],
            ['kategori' => 'race', 'nama' => 'Ghoul Race', 'harga' => 25000],
            ['kategori' => 'race', 'nama' => 'Cyborg Race (Insert FOD)', 'harga' => 15000],
            ['kategori' => 'race', 'nama' => 'Cyborg Race (Full)', 'harga' => 25000],

            // Boss Raid
            ['kategori' => 'boss_raid', 'nama' => 'Rip Indra', 'harga' => 15000],
            ['kategori' => 'boss_raid', 'nama' => 'Dough King', 'harga' => 15000],
            ['kategori' => 'boss_raid', 'nama' => 'Darkbeard', 'harga' => 12000],

            // Haki Legendary
            ['kategori' => 'haki', 'nama' => '1 Legendary Haki (Recipe)', 'harga' => 10000],
            ['kategori' => 'haki', 'nama' => '1 Legendary Haki (Recipe + Craft)', 'harga' => 15000],

            // Instinct
            ['kategori' => 'instinct', 'nama' => '1K Exp Instinct', 'harga' => 8000],
            ['kategori' => 'instinct', 'nama' => 'Full Instinct V2 (Ready 5M Belly)', 'harga' => 30000],
            ['kategori' => 'instinct', 'nama' => 'Full Instinct V2 (Tidak Ready 5M Belly)', 'harga' => 37000],

            // Awaken Fruit
            ['kategori' => 'awaken', 'nama' => 'Raid Normal (Full Awaken)', 'harga' => 15000],
            ['kategori' => 'awaken', 'nama' => 'Raid Advanced (Full Awaken)', 'harga' => 20000],

            // Material
            ['kategori' => 'material', 'nama' => 'Dragon Scale (4 pcs)', 'harga' => 2000],
            ['kategori' => 'material', 'nama' => 'Scrap Metal (20 pcs)', 'harga' => 3000],
            ['kategori' => 'material', 'nama' => 'Rare Material (5 pcs)', 'harga' => 2000],
            ['kategori' => 'material', 'nama' => 'Uncommon Material (10 pcs)', 'harga' => 1000],
        ];

        foreach ($data as $item) {
            JokiService::updateOrCreate(
                ['kategori' => $item['kategori'], 'nama' => $item['nama']],
                $item
            );
        }
    }
}
