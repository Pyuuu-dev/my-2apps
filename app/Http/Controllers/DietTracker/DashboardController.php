<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\MonthlyLog;
use App\Models\DietTracker\Meal;
use App\Models\DietTracker\Exercise;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\FastingLog;
use App\Models\DietTracker\DailyActivity;
use App\Services\DietHelperService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $planAktif = DietPlan::getActivePlan();
        $today = Carbon::today();

        $analisis = null;
        $smartPlan = null;
        $rekomendasiMenu = null;
        $rekomendasiOlahraga = null;
        $makanHariIni = collect();
        $olahragaHariIni = collect();
        $totalMinum = 0;
        $targetAir = 0;
        $aktivitasHariIni = null;
        $puasaHariIni = null;
        $tipsPuasa = [];
        $bulanIni = null;
        $lastMonthLog = null;
        $allLogs = collect();

        if ($planAktif) {
            $analisis = DietHelperService::analisisProgress($planAktif);

            $smartPlan = DietHelperService::generateSmartPlan(
                $planAktif->gender, $planAktif->umur, $planAktif->tinggi_cm,
                $analisis['berat_sekarang'], $planAktif->level_aktivitas
            );

            $rekomendasiMenu = DietHelperService::rekomendasiMenu($planAktif->kalori_harian_target);

            $kaloriPerluDibakar = max(200, $analisis['kalori_masuk'] - $planAktif->kalori_harian_target + 200);
            $rekomendasiOlahraga = DietHelperService::rekomendasiOlahraga($kaloriPerluDibakar)->take(6);

            $makanHariIni = Meal::where('diet_plan_id', $planAktif->id)
                ->whereDate('tanggal', $today)->orderBy('created_at')->get();
            $olahragaHariIni = Exercise::where('diet_plan_id', $planAktif->id)
                ->whereDate('tanggal', $today)->get();

            // Data minum hari ini
            $totalMinum = WaterLog::where('diet_plan_id', $planAktif->id)
                ->whereDate('tanggal', $today)->sum('jumlah_ml');
            $targetAir = $smartPlan['target_harian']['air_ml'];

            // Aktivitas hari ini
            $aktivitasHariIni = DailyActivity::where('diet_plan_id', $planAktif->id)
                ->whereDate('tanggal', $today)->first();

            // Cek puasa hari ini
            $puasaHariIni = FastingLog::getTodayFasting($planAktif->id);
            $configPuasa = null;
            if ($puasaHariIni) {
                $configPuasa = DietHelperService::getConfigPuasa($puasaHariIni->tipe);
                $tipsPuasa = $configPuasa['tips'];
                // Override rekomendasi menu dengan versi puasa
                $rekomendasiMenu = DietHelperService::rekomendasiMenu($planAktif->kalori_harian_target, [
                    'waktu_sahur' => $puasaHariIni->waktu_sahur,
                    'waktu_berbuka' => $puasaHariIni->waktu_berbuka,
                ]);
            }

            // Data bulan ini (live)
            $startOfMonth = now()->startOfMonth();
            $currentMonth = now()->format('Y-m');
            $hariLewat = $startOfMonth->diffInDays($today) + 1;

            $avgKaloriMasuk = (int) round(Meal::where('diet_plan_id', $planAktif->id)
                ->whereBetween('tanggal', [$startOfMonth, $today])->avg('kalori') ?? 0);
            $avgKaloriKeluar = (int) round(Exercise::where('diet_plan_id', $planAktif->id)
                ->whereBetween('tanggal', [$startOfMonth, $today])->avg('kalori_terbakar') ?? 0);
            $hariOlahraga = Exercise::where('diet_plan_id', $planAktif->id)
                ->whereBetween('tanggal', [$startOfMonth, $today])->distinct('tanggal')->count('tanggal');
            $hariCatat = Meal::where('diet_plan_id', $planAktif->id)
                ->whereBetween('tanggal', [$startOfMonth, $today])->distinct('tanggal')->count('tanggal');

            $bulanIni = [
                'label' => now()->translatedFormat('F Y'),
                'hari_lewat' => $hariLewat,
                'hari_di_bulan' => now()->daysInMonth,
                'avg_kalori_masuk' => $avgKaloriMasuk,
                'avg_kalori_keluar' => $avgKaloriKeluar,
                'hari_olahraga' => $hariOlahraga,
                'hari_catat' => $hariCatat,
                'konsistensi' => $hariLewat > 0 ? (int) round(($hariCatat / $hariLewat) * 100) : 0,
                'sudah_update' => MonthlyLog::where('diet_plan_id', $planAktif->id)->where('bulan', $currentMonth)->exists(),
            ];

            // Last month log
            $lastMonthLog = $planAktif->monthlyLogs()->orderByDesc('bulan')->first();

            // All logs for mini timeline
            $allLogs = $planAktif->monthlyLogs()->orderBy('bulan')->get();
        }

        return view('diet.dashboard.index', compact(
            'planAktif', 'analisis', 'smartPlan', 'rekomendasiMenu', 'rekomendasiOlahraga',
            'makanHariIni', 'olahragaHariIni', 'totalMinum', 'targetAir',
            'aktivitasHariIni', 'puasaHariIni', 'configPuasa', 'tipsPuasa', 'bulanIni', 'lastMonthLog', 'allLogs'
        ));
    }
}
