<?php

namespace App\Services;

use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\FoodDatabase;
use App\Models\DietTracker\ExerciseDatabase;
use App\Models\DietTracker\Meal;
use App\Models\DietTracker\Exercise;
use App\Models\DietTracker\DailyActivity;
use App\Models\DietTracker\WeightLog;
use Carbon\Carbon;

class DietHelperService
{
    /**
     * Hitung BMR (Basal Metabolic Rate) - Mifflin-St Jeor
     */
    public static function hitungBMR(string $gender, float $beratKg, float $tinggiCm, int $umur): int
    {
        if ($gender === 'pria') {
            return (int) round((10 * $beratKg) + (6.25 * $tinggiCm) - (5 * $umur) + 5);
        }
        return (int) round((10 * $beratKg) + (6.25 * $tinggiCm) - (5 * $umur) - 161);
    }

    /**
     * Hitung TDEE (Total Daily Energy Expenditure)
     */
    public static function hitungTDEE(int $bmr, string $levelAktivitas): int
    {
        $multiplier = match ($levelAktivitas) {
            'tidak_aktif' => 1.2,
            'ringan' => 1.375,
            'sedang' => 1.55,
            'aktif' => 1.725,
            'sangat_aktif' => 1.9,
            default => 1.55,
        };
        return (int) round($bmr * $multiplier);
    }

    /**
     * Hitung target kalori harian untuk turun berat badan
     * Defisit 500 kkal = turun ~0.5kg/minggu
     */
    public static function hitungTargetKalori(int $tdee, float $beratSekarang, float $beratTarget): int
    {
        if ($beratSekarang > $beratTarget) {
            // Mau turun - defisit 500 tapi minimal 1200
            return max(1200, $tdee - 500);
        } elseif ($beratSekarang < $beratTarget) {
            // Mau naik - surplus 300
            return $tdee + 300;
        }
        return $tdee; // maintain
    }

    /**
     * Generate rekomendasi menu harian berdasarkan target kalori
     */
    public static function rekomendasiMenu(int $targetKalori, ?array $puasa = null): array
    {
        if ($puasa) {
            // Mode puasa: sahur 40%, berbuka 35%, snack malam 15%, tarawih snack 10%
            $sahurTarget = (int) round($targetKalori * 0.40);
            $berbukaTarget = (int) round($targetKalori * 0.35);
            $snackMalamTarget = (int) round($targetKalori * 0.15);
            $tarawihTarget = (int) round($targetKalori * 0.10);

            return [
                'sahur' => [
                    'target_kalori' => $sahurTarget,
                    'menu' => self::cariKombinasiMakanan('sarapan', $sahurTarget),
                ],
                'berbuka' => [
                    'target_kalori' => $berbukaTarget,
                    'menu' => self::cariKombinasiMakanan('makan_utama', $berbukaTarget),
                ],
                'snack_malam' => [
                    'target_kalori' => $snackMalamTarget,
                    'menu' => self::cariKombinasiMakanan('snack', $snackMalamTarget),
                ],
                'tarawih_snack' => [
                    'target_kalori' => $tarawihTarget,
                    'menu' => self::cariKombinasiMakanan('snack', $tarawihTarget),
                ],
            ];
        }

        // Distribusi normal: sarapan 25%, siang 35%, malam 30%, snack 10%
        $sarapanTarget = (int) round($targetKalori * 0.25);
        $siangTarget = (int) round($targetKalori * 0.35);
        $malamTarget = (int) round($targetKalori * 0.30);
        $snackTarget = (int) round($targetKalori * 0.10);

        return [
            'sarapan' => [
                'target_kalori' => $sarapanTarget,
                'menu' => self::cariKombinasiMakanan('sarapan', $sarapanTarget),
            ],
            'makan_siang' => [
                'target_kalori' => $siangTarget,
                'menu' => self::cariKombinasiMakanan('makan_utama', $siangTarget),
            ],
            'makan_malam' => [
                'target_kalori' => $malamTarget,
                'menu' => self::cariKombinasiMakanan('makan_utama', $malamTarget),
            ],
            'snack' => [
                'target_kalori' => $snackTarget,
                'menu' => self::cariKombinasiMakanan('snack', $snackTarget),
            ],
        ];
    }

    private static function cariKombinasiMakanan(string $kategori, int $targetKalori): \Illuminate\Support\Collection
    {
        $foods = FoodDatabase::where('kategori', $kategori)
            ->orWhere('kategori', 'umum')
            ->inRandomOrder()
            ->get();

        $selected = collect();
        $totalKalori = 0;
        $tolerance = $targetKalori * 0.15; // 15% tolerance

        foreach ($foods as $food) {
            if ($totalKalori + $food->kalori <= $targetKalori + $tolerance) {
                $selected->push($food);
                $totalKalori += $food->kalori;
                if ($totalKalori >= $targetKalori - $tolerance) break;
            }
        }

        return $selected;
    }

