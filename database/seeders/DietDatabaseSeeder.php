<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DietTracker\FoodDatabase;
use App\Models\DietTracker\ExerciseDatabase;

class DietDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        //  DATABASE MAKANAN INDONESIA
        // ============================================================
        $foods = [
            // === SARAPAN ===
            ['nama' => 'Nasi Goreng', 'kategori' => 'sarapan', 'kalori' => 267, 'protein' => 8.5, 'karbohidrat' => 38.0, 'lemak' => 9.0, 'satuan_porsi' => '1 piring', 'berat_gram' => 200],
            ['nama' => 'Bubur Ayam', 'kategori' => 'sarapan', 'kalori' => 230, 'protein' => 12.0, 'karbohidrat' => 35.0, 'lemak' => 5.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 300],
            ['nama' => 'Lontong Sayur', 'kategori' => 'sarapan', 'kalori' => 210, 'protein' => 7.0, 'karbohidrat' => 30.0, 'lemak' => 7.5, 'satuan_porsi' => '1 porsi', 'berat_gram' => 250],
            ['nama' => 'Nasi Uduk', 'kategori' => 'sarapan', 'kalori' => 300, 'protein' => 6.0, 'karbohidrat' => 42.0, 'lemak' => 12.0, 'satuan_porsi' => '1 piring', 'berat_gram' => 200],
            ['nama' => 'Roti Bakar + Selai', 'kategori' => 'sarapan', 'kalori' => 180, 'protein' => 5.0, 'karbohidrat' => 28.0, 'lemak' => 5.5, 'satuan_porsi' => '2 lembar', 'berat_gram' => 80],
            ['nama' => 'Telur Dadar', 'kategori' => 'sarapan', 'kalori' => 154, 'protein' => 11.0, 'karbohidrat' => 1.0, 'lemak' => 12.0, 'satuan_porsi' => '2 butir', 'berat_gram' => 120],
            ['nama' => 'Telur Rebus', 'kategori' => 'sarapan', 'kalori' => 140, 'protein' => 12.0, 'karbohidrat' => 1.0, 'lemak' => 10.0, 'satuan_porsi' => '2 butir', 'berat_gram' => 100],
            ['nama' => 'Oatmeal + Pisang', 'kategori' => 'sarapan', 'kalori' => 220, 'protein' => 7.0, 'karbohidrat' => 40.0, 'lemak' => 4.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 250],
            ['nama' => 'Mie Goreng Instan', 'kategori' => 'sarapan', 'kalori' => 380, 'protein' => 8.0, 'karbohidrat' => 52.0, 'lemak' => 16.0, 'satuan_porsi' => '1 bungkus', 'berat_gram' => 85],
            ['nama' => 'Ketoprak', 'kategori' => 'sarapan', 'kalori' => 250, 'protein' => 10.0, 'karbohidrat' => 32.0, 'lemak' => 9.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 250],

            // === MAKAN UTAMA ===
            ['nama' => 'Nasi Putih', 'kategori' => 'makan_utama', 'kalori' => 204, 'protein' => 4.0, 'karbohidrat' => 44.0, 'lemak' => 0.4, 'satuan_porsi' => '1 piring', 'berat_gram' => 150],
            ['nama' => 'Nasi Merah', 'kategori' => 'makan_utama', 'kalori' => 180, 'protein' => 4.0, 'karbohidrat' => 38.0, 'lemak' => 1.0, 'satuan_porsi' => '1 piring', 'berat_gram' => 150],
            ['nama' => 'Ayam Goreng', 'kategori' => 'makan_utama', 'kalori' => 260, 'protein' => 27.0, 'karbohidrat' => 3.0, 'lemak' => 15.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 120],
            ['nama' => 'Ayam Bakar', 'kategori' => 'makan_utama', 'kalori' => 200, 'protein' => 28.0, 'karbohidrat' => 2.0, 'lemak' => 9.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 120],
            ['nama' => 'Ayam Geprek', 'kategori' => 'makan_utama', 'kalori' => 350, 'protein' => 25.0, 'karbohidrat' => 18.0, 'lemak' => 20.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 180],
            ['nama' => 'Ikan Goreng', 'kategori' => 'makan_utama', 'kalori' => 190, 'protein' => 22.0, 'karbohidrat' => 5.0, 'lemak' => 9.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 120],
            ['nama' => 'Ikan Bakar', 'kategori' => 'makan_utama', 'kalori' => 160, 'protein' => 24.0, 'karbohidrat' => 0.0, 'lemak' => 7.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 120],
            ['nama' => 'Tempe Goreng', 'kategori' => 'makan_utama', 'kalori' => 160, 'protein' => 11.0, 'karbohidrat' => 8.0, 'lemak' => 10.0, 'satuan_porsi' => '2 potong', 'berat_gram' => 80],
            ['nama' => 'Tahu Goreng', 'kategori' => 'makan_utama', 'kalori' => 115, 'protein' => 8.0, 'karbohidrat' => 4.0, 'lemak' => 8.0, 'satuan_porsi' => '2 potong', 'berat_gram' => 80],
            ['nama' => 'Rendang Daging', 'kategori' => 'makan_utama', 'kalori' => 285, 'protein' => 22.0, 'karbohidrat' => 5.0, 'lemak' => 20.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 100],
            ['nama' => 'Soto Ayam', 'kategori' => 'makan_utama', 'kalori' => 180, 'protein' => 15.0, 'karbohidrat' => 12.0, 'lemak' => 8.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 300],
            ['nama' => 'Bakso', 'kategori' => 'makan_utama', 'kalori' => 250, 'protein' => 14.0, 'karbohidrat' => 28.0, 'lemak' => 9.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 350],
            ['nama' => 'Mie Ayam', 'kategori' => 'makan_utama', 'kalori' => 320, 'protein' => 15.0, 'karbohidrat' => 42.0, 'lemak' => 10.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 300],
            ['nama' => 'Gado-gado', 'kategori' => 'makan_utama', 'kalori' => 220, 'protein' => 10.0, 'karbohidrat' => 18.0, 'lemak' => 13.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 250],
            ['nama' => 'Pecel Lele', 'kategori' => 'makan_utama', 'kalori' => 280, 'protein' => 20.0, 'karbohidrat' => 8.0, 'lemak' => 18.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 150],
            ['nama' => 'Nasi Padang (komplit)', 'kategori' => 'makan_utama', 'kalori' => 550, 'protein' => 25.0, 'karbohidrat' => 55.0, 'lemak' => 25.0, 'satuan_porsi' => '1 piring', 'berat_gram' => 400],
            ['nama' => 'Sate Ayam (10 tusuk)', 'kategori' => 'makan_utama', 'kalori' => 350, 'protein' => 28.0, 'karbohidrat' => 12.0, 'lemak' => 22.0, 'satuan_porsi' => '10 tusuk', 'berat_gram' => 200],
            ['nama' => 'Rawon', 'kategori' => 'makan_utama', 'kalori' => 240, 'protein' => 18.0, 'karbohidrat' => 10.0, 'lemak' => 14.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 300],
            ['nama' => 'Sayur Asem', 'kategori' => 'makan_utama', 'kalori' => 60, 'protein' => 2.0, 'karbohidrat' => 10.0, 'lemak' => 1.5, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 200],
            ['nama' => 'Sayur Bayam', 'kategori' => 'makan_utama', 'kalori' => 45, 'protein' => 3.0, 'karbohidrat' => 6.0, 'lemak' => 1.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 150],
            ['nama' => 'Capcay', 'kategori' => 'makan_utama', 'kalori' => 120, 'protein' => 5.0, 'karbohidrat' => 12.0, 'lemak' => 6.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Tumis Kangkung', 'kategori' => 'makan_utama', 'kalori' => 70, 'protein' => 3.0, 'karbohidrat' => 5.0, 'lemak' => 4.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 150],
            ['nama' => 'Sambal Goreng Kentang', 'kategori' => 'makan_utama', 'kalori' => 180, 'protein' => 3.0, 'karbohidrat' => 22.0, 'lemak' => 9.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 100],
            ['nama' => 'Perkedel Kentang', 'kategori' => 'makan_utama', 'kalori' => 150, 'protein' => 5.0, 'karbohidrat' => 15.0, 'lemak' => 8.0, 'satuan_porsi' => '2 buah', 'berat_gram' => 80],
            ['nama' => 'Udang Goreng Tepung', 'kategori' => 'makan_utama', 'kalori' => 220, 'protein' => 18.0, 'karbohidrat' => 12.0, 'lemak' => 12.0, 'satuan_porsi' => '5 ekor', 'berat_gram' => 100],
            ['nama' => 'Cumi Goreng Tepung', 'kategori' => 'makan_utama', 'kalori' => 200, 'protein' => 15.0, 'karbohidrat' => 14.0, 'lemak' => 10.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 100],
            ['nama' => 'Daging Sapi Goreng', 'kategori' => 'makan_utama', 'kalori' => 250, 'protein' => 26.0, 'karbohidrat' => 0.0, 'lemak' => 16.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 100],
            ['nama' => 'Sup Sayuran', 'kategori' => 'makan_utama', 'kalori' => 80, 'protein' => 3.0, 'karbohidrat' => 12.0, 'lemak' => 2.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 250],
            ['nama' => 'Sop Buntut', 'kategori' => 'makan_utama', 'kalori' => 300, 'protein' => 20.0, 'karbohidrat' => 8.0, 'lemak' => 22.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 350],
            ['nama' => 'Tongseng Kambing', 'kategori' => 'makan_utama', 'kalori' => 280, 'protein' => 18.0, 'karbohidrat' => 10.0, 'lemak' => 19.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Nasi + Lauk Sederhana', 'kategori' => 'makan_utama', 'kalori' => 400, 'protein' => 18.0, 'karbohidrat' => 50.0, 'lemak' => 14.0, 'satuan_porsi' => '1 piring', 'berat_gram' => 300],

            // === SNACK ===
            ['nama' => 'Pisang', 'kategori' => 'snack', 'kalori' => 105, 'protein' => 1.3, 'karbohidrat' => 27.0, 'lemak' => 0.4, 'satuan_porsi' => '1 buah', 'berat_gram' => 120],
            ['nama' => 'Apel', 'kategori' => 'snack', 'kalori' => 72, 'protein' => 0.4, 'karbohidrat' => 19.0, 'lemak' => 0.2, 'satuan_porsi' => '1 buah', 'berat_gram' => 150],
            ['nama' => 'Jeruk', 'kategori' => 'snack', 'kalori' => 62, 'protein' => 1.2, 'karbohidrat' => 15.0, 'lemak' => 0.2, 'satuan_porsi' => '1 buah', 'berat_gram' => 130],
            ['nama' => 'Pepaya', 'kategori' => 'snack', 'kalori' => 55, 'protein' => 0.6, 'karbohidrat' => 14.0, 'lemak' => 0.1, 'satuan_porsi' => '1 potong', 'berat_gram' => 140],
            ['nama' => 'Semangka', 'kategori' => 'snack', 'kalori' => 46, 'protein' => 0.9, 'karbohidrat' => 11.5, 'lemak' => 0.2, 'satuan_porsi' => '1 potong', 'berat_gram' => 150],
            ['nama' => 'Kacang Tanah Rebus', 'kategori' => 'snack', 'kalori' => 160, 'protein' => 7.0, 'karbohidrat' => 6.0, 'lemak' => 12.0, 'satuan_porsi' => '1 genggam', 'berat_gram' => 30],
            ['nama' => 'Keripik Tempe', 'kategori' => 'snack', 'kalori' => 200, 'protein' => 8.0, 'karbohidrat' => 18.0, 'lemak' => 11.0, 'satuan_porsi' => '1 bungkus kecil', 'berat_gram' => 50],
            ['nama' => 'Gorengan (bakwan)', 'kategori' => 'snack', 'kalori' => 150, 'protein' => 3.0, 'karbohidrat' => 15.0, 'lemak' => 9.0, 'satuan_porsi' => '2 buah', 'berat_gram' => 60],
            ['nama' => 'Kue Putu', 'kategori' => 'snack', 'kalori' => 130, 'protein' => 2.0, 'karbohidrat' => 22.0, 'lemak' => 4.0, 'satuan_porsi' => '2 buah', 'berat_gram' => 80],
            ['nama' => 'Onde-onde', 'kategori' => 'snack', 'kalori' => 170, 'protein' => 3.0, 'karbohidrat' => 28.0, 'lemak' => 5.0, 'satuan_porsi' => '2 buah', 'berat_gram' => 80],
            ['nama' => 'Yogurt', 'kategori' => 'snack', 'kalori' => 100, 'protein' => 5.0, 'karbohidrat' => 15.0, 'lemak' => 2.0, 'satuan_porsi' => '1 cup', 'berat_gram' => 150],
            ['nama' => 'Roti Gandum', 'kategori' => 'snack', 'kalori' => 80, 'protein' => 3.0, 'karbohidrat' => 14.0, 'lemak' => 1.0, 'satuan_porsi' => '1 lembar', 'berat_gram' => 30],
            ['nama' => 'Salad Buah', 'kategori' => 'snack', 'kalori' => 120, 'protein' => 1.0, 'karbohidrat' => 25.0, 'lemak' => 2.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 200],
            ['nama' => 'Martabak Manis (1 potong)', 'kategori' => 'snack', 'kalori' => 280, 'protein' => 6.0, 'karbohidrat' => 35.0, 'lemak' => 13.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 80],
            ['nama' => 'Es Krim', 'kategori' => 'snack', 'kalori' => 200, 'protein' => 3.5, 'karbohidrat' => 24.0, 'lemak' => 10.0, 'satuan_porsi' => '1 scoop', 'berat_gram' => 100],

            // === MINUMAN ===
            ['nama' => 'Air Putih', 'kategori' => 'minuman', 'kalori' => 0, 'protein' => 0, 'karbohidrat' => 0, 'lemak' => 0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Teh Manis', 'kategori' => 'minuman', 'kalori' => 80, 'protein' => 0, 'karbohidrat' => 20.0, 'lemak' => 0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Teh Tawar', 'kategori' => 'minuman', 'kalori' => 2, 'protein' => 0, 'karbohidrat' => 0.5, 'lemak' => 0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Kopi Hitam (tanpa gula)', 'kategori' => 'minuman', 'kalori' => 5, 'protein' => 0.3, 'karbohidrat' => 0, 'lemak' => 0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Kopi Susu', 'kategori' => 'minuman', 'kalori' => 120, 'protein' => 3.0, 'karbohidrat' => 18.0, 'lemak' => 4.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Susu Full Cream', 'kategori' => 'minuman', 'kalori' => 150, 'protein' => 8.0, 'karbohidrat' => 12.0, 'lemak' => 8.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Susu Rendah Lemak', 'kategori' => 'minuman', 'kalori' => 100, 'protein' => 8.0, 'karbohidrat' => 12.0, 'lemak' => 2.5, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Jus Jeruk', 'kategori' => 'minuman', 'kalori' => 110, 'protein' => 1.5, 'karbohidrat' => 26.0, 'lemak' => 0.3, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Jus Alpukat', 'kategori' => 'minuman', 'kalori' => 200, 'protein' => 3.0, 'karbohidrat' => 20.0, 'lemak' => 12.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Es Teh Manis', 'kategori' => 'minuman', 'kalori' => 90, 'protein' => 0, 'karbohidrat' => 22.0, 'lemak' => 0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 300],
            ['nama' => 'Minuman Bersoda', 'kategori' => 'minuman', 'kalori' => 140, 'protein' => 0, 'karbohidrat' => 35.0, 'lemak' => 0, 'satuan_porsi' => '1 kaleng', 'berat_gram' => 330],
        ];

        foreach ($foods as $food) {
            FoodDatabase::updateOrCreate(['nama' => $food['nama']], $food);
        }
        $this->command->info('Seeded ' . count($foods) . ' makanan.');

        // ============================================================
        //  DATABASE OLAHRAGA
        // ============================================================
        $exercises = [
            // Kardio
            ['nama' => 'Jalan Kaki', 'kategori' => 'kardio', 'intensitas' => 'ringan', 'kalori_per_menit' => 4, 'deskripsi' => 'Jalan santai 4-5 km/jam. Cocok untuk pemula.'],
            ['nama' => 'Jalan Cepat', 'kategori' => 'kardio', 'intensitas' => 'sedang', 'kalori_per_menit' => 6, 'deskripsi' => 'Jalan cepat 6-7 km/jam.'],
            ['nama' => 'Jogging', 'kategori' => 'kardio', 'intensitas' => 'sedang', 'kalori_per_menit' => 8, 'deskripsi' => 'Lari santai 7-9 km/jam.'],
            ['nama' => 'Lari', 'kategori' => 'kardio', 'intensitas' => 'berat', 'kalori_per_menit' => 12, 'deskripsi' => 'Lari cepat 10+ km/jam.'],
            ['nama' => 'Bersepeda', 'kategori' => 'kardio', 'intensitas' => 'sedang', 'kalori_per_menit' => 7, 'deskripsi' => 'Bersepeda santai hingga sedang.'],
            ['nama' => 'Bersepeda Cepat', 'kategori' => 'kardio', 'intensitas' => 'berat', 'kalori_per_menit' => 11, 'deskripsi' => 'Bersepeda kecepatan tinggi / menanjak.'],
            ['nama' => 'Renang', 'kategori' => 'kardio', 'intensitas' => 'sedang', 'kalori_per_menit' => 9, 'deskripsi' => 'Renang gaya bebas atau dada.'],
            ['nama' => 'Lompat Tali', 'kategori' => 'kardio', 'intensitas' => 'berat', 'kalori_per_menit' => 13, 'deskripsi' => 'Skipping / lompat tali intensitas tinggi.'],
            ['nama' => 'Senam Aerobik', 'kategori' => 'kardio', 'intensitas' => 'sedang', 'kalori_per_menit' => 7, 'deskripsi' => 'Senam aerobik / zumba.'],
            ['nama' => 'Naik Tangga', 'kategori' => 'kardio', 'intensitas' => 'berat', 'kalori_per_menit' => 10, 'deskripsi' => 'Naik turun tangga.'],

            // Kekuatan
            ['nama' => 'Push Up', 'kategori' => 'kekuatan', 'intensitas' => 'sedang', 'kalori_per_menit' => 7, 'deskripsi' => 'Latihan dada dan trisep.'],
            ['nama' => 'Sit Up', 'kategori' => 'kekuatan', 'intensitas' => 'sedang', 'kalori_per_menit' => 6, 'deskripsi' => 'Latihan perut.'],
            ['nama' => 'Plank', 'kategori' => 'kekuatan', 'intensitas' => 'sedang', 'kalori_per_menit' => 5, 'deskripsi' => 'Latihan core / perut. Tahan posisi.'],
            ['nama' => 'Squat', 'kategori' => 'kekuatan', 'intensitas' => 'sedang', 'kalori_per_menit' => 8, 'deskripsi' => 'Latihan kaki dan bokong.'],
            ['nama' => 'Angkat Beban', 'kategori' => 'kekuatan', 'intensitas' => 'berat', 'kalori_per_menit' => 9, 'deskripsi' => 'Weight training di gym.'],
            ['nama' => 'Pull Up', 'kategori' => 'kekuatan', 'intensitas' => 'berat', 'kalori_per_menit' => 8, 'deskripsi' => 'Latihan punggung dan bisep.'],
            ['nama' => 'Lunges', 'kategori' => 'kekuatan', 'intensitas' => 'sedang', 'kalori_per_menit' => 7, 'deskripsi' => 'Latihan kaki bergantian.'],

            // Fleksibilitas
            ['nama' => 'Yoga', 'kategori' => 'fleksibilitas', 'intensitas' => 'ringan', 'kalori_per_menit' => 4, 'deskripsi' => 'Yoga untuk fleksibilitas dan relaksasi.'],
            ['nama' => 'Stretching', 'kategori' => 'fleksibilitas', 'intensitas' => 'ringan', 'kalori_per_menit' => 3, 'deskripsi' => 'Peregangan otot seluruh tubuh.'],
            ['nama' => 'Pilates', 'kategori' => 'fleksibilitas', 'intensitas' => 'sedang', 'kalori_per_menit' => 5, 'deskripsi' => 'Latihan core dan fleksibilitas.'],

            // HIIT
            ['nama' => 'HIIT Workout', 'kategori' => 'hiit', 'intensitas' => 'berat', 'kalori_per_menit' => 14, 'deskripsi' => 'High Intensity Interval Training. Bakar kalori maksimal.'],
            ['nama' => 'Burpees', 'kategori' => 'hiit', 'intensitas' => 'berat', 'kalori_per_menit' => 12, 'deskripsi' => 'Full body exercise intensitas tinggi.'],
            ['nama' => 'Mountain Climbers', 'kategori' => 'hiit', 'intensitas' => 'berat', 'kalori_per_menit' => 11, 'deskripsi' => 'Latihan kardio + core.'],
            ['nama' => 'Tabata', 'kategori' => 'hiit', 'intensitas' => 'berat', 'kalori_per_menit' => 15, 'deskripsi' => '20 detik max effort, 10 detik rest. 4 menit per set.'],
        ];

        foreach ($exercises as $ex) {
            ExerciseDatabase::updateOrCreate(['nama' => $ex['nama']], $ex);
        }
        $this->command->info('Seeded ' . count($exercises) . ' olahraga.');
    }
}
