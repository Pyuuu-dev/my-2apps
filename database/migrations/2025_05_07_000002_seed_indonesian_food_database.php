<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $foods = [
            // Nasi & Karbohidrat
            ['nama' => 'Nasi Putih', 'kategori' => 'nasi', 'kalori' => 175, 'protein' => 3.0, 'karbohidrat' => 40.0, 'lemak' => 0.3, 'satuan_porsi' => '1 centong (100g)', 'berat_gram' => 100],
            ['nama' => 'Nasi Goreng', 'kategori' => 'nasi', 'kalori' => 350, 'protein' => 8.0, 'karbohidrat' => 45.0, 'lemak' => 15.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Nasi Uduk', 'kategori' => 'nasi', 'kalori' => 240, 'protein' => 4.0, 'karbohidrat' => 38.0, 'lemak' => 8.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 150],
            ['nama' => 'Nasi Kuning', 'kategori' => 'nasi', 'kalori' => 220, 'protein' => 4.0, 'karbohidrat' => 40.0, 'lemak' => 5.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 150],
            ['nama' => 'Lontong', 'kategori' => 'nasi', 'kalori' => 150, 'protein' => 2.5, 'karbohidrat' => 33.0, 'lemak' => 0.5, 'satuan_porsi' => '1 potong', 'berat_gram' => 100],
            ['nama' => 'Bubur Ayam', 'kategori' => 'nasi', 'kalori' => 280, 'protein' => 12.0, 'karbohidrat' => 35.0, 'lemak' => 10.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 300],
            ['nama' => 'Ketoprak', 'kategori' => 'nasi', 'kalori' => 320, 'protein' => 10.0, 'karbohidrat' => 40.0, 'lemak' => 14.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 250],

            // Mie
            ['nama' => 'Mie Goreng', 'kategori' => 'mie', 'kalori' => 380, 'protein' => 8.0, 'karbohidrat' => 50.0, 'lemak' => 16.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Mie Ayam', 'kategori' => 'mie', 'kalori' => 400, 'protein' => 15.0, 'karbohidrat' => 48.0, 'lemak' => 16.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 300],
            ['nama' => 'Mie Instan', 'kategori' => 'mie', 'kalori' => 380, 'protein' => 7.0, 'karbohidrat' => 52.0, 'lemak' => 15.0, 'satuan_porsi' => '1 bungkus', 'berat_gram' => 80],
            ['nama' => 'Kwetiau Goreng', 'kategori' => 'mie', 'kalori' => 350, 'protein' => 10.0, 'karbohidrat' => 45.0, 'lemak' => 14.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Bihun Goreng', 'kategori' => 'mie', 'kalori' => 320, 'protein' => 6.0, 'karbohidrat' => 48.0, 'lemak' => 12.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 180],

            // Lauk Ayam
            ['nama' => 'Ayam Goreng', 'kategori' => 'lauk', 'kalori' => 260, 'protein' => 25.0, 'karbohidrat' => 5.0, 'lemak' => 16.0, 'satuan_porsi' => '1 potong paha', 'berat_gram' => 120],
            ['nama' => 'Ayam Geprek', 'kategori' => 'lauk', 'kalori' => 350, 'protein' => 28.0, 'karbohidrat' => 15.0, 'lemak' => 20.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 150],
            ['nama' => 'Ayam Bakar', 'kategori' => 'lauk', 'kalori' => 220, 'protein' => 27.0, 'karbohidrat' => 3.0, 'lemak' => 12.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 120],
            ['nama' => 'Ayam Penyet', 'kategori' => 'lauk', 'kalori' => 300, 'protein' => 26.0, 'karbohidrat' => 8.0, 'lemak' => 18.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 130],
            ['nama' => 'Sate Ayam', 'kategori' => 'lauk', 'kalori' => 250, 'protein' => 20.0, 'karbohidrat' => 10.0, 'lemak' => 15.0, 'satuan_porsi' => '10 tusuk', 'berat_gram' => 150],
            ['nama' => 'Nugget Ayam', 'kategori' => 'lauk', 'kalori' => 200, 'protein' => 12.0, 'karbohidrat' => 15.0, 'lemak' => 12.0, 'satuan_porsi' => '5 potong', 'berat_gram' => 100],

            // Lauk Daging
            ['nama' => 'Rendang', 'kategori' => 'lauk', 'kalori' => 280, 'protein' => 22.0, 'karbohidrat' => 5.0, 'lemak' => 20.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 100],
            ['nama' => 'Daging Sapi Goreng', 'kategori' => 'lauk', 'kalori' => 250, 'protein' => 26.0, 'karbohidrat' => 0.0, 'lemak' => 16.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 100],
            ['nama' => 'Empal', 'kategori' => 'lauk', 'kalori' => 230, 'protein' => 24.0, 'karbohidrat' => 5.0, 'lemak' => 13.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 80],

            // Lauk Ikan & Seafood
            ['nama' => 'Ikan Goreng', 'kategori' => 'lauk', 'kalori' => 180, 'protein' => 22.0, 'karbohidrat' => 3.0, 'lemak' => 9.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 100],
            ['nama' => 'Ikan Bakar', 'kategori' => 'lauk', 'kalori' => 150, 'protein' => 24.0, 'karbohidrat' => 0.0, 'lemak' => 6.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 100],
            ['nama' => 'Udang Goreng Tepung', 'kategori' => 'lauk', 'kalori' => 220, 'protein' => 18.0, 'karbohidrat' => 12.0, 'lemak' => 12.0, 'satuan_porsi' => '5 ekor', 'berat_gram' => 100],
            ['nama' => 'Cumi Goreng Tepung', 'kategori' => 'lauk', 'kalori' => 240, 'protein' => 15.0, 'karbohidrat' => 18.0, 'lemak' => 13.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 100],

            // Bakso & Soto
            ['nama' => 'Bakso', 'kategori' => 'kuah', 'kalori' => 350, 'protein' => 18.0, 'karbohidrat' => 35.0, 'lemak' => 15.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 350],
            ['nama' => 'Soto Ayam', 'kategori' => 'kuah', 'kalori' => 280, 'protein' => 15.0, 'karbohidrat' => 25.0, 'lemak' => 14.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 350],
            ['nama' => 'Soto Betawi', 'kategori' => 'kuah', 'kalori' => 380, 'protein' => 18.0, 'karbohidrat' => 20.0, 'lemak' => 25.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 350],
            ['nama' => 'Rawon', 'kategori' => 'kuah', 'kalori' => 300, 'protein' => 20.0, 'karbohidrat' => 15.0, 'lemak' => 18.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 350],
            ['nama' => 'Sop Buntut', 'kategori' => 'kuah', 'kalori' => 350, 'protein' => 22.0, 'karbohidrat' => 10.0, 'lemak' => 25.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 350],

            // Sayur
            ['nama' => 'Sayur Asem', 'kategori' => 'sayur', 'kalori' => 60, 'protein' => 2.0, 'karbohidrat' => 10.0, 'lemak' => 1.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 200],
            ['nama' => 'Sayur Lodeh', 'kategori' => 'sayur', 'kalori' => 120, 'protein' => 3.0, 'karbohidrat' => 8.0, 'lemak' => 9.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 200],
            ['nama' => 'Gado-gado', 'kategori' => 'sayur', 'kalori' => 300, 'protein' => 12.0, 'karbohidrat' => 25.0, 'lemak' => 18.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 250],
            ['nama' => 'Pecel', 'kategori' => 'sayur', 'kalori' => 280, 'protein' => 10.0, 'karbohidrat' => 22.0, 'lemak' => 17.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Cap Cay', 'kategori' => 'sayur', 'kalori' => 150, 'protein' => 8.0, 'karbohidrat' => 12.0, 'lemak' => 8.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Tumis Kangkung', 'kategori' => 'sayur', 'kalori' => 80, 'protein' => 3.0, 'karbohidrat' => 5.0, 'lemak' => 5.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 100],
            ['nama' => 'Lalapan', 'kategori' => 'sayur', 'kalori' => 30, 'protein' => 1.5, 'karbohidrat' => 5.0, 'lemak' => 0.5, 'satuan_porsi' => '1 porsi', 'berat_gram' => 80],

            // Gorengan & Snack
            ['nama' => 'Gorengan (Bakwan)', 'kategori' => 'snack', 'kalori' => 150, 'protein' => 3.0, 'karbohidrat' => 15.0, 'lemak' => 9.0, 'satuan_porsi' => '1 buah', 'berat_gram' => 50],
            ['nama' => 'Gorengan (Tahu Isi)', 'kategori' => 'snack', 'kalori' => 130, 'protein' => 5.0, 'karbohidrat' => 10.0, 'lemak' => 8.0, 'satuan_porsi' => '1 buah', 'berat_gram' => 50],
            ['nama' => 'Gorengan (Tempe Goreng)', 'kategori' => 'snack', 'kalori' => 160, 'protein' => 8.0, 'karbohidrat' => 8.0, 'lemak' => 11.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 50],
            ['nama' => 'Gorengan (Pisang Goreng)', 'kategori' => 'snack', 'kalori' => 180, 'protein' => 2.0, 'karbohidrat' => 25.0, 'lemak' => 8.0, 'satuan_porsi' => '1 buah', 'berat_gram' => 60],
            ['nama' => 'Risoles', 'kategori' => 'snack', 'kalori' => 170, 'protein' => 5.0, 'karbohidrat' => 18.0, 'lemak' => 9.0, 'satuan_porsi' => '1 buah', 'berat_gram' => 60],
            ['nama' => 'Martabak Telur', 'kategori' => 'snack', 'kalori' => 450, 'protein' => 18.0, 'karbohidrat' => 35.0, 'lemak' => 28.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 150],
            ['nama' => 'Martabak Manis', 'kategori' => 'snack', 'kalori' => 380, 'protein' => 6.0, 'karbohidrat' => 50.0, 'lemak' => 18.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 100],
            ['nama' => 'Siomay', 'kategori' => 'snack', 'kalori' => 250, 'protein' => 12.0, 'karbohidrat' => 20.0, 'lemak' => 14.0, 'satuan_porsi' => '1 porsi (5 buah)', 'berat_gram' => 150],
            ['nama' => 'Batagor', 'kategori' => 'snack', 'kalori' => 280, 'protein' => 10.0, 'karbohidrat' => 25.0, 'lemak' => 16.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 150],
            ['nama' => 'Pempek', 'kategori' => 'snack', 'kalori' => 300, 'protein' => 12.0, 'karbohidrat' => 35.0, 'lemak' => 13.0, 'satuan_porsi' => '3 buah', 'berat_gram' => 200],

            // Telur & Tahu Tempe
            ['nama' => 'Telur Ceplok', 'kategori' => 'lauk', 'kalori' => 120, 'protein' => 7.0, 'karbohidrat' => 1.0, 'lemak' => 10.0, 'satuan_porsi' => '1 butir', 'berat_gram' => 60],
            ['nama' => 'Telur Dadar', 'kategori' => 'lauk', 'kalori' => 150, 'protein' => 8.0, 'karbohidrat' => 2.0, 'lemak' => 12.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 70],
            ['nama' => 'Telur Rebus', 'kategori' => 'lauk', 'kalori' => 75, 'protein' => 6.5, 'karbohidrat' => 0.5, 'lemak' => 5.0, 'satuan_porsi' => '1 butir', 'berat_gram' => 50],
            ['nama' => 'Tahu Goreng', 'kategori' => 'lauk', 'kalori' => 80, 'protein' => 5.0, 'karbohidrat' => 3.0, 'lemak' => 6.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 50],
            ['nama' => 'Tempe Goreng', 'kategori' => 'lauk', 'kalori' => 160, 'protein' => 10.0, 'karbohidrat' => 8.0, 'lemak' => 11.0, 'satuan_porsi' => '2 potong', 'berat_gram' => 60],
            ['nama' => 'Tempe Orek', 'kategori' => 'lauk', 'kalori' => 180, 'protein' => 10.0, 'karbohidrat' => 12.0, 'lemak' => 11.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 80],

            // Minuman
            ['nama' => 'Es Teh Manis', 'kategori' => 'minuman', 'kalori' => 80, 'protein' => 0.0, 'karbohidrat' => 20.0, 'lemak' => 0.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Teh Tawar', 'kategori' => 'minuman', 'kalori' => 2, 'protein' => 0.0, 'karbohidrat' => 0.5, 'lemak' => 0.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Kopi Hitam', 'kategori' => 'minuman', 'kalori' => 5, 'protein' => 0.3, 'karbohidrat' => 0.0, 'lemak' => 0.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 200],
            ['nama' => 'Kopi Susu', 'kategori' => 'minuman', 'kalori' => 120, 'protein' => 3.0, 'karbohidrat' => 15.0, 'lemak' => 5.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Es Jeruk', 'kategori' => 'minuman', 'kalori' => 90, 'protein' => 0.5, 'karbohidrat' => 22.0, 'lemak' => 0.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Jus Alpukat', 'kategori' => 'minuman', 'kalori' => 200, 'protein' => 2.0, 'karbohidrat' => 20.0, 'lemak' => 12.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 300],
            ['nama' => 'Susu Full Cream', 'kategori' => 'minuman', 'kalori' => 150, 'protein' => 8.0, 'karbohidrat' => 12.0, 'lemak' => 8.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 250],
            ['nama' => 'Air Mineral', 'kategori' => 'minuman', 'kalori' => 0, 'protein' => 0.0, 'karbohidrat' => 0.0, 'lemak' => 0.0, 'satuan_porsi' => '1 gelas (250ml)', 'berat_gram' => 250],
            ['nama' => 'Es Campur', 'kategori' => 'minuman', 'kalori' => 200, 'protein' => 2.0, 'karbohidrat' => 40.0, 'lemak' => 4.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 300],
            ['nama' => 'Cendol/Dawet', 'kategori' => 'minuman', 'kalori' => 180, 'protein' => 1.0, 'karbohidrat' => 35.0, 'lemak' => 5.0, 'satuan_porsi' => '1 gelas', 'berat_gram' => 300],

            // Roti & Cemilan
            ['nama' => 'Roti Tawar', 'kategori' => 'roti', 'kalori' => 80, 'protein' => 3.0, 'karbohidrat' => 15.0, 'lemak' => 1.0, 'satuan_porsi' => '1 lembar', 'berat_gram' => 30],
            ['nama' => 'Roti Bakar', 'kategori' => 'roti', 'kalori' => 250, 'protein' => 5.0, 'karbohidrat' => 30.0, 'lemak' => 12.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 80],
            ['nama' => 'Donat', 'kategori' => 'roti', 'kalori' => 250, 'protein' => 4.0, 'karbohidrat' => 30.0, 'lemak' => 13.0, 'satuan_porsi' => '1 buah', 'berat_gram' => 60],

            // Buah
            ['nama' => 'Pisang', 'kategori' => 'buah', 'kalori' => 90, 'protein' => 1.0, 'karbohidrat' => 23.0, 'lemak' => 0.3, 'satuan_porsi' => '1 buah', 'berat_gram' => 100],
            ['nama' => 'Apel', 'kategori' => 'buah', 'kalori' => 72, 'protein' => 0.4, 'karbohidrat' => 19.0, 'lemak' => 0.2, 'satuan_porsi' => '1 buah', 'berat_gram' => 150],
            ['nama' => 'Jeruk', 'kategori' => 'buah', 'kalori' => 45, 'protein' => 0.9, 'karbohidrat' => 11.0, 'lemak' => 0.1, 'satuan_porsi' => '1 buah', 'berat_gram' => 100],
            ['nama' => 'Semangka', 'kategori' => 'buah', 'kalori' => 45, 'protein' => 0.6, 'karbohidrat' => 11.0, 'lemak' => 0.2, 'satuan_porsi' => '1 potong', 'berat_gram' => 150],
            ['nama' => 'Mangga', 'kategori' => 'buah', 'kalori' => 100, 'protein' => 0.8, 'karbohidrat' => 25.0, 'lemak' => 0.4, 'satuan_porsi' => '1 buah', 'berat_gram' => 150],
            ['nama' => 'Pepaya', 'kategori' => 'buah', 'kalori' => 60, 'protein' => 0.5, 'karbohidrat' => 15.0, 'lemak' => 0.1, 'satuan_porsi' => '1 potong', 'berat_gram' => 150],

            // Makanan Berat Lainnya
            ['nama' => 'Nasi Padang (komplit)', 'kategori' => 'nasi', 'kalori' => 700, 'protein' => 30.0, 'karbohidrat' => 60.0, 'lemak' => 38.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 400],
            ['nama' => 'Nasi Warteg (komplit)', 'kategori' => 'nasi', 'kalori' => 550, 'protein' => 20.0, 'karbohidrat' => 55.0, 'lemak' => 28.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 350],
            ['nama' => 'Pecel Lele', 'kategori' => 'lauk', 'kalori' => 400, 'protein' => 22.0, 'karbohidrat' => 40.0, 'lemak' => 18.0, 'satuan_porsi' => '1 porsi + nasi', 'berat_gram' => 300],
            ['nama' => 'Nasi Bebek', 'kategori' => 'nasi', 'kalori' => 550, 'protein' => 25.0, 'karbohidrat' => 45.0, 'lemak' => 30.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 300],
            ['nama' => 'Indomie Goreng', 'kategori' => 'mie', 'kalori' => 380, 'protein' => 8.0, 'karbohidrat' => 52.0, 'lemak' => 16.0, 'satuan_porsi' => '1 bungkus', 'berat_gram' => 85],
            ['nama' => 'Indomie Kuah', 'kategori' => 'mie', 'kalori' => 340, 'protein' => 7.0, 'karbohidrat' => 48.0, 'lemak' => 13.0, 'satuan_porsi' => '1 bungkus', 'berat_gram' => 75],
            ['nama' => 'Nasi Campur Bali', 'kategori' => 'nasi', 'kalori' => 600, 'protein' => 25.0, 'karbohidrat' => 55.0, 'lemak' => 30.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 350],
            ['nama' => 'Gudeg', 'kategori' => 'lauk', 'kalori' => 200, 'protein' => 5.0, 'karbohidrat' => 30.0, 'lemak' => 8.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 150],
            ['nama' => 'Tongseng', 'kategori' => 'kuah', 'kalori' => 320, 'protein' => 20.0, 'karbohidrat' => 15.0, 'lemak' => 22.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Gulai Kambing', 'kategori' => 'kuah', 'kalori' => 350, 'protein' => 22.0, 'karbohidrat' => 8.0, 'lemak' => 26.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 200],
            ['nama' => 'Opor Ayam', 'kategori' => 'kuah', 'kalori' => 280, 'protein' => 20.0, 'karbohidrat' => 5.0, 'lemak' => 20.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 150],
            ['nama' => 'Sop Ayam', 'kategori' => 'kuah', 'kalori' => 180, 'protein' => 15.0, 'karbohidrat' => 10.0, 'lemak' => 8.0, 'satuan_porsi' => '1 mangkuk', 'berat_gram' => 300],

            // Fast Food
            ['nama' => 'Burger', 'kategori' => 'fast_food', 'kalori' => 450, 'protein' => 22.0, 'karbohidrat' => 35.0, 'lemak' => 25.0, 'satuan_porsi' => '1 buah', 'berat_gram' => 200],
            ['nama' => 'Pizza', 'kategori' => 'fast_food', 'kalori' => 280, 'protein' => 12.0, 'karbohidrat' => 30.0, 'lemak' => 13.0, 'satuan_porsi' => '1 slice', 'berat_gram' => 120],
            ['nama' => 'Kentang Goreng', 'kategori' => 'fast_food', 'kalori' => 320, 'protein' => 4.0, 'karbohidrat' => 40.0, 'lemak' => 16.0, 'satuan_porsi' => '1 porsi medium', 'berat_gram' => 120],
            ['nama' => 'Fried Chicken (KFC/McD)', 'kategori' => 'fast_food', 'kalori' => 300, 'protein' => 22.0, 'karbohidrat' => 12.0, 'lemak' => 19.0, 'satuan_porsi' => '1 potong', 'berat_gram' => 130],

            // Protein Shake & Suplemen
            ['nama' => 'Whey Protein', 'kategori' => 'suplemen', 'kalori' => 120, 'protein' => 24.0, 'karbohidrat' => 3.0, 'lemak' => 1.5, 'satuan_porsi' => '1 scoop', 'berat_gram' => 30],
            ['nama' => 'Oatmeal', 'kategori' => 'sarapan', 'kalori' => 150, 'protein' => 5.0, 'karbohidrat' => 27.0, 'lemak' => 3.0, 'satuan_porsi' => '1 porsi', 'berat_gram' => 40],
            ['nama' => 'Granola', 'kategori' => 'sarapan', 'kalori' => 200, 'protein' => 5.0, 'karbohidrat' => 30.0, 'lemak' => 8.0, 'satuan_porsi' => '1/2 cup', 'berat_gram' => 50],
        ];

        $now = now();
        foreach ($foods as &$food) {
            $food['created_at'] = $now;
            $food['updated_at'] = $now;
        }

        // Insert in chunks
        foreach (array_chunk($foods, 20) as $chunk) {
            DB::table('diet_food_database')->insert($chunk);
        }
    }

    public function down(): void
    {
        DB::table('diet_food_database')->truncate();
    }
};