    /**
     * Rekomendasi olahraga berdasarkan kalori yang perlu dibakar
     */
    public static function rekomendasiOlahraga(int $kaloriTarget): \Illuminate\Support\Collection
    {
        return ExerciseDatabase::get()->map(function ($ex) use ($kaloriTarget) {
            $durasiMenit = $ex->kalori_per_menit > 0
                ? (int) ceil($kaloriTarget / $ex->kalori_per_menit)
                : 60;
            $ex->durasi_rekomendasi = $durasiMenit;
            $ex->kalori_estimasi = $durasiMenit * $ex->kalori_per_menit;
            return $ex;
        })->sortBy('durasi_rekomendasi')->values();
    }

    /**
     * Analisis progress dan generate tips
     */
    public static function analisisProgress(DietPlan $plan): array
    {
        $today = Carbon::today();
        $hariKe = $plan->tanggal_mulai->diffInDays($today) + 1;

        // Data hari ini
        $kaloriMasukHariIni = Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->sum('kalori');
        $kaloriKeluarHariIni = Exercise::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->sum('kalori_terbakar');

        // Data 7 hari terakhir
        $weekAgo = $today->copy()->subDays(7);
        $avgKaloriMasuk = Meal::where('diet_plan_id', $plan->id)->where('tanggal', '>=', $weekAgo)->avg('kalori') ?? 0;
        $avgKaloriKeluar = Exercise::where('diet_plan_id', $plan->id)->where('tanggal', '>=', $weekAgo)->avg('kalori_terbakar') ?? 0;

        // Berat badan progress
        $beratTerakhir = WeightLog::where('diet_plan_id', $plan->id)->orderByDesc('tanggal')->first();
        $beratSekarang = $beratTerakhir?->berat ?? $plan->berat_sekarang ?? $plan->berat_awal;
        $beratTurun = $plan->berat_awal - $beratSekarang;
        $beratSisa = $beratSekarang - $plan->berat_target;
        $totalTarget = $plan->berat_awal - $plan->berat_target;
        $progressPersen = $totalTarget > 0 ? min(100, round(($beratTurun / $totalTarget) * 100)) : 0;

        // Estimasi waktu selesai (0.5kg/minggu)
        $mingguSisa = $beratSisa > 0 ? ceil($beratSisa / 0.5) : 0;

        // Generate tips
        $tips = [];
        $status = 'on_track';

        if ($kaloriMasukHariIni > $plan->kalori_harian_target * 1.1) {
            $tips[] = ['type' => 'warning', 'text' => 'Kalori hari ini melebihi target. Coba kurangi porsi makan malam atau tambah olahraga ringan.'];
            $status = 'over';
        } elseif ($kaloriMasukHariIni > 0 && $kaloriMasukHariIni < $plan->kalori_harian_target * 0.7) {
            $tips[] = ['type' => 'warning', 'text' => 'Kalori terlalu rendah! Makan terlalu sedikit bisa memperlambat metabolisme.'];
            $status = 'under';
        }

        if ($kaloriKeluarHariIni == 0 && now()->hour >= 14) {
            $tips[] = ['type' => 'info', 'text' => 'Belum olahraga hari ini. Jalan kaki 30 menit bisa membakar ~150 kalori.'];
        }

        if ($beratTurun > 0) {
            $tips[] = ['type' => 'success', 'text' => "Kamu sudah turun {$beratTurun} kg! Pertahankan konsistensi."];
        }

        $sisaKalori = $plan->kalori_harian_target - $kaloriMasukHariIni + $kaloriKeluarHariIni;
        if ($sisaKalori > 0 && $kaloriMasukHariIni > 0) {
            $tips[] = ['type' => 'info', 'text' => "Sisa budget kalori hari ini: {$sisaKalori} kkal."];
        }

        if ($mingguSisa > 0) {
            $tips[] = ['type' => 'info', 'text' => "Estimasi target tercapai dalam ~{$mingguSisa} minggu lagi."];
        }

        // Rekomendasi air minum
        $airTarget = (int) round($beratSekarang * 33); // 33ml per kg berat badan
        $activity = DailyActivity::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->first();
        $airMinum = $activity?->air_minum_ml ?? 0;
        if ($airMinum < $airTarget * 0.5) {
            $tips[] = ['type' => 'warning', 'text' => "Minum air masih kurang. Target: " . number_format($airTarget) . " ml/hari."];
        }

        return [
            'hari_ke' => $hariKe,
            'status' => $status,
            'kalori_masuk' => $kaloriMasukHariIni,
            'kalori_keluar' => $kaloriKeluarHariIni,
            'sisa_kalori' => $sisaKalori,
            'berat_sekarang' => $beratSekarang,
            'berat_turun' => $beratTurun,
            'berat_sisa' => $beratSisa,
            'progress_persen' => $progressPersen,
            'minggu_sisa' => $mingguSisa,
            'air_target' => $airTarget,
            'air_minum' => $airMinum,
            'tips' => $tips,
        ];
    }

    /**
     * Hitung berat ideal berdasarkan tinggi (Broca formula modified)
     */
    public static function beratIdeal(string $gender, float $tinggiCm): float
    {
        if ($gender === 'pria') {
            return round(($tinggiCm - 100) - (($tinggiCm - 100) * 0.10), 1);
        }
        return round(($tinggiCm - 100) - (($tinggiCm - 100) * 0.15), 1);
    }

    /**
     * Hitung BMI (Body Mass Index)
     */
    public static function hitungBMI(float $beratKg, float $tinggiCm): array
    {
        $tinggiM = $tinggiCm / 100;
        $bmi = round($beratKg / ($tinggiM * $tinggiM), 1);

        $kategori = match (true) {
            $bmi < 18.5 => 'Kurus',
            $bmi < 25.0 => 'Normal',
            $bmi < 30.0 => 'Gemuk',
            default => 'Obesitas',
        };

        return ['bmi' => $bmi, 'kategori' => $kategori];
    }

    /**
     * Auto-generate semua dari data dasar
     * User cuma input: gender, umur, tinggi, berat, level_aktivitas
     * Sistem tentukan: berat target, kalori target, timeline, semuanya
     */
    public static function generateSmartPlan(string $gender, int $umur, float $tinggiCm, float $beratAwal, string $levelAktivitas): array
    {
        $bmr = self::hitungBMR($gender, $beratAwal, $tinggiCm, $umur);
        $tdee = self::hitungTDEE($bmr, $levelAktivitas);
        $bmi = self::hitungBMI($beratAwal, $tinggiCm);
        $beratIdeal = self::beratIdeal($gender, $tinggiCm);

        // Tentukan target berat otomatis
        if ($bmi['kategori'] === 'Normal') {
            $beratTarget = $beratAwal; // maintain
            $mode = 'maintain';
        } elseif ($bmi['kategori'] === 'Kurus') {
            $beratTarget = $beratIdeal; // naik ke ideal
            $mode = 'bulk';
        } else {
            // Gemuk/Obesitas - target ke berat ideal, tapi max turun 15kg per program
            $beratTarget = max($beratIdeal, $beratAwal - 15);
            $mode = 'cut';
        }

        // Hitung target kalori
        $targetKalori = self::hitungTargetKalori($tdee, $beratAwal, $beratTarget);

        // Hitung timeline
        $selisihBerat = abs($beratAwal - $beratTarget);
        if ($mode === 'cut') {
            $minggu = ceil($selisihBerat / 0.5); // 0.5kg/minggu (sehat)
        } elseif ($mode === 'bulk') {
            $minggu = ceil($selisihBerat / 0.3); // 0.3kg/minggu
        } else {
            $minggu = 12; // 3 bulan maintain
        }
        $tanggalSelesai = now()->addWeeks((int) $minggu);

        // Distribusi makro harian
        $proteinGram = round($beratAwal * ($mode === 'bulk' ? 2.0 : 1.6)); // gram per kg
        $lemakKalori = round($targetKalori * 0.25);
        $lemakGram = round($lemakKalori / 9);
        $proteinKalori = $proteinGram * 4;
        $karboKalori = $targetKalori - $proteinKalori - $lemakKalori;
        $karboGram = round($karboKalori / 4);

        // Target air minum
        $airTarget = (int) round($beratAwal * 33);

        // Target langkah kaki
        $langkahTarget = match ($levelAktivitas) {
            'tidak_aktif' => 5000,
            'ringan' => 7000,
            'sedang' => 8500,
            'aktif' => 10000,
            'sangat_aktif' => 12000,
            default => 8000,
        };

        // Target olahraga per minggu
        $olahragaPerMinggu = match ($levelAktivitas) {
            'tidak_aktif' => 2,
            'ringan' => 3,
            'sedang' => 4,
            'aktif' => 5,
            'sangat_aktif' => 6,
            default => 3,
        };

        // Target tidur
        $tidurTarget = $umur < 18 ? 9 : ($umur < 65 ? 8 : 7);

        return [
            'bmr' => $bmr,
            'tdee' => $tdee,
            'bmi' => $bmi,
            'berat_ideal' => $beratIdeal,
            'berat_target' => $beratTarget,
            'mode' => $mode,
            'target_kalori' => $targetKalori,
            'minggu' => $minggu,
            'tanggal_selesai' => $tanggalSelesai,
            'makro' => [
                'protein' => $proteinGram,
                'karbohidrat' => $karboGram,
                'lemak' => $lemakGram,
            ],
            'target_harian' => [
                'air_ml' => $airTarget,
                'langkah' => $langkahTarget,
                'olahraga_per_minggu' => $olahragaPerMinggu,
                'tidur_jam' => $tidurTarget,
            ],
        ];
    }

    /**
     * Jadwal makan rutin yang disarankan berdasarkan target kalori
     */
    public static function jadwalMakanIdeal(int $targetKalori, ?array $puasa = null): array
    {
        if ($puasa) {
            return self::jadwalMakanPuasa($targetKalori, $puasa['waktu_sahur'], $puasa['waktu_berbuka']);
        }

        return [
            ['waktu' => '07:00', 'label' => 'Sarapan', 'key' => 'sarapan', 'persen' => 25, 'kalori' => (int) round($targetKalori * 0.25), 'tips' => 'Karbohidrat kompleks + protein. Contoh: oatmeal + telur, atau nasi merah + ayam.'],
            ['waktu' => '10:00', 'label' => 'Snack Pagi', 'key' => 'snack', 'persen' => 5, 'kalori' => (int) round($targetKalori * 0.05), 'tips' => 'Buah atau yogurt. Jaga metabolisme tetap aktif.'],
            ['waktu' => '12:30', 'label' => 'Makan Siang', 'key' => 'makan_siang', 'persen' => 35, 'kalori' => (int) round($targetKalori * 0.35), 'tips' => 'Porsi terbesar. Nasi + protein + sayur. Kurangi gorengan.'],
            ['waktu' => '15:30', 'label' => 'Snack Sore', 'key' => 'snack', 'persen' => 5, 'kalori' => (int) round($targetKalori * 0.05), 'tips' => 'Kacang-kacangan atau buah. Hindari snack manis.'],
            ['waktu' => '18:30', 'label' => 'Makan Malam', 'key' => 'makan_malam', 'persen' => 25, 'kalori' => (int) round($targetKalori * 0.25), 'tips' => 'Porsi lebih kecil dari siang. Perbanyak protein + sayur, kurangi karbo.'],
            ['waktu' => '20:00', 'label' => 'Snack Malam', 'key' => 'snack', 'persen' => 5, 'kalori' => (int) round($targetKalori * 0.05), 'tips' => 'Opsional. Susu rendah lemak atau buah ringan. Hindari makan berat setelah jam 8.'],
        ];
    }

    /**
     * Jadwal makan khusus puasa
     */
    public static function jadwalMakanPuasa(int $targetKalori, string $waktuSahur = '04:00', string $waktuBerbuka = '18:15'): array
    {
        // Saat puasa, kalori tetap sama tapi dipadatkan ke sahur & berbuka
        $sahurTime = $waktuSahur;
        $berbukaTime = $waktuBerbuka;

        // Hitung waktu setelah berbuka
        $berbuka1 = \Carbon\Carbon::parse($berbukaTime)->format('H:i');
        $berbuka2 = \Carbon\Carbon::parse($berbukaTime)->addMinutes(30)->format('H:i');
        $makanMalam = \Carbon\Carbon::parse($berbukaTime)->addHour()->format('H:i');
        $snackMalam = \Carbon\Carbon::parse($berbukaTime)->addHours(2)->format('H:i');
        $sahurBangun = \Carbon\Carbon::parse($sahurTime)->subMinutes(30)->format('H:i');

        return [
            ['waktu' => $sahurBangun, 'label' => 'Bangun Sahur', 'key' => 'sarapan', 'persen' => 0, 'kalori' => 0, 'tips' => 'Minum 1-2 gelas air putih segera setelah bangun. Hidrasi penting sebelum puasa seharian.'],
            ['waktu' => $sahurTime, 'label' => 'Sahur', 'key' => 'sarapan', 'persen' => 40, 'kalori' => (int) round($targetKalori * 0.40), 'tips' => 'Karbohidrat kompleks (nasi merah/oat) + protein (telur/ayam) + sayur + buah. Makan pelan-pelan, jangan terburu-buru.'],
            ['waktu' => $berbuka1, 'label' => 'Berbuka Puasa', 'key' => 'makan_malam', 'persen' => 10, 'kalori' => (int) round($targetKalori * 0.10), 'tips' => 'Buka dengan kurma + air putih. Jangan langsung makan berat! Beri jeda 15-30 menit.'],
            ['waktu' => $berbuka2, 'label' => 'Makan Berbuka', 'key' => 'makan_malam', 'persen' => 35, 'kalori' => (int) round($targetKalori * 0.35), 'tips' => 'Makan utama setelah sholat. Nasi + protein + sayur. Porsi normal, jangan berlebihan meski lapar.'],
            ['waktu' => $makanMalam, 'label' => 'Snack Malam', 'key' => 'snack', 'persen' => 10, 'kalori' => (int) round($targetKalori * 0.10), 'tips' => 'Buah, yogurt, atau kacang-kacangan. Jaga asupan nutrisi sebelum tidur.'],
            ['waktu' => $snackMalam, 'label' => 'Minum & Hidrasi', 'key' => 'snack', 'persen' => 5, 'kalori' => (int) round($targetKalori * 0.05), 'tips' => 'Minum air putih bertahap sampai tidur. Target minimal 1.5-2 liter antara berbuka dan sahur.'],
        ];
    }

    /**
     * Tips khusus puasa
     */
    /**
     * Config lengkap per tipe puasa
     */
    public static function getConfigPuasa(string $tipe): array
    {
        $base = [
            'sahur_default' => '04:00',
            'berbuka_default' => '18:15',
            'boleh_olahraga_berat' => false,
            'waktu_olahraga' => 'sebelum_berbuka', // sebelum_berbuka, setelah_berbuka, setelah_tarawih
            'target_air_persen' => 100, // persen dari target normal
            'tips' => [],
            'jadwal_minum' => [],
            'jadwal_olahraga' => [],
            'jadwal_aktivitas' => [],
        ];

        return match ($tipe) {
            'ramadhan' => array_merge($base, [
                'sahur_default' => '03:30',
                'berbuka_default' => '18:10',
                'boleh_olahraga_berat' => false,
                'waktu_olahraga' => 'setelah_tarawih',
                'target_air_persen' => 100,
                'tips' => [
                    ['type' => 'info', 'text' => 'Sahur: Perbanyak karbohidrat kompleks (nasi merah, oat) + protein agar kenyang lebih lama.'],
                    ['type' => 'info', 'text' => 'Pola minum 2-4-2: 2 gelas sahur, 4 gelas berbuka-isya, 2 gelas sebelum tidur.'],
                    ['type' => 'warning', 'text' => 'Hindari makanan asin & pedas saat sahur - bikin cepat haus saat puasa.'],
                    ['type' => 'warning', 'text' => 'Jangan langsung makan berat saat berbuka - mulai dengan kurma + air putih.'],
                    ['type' => 'info', 'text' => 'Olahraga ringan 30 menit sebelum berbuka atau setelah tarawih.'],
                    ['type' => 'success', 'text' => 'Puasa Ramadhan membantu detox tubuh dan meningkatkan disiplin makan.'],
                ],
                'jadwal_minum' => [
                    ['waktu' => '03:30', 'label' => 'Sahur - Minum 2 gelas', 'ml' => 500],
                    ['waktu' => '18:10', 'label' => 'Berbuka - Minum 2 gelas', 'ml' => 500],
                    ['waktu' => '19:00', 'label' => 'Setelah sholat - 1 gelas', 'ml' => 250],
                    ['waktu' => '20:00', 'label' => 'Tarawih - 1 gelas', 'ml' => 250],
                    ['waktu' => '21:00', 'label' => 'Setelah tarawih - 2 gelas', 'ml' => 500],
                    ['waktu' => '22:00', 'label' => 'Sebelum tidur - 1 gelas', 'ml' => 250],
                ],
                'jadwal_olahraga' => [
                    ['waktu' => '17:30', 'jenis' => 'Jalan Santai', 'durasi' => 20, 'intensitas' => 'ringan', 'catatan' => 'Jalan santai 30-60 menit sebelum berbuka. Jangan terlalu berat.'],
                    ['waktu' => '21:30', 'jenis' => 'Olahraga Ringan', 'durasi' => 30, 'intensitas' => 'ringan', 'catatan' => 'Setelah tarawih. Stretching, yoga, atau jalan cepat.'],
                ],
                'jadwal_aktivitas' => [
                    ['waktu' => '03:00', 'aktivitas' => 'Bangun sahur + sholat tahajud'],
                    ['waktu' => '03:30', 'aktivitas' => 'Sahur - makan & minum yang cukup'],
                    ['waktu' => '04:15', 'aktivitas' => 'Sholat subuh'],
                    ['waktu' => '05:00', 'aktivitas' => 'Tidur lagi / dzikir pagi'],
                    ['waktu' => '12:00', 'aktivitas' => 'Sholat dzuhur + istirahat'],
                    ['waktu' => '15:30', 'aktivitas' => 'Sholat ashar'],
                    ['waktu' => '17:30', 'aktivitas' => 'Jalan santai ringan (opsional)'],
                    ['waktu' => '18:10', 'aktivitas' => 'Berbuka puasa + sholat maghrib'],
                    ['waktu' => '19:15', 'aktivitas' => 'Makan utama + sholat isya'],
                    ['waktu' => '20:00', 'aktivitas' => 'Sholat tarawih'],
                    ['waktu' => '21:30', 'aktivitas' => 'Olahraga ringan (opsional)'],
                    ['waktu' => '22:30', 'aktivitas' => 'Persiapan tidur'],
                ],
            ]),

            'senin_kamis' => array_merge($base, [
                'sahur_default' => '04:00',
                'berbuka_default' => '18:15',
                'boleh_olahraga_berat' => false,
                'waktu_olahraga' => 'setelah_berbuka',
                'target_air_persen' => 100,
                'tips' => [
                    ['type' => 'info', 'text' => 'Puasa Senin-Kamis: Sunnah Rasulullah. Sahur cukup dengan kurma & air.'],
                    ['type' => 'info', 'text' => 'Minum air bertahap: 2 gelas sahur, sisanya setelah berbuka sampai tidur.'],
                    ['type' => 'warning', 'text' => 'Hindari olahraga berat saat puasa. Jalan kaki ringan saja.'],
                    ['type' => 'success', 'text' => 'Puasa 2x seminggu membantu intermittent fasting dan kontrol berat badan.'],
                ],
                'jadwal_minum' => [
                    ['waktu' => '04:00', 'label' => 'Sahur - Minum 2 gelas', 'ml' => 500],
                    ['waktu' => '18:15', 'label' => 'Berbuka - Minum 2 gelas', 'ml' => 500],
                    ['waktu' => '19:00', 'label' => 'Setelah makan - 2 gelas', 'ml' => 500],
                    ['waktu' => '20:30', 'label' => 'Malam - 2 gelas', 'ml' => 500],
                    ['waktu' => '22:00', 'label' => 'Sebelum tidur - 1 gelas', 'ml' => 250],
                ],
                'jadwal_olahraga' => [
                    ['waktu' => '17:30', 'jenis' => 'Jalan Kaki Ringan', 'durasi' => 15, 'intensitas' => 'ringan', 'catatan' => 'Jalan santai sebelum berbuka.'],
                    ['waktu' => '19:30', 'jenis' => 'Olahraga Sedang', 'durasi' => 30, 'intensitas' => 'sedang', 'catatan' => '1 jam setelah berbuka. Boleh cardio ringan atau strength training.'],
                ],
                'jadwal_aktivitas' => [
                    ['waktu' => '03:30', 'aktivitas' => 'Bangun sahur + sholat tahajud'],
                    ['waktu' => '04:00', 'aktivitas' => 'Sahur'],
                    ['waktu' => '04:30', 'aktivitas' => 'Sholat subuh'],
                    ['waktu' => '07:00', 'aktivitas' => 'Aktivitas normal / kerja'],
                    ['waktu' => '12:00', 'aktivitas' => 'Sholat dzuhur + istirahat'],
                    ['waktu' => '17:30', 'aktivitas' => 'Jalan kaki ringan (opsional)'],
                    ['waktu' => '18:15', 'aktivitas' => 'Berbuka puasa'],
                    ['waktu' => '19:30', 'aktivitas' => 'Olahraga setelah berbuka'],
                    ['waktu' => '22:00', 'aktivitas' => 'Persiapan tidur'],
                ],
            ]),

            'daud' => array_merge($base, [
                'tips' => [
                    ['type' => 'info', 'text' => 'Puasa Daud: Puasa sehari, tidak sehari. Puasa terbaik menurut Rasulullah.'],
                    ['type' => 'info', 'text' => 'Di hari tidak puasa, makan normal tapi tetap jaga kalori.'],
                    ['type' => 'success', 'text' => 'Pola ini mirip alternate-day fasting yang terbukti efektif untuk fat loss.'],
                ],
                'jadwal_minum' => [
                    ['waktu' => '04:00', 'label' => 'Sahur - 2 gelas', 'ml' => 500],
                    ['waktu' => '18:15', 'label' => 'Berbuka - 2 gelas', 'ml' => 500],
                    ['waktu' => '19:30', 'label' => 'Malam - 2 gelas', 'ml' => 500],
                    ['waktu' => '21:00', 'label' => 'Sebelum tidur - 2 gelas', 'ml' => 500],
                ],
                'jadwal_olahraga' => [
                    ['waktu' => '17:30', 'jenis' => 'Jalan Santai', 'durasi' => 20, 'intensitas' => 'ringan', 'catatan' => 'Ringan saja sebelum berbuka.'],
                    ['waktu' => '19:30', 'jenis' => 'Olahraga Ringan-Sedang', 'durasi' => 25, 'intensitas' => 'sedang', 'catatan' => 'Setelah berbuka. Simpan olahraga berat untuk hari tidak puasa.'],
                ],
                'jadwal_aktivitas' => [
                    ['waktu' => '03:30', 'aktivitas' => 'Bangun sahur'],
                    ['waktu' => '04:00', 'aktivitas' => 'Sahur + sholat subuh'],
                    ['waktu' => '07:00', 'aktivitas' => 'Aktivitas normal'],
                    ['waktu' => '17:30', 'aktivitas' => 'Jalan santai (opsional)'],
                    ['waktu' => '18:15', 'aktivitas' => 'Berbuka puasa'],
                    ['waktu' => '19:30', 'aktivitas' => 'Olahraga ringan'],
                    ['waktu' => '22:00', 'aktivitas' => 'Tidur'],
                ],
            ]),

            // Default untuk sunnah, ayyamul_bidh, syawal, arafah, asyura, custom
            default => array_merge($base, [
                'tips' => [
                    ['type' => 'info', 'text' => 'Sahur: Makan karbohidrat kompleks + protein agar kenyang lebih lama.'],
                    ['type' => 'info', 'text' => 'Minum air bertahap: 2 gelas sahur, sisanya setelah berbuka.'],
                    ['type' => 'warning', 'text' => 'Hindari olahraga berat saat puasa. Olahraga ringan sebelum berbuka.'],
                    ['type' => 'warning', 'text' => 'Jangan langsung makan berat saat berbuka - mulai dengan kurma & air.'],
                    ['type' => 'success', 'text' => 'Puasa membantu autophagy (regenerasi sel) dan meningkatkan sensitivitas insulin.'],
                ],
                'jadwal_minum' => [
                    ['waktu' => '04:00', 'label' => 'Sahur - 2 gelas', 'ml' => 500],
                    ['waktu' => '18:15', 'label' => 'Berbuka - 2 gelas', 'ml' => 500],
                    ['waktu' => '19:00', 'label' => 'Setelah makan - 1 gelas', 'ml' => 250],
                    ['waktu' => '20:00', 'label' => 'Malam - 2 gelas', 'ml' => 500],
                    ['waktu' => '22:00', 'label' => 'Sebelum tidur - 1 gelas', 'ml' => 250],
                ],
                'jadwal_olahraga' => [
                    ['waktu' => '17:30', 'jenis' => 'Jalan Kaki Ringan', 'durasi' => 15, 'intensitas' => 'ringan', 'catatan' => 'Jalan santai 30-60 menit sebelum berbuka.'],
                    ['waktu' => '19:30', 'jenis' => 'Olahraga Ringan', 'durasi' => 25, 'intensitas' => 'ringan', 'catatan' => 'Setelah berbuka. Stretching atau cardio ringan.'],
                ],
                'jadwal_aktivitas' => [
                    ['waktu' => '03:30', 'aktivitas' => 'Bangun sahur'],
                    ['waktu' => '04:00', 'aktivitas' => 'Sahur + sholat subuh'],
                    ['waktu' => '07:00', 'aktivitas' => 'Aktivitas normal'],
                    ['waktu' => '17:30', 'aktivitas' => 'Jalan kaki ringan (opsional)'],
                    ['waktu' => '18:15', 'aktivitas' => 'Berbuka puasa'],
                    ['waktu' => '19:30', 'aktivitas' => 'Olahraga ringan (opsional)'],
                    ['waktu' => '22:00', 'aktivitas' => 'Persiapan tidur'],
                ],
            ]),
        };
    }

    /**
     * Tips puasa berdasarkan tipe
     */
    public static function tipsPuasa(string $tipe = 'sunnah'): array
    {
        $config = self::getConfigPuasa($tipe);
        return $config['tips'];
    }

    /**
     * Jadwal olahraga mingguan yang disarankan
     */
    public static function jadwalOlahragaIdeal(string $levelAktivitas, string $mode): array
    {
        $frekuensi = match ($levelAktivitas) {
            'tidak_aktif' => 3,
            'ringan' => 3,
            'sedang' => 4,
            'aktif' => 5,
            'sangat_aktif' => 6,
            default => 4,
        };

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Template jadwal mingguan
        $templates = [
            3 => [
                ['hari' => 'Senin', 'jenis' => 'Kardio (Jalan Cepat / Jogging)', 'durasi' => 30, 'intensitas' => 'sedang'],
                ['hari' => 'Rabu', 'jenis' => 'Kekuatan (Push Up, Squat, Plank)', 'durasi' => 25, 'intensitas' => 'sedang'],
                ['hari' => 'Jumat', 'jenis' => 'Kardio + Stretching', 'durasi' => 35, 'intensitas' => 'ringan'],
            ],
            4 => [
                ['hari' => 'Senin', 'jenis' => 'Kardio (Jogging / Bersepeda)', 'durasi' => 30, 'intensitas' => 'sedang'],
                ['hari' => 'Selasa', 'jenis' => 'Kekuatan Upper Body', 'durasi' => 25, 'intensitas' => 'sedang'],
                ['hari' => 'Kamis', 'jenis' => 'Kardio (Renang / Lompat Tali)', 'durasi' => 30, 'intensitas' => 'sedang'],
                ['hari' => 'Sabtu', 'jenis' => 'Kekuatan Lower Body + Core', 'durasi' => 30, 'intensitas' => 'sedang'],
            ],
            5 => [
                ['hari' => 'Senin', 'jenis' => 'Kardio Intensif (Lari / HIIT)', 'durasi' => 35, 'intensitas' => 'berat'],
                ['hari' => 'Selasa', 'jenis' => 'Kekuatan Upper Body', 'durasi' => 30, 'intensitas' => 'sedang'],
                ['hari' => 'Rabu', 'jenis' => 'Kardio Ringan + Yoga', 'durasi' => 40, 'intensitas' => 'ringan'],
                ['hari' => 'Kamis', 'jenis' => 'Kekuatan Lower Body', 'durasi' => 30, 'intensitas' => 'sedang'],
                ['hari' => 'Sabtu', 'jenis' => 'HIIT / Tabata', 'durasi' => 25, 'intensitas' => 'berat'],
            ],
            6 => [
                ['hari' => 'Senin', 'jenis' => 'Kardio Intensif', 'durasi' => 40, 'intensitas' => 'berat'],
                ['hari' => 'Selasa', 'jenis' => 'Kekuatan Upper Body', 'durasi' => 35, 'intensitas' => 'sedang'],
                ['hari' => 'Rabu', 'jenis' => 'Kardio + Core', 'durasi' => 35, 'intensitas' => 'sedang'],
                ['hari' => 'Kamis', 'jenis' => 'Kekuatan Lower Body', 'durasi' => 35, 'intensitas' => 'sedang'],
                ['hari' => 'Jumat', 'jenis' => 'HIIT / Tabata', 'durasi' => 25, 'intensitas' => 'berat'],
                ['hari' => 'Sabtu', 'jenis' => 'Kardio Ringan + Stretching', 'durasi' => 45, 'intensitas' => 'ringan'],
            ],
        ];

        $jadwal = [];
        $template = $templates[$frekuensi] ?? $templates[4];
        $hariOlahraga = collect($template)->pluck('hari')->toArray();

        foreach ($hari as $h) {
            $found = collect($template)->firstWhere('hari', $h);
            $jadwal[] = [
                'hari' => $h,
                'aktif' => $found !== null,
                'jenis' => $found['jenis'] ?? 'Istirahat',
                'durasi' => $found['durasi'] ?? 0,
                'intensitas' => $found['intensitas'] ?? '-',
                'tips' => $found ? null : 'Recovery day. Tetap aktif ringan (jalan kaki, stretching).',
            ];
        }

        return $jadwal;
    }

    /**
     * Target aktivitas harian yang disarankan
     */
    public static function targetAktivitasHarian(DietPlan $plan): array
    {
        $smart = self::generateSmartPlan($plan->gender, $plan->umur, $plan->tinggi_cm, $plan->berat_sekarang ?? $plan->berat_awal, $plan->level_aktivitas);

        return [
            ['label' => 'Langkah Kaki', 'target' => number_format($smart['target_harian']['langkah']), 'satuan' => 'langkah', 'icon' => 'walking', 'tips' => 'Jalan kaki ke kantor, naik tangga, jalan saat istirahat.'],
            ['label' => 'Air Minum', 'target' => number_format($smart['target_harian']['air_ml']), 'satuan' => 'ml', 'icon' => 'water', 'tips' => 'Minum 1 gelas setiap 1-2 jam. Bawa botol minum kemana-mana.'],
            ['label' => 'Tidur', 'target' => $smart['target_harian']['tidur_jam'], 'satuan' => 'jam', 'icon' => 'sleep', 'tips' => 'Tidur cukup membantu recovery otot dan mengontrol hormon lapar.'],
            ['label' => 'Kalori Terbakar', 'target' => number_format((int) round($plan->kalori_harian_target * 0.15)), 'satuan' => 'kkal', 'icon' => 'fire', 'tips' => 'Dari aktivitas harian + olahraga. Minimal 15% dari target kalori.'],
        ];
    }

    /**
     * Hitung konsistensi (streak & stats)
     */
    public static function hitungKonsistensi(DietPlan $plan): array
    {
        $today = Carbon::today();

        // Streak makan (berapa hari berturut-turut catat makanan)
        $streakMakan = 0;
        $date = $today->copy();
        while (true) {
            $ada = Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', $date)->exists();
            if (!$ada) break;
            $streakMakan++;
            $date->subDay();
        }

        // Streak olahraga (berapa hari berturut-turut olahraga, skip rest day)
        $streakOlahraga = 0;
        $date = $today->copy();
        for ($i = 0; $i < 30; $i++) {
            $ada = Exercise::where('diet_plan_id', $plan->id)->whereDate('tanggal', $date)->exists();
            if ($ada) $streakOlahraga++;
            elseif ($i < 2) break; // allow 1 rest day
            $date->subDay();
        }

        // Total hari aktif (ada catatan apapun)
        $totalHariAktif = Meal::where('diet_plan_id', $plan->id)
            ->distinct('tanggal')->count('tanggal');

        // Total hari program
        $totalHariProgram = max(1, $plan->tanggal_mulai->diffInDays($today) + 1);

        // Persentase konsistensi
        $konsistensiPersen = min(100, round(($totalHariAktif / $totalHariProgram) * 100));

        // Rata-rata kalori 7 hari terakhir
        $weekAgo = $today->copy()->subDays(7);
        $avgKalori7Hari = (int) round(Meal::where('diet_plan_id', $plan->id)
            ->where('tanggal', '>=', $weekAgo)
            ->whereDate('tanggal', '<=', $today)
            ->avg(\DB::raw('(SELECT SUM(m2.kalori) FROM meals m2 WHERE m2.diet_plan_id = meals.diet_plan_id AND m2.tanggal = meals.tanggal)')) ?? 0);

        // Minggu ini olahraga berapa kali
        $startOfWeek = $today->copy()->startOfWeek();
        $olahragaMingguIni = Exercise::where('diet_plan_id', $plan->id)
            ->where('tanggal', '>=', $startOfWeek)
            ->distinct('tanggal')->count('tanggal');

        return [
            'streak_makan' => $streakMakan,
            'streak_olahraga' => $streakOlahraga,
            'total_hari_aktif' => $totalHariAktif,
            'total_hari_program' => $totalHariProgram,
            'konsistensi_persen' => $konsistensiPersen,
            'olahraga_minggu_ini' => $olahragaMingguIni,
        ];
    }

    /**
     * Label level aktivitas
     */
    public static function labelAktivitas(): array
    {
        return [
            'tidak_aktif' => 'Tidak Aktif (kerja duduk, jarang gerak)',
            'ringan' => 'Ringan (olahraga 1-3x/minggu)',
            'sedang' => 'Sedang (olahraga 3-5x/minggu)',
            'aktif' => 'Aktif (olahraga 6-7x/minggu)',
            'sangat_aktif' => 'Sangat Aktif (atlet / kerja fisik berat)',
        ];
    }

    /**
     * Label mode diet
     */
    public static function labelMode(string $mode): string
    {
        return match ($mode) {
            'cut' => 'Turun Berat Badan',
            'bulk' => 'Naik Berat Badan',
            'maintain' => 'Jaga Berat Badan',
            default => $mode,
        };
    }
}
