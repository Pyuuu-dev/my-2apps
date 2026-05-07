<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\FoodFavorite;
use App\Models\DietTracker\WeightLog;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\ExerciseLog;
use App\Models\DietTracker\FastingLog;
use App\Models\DietTracker\SleepLog;
use App\Models\DietTracker\Streak;
use App\Models\DietTracker\Badge;
use App\Models\DietTracker\DailySummary;
use App\Models\DietTracker\FoodDatabase;
use App\Services\AiNutritionService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    private TelegramService $telegram;
    private AiNutritionService $ai;

    public function __construct()
    {
        $this->telegram = new TelegramService();
        $this->ai = new AiNutritionService();
    }

    public function handle(Request $request)
    {
        $update = $request->all();
        try {
            if (isset($update['callback_query'])) return $this->handleCallback($update['callback_query']);
            if (isset($update['message'])) return $this->handleMessage($update['message']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
        return response()->json(['ok' => true]);
    }

    private function handleMessage(array $message): \Illuminate\Http\JsonResponse
    {
        $chatId = (string) $message['chat']['id'];
        $text = $message['text'] ?? '';
        $photo = $message['photo'] ?? null;
        $profile = UserProfile::findOrCreateByChatId($chatId, [
            'nama' => $message['from']['first_name'] ?? 'User',
            'username' => $message['from']['username'] ?? null,
        ]);
        if ($photo) return $this->handlePhoto($profile, $message);
        if ($profile->state) return $this->handleState($profile, $text);
        if (str_starts_with($text, '/')) return $this->handleCommand($profile, $text);
        return $this->handleNaturalText($profile, $text);
    }

    private function handleCommand(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $parts = explode(' ', $text, 2);
        $command = strtolower(explode('@', $parts[0])[0]);
        $args = $parts[1] ?? '';
        return match ($command) {
            '/start' => $this->cmdStart($profile),
            '/menu', '/m' => $this->cmdMenu($profile),
            '/makan' => $this->cmdMakan($profile, $args, '/makan'),
            '/s' => $this->cmdMakan($profile, $args, '/s'),
            '/ms' => $this->cmdMakan($profile, $args, '/ms'),
            '/mm' => $this->cmdMakan($profile, $args, '/mm'),
            '/air' => $this->cmdAir($profile, $args),
            '/berat' => $this->cmdBerat($profile, $args),
            '/olahraga' => $this->cmdOlahraga($profile, $args),
            '/dashboard', '/d' => $this->cmdDashboard($profile),
            '/stats' => $this->cmdStats($profile),
            '/profil' => $this->cmdProfil($profile),
            '/target' => $this->cmdTarget($profile),
            '/riwayat' => $this->cmdRiwayat($profile, $args),
            '/rekomendasi' => $this->cmdRekomendasi($profile),
            '/badge' => $this->cmdBadge($profile),
            '/reminder' => $this->cmdReminder($profile),
            '/favorit', '/fav' => $this->cmdFavorit($profile),
            '/puasa' => $this->cmdPuasa($profile, $args),
            '/tidur' => $this->cmdTidur($profile, $args),
            '/hapus' => $this->cmdHapus($profile, $args),
            '/timeline' => $this->cmdTimeline($profile),
            '/motivasi' => $this->cmdMotivasi($profile),
            '/smartreminder' => $this->cmdSmartReminder($profile),
            '/help' => $this->cmdHelp($profile),
            '/setup' => $this->cmdSetup($profile),
            '/reset' => $this->cmdReset($profile),
            '/batal', '/cancel' => $this->cmdBatal($profile),
            default => $this->cmdUnknown($profile),
        };
    }

    private function cmdStart(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $nama = $profile->nama ?? 'Kamu';
        $g = $this->getGreeting();
        if (!$profile->tinggi_cm || !$profile->berat_kg) {
            $text = "🎉 <b>{$g}, {$nama}!</b>\n\nAku personal nutrition coach kamu:\n\n🍽 Catat makanan & kalori otomatis\n📸 Analisis foto makanan AI\n⚖️ Pantau berat & BMI\n💧 Tracking air minum\n🏃 Catat olahraga\n🕐 Intermittent Fasting\n😴 Sleep tracker\n📊 Statistik & progress\n🏆 Gamification\n⏰ Reminder\n💡 Rekomendasi AI\n\nKetik /setup untuk mulai 👇";
            $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        } else { $this->cmdMenu($profile); }
        return response()->json(['ok' => true]);
    }

    private function cmdMenu(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $level = $profile->getLevel();
        $streak = $profile->streak;
        $text = "📋 <b>Menu Utama</b>\n━━━━━━━━━━━━━━━\n" . $this->getGreeting() . ", {$profile->nama}! {$level['icon']} Lv.{$level['level']} | 🔥 " . ($streak->current_streak ?? 0) . "\n";
        $fasting = $profile->getActiveFasting();
        if ($fasting) { $e = $fasting->getElapsedMinutes(); $text .= "🕐 Puasa: " . intdiv($e,60) . "j" . ($e%60) . "m ({$fasting->getProgressPercent()}%)\n"; }
        $keyboard = [
            [['text'=>'📊 Dashboard','callback_data'=>'dashboard'],['text'=>'🍽 Makan','callback_data'=>'log_food']],
            [['text'=>'💧 Air','callback_data'=>'log_water'],['text'=>'⚖️ Berat','callback_data'=>'log_weight']],
            [['text'=>'🏃 Olahraga','callback_data'=>'log_exercise'],['text'=>'⭐ Favorit','callback_data'=>'favorites']],
            [['text'=>'🕐 Puasa','callback_data'=>'fasting_menu'],['text'=>'😴 Tidur','callback_data'=>'sleep_menu']],
            [['text'=>'📈 Stats','callback_data'=>'stats'],['text'=>'🏆 Badge','callback_data'=>'badges']],
            [['text'=>'💡 Rekomendasi','callback_data'=>'recommend'],['text'=>'🎯 Timeline','callback_data'=>'timeline']],
            [['text'=>'🧠 Smart Reminder','callback_data'=>'smart_reminder'],['text'=>'💪 Motivasi','callback_data'=>'motivasi']],
            [['text'=>'👤 Profil','callback_data'=>'profile'],['text'=>'⏰ Reminder','callback_data'=>'reminder_menu']],
            [['text'=>'❓ Help','callback_data'=>'help']],
        ];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function cmdMakan(UserProfile $profile, string $args, string $cmd = '/makan'): \Illuminate\Http\JsonResponse
    {
        $presetWaktu = match($cmd){'/s'=>'sarapan','/ms'=>'makan_siang','/mm'=>'makan_malam',default=>null};
        if (empty($args) && !$presetWaktu) {
            $profile->update(['state'=>'waiting_food_time','state_data'=>[]]);
            $text = "🍽 <b>Catat Makanan</b>\n\nPilih waktu atau shortcut:\n• <code>/s nasi goreng</code> (sarapan)\n• <code>/ms ayam geprek</code> (siang)\n• <code>/mm soto</code> (malam)\n\nAtau kirim 📸 foto!";
            $keyboard = [[['text'=>'🌅 Sarapan','callback_data'=>'meal_sarapan'],['text'=>'☀️ Siang','callback_data'=>'meal_siang']],[['text'=>'🌙 Malam','callback_data'=>'meal_malam'],['text'=>'🍪 Snack','callback_data'=>'meal_snack']],[['text'=>'⭐ Dari Favorit','callback_data'=>'quick_add_menu']]];
            $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
            return response()->json(['ok' => true]);
        }
        if ($presetWaktu) $profile->update(['state_data'=>['waktu_makan'=>$presetWaktu]]);
        if (!empty($args)) return $this->estimateAndConfirm($profile, $args);
        return response()->json(['ok' => true]);
    }

    private function cmdAir(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if (!empty($args)) { $ml=(int)$args; if($ml<=0||$ml>5000){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ 1-5000 ml.");return response()->json(['ok'=>true]);} return $this->logWater($profile,$ml); }
        $today = now('Asia/Singapore')->toDateString();
        $tot = WaterLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->sum('jumlah_ml');
        $tar = $profile->getAirTarget(); $pct = min(100,round(($tot/$tar)*100));
        $text = "💧 <b>Air</b>\n\n{$tot}/{$tar}ml ({$pct}%)\n".$this->progressBar($pct)."\n\nPilih atau <code>/air 300</code>";
        $kb = [[['text'=>'🥤 250ml','callback_data'=>'water_250'],['text'=>'🥤 500ml','callback_data'=>'water_500']],[['text'=>'🥤 750ml','callback_data'=>'water_750'],['text'=>'🥤 1L','callback_data'=>'water_1000']]];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $kb);
        return response()->json(['ok' => true]);
    }

    private function cmdBerat(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if (!empty($args)) return $this->logWeight($profile,(float)$args);
        $profile->update(['state'=>'waiting_weight','state_data'=>null]);
        $last = WeightLog::where('profile_id',$profile->id)->orderByDesc('tanggal')->first();
        $text = "⚖️ <b>Berat</b>\n\n"; if($last) $text .= "Terakhir: {$last->berat_kg}kg ({$last->tanggal->format('d/m')})\n";
        $text .= "\nKetik (kg): <code>70.5</code>";
        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function cmdOlahraga(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if (!empty($args)) return $this->parseAndLogExercise($profile, $args);
        $profile->update(['state'=>'waiting_exercise','state_data'=>null]);
        $kb = [[['text'=>'🏃 Lari','callback_data'=>'exercise_lari'],['text'=>'🚶 Jalan','callback_data'=>'exercise_jalan'],['text'=>'🏋️ Gym','callback_data'=>'exercise_gym']],[['text'=>'🏊 Renang','callback_data'=>'exercise_renang'],['text'=>'🚴 Sepeda','callback_data'=>'exercise_sepeda'],['text'=>'🧘 Yoga','callback_data'=>'exercise_yoga']],[['text'=>'⚡ HIIT','callback_data'=>'exercise_hiit'],['text'=>'🏸 Badminton','callback_data'=>'exercise_badminton'],['text'=>'⚽ Futsal','callback_data'=>'exercise_futsal']]];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, "🏃 <b>Olahraga</b>\n\nKetik: <code>lari 30 menit</code>\nAtau pilih:", $kb);
        return response()->json(['ok' => true]);
    }

    private function cmdDashboard(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $today = now('Asia/Singapore')->toDateString();
        $fl = FoodLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->get();
        $water = WaterLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->sum('jumlah_ml');
        $ex = ExerciseLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->get();
        $tKal=$fl->sum('kalori'); $tP=$fl->sum('protein'); $tK=$fl->sum('karbohidrat'); $tL=$fl->sum('lemak'); $exCal=$ex->sum('kalori_terbakar');
        $tarK=$profile->kalori_target?:2000; $tarP=$profile->protein_target?:100; $tarA=$profile->getAirTarget();
        $sisa=$tarK-$tKal+$exCal; $pK=min(100,round(($tKal/$tarK)*100)); $pP=min(100,round(($tP/max(1,$tarP))*100)); $pA=min(100,round(($water/$tarA)*100));
        $streak=$profile->streak; $level=$profile->getLevel();

        // Net calories (makan - olahraga)
        $netKal = $tKal - $exCal;
        $pctNet = $tarK > 0 ? min(100, round(($netKal / $tarK) * 100)) : 0;

        $text = "📊 <b>Dashboard</b>\n📅 " . now('Asia/Singapore')->translatedFormat('l, d M Y') . "\n━━━━━━━━━━━━━━━\n";
        $text .= "{$level['icon']} Lv.{$level['level']} | 🔥 " . ($streak->current_streak ?? 0) . " hari\n\n";

        // Kalori masuk (estimasi tinggi)
        $text .= "🔥 <b>Kalori Masuk</b>\n";
        $text .= $this->progressBar($pK) . " {$pK}%\n";
        $text .= "   {$tKal} / {$tarK} kkal\n";

        // Olahraga (estimasi rendah)
        if ($exCal > 0) {
            $text .= "🏃 Olahraga: -{$exCal} kkal (" . $ex->sum('durasi_menit') . " min)\n";
            $text .= "📊 <b>Net: {$netKal} kkal</b> ({$pctNet}%)\n";
        }

        $text .= "   Sisa: <b>{$sisa} kkal</b>\n\n";

        // Macros
        $text .= "🥩 P: {$tP}g/{$tarP}g " . $this->progressBar($pP, 10) . "\n";
        $text .= "🍚 K: {$tK}g/" . ($profile->karbo_target ?: 250) . "g\n";
        $text .= "🧈 L: {$tL}g/" . ($profile->lemak_target ?: 65) . "g\n\n";

        // Air
        $text .= "💧 {$water}/{$tarA}ml " . $this->progressBar($pA, 10) . "\n";

        // Fasting
        $fasting = $profile->getActiveFasting();
        if ($fasting) {
            $e = $fasting->getElapsedMinutes();
            $text .= "🕐 Puasa: " . intdiv($e, 60) . "j" . ($e % 60) . "m ({$fasting->getProgressPercent()}%)\n";
        }

        // Smart warnings
        $hour = (int) now('Asia/Singapore')->format('H');
        if ($pK > 100) {
            $over = $tKal - $tarK;
            $text .= "\n🚨 <b>OVER {$over} kkal!</b>";
            if ($over > 500) $text .= " Sangat berlebihan!";
            $text .= "\n💡 Olahraga 30 min bisa bantu bakar ~150 kkal.\n";
        } elseif ($pK >= 85 && $pK <= 100) {
            $text .= "\n✅ Target hampir tercapai. Jaga!\n";
        } elseif ($pK < 30 && $hour >= 15) {
            $text .= "\n⚠️ Kalori terlalu rendah! Jangan skip makan.\n";
        } elseif ($pK < 50 && $hour >= 19) {
            $text .= "\n⚠️ Makan terlalu sedikit hari ini. Metabolisme bisa turun.\n";
        }

        // Protein check
        if ($tP < ($tarP * 0.5) && $hour >= 18) {
            $text .= "⚠️ Protein baru {$tP}g/{$tarP}g. Tambah protein!\n";
        }

        // Water check
        if ($pA < 40 && $hour >= 15) {
            $text .= "💧 Minum air masih kurang! Target {$tarA}ml.\n";
        }

        $kb = [[['text'=>'🍽 +Makan','callback_data'=>'log_food'],['text'=>'💧 +Air','callback_data'=>'log_water'],['text'=>'🏃 +Sport','callback_data'=>'log_exercise']],[['text'=>'📋 Riwayat','callback_data'=>'history'],['text'=>'💡 Saran','callback_data'=>'recommend']],[['text'=>'⭐ Quick','callback_data'=>'quick_add_menu'],['text'=>'📋 Menu','callback_data'=>'menu']]];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $kb);
        DailySummary::recalculate($profile->id, $today);
        return response()->json(['ok' => true]);
    }

    private function cmdStats(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $today=now('Asia/Singapore'); $ws=$today->copy()->startOfWeek();
        $sums=DailySummary::where('profile_id',$profile->id)->whereBetween('tanggal',[$ws->toDateString(),$today->toDateString()])->orderBy('tanggal')->get();
        $wts=WeightLog::where('profile_id',$profile->id)->where('tanggal','>=',$today->copy()->subDays(30)->toDateString())->orderBy('tanggal')->get();
        $slps=SleepLog::where('profile_id',$profile->id)->where('tanggal','>=',$ws->toDateString())->get();
        $text="📈 <b>Stats Mingguan</b>\n{$ws->format('d/m')} - {$today->format('d/m')}\n━━━━━━━━━━━━━━━\n\n";
        if($sums->isEmpty()){$text.="Belum ada data.\n";}
        else{
            $text.="🔥 ".round($sums->avg('total_kalori'))." kkal/hari\n🥩 ".round($sums->avg('total_protein'),1)."g | 💧 ".round($sums->avg('total_air_ml'))."ml\n🏃 ".$sums->sum('total_exercise_menit')." menit total\n";
            if($slps->count()>0) $text.="😴 ".round($slps->avg('durasi_jam'),1)." jam\n";
            $text.="\n"; $days=['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
            foreach($sums as $s) $text.=$days[$s->tanggal->dayOfWeekIso-1].": ".$this->miniBar($s->pct_target)." {$s->total_kalori}\n";
            $text.="\n";
        }
        if($wts->count()>=2){$d=round($wts->last()->berat_kg-$wts->first()->berat_kg,1);$text.="⚖️ {$wts->first()->berat_kg}→{$wts->last()->berat_kg} (".($d>0?"+":"")."{$d}kg)\n";}
        $kb=[[['text'=>'📊 Dashboard','callback_data'=>'dashboard'],['text'=>'🧠 AI Analisis','callback_data'=>'analyze_weekly']]];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,$text,$kb);
        return response()->json(['ok' => true]);
    }

    private function cmdProfil(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $bmi=$profile->bmi?:$profile->hitungBMI(); $level=$profile->getLevel();
        $text="👤 <b>Profil</b>\n━━━━━━━━━━━━━━━\n\n{$level['icon']} {$level['nama']} Lv.{$level['level']}\n\n";
        $text.="{$profile->nama} | ".ucfirst($profile->gender??'-')." | {$profile->umur}thn\n";
        $text.="📏 {$profile->tinggi_cm}cm | ⚖️ {$profile->berat_kg}kg | 🎯 ".($profile->berat_target??'-')."kg\n";
        $text.="Goal: ".ucfirst($profile->goal??'-')." | {$profile->level_aktivitas}\n\n";
        $text.="BMI: <b>{$bmi}</b> (".$this->getBmiCategory($bmi).")\nBMR: ".round($profile->bmr??0)." | TDEE: ".round($profile->tdee??0)." | BF: ".($profile->body_fat_pct??'-')."%\n\n";
        $text.="🎯 {$profile->kalori_target} kkal | P:{$profile->protein_target}g K:{$profile->karbo_target}g L:{$profile->lemak_target}g\n💧 ".$profile->getAirTarget()."ml";
        $kb=[[['text'=>'✏️ Edit','callback_data'=>'edit_profile'],['text'=>'🔄 Recalc','callback_data'=>'recalculate']],[['text'=>'◀️ Menu','callback_data'=>'menu']]];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,$text,$kb);
        return response()->json(['ok' => true]);
    }

    private function cmdTarget(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $today=now('Asia/Singapore')->toDateString(); $fl=FoodLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->get();
        $items=[['🔥','Kalori',$fl->sum('kalori'),$profile->kalori_target?:2000,'kkal'],['🥩','Protein',$fl->sum('protein'),$profile->protein_target?:100,'g'],['🍚','Karbo',$fl->sum('karbohidrat'),$profile->karbo_target?:250,'g'],['🧈','Lemak',$fl->sum('lemak'),$profile->lemak_target?:65,'g']];
        $text="🎯 <b>Target</b>\n━━━━━━━━━━━━━━━\n\n";
        foreach($items as[$i,$n,$c,$t,$u]){$p=min(100,round(($c/max(1,$t))*100));$text.="{$i} {$n}: {$c}/{$t}{$u}\n".$this->progressBar($p)." {$p}%\n\n";}
        $this->telegram->sendMessage($profile->telegram_chat_id,$text);
        return response()->json(['ok' => true]);
    }

    private function cmdRiwayat(UserProfile $profile, string $args=''): \Illuminate\Http\JsonResponse
    {
        $today=now('Asia/Singapore'); $date=$today->toDateString();
        if($args==='kemarin') $date=$today->copy()->subDay()->toDateString();
        elseif(preg_match('/^(\d+)\s*(hari|day)/',$args,$m)) $date=$today->copy()->subDays((int)$m[1])->toDateString();
        elseif(preg_match('/^\d{4}-\d{2}-\d{2}$/',$args)) $date=$args;
        $logs=FoodLog::where('profile_id',$profile->id)->whereDate('tanggal',$date)->orderBy('created_at')->get();
        $text="📋 <b>Riwayat</b>\n📅 ".\Carbon\Carbon::parse($date)->translatedFormat('l, d M')."\n━━━━━━━━━━━━━━━\n\n";
        if($logs->isEmpty()){$text.="Kosong.\n";}
        else{
            $grouped=$logs->groupBy('waktu_makan');
            $labels=['sarapan'=>'🌅 Sarapan','makan_siang'=>'☀️ Siang','makan_malam'=>'🌙 Malam','snack'=>'🍪 Snack'];
            foreach($labels as $k=>$l){if(isset($grouped[$k])){$text.="<b>{$l}:</b>\n";foreach($grouped[$k] as $log)$text.="  #{$log->id} {$log->nama_makanan} ({$log->kalori})\n";$text.="  <i>Sub: ".$grouped[$k]->sum('kalori')."</i>\n\n";}}
            $text.="━━━━━━━━━━━━━━━\n📊 <b>Total: {$logs->sum('kalori')} kkal</b>\n";
        }
        $kb=[[['text'=>'◀️','callback_data'=>'history_prev_'.$date],['text'=>'▶️','callback_data'=>'history_next_'.$date]],[['text'=>'🗑 Hapus','callback_data'=>'delete_select'],['text'=>'📊 Dashboard','callback_data'=>'dashboard']]];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,$text,$kb);
        return response()->json(['ok' => true]);
    }

    private function cmdRekomendasi(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        if(!$profile->canUseAi()){$this->telegram->sendMessage($profile->telegram_chat_id,"⚠️ AI limit tercapai.");return response()->json(['ok'=>true]);}
        $this->telegram->sendChatAction($profile->telegram_chat_id,'typing');
        $today=now('Asia/Singapore')->toDateString(); $tKal=FoodLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->sum('kalori');
        $sisa=($profile->kalori_target?:2000)-$tKal;
        $macros=['protein'=>FoodLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->sum('protein'),'karbo'=>FoodLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->sum('karbohidrat'),'lemak'=>FoodLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->sum('lemak')];
        $result=$this->ai->recommendMenu($sisa,$profile->goal??'diet',$macros,$profile->id); $profile->incrementAiUsage();
        if(!$result['success']){$text="💡 Sisa: <b>{$sisa} kkal</b>\n\n".$this->getFallbackRecommendation($sisa);}
        else{$data=$result['data'];$text="💡 <b>AI Rekomendasi</b>\nSisa: <b>{$sisa} kkal</b>\n━━━━━━━━━━━━━━━\n\n";if(isset($data['rekomendasi']))foreach($data['rekomendasi'] as $i=>$r)$text.=($i+1).". <b>".($r['nama']??'-')."</b> (~".($r['kalori']??'?').")\n";if(isset($data['tips']))$text.="\n💡 {$data['tips']}\n";if(!empty($data['warning']))$text.="⚠️ {$data['warning']}\n";}
        $this->telegram->sendMessage($profile->telegram_chat_id,$text);
        return response()->json(['ok' => true]);
    }

    private function cmdBadge(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $badges=Badge::where('profile_id',$profile->id)->orderByDesc('earned_at')->get(); $level=$profile->getLevel(); $streak=$profile->streak;
        $text="🏆 <b>Badges</b>\n━━━━━━━━━━━━━━━\n\n{$level['icon']} {$level['nama']} Lv.{$level['level']}\n🔥 ".($streak->current_streak??0)." | Best: ".($streak->longest_streak??0)." | Total: ".($streak->total_days_logged??0)."\n\n";
        if($badges->isEmpty()) $text.="Belum ada. Konsisten! 💪\n🔓 🔥3d ⭐7d 🌟14d 💎30d 👑60d 🏆100d 💧Water 💪Workout 🎯Goal 🕐Fasting\n";
        else foreach($badges as $b) $text.="{$b->badge_icon} <b>{$b->badge_name}</b> ({$b->earned_at->format('d/m')})\n";
        $this->telegram->sendMessage($profile->telegram_chat_id,$text);
        return response()->json(['ok' => true]);
    }

    private function cmdReminder(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $kb=[[['text'=>'💧 Air','callback_data'=>'reminder_water'],['text'=>'🍽 Makan','callback_data'=>'reminder_meal']],[['text'=>'🏃 Sport','callback_data'=>'reminder_exercise'],['text'=>'😴 Tidur','callback_data'=>'reminder_sleep']],[['text'=>'📋 Lihat','callback_data'=>'reminder_list'],['text'=>'🗑 Hapus','callback_data'=>'reminder_clear']]];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,"⏰ <b>Reminder</b>\n\nPilih:",$kb);
        return response()->json(['ok' => true]);
    }

    private function cmdFavorit(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $favs=FoodFavorite::getTopFavorites($profile->id,10);
        $text="⭐ <b>Favorit</b>\n━━━━━━━━━━━━━━━\n\n";
        if($favs->isEmpty()) $text.="Belum ada. Otomatis terisi dari makanan yang sering dicatat!\n";
        else foreach($favs as $i=>$f) $text.=($i+1).". <b>{$f->nama_makanan}</b> ({$f->kalori}) x{$f->use_count}\n";
        $kb=[];foreach($favs->take(5) as $f) $kb[]=[['text'=>"➕ {$f->nama_makanan}",'callback_data'=>"quickadd_{$f->id}"]];
        if(!empty($kb)) $kb[]=[['text'=>'◀️ Menu','callback_data'=>'menu']];
        if(!empty($kb)) $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,$text,$kb);
        else $this->telegram->sendMessage($profile->telegram_chat_id,$text);
        return response()->json(['ok' => true]);
    }

    private function cmdPuasa(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        $active=$profile->getActiveFasting();
        if($args==='stop'||$args==='buka'){if(!$active){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ Tidak ada puasa aktif.");return response()->json(['ok'=>true]);}return $this->stopFasting($profile,$active);}
        if($active){$e=$active->getElapsedMinutes();$pct=$active->getProgressPercent();$text="🕐 <b>Puasa</b>\n\nTipe: {$active->tipe} ({$active->getTargetHours()}j)\nMulai: {$active->mulai_puasa->format('H:i')}\nDurasi: ".intdiv($e,60)."j".($e%60)."m\n".$this->progressBar((int)$pct)." {$pct}%\n";if($pct>=100)$text.="\n🎉 Target!";else{$r=($active->getTargetHours()*60)-$e;$text.="\n⏳ Sisa: ".intdiv($r,60)."j".($r%60)."m";}$this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,$text,[[['text'=>'🍽 Buka','callback_data'=>'fasting_stop']]]);return response()->json(['ok'=>true]);}
        $kb=[[['text'=>'16:8','callback_data'=>'fasting_start_16_8'],['text'=>'18:6','callback_data'=>'fasting_start_18_6']],[['text'=>'20:4','callback_data'=>'fasting_start_20_4'],['text'=>'OMAD','callback_data'=>'fasting_start_omad']]];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,"🕐 <b>Fasting</b>\n\nPilih tipe:",$kb);
        return response()->json(['ok' => true]);
    }

    private function cmdTidur(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if(empty($args)){$profile->update(['state'=>'waiting_sleep','state_data'=>null]);$this->telegram->sendMessage($profile->telegram_chat_id,"😴 <b>Tidur</b>\n\nFormat: <code>23:00 06:30</code>\n(tidur spasi bangun)");return response()->json(['ok'=>true]);}
        return $this->parseSleep($profile,$args);
    }

    private function cmdHapus(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if(empty($args)||$args==='terakhir'){$log=FoodLog::where('profile_id',$profile->id)->orderByDesc('created_at')->first();if($log){$n=$log->nama_makanan;$log->delete();DailySummary::recalculate($profile->id,now('Asia/Singapore')->toDateString());$this->telegram->sendMessage($profile->telegram_chat_id,"🗑 {$n}");}else $this->telegram->sendMessage($profile->telegram_chat_id,"❌ Kosong.");}
        elseif(is_numeric($args)){$log=FoodLog::where('profile_id',$profile->id)->where('id',(int)$args)->first();if($log){$n=$log->nama_makanan;$log->delete();DailySummary::recalculate($profile->id,now('Asia/Singapore')->toDateString());$this->telegram->sendMessage($profile->telegram_chat_id,"🗑 {$n}");}else $this->telegram->sendMessage($profile->telegram_chat_id,"❌ Not found.");}
        return response()->json(['ok' => true]);
    }

    private function cmdTimeline(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        if (!$profile->berat_target || !$profile->berat_kg) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Setup berat & target dulu. /setup");
            return response()->json(['ok' => true]);
        }
        if (!$profile->canUseAi()) { $this->telegram->sendMessage($profile->telegram_chat_id, "⚠️ AI limit."); return response()->json(['ok' => true]); }

        $this->telegram->sendChatAction($profile->telegram_chat_id, 'typing');

        $weeklyAvgKal = DailySummary::where('profile_id', $profile->id)
            ->where('tanggal', '>=', now('Asia/Singapore')->subDays(7)->toDateString())
            ->avg('total_kalori') ?? 0;

        $result = $this->ai->estimateGoalTimeline([
            'berat_sekarang' => $profile->berat_kg,
            'berat_target' => $profile->berat_target,
            'goal' => $profile->goal,
            'tdee' => $profile->tdee,
            'kalori_target' => $profile->kalori_target,
            'avg_kalori_aktual' => round($weeklyAvgKal),
            'gender' => $profile->gender,
            'umur' => $profile->umur,
        ], $profile->id);
        $profile->incrementAiUsage();

        if (!$result['success']) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Gagal hitung timeline.");
            return response()->json(['ok' => true]);
        }

        $d = $result['data'];
        $diff = round(abs($profile->berat_kg - $profile->berat_target), 1);
        $arrow = $profile->goal === 'bulking' ? '📈' : '📉';

        $text = "🎯 <b>Timeline Goal</b>\n━━━━━━━━━━━━━━━\n\n";
        $text .= "⚖️ Sekarang: <b>{$profile->berat_kg} kg</b>\n";
        $text .= "🎯 Target: <b>{$profile->berat_target} kg</b> ({$arrow} {$diff} kg)\n\n";
        $text .= "📅 Estimasi: <b>" . ($d['estimasi_minggu'] ?? '?') . " minggu</b>\n";
        if (isset($d['estimasi_tanggal'])) $text .= "📆 Target: <b>{$d['estimasi_tanggal']}</b>\n";
        if (isset($d['kg_per_minggu'])) $text .= "📊 Rate: {$d['kg_per_minggu']} kg/minggu\n";
        if (isset($d['defisit_harian'])) $text .= "🔥 Defisit: {$d['defisit_harian']} kkal/hari\n\n";
        if (isset($d['saran'])) $text .= "💡 {$d['saran']}\n";
        if (!empty($d['peringatan'])) $text .= "\n⚠️ {$d['peringatan']}";

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function cmdMotivasi(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        if (!$profile->canUseAi()) { $this->telegram->sendMessage($profile->telegram_chat_id, "⚠️ AI limit."); return response()->json(['ok' => true]); }
        $this->telegram->sendChatAction($profile->telegram_chat_id, 'typing');

        $today = now('Asia/Singapore')->toDateString();
        $todayKal = FoodLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->sum('kalori');
        $streak = $profile->streak;

        $result = $this->ai->generateMotivation([
            'streak' => $streak->current_streak ?? 0,
            'longest_streak' => $streak->longest_streak ?? 0,
            'berat_sekarang' => $profile->berat_kg,
            'berat_target' => $profile->berat_target,
            'goal' => $profile->goal,
            'kalori_hari_ini' => $todayKal,
            'target_kalori' => $profile->kalori_target,
            'pct_target' => $profile->kalori_target ? round(($todayKal / $profile->kalori_target) * 100) : 0,
            'jam_sekarang' => now('Asia/Singapore')->format('H:i'),
            'hari' => now('Asia/Singapore')->translatedFormat('l'),
        ], $profile->id);
        $profile->incrementAiUsage();

        if (!$result['success']) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "💪 Tetap semangat! Konsistensi adalah kunci.");
            return response()->json(['ok' => true]);
        }

        $d = $result['data'];
        $text = ($d['emoji'] ?? '💪') . " " . ($d['motivasi'] ?? 'Semangat!') . "\n\n";
        if (isset($d['tip_hari_ini'])) $text .= "💡 <b>Tip:</b> {$d['tip_hari_ini']}";

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function cmdSmartReminder(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        if (!$profile->canUseAi()) { $this->telegram->sendMessage($profile->telegram_chat_id, "⚠️ AI limit."); return response()->json(['ok' => true]); }
        $this->telegram->sendChatAction($profile->telegram_chat_id, 'typing');

        // Kumpulkan pola user
        $recentFood = FoodLog::where('profile_id', $profile->id)
            ->where('tanggal', '>=', now('Asia/Singapore')->subDays(7)->toDateString())
            ->get();

        $mealTimes = $recentFood->groupBy('waktu_makan')->map(fn($g) => $g->count());
        $avgSleep = SleepLog::where('profile_id', $profile->id)->avg('durasi_jam');

        $result = $this->ai->suggestReminders([
            'goal' => $profile->goal,
            'berat_kg' => $profile->berat_kg,
            'air_target' => $profile->getAirTarget(),
            'pola_makan' => $mealTimes->toArray(),
            'avg_tidur' => $avgSleep ? round($avgSleep, 1) : null,
            'reminder_aktif' => $profile->reminders()->where('aktif', true)->count(),
        ], $profile->id);
        $profile->incrementAiUsage();

        if (!$result['success'] || !isset($result['data']['reminders'])) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Gagal generate saran reminder.");
            return response()->json(['ok' => true]);
        }

        $reminders = $result['data']['reminders'];
        $text = "🧠 <b>AI Saran Reminder</b>\n━━━━━━━━━━━━━━━\n\n";

        $keyboard = [];
        foreach ($reminders as $i => $r) {
            $waktu = $r['waktu'] ?? '08:00';
            $judul = $r['judul'] ?? 'Reminder';
            $text .= ($i + 1) . ". ⏰ <b>{$waktu}</b> - {$judul}\n";
            if (isset($r['alasan'])) $text .= "   <i>{$r['alasan']}</i>\n";
            $text .= "\n";

            // Tombol untuk set reminder ini
            $tipe = $r['tipe'] ?? 'custom';
            $keyboard[] = [['text' => "✅ Set {$waktu} {$judul}", 'callback_data' => "setreminder_{$tipe}_{$waktu}"]];
        }

        if (isset($result['data']['tips_umum'])) {
            $text .= "💡 {$result['data']['tips_umum']}\n";
        }

        $keyboard[] = [['text' => '✅ Set Semua', 'callback_data' => 'setreminder_all'], ['text' => '◀️ Menu', 'callback_data' => 'menu']];

        // Simpan saran di state untuk dipakai callback
        $profile->update(['state_data' => ['suggested_reminders' => $reminders]]);

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function cmdHelp(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $text = "❓ <b>Help</b>\n━━━━━━━━━━━━━━━\n\n";
        $text .= "🍽 <b>MAKAN:</b>\n";
        $text .= "/makan nasi goreng\n";
        $text .= "/makan nasi goreng + ayam + es teh\n";
        $text .= "/s ayam (sarapan) | /ms (siang) | /mm (malam)\n";
        $text .= "Kirim foto + caption | Ketik langsung\n\n";
        $text .= "💧 /air 500 | ⚖️ /berat 70.5\n";
        $text .= "🏃 /olahraga lari 30 menit\n";
        $text .= "🕐 /puasa | /puasa buka\n";
        $text .= "😴 /tidur 23:00 06:30\n\n";
        $text .= "📊 /d (dashboard) | /stats | /target\n";
        $text .= "📋 /riwayat | /riwayat kemarin\n";
        $text .= "⭐ /fav | 🏆 /badge\n\n";
        $text .= "🤖 <b>AI:</b>\n";
        $text .= "/rekomendasi - Saran menu\n";
        $text .= "/smartreminder - AI saran jadwal\n";
        $text .= "/timeline - Estimasi capai target\n";
        $text .= "/motivasi - Motivasi harian\n\n";
        $text .= "⚙️ /setup | /reminder | /hapus [ID]\n";
        $text .= "/menu | /batal | /reset\n\n";
        $text .= "💡 <b>Tips:</b> Ketik nama makanan langsung!\n";
        $text .= "Support multi: <code>bakso + es teh + gorengan</code>";
        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function cmdSetup(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $profile->update(['state'=>'setup_gender','state_data'=>[]]);
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,"⚙️ <b>Setup</b>\n\nGender:",[[['text'=>'👨 Pria','callback_data'=>'setup_gender_pria'],['text'=>'👩 Wanita','callback_data'=>'setup_gender_wanita']]]);
        return response()->json(['ok' => true]);
    }

    private function cmdReset(UserProfile $profile): \Illuminate\Http\JsonResponse { $profile->update(['state'=>null,'state_data'=>null]); $this->telegram->sendMessage($profile->telegram_chat_id,"✅ Reset. /menu"); return response()->json(['ok'=>true]); }
    private function cmdBatal(UserProfile $profile): \Illuminate\Http\JsonResponse { $profile->update(['state'=>null,'state_data'=>null]); $this->telegram->sendMessage($profile->telegram_chat_id,"❌ Batal. /menu"); return response()->json(['ok'=>true]); }
    private function cmdUnknown(UserProfile $profile): \Illuminate\Http\JsonResponse { $this->telegram->sendMessage($profile->telegram_chat_id,"❓ /help"); return response()->json(['ok'=>true]); }

    // === STATE HANDLERS ===
    private function handleState(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        if(in_array($text,['/batal','/cancel'])) return $this->cmdBatal($profile);
        if(str_starts_with($text,'/')){$profile->update(['state'=>null,'state_data'=>null]);return $this->handleCommand($profile,$text);}
        return match($profile->state){
            'waiting_food','waiting_food_time'=>$this->estimateAndConfirm($profile,$text),
            'waiting_weight'=>$this->stateWeight($profile,$text),
            'waiting_exercise'=>$this->parseAndLogExercise($profile,$text),
            'waiting_exercise_duration'=>$this->stateExDuration($profile,$text),
            'waiting_sleep'=>$this->parseSleep($profile,$text),
            'setup_umur'=>$this->stateSetupUmur($profile,$text),
            'setup_tinggi'=>$this->stateSetupTinggi($profile,$text),
            'setup_berat'=>$this->stateSetupBerat($profile,$text),
            'setup_target'=>$this->stateSetupTarget($profile,$text),
            'reminder_time'=>$this->stateReminderTime($profile,$text),
            'edit_food_kalori'=>$this->stateEditKalori($profile,$text),
            'confirm_food'=>$this->handleNaturalText($profile,$text),
            default=>$this->handleNaturalText($profile,$text),
        };
    }

    private function stateWeight(UserProfile $profile, string $t): \Illuminate\Http\JsonResponse { $b=(float)$t; if($b<20||$b>300){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ 20-300kg.");return response()->json(['ok'=>true]);} $profile->update(['state'=>null,'state_data'=>null]); return $this->logWeight($profile,$b); }
    private function stateExDuration(UserProfile $profile, string $t): \Illuminate\Http\JsonResponse { $d=(int)preg_replace('/[^0-9]/','',$t); if($d<=0||$d>480){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ 1-480min.");return response()->json(['ok'=>true]);} $data=$profile->state_data??[]; $profile->update(['state'=>null,'state_data'=>null]); return $this->saveExercise($profile,$data['jenis']??'Olahraga',$d); }
    private function stateSetupUmur(UserProfile $profile, string $t): \Illuminate\Http\JsonResponse { $u=(int)$t; if($u<10||$u>100){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ 10-100.");return response()->json(['ok'=>true]);} $d=$profile->state_data??[];$d['umur']=$u; $profile->update(['state'=>'setup_tinggi','state_data'=>$d]); $this->telegram->sendMessage($profile->telegram_chat_id,"✅ Umur: {$u}\n\n📏 Tinggi (cm)? <code>170</code>"); return response()->json(['ok'=>true]); }
    private function stateSetupTinggi(UserProfile $profile, string $t): \Illuminate\Http\JsonResponse { $v=(float)$t; if($v<100||$v>250){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ 100-250cm.");return response()->json(['ok'=>true]);} $d=$profile->state_data??[];$d['tinggi']=$v; $profile->update(['state'=>'setup_berat','state_data'=>$d]); $this->telegram->sendMessage($profile->telegram_chat_id,"✅ {$v}cm\n\n⚖️ Berat (kg)? <code>70.5</code>"); return response()->json(['ok'=>true]); }
    private function stateSetupBerat(UserProfile $profile, string $t): \Illuminate\Http\JsonResponse { $b=(float)$t; if($b<20||$b>300){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ 20-300kg.");return response()->json(['ok'=>true]);} $d=$profile->state_data??[];$d['berat']=$b; $profile->update(['state'=>'setup_target','state_data'=>$d]); $this->telegram->sendMessage($profile->telegram_chat_id,"✅ {$b}kg\n\n🎯 Target (kg)? <code>0</code> jika tidak ada."); return response()->json(['ok'=>true]); }
    private function stateSetupTarget(UserProfile $profile, string $t): \Illuminate\Http\JsonResponse { $v=(float)$t; $d=$profile->state_data??[]; $d['target']=$v>0?$v:null; $d['goal']=$v>0?($v<$d['berat']?'cutting':($v>$d['berat']?'bulking':'maintenance')):'maintenance'; $profile->update(['state'=>'setup_activity','state_data'=>$d]); $kb=[[['text'=>'🪑 Sedentary','callback_data'=>'setup_activity_sedentary']],[['text'=>'🚶 Light','callback_data'=>'setup_activity_light']],[['text'=>'🏃 Moderate','callback_data'=>'setup_activity_moderate']],[['text'=>'💪 Active','callback_data'=>'setup_activity_active']],[['text'=>'🔥 Very Active','callback_data'=>'setup_activity_very_active']]]; $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,"✅\n\n🏃 Aktivitas:",$kb); return response()->json(['ok'=>true]); }
    private function stateReminderTime(UserProfile $profile, string $t): \Illuminate\Http\JsonResponse { if(!preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/',$t)){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ HH:MM");return response()->json(['ok'=>true]);} $d=$profile->state_data??[];$tipe=$d['tipe']??'custom'; $msgs=['minum'=>['💧 Minum!','Target '.$profile->getAirTarget().'ml'],'makan'=>['🍽 Makan!','Pilih sehat.'],'olahraga'=>['🏃 Olahraga!','30 menit.'],'tidur'=>['😴 Tidur!','7-8 jam.']]; $m=$msgs[$tipe]??['Reminder','!']; $profile->reminders()->create(['tipe'=>$tipe,'judul'=>$m[0],'pesan'=>$m[1],'waktu'=>$t.':00','hari_aktif'=>[1,2,3,4,5,6,7],'aktif'=>true]); $profile->update(['state'=>null,'state_data'=>null]); $this->telegram->sendMessage($profile->telegram_chat_id,"✅ {$m[0]} jam {$t}"); return response()->json(['ok'=>true]); }
    private function stateEditKalori(UserProfile $profile, string $t): \Illuminate\Http\JsonResponse { $k=(int)$t; if($k<=0||$k>5000){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ 1-5000.");return response()->json(['ok'=>true]);} $d=$profile->state_data??[];$d['pending_food']['kalori']=$k; $profile->update(['state'=>'confirm_food','state_data'=>$d]); $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,"✅ Kalori: <b>{$k}</b>\n\nSimpan?",[[['text'=>'✅ Simpan','callback_data'=>'food_confirm'],['text'=>'❌ Batal','callback_data'=>'food_cancel']]]); return response()->json(['ok'=>true]); }

    // === FOOD ESTIMATION + CONFIRMATION ===
    private function estimateAndConfirm(UserProfile $profile, string $foodText): \Illuminate\Http\JsonResponse
    {
        $this->telegram->sendChatAction($profile->telegram_chat_id,'typing');
        if(!$profile->canUseAi()){$db=FoodDatabase::search($foodText,1)->first();if($db)return $this->showConfirm($profile,['nama'=>$db->nama,'kalori'=>$db->kalori,'protein'=>$db->protein,'karbohidrat'=>$db->karbohidrat,'lemak'=>$db->lemak,'porsi'=>1,'satuan_porsi'=>$db->satuan_porsi],'database');$this->telegram->sendMessage($profile->telegram_chat_id,"⚠️ AI limit & tidak di DB.");return response()->json(['ok'=>true]);}
        $result=$this->ai->estimateFromText($foodText,$profile->id);
        if($result['source']!=='database') $profile->incrementAiUsage();
        if(!$result['success']){$db=FoodDatabase::search($foodText,1)->first();if($db)return $this->showConfirm($profile,['nama'=>$db->nama,'kalori'=>$db->kalori,'protein'=>$db->protein,'karbohidrat'=>$db->karbohidrat,'lemak'=>$db->lemak,'porsi'=>1,'satuan_porsi'=>$db->satuan_porsi],'database');$this->telegram->sendMessage($profile->telegram_chat_id,"❌ Gagal: <b>{$foodText}</b>\nCoba spesifik/foto.");return response()->json(['ok'=>true]);}
        return $this->showConfirm($profile,$result['data'],$result['source']);
    }

    private function showConfirm(UserProfile $profile, array $data, string $source): \Illuminate\Http\JsonResponse
    {
        $nama = $data['nama'] ?? 'Makanan';
        $kal = (int) ($data['kalori'] ?? 0);
        $p = round((float) ($data['protein'] ?? 0), 1);
        $k = round((float) ($data['karbohidrat'] ?? 0), 1);
        $l = round((float) ($data['lemak'] ?? 0), 1);
        $porsi = (float) ($data['porsi'] ?? 1);
        $sat = $data['satuan_porsi'] ?? 'porsi';
        $peringatan = $data['peringatan'] ?? null;

        // Handle multi-food display
        $isMulti = isset($data['items']) && is_array($data['items']);
        $icon = $source === 'database' ? '📚' : ($source === 'multi' ? '📋' : '🤖');

        $text = "{$icon} <b>Estimasi Nutrisi</b>\n━━━━━━━━━━━━━━━\n\n";

        if ($isMulti) {
            foreach ($data['items'] as $item) {
                $srcIcon = ($item['source'] ?? '') === 'database' ? '📚' : '🤖';
                $text .= "{$srcIcon} <b>{$item['nama']}</b> — {$item['kalori']} kkal\n";
            }
            $text .= "\n📊 <b>Total: {$kal} kkal</b>\n";
            $text .= "🥩 P:{$p}g | 🍚 K:{$k}g | 🧈 L:{$l}g\n";
        } else {
            $text .= "🍽 <b>{$nama}</b> ({$porsi} {$sat})\n\n";
            $text .= "🔥 Kalori: <b>{$kal}</b> kkal\n";
            $text .= "🥩 P: {$p}g | 🍚 K: {$k}g | 🧈 L: {$l}g\n";
        }

        $text .= "\n<i>* Estimasi batas atas (porsi besar)</i>\n";

        // Peringatan diet dari AI
        if ($peringatan) {
            $text .= "\n⚠️ {$peringatan}\n";
        }

        // Warning kalori tinggi
        if ($kal > 500) {
            $text .= "\n🚨 Kalori tinggi! Pertimbangkan porsi lebih kecil.\n";
        }

        $text .= "\nSimpan?";

        $sd = $profile->state_data ?? [];
        $pendingData = [
            'nama' => $nama, 'kalori' => $kal, 'protein' => $p,
            'karbohidrat' => $k, 'lemak' => $l, 'porsi' => $porsi,
            'satuan_porsi' => $sat, 'sumber' => $source === 'database' ? 'database' : 'manual',
            'peringatan' => $peringatan,
        ];
        if ($isMulti) $pendingData['items'] = $data['items'];

        $sd['pending_food'] = $pendingData;
        $profile->update(['state' => 'confirm_food', 'state_data' => $sd]);

        $kb = [
            [['text' => '✅ Simpan', 'callback_data' => 'food_confirm'], ['text' => '❌ Batal', 'callback_data' => 'food_cancel']],
            [['text' => '✏️ Edit Kalori', 'callback_data' => 'food_edit_kalori'], ['text' => '⭐ +Favorit', 'callback_data' => 'food_confirm_fav']],
        ];
        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $kb);
        return response()->json(['ok' => true]);
    }

    // === PHOTO ===
    private function handlePhoto(UserProfile $profile, array $message): \Illuminate\Http\JsonResponse
    {
        // Caption pada foto = langsung estimasi sebagai nama makanan
        $caption = $message['caption'] ?? '';
        if (!empty($caption)) {
            return $this->estimateAndConfirm($profile, $caption);
        }

        // Tanpa caption: minta user ketik nama makanan
        $profile->update(['state' => 'waiting_food', 'state_data' => ['waktu_makan' => $this->detectMealTime()]]);

        $text = "📸 <b>Foto diterima!</b>\n\n";
        $text .= "Saat ini analisis foto otomatis belum tersedia.\n\n";
        $text .= "💡 <b>Ketik nama makanan</b> yang ada di foto:\n";
        $text .= "Contoh:\n";
        $text .= "• <code>nasi goreng + ayam geprek</code>\n";
        $text .= "• <code>bakso 1 mangkuk</code>\n";
        $text .= "• <code>indomie goreng + telur</code>\n\n";
        $text .= "AI akan otomatis hitung kalori & nutrisinya!";

        $keyboard = [
            [['text' => '⭐ Dari Favorit', 'callback_data' => 'quick_add_menu']],
            [['text' => '❌ Batal', 'callback_data' => 'food_cancel']],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    // === NATURAL TEXT ===
    private function handleNaturalText(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $text=trim($text); if(empty($text)) return response()->json(['ok'=>true]);
        $kws=['nasi','mie','ayam','ikan','sayur','buah','roti','telur','tahu','tempe','bakso','soto','goreng','bakar','rebus','kukus','kopi','teh','susu','jus','es ','rendang','sate','gado','pecel','rawon','gudeg','pizza','burger','indomie','porsi','mangkuk','potong','gelas','piring','bungkus','martabak','siomay','batagor','pempek','donat','oatmeal','granola','whey','salad','kentang','udang','cumi','sop','opor','gulai','tongseng','bubur','lontong','ketoprak','nugget','sosis'];
        $isFood=false; $lower=strtolower($text);
        foreach($kws as $k){if(str_contains($lower,$k)){$isFood=true;break;}}
        if(!$isFood) $isFood=FoodDatabase::where('nama','like',"%{$text}%")->exists();
        if($isFood) return $this->estimateAndConfirm($profile,$text);
        $this->telegram->sendMessage($profile->telegram_chat_id,"🤔 Tidak dikenali.\n\nJika makanan: <code>/makan {$text}</code>\n/menu");
        return response()->json(['ok' => true]);
    }

    // === CALLBACK HANDLER ===
    private function handleCallback(array $callback): \Illuminate\Http\JsonResponse
    {
        $chatId=(string)$callback['message']['chat']['id']; $cbId=$callback['id']; $data=$callback['data'];
        $profile=UserProfile::findByChatId($chatId);
        if(!$profile){$this->telegram->answerCallback($cbId,'Error');return response()->json(['ok'=>true]);}
        $this->telegram->answerCallback($cbId);
        return match(true){
            $data==='dashboard'=>$this->cmdDashboard($profile), $data==='menu'=>$this->cmdMenu($profile),
            $data==='help'=>$this->cmdHelp($profile), $data==='stats'=>$this->cmdStats($profile),
            $data==='badges'=>$this->cmdBadge($profile), $data==='profile'=>$this->cmdProfil($profile),
            $data==='target'=>$this->cmdTarget($profile), $data==='history'=>$this->cmdRiwayat($profile),
            $data==='recommend'=>$this->cmdRekomendasi($profile), $data==='favorites'=>$this->cmdFavorit($profile),
            $data==='log_food'=>$this->cmdMakan($profile,'','/makan'), $data==='log_water'=>$this->cmdAir($profile,''),
            $data==='log_weight'=>$this->cmdBerat($profile,''), $data==='log_exercise'=>$this->cmdOlahraga($profile,''),
            $data==='food_confirm'=>$this->confirmFood($profile,false), $data==='food_confirm_fav'=>$this->confirmFood($profile,true),
            $data==='food_cancel'=>$this->cancelFood($profile), $data==='food_edit_kalori'=>$this->editFoodKalori($profile),
            str_starts_with($data,'water_')=>$this->logWater($profile,(int)str_replace('water_','',$data)),
            str_starts_with($data,'meal_')=>$this->cbMealTime($profile,$data),
            $data==='quick_add_menu'=>$this->cmdFavorit($profile),
            str_starts_with($data,'quickadd_')=>$this->quickAdd($profile,$data),
            str_starts_with($data,'exercise_')=>$this->cbExercise($profile,$data),
            str_starts_with($data,'setup_gender_')=>$this->cbSetupGender($profile,$data),
            str_starts_with($data,'setup_activity_')=>$this->cbSetupActivity($profile,$data),
            $data==='edit_profile'=>$this->cmdSetup($profile), $data==='recalculate'=>$this->cbRecalc($profile),
            $data==='fasting_menu'=>$this->cmdPuasa($profile,''),
            str_starts_with($data,'fasting_start_')=>$this->startFasting($profile,str_replace('fasting_start_','',$data)),
            $data==='fasting_stop'=>$this->stopFasting($profile,$profile->getActiveFasting()),
            $data==='sleep_menu'=>$this->cmdTidur($profile,''),
            $data==='reminder_menu'=>$this->cmdReminder($profile),
            str_starts_with($data,'reminder_')=>$this->cbReminder($profile,$data),
            str_starts_with($data,'history_prev_')=>$this->histNav($profile,$data,-1),
            str_starts_with($data,'history_next_')=>$this->histNav($profile,$data,1),
            $data==='delete_last_food'=>$this->cmdHapus($profile,'terakhir'),
            $data==='delete_select'=>$this->showDelMenu($profile),
            str_starts_with($data,'del_food_')=>$this->delSpecific($profile,$data),
            $data==='analyze_weekly'=>$this->cbAnalyze($profile),
            $data==='timeline'=>$this->cmdTimeline($profile),
            $data==='motivasi'=>$this->cmdMotivasi($profile),
            $data==='smart_reminder'=>$this->cmdSmartReminder($profile),
            str_starts_with($data,'setreminder_')=>$this->cbSetSmartReminder($profile,$data),
            default=>response()->json(['ok'=>true]),
        };
    }

    // === CALLBACK IMPLEMENTATIONS ===
    private function confirmFood(UserProfile $profile, bool $fav): \Illuminate\Http\JsonResponse
    {
        $sd = $profile->state_data ?? [];
        $p = $sd['pending_food'] ?? null;
        if (!$p) { $this->telegram->sendMessage($profile->telegram_chat_id, "❌ No data."); return response()->json(['ok' => true]); }

        $today = now('Asia/Singapore')->toDateString();
        $waktu = $sd['waktu_makan'] ?? $this->detectMealTime();

        // Handle multi-food (items array from multi-input)
        if (isset($p['items']) && is_array($p['items'])) {
            $text = "✅ <b>Tercatat!</b>\n\n";
            foreach ($p['items'] as $item) {
                FoodLog::create([
                    'profile_id' => $profile->id, 'tanggal' => $today, 'waktu_makan' => $waktu,
                    'nama_makanan' => $item['nama'] ?? '?', 'porsi' => $item['porsi'] ?? 1,
                    'satuan_porsi' => $item['satuan_porsi'] ?? 'porsi',
                    'kalori' => $item['kalori'] ?? 0, 'protein' => $item['protein'] ?? 0,
                    'karbohidrat' => $item['karbohidrat'] ?? 0, 'lemak' => $item['lemak'] ?? 0,
                    'sumber' => $item['source'] ?? 'manual',
                ]);
                $this->autoFavorite($profile, $item);
                $text .= "• {$item['nama']} ({$item['kalori']} kkal)\n";
            }
            $text .= "\n📊 Total: <b>{$p['kalori']} kkal</b>\n";
        } else {
            // Single food
            FoodLog::create([
                'profile_id' => $profile->id, 'tanggal' => $today, 'waktu_makan' => $waktu,
                'nama_makanan' => $p['nama'], 'porsi' => $p['porsi'], 'satuan_porsi' => $p['satuan_porsi'],
                'kalori' => $p['kalori'], 'protein' => $p['protein'],
                'karbohidrat' => $p['karbohidrat'], 'lemak' => $p['lemak'],
                'sumber' => $p['sumber'] ?? 'manual',
            ]);
            $this->autoFavorite($profile, $p);
            if ($fav) FoodFavorite::updateOrCreate(
                ['profile_id' => $profile->id, 'nama_makanan' => $p['nama']],
                ['kalori' => $p['kalori'], 'protein' => $p['protein'], 'karbohidrat' => $p['karbohidrat'],
                 'lemak' => $p['lemak'], 'porsi' => $p['porsi'], 'satuan_porsi' => $p['satuan_porsi'], 'waktu_makan' => $waktu]
            );
            $text = "✅ <b>{$p['nama']}</b> ({$p['kalori']} kkal)\n";
        }

        $profile->update(['state' => null, 'state_data' => null]);
        $badges = $this->updateStreak($profile);
        $sum = DailySummary::recalculate($profile->id, $today);

        $tK = $profile->kalori_target ?: 2000;
        $sisa = $tK - $sum->total_kalori + $sum->total_kalori_terbakar;
        $pct = min(100, round(($sum->total_kalori / $tK) * 100));

        $text .= $this->getMealLabel($waktu) . "\n\n";
        $text .= "📊 {$sum->total_kalori}/{$tK} ({$pct}%) " . $this->progressBar($pct, 10) . "\n";
        $text .= "Sisa: <b>{$sisa} kkal</b>\n";
        if ($fav) $text .= "⭐ +Favorit\n";

        // Smart warnings berdasarkan kondisi
        if ($sisa < 0) {
            $over = abs($sisa);
            $text .= "\n🚨 <b>OVER {$over} kkal!</b>\n";
            if ($over > 500) $text .= "Sangat berlebihan! Pertimbangkan olahraga atau skip snack.\n";
            elseif ($over > 200) $text .= "Cukup over. Hindari makan lagi hari ini.\n";
            else $text .= "Sedikit over. Minum air putih saja.\n";
        } elseif ($sisa < 200 && $sisa >= 0) {
            $text .= "\n⚠️ Sisa tinggal sedikit. Cukup untuk hari ini!\n";
        } elseif ($pct >= 70 && $pct < 85) {
            $text .= "\n✅ Bagus, masih dalam jalur.\n";
        }

        // Post-meal tips berdasarkan kalori makanan yang baru dicatat
        $kaloriMakan = isset($p['items']) ? $p['kalori'] : ($p['kalori'] ?? 0);
        if ($kaloriMakan > 600) {
            $text .= "💡 Makanan ini tinggi kalori. Jalan kaki 30 menit bisa bantu bakar ~100 kkal.\n";
        } elseif ($kaloriMakan > 400) {
            $text .= "💡 Porsi cukup besar. Minum air putih untuk bantu kenyang.\n";
        }

        // Peringatan dari AI jika ada
        if (isset($p['peringatan']) && $p['peringatan']) {
            $text .= "⚠️ {$p['peringatan']}\n";
        }

        if (!empty($badges)) { $text .= "\n🎉 "; foreach ($badges as $b) $text .= $b->badge_icon . " "; $text .= "\n"; }

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text,
            [[['text' => '🍽 +Lagi', 'callback_data' => 'log_food'], ['text' => '📊 Dashboard', 'callback_data' => 'dashboard']]]);
        return response()->json(['ok' => true]);
    }

    private function cancelFood(UserProfile $profile): \Illuminate\Http\JsonResponse { $profile->update(['state'=>null,'state_data'=>null]); $this->telegram->sendMessage($profile->telegram_chat_id,"❌ Batal."); return response()->json(['ok'=>true]); }
    private function editFoodKalori(UserProfile $profile): \Illuminate\Http\JsonResponse { $sd=$profile->state_data??[]; $kal=$sd['pending_food']['kalori']??0; $profile->update(['state'=>'edit_food_kalori']); $this->telegram->sendMessage($profile->telegram_chat_id,"✏️ Sekarang: {$kal}\nKetik kalori baru:"); return response()->json(['ok'=>true]); }
    private function cbMealTime(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse { $map=['meal_sarapan'=>'sarapan','meal_siang'=>'makan_siang','meal_malam'=>'makan_malam','meal_snack'=>'snack']; $w=$map[$data]??'snack'; $sd=$profile->state_data??[];$sd['waktu_makan']=$w; $profile->update(['state'=>'waiting_food','state_data'=>$sd]); $this->telegram->sendMessage($profile->telegram_chat_id,"✅ ".$this->getMealLabel($w)."\n\nKetik makanan/foto:"); return response()->json(['ok'=>true]); }
    private function quickAdd(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse { $id=(int)str_replace('quickadd_','',$data); $f=FoodFavorite::where('profile_id',$profile->id)->where('id',$id)->first(); if(!$f){$this->telegram->sendMessage($profile->telegram_chat_id,"❌");return response()->json(['ok'=>true]);} $today=now('Asia/Singapore')->toDateString(); FoodLog::create(['profile_id'=>$profile->id,'tanggal'=>$today,'waktu_makan'=>$this->detectMealTime(),'nama_makanan'=>$f->nama_makanan,'porsi'=>$f->porsi,'satuan_porsi'=>$f->satuan_porsi,'kalori'=>$f->kalori,'protein'=>$f->protein,'karbohidrat'=>$f->karbohidrat,'lemak'=>$f->lemak,'sumber'=>'database']); $f->incrementUse(); $this->updateStreak($profile); DailySummary::recalculate($profile->id,$today); $this->telegram->sendMessage($profile->telegram_chat_id,"⭐ <b>{$f->nama_makanan}</b> ({$f->kalori} kkal) ✅"); return response()->json(['ok'=>true]); }
    private function cbExercise(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse { $j=str_replace('exercise_','',$data); $map=['lari'=>'Lari','jalan'=>'Jalan Kaki','gym'=>'Gym','renang'=>'Renang','sepeda'=>'Bersepeda','yoga'=>'Yoga','hiit'=>'HIIT','badminton'=>'Badminton','futsal'=>'Futsal']; $profile->update(['state'=>'waiting_exercise_duration','state_data'=>['jenis'=>$map[$j]??ucfirst($j)]]); $this->telegram->sendMessage($profile->telegram_chat_id,"🏃 <b>".($map[$j]??$j)."</b>\nMenit? <code>30</code>"); return response()->json(['ok'=>true]); }
    private function cbSetupGender(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse { $g=str_replace('setup_gender_','',$data); $d=$profile->state_data??[];$d['gender']=$g; $profile->update(['state'=>'setup_umur','state_data'=>$d]); $this->telegram->sendMessage($profile->telegram_chat_id,"✅ ".ucfirst($g)."\n\n🎂 Umur? <code>25</code>"); return response()->json(['ok'=>true]); }
    private function cbSetupActivity(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse
    {
        $act=str_replace('setup_activity_','',$data); $sd=$profile->state_data??[];
        $profile->update(['gender'=>$sd['gender']??$profile->gender,'umur'=>$sd['umur']??$profile->umur,'tinggi_cm'=>$sd['tinggi']??$profile->tinggi_cm,'berat_kg'=>$sd['berat']??$profile->berat_kg,'berat_target'=>$sd['target']??$profile->berat_target,'goal'=>$sd['goal']??$profile->goal,'level_aktivitas'=>$act,'state'=>null,'state_data'=>null]);
        $profile->refresh(); $profile->recalculate(); $profile->update(['air_target_ml'=>(int)round($profile->berat_kg*33)]);
        $text="🎉 <b>Setup!</b>\n\nBMI: {$profile->bmi} (".$this->getBmiCategory($profile->bmi).")\nTarget: <b>{$profile->kalori_target}</b> kkal\nP:{$profile->protein_target}g K:{$profile->karbo_target}g L:{$profile->lemak_target}g\n💧 ".$profile->getAirTarget()."ml | ".ucfirst($profile->goal)."\n\n/menu untuk mulai!";
        Streak::firstOrCreate(['profile_id'=>$profile->id]); Badge::firstOrCreate(['profile_id'=>$profile->id,'badge_code'=>'first_setup'],['badge_name'=>'Langkah Pertama!','badge_icon'=>'🎯','deskripsi'=>'Setup profil','earned_at'=>now()]);
        $this->telegram->sendMessage($profile->telegram_chat_id,$text);
        return response()->json(['ok' => true]);
    }

    private function cbRecalc(UserProfile $profile): \Illuminate\Http\JsonResponse { $profile->recalculate(); $profile->update(['air_target_ml'=>(int)round($profile->berat_kg*33)]); $this->telegram->sendMessage($profile->telegram_chat_id,"✅ Target: {$profile->kalori_target} kkal | 💧 ".$profile->getAirTarget()."ml"); return response()->json(['ok'=>true]); }
    private function cbReminder(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse { $act=str_replace('reminder_','',$data); if($act==='list'){$rs=$profile->reminders()->where('aktif',true)->get();$t="⏰ <b>Aktif:</b>\n\n";if($rs->isEmpty())$t.="Kosong.\n";else foreach($rs as $r)$t.="✅ {$r->judul} {$r->waktu}\n";$this->telegram->sendMessage($profile->telegram_chat_id,$t);return response()->json(['ok'=>true]);} if($act==='clear'){$profile->reminders()->delete();$this->telegram->sendMessage($profile->telegram_chat_id,"🗑 Done.");return response()->json(['ok'=>true]);} $map=['water'=>'minum','meal'=>'makan','exercise'=>'olahraga','sleep'=>'tidur']; $profile->update(['state'=>'reminder_time','state_data'=>['tipe'=>$map[$act]??'custom']]); $this->telegram->sendMessage($profile->telegram_chat_id,"⏰ Jam? <code>08:00</code>"); return response()->json(['ok'=>true]); }
    private function startFasting(UserProfile $profile, string $tipe): \Illuminate\Http\JsonResponse { $now=now('Asia/Singapore'); FastingLog::create(['profile_id'=>$profile->id,'tanggal'=>$now->toDateString(),'tipe'=>$tipe,'mulai_puasa'=>$now,'completed'=>false]); $profile->update(['fasting_active'=>true,'fasting_type'=>$tipe]); $h=match($tipe){'16_8'=>16,'18_6'=>18,'20_4'=>20,'omad'=>23,default=>16}; $this->telegram->sendMessage($profile->telegram_chat_id,"🕐 <b>Puasa!</b>\n\n{$tipe} ({$h}j)\nMulai: {$now->format('H:i')}\nBuka: <b>".$now->copy()->addHours($h)->format('H:i')."</b>\n\n/puasa untuk cek 💪"); return response()->json(['ok'=>true]); }
    private function stopFasting(UserProfile $profile, ?FastingLog $f): \Illuminate\Http\JsonResponse { if(!$f){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ Tidak aktif.");return response()->json(['ok'=>true]);} $now=now('Asia/Singapore');$dur=$f->getElapsedMinutes(); $f->update(['buka_puasa'=>$now,'durasi_menit'=>$dur,'completed'=>true]); $profile->update(['fasting_active'=>false]); $ok=$dur>=($f->getTargetHours()*60); $text="🍽 <b>Selesai!</b>\n\n".intdiv($dur,60)."j".($dur%60)."m / {$f->getTargetHours()}j\n\n"; if($ok){$text.="🎉 Target!";Badge::firstOrCreate(['profile_id'=>$profile->id,'badge_code'=>'fasting_complete'],['badge_name'=>'Fasting Warrior!','badge_icon'=>'🕐','deskripsi'=>'Target puasa','earned_at'=>now()]);}else $text.="Belum target, tapi bagus! 💪"; $this->telegram->sendMessage($profile->telegram_chat_id,$text); return response()->json(['ok'=>true]); }
    private function histNav(UserProfile $profile, string $data, int $dir): \Illuminate\Http\JsonResponse { $pfx=$dir===-1?'history_prev_':'history_next_'; $date=\Carbon\Carbon::parse(str_replace($pfx,'',$data))->addDays($dir)->toDateString(); if($date>now('Asia/Singapore')->toDateString()) return $this->cmdRiwayat($profile,''); return $this->cmdRiwayat($profile,$date); }
    private function showDelMenu(UserProfile $profile): \Illuminate\Http\JsonResponse { $logs=FoodLog::where('profile_id',$profile->id)->whereDate('tanggal',now('Asia/Singapore')->toDateString())->orderByDesc('created_at')->limit(5)->get(); if($logs->isEmpty()){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ Kosong.");return response()->json(['ok'=>true]);} $text="🗑 <b>Hapus:</b>\n\n";$kb=[]; foreach($logs as $l){$text.="#{$l->id} {$l->nama_makanan} ({$l->kalori})\n";$kb[]=[['text'=>"🗑 #{$l->id} {$l->nama_makanan}",'callback_data'=>"del_food_{$l->id}"]];} $kb[]=[['text'=>'◀️','callback_data'=>'history']]; $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,$text,$kb); return response()->json(['ok'=>true]); }
    private function delSpecific(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse { $id=(int)str_replace('del_food_','',$data); $log=FoodLog::where('profile_id',$profile->id)->where('id',$id)->first(); if($log){$n=$log->nama_makanan;$log->delete();DailySummary::recalculate($profile->id,now('Asia/Singapore')->toDateString());$this->telegram->sendMessage($profile->telegram_chat_id,"🗑 {$n}");}else $this->telegram->sendMessage($profile->telegram_chat_id,"❌"); return response()->json(['ok'=>true]); }
    private function cbAnalyze(UserProfile $profile): \Illuminate\Http\JsonResponse { if(!$profile->canUseAi()){$this->telegram->sendMessage($profile->telegram_chat_id,"⚠️ Limit.");return response()->json(['ok'=>true]);} $this->telegram->sendChatAction($profile->telegram_chat_id,'typing'); $sums=DailySummary::where('profile_id',$profile->id)->where('tanggal','>=',now('Asia/Singapore')->startOfWeek()->toDateString())->get(); if($sums->isEmpty()){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ No data.");return response()->json(['ok'=>true]);} $wd=['target_kalori'=>$profile->kalori_target,'goal'=>$profile->goal,'days'=>$sums->map(fn($s)=>['tanggal'=>$s->tanggal->format('d/m'),'kalori'=>$s->total_kalori,'protein'=>$s->total_protein,'air_ml'=>$s->total_air_ml,'exercise_menit'=>$s->total_exercise_menit])->toArray()]; $r=$this->ai->analyzeWeeklyPattern($wd,$profile->id); $profile->incrementAiUsage(); if(!$r['success']){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ AI error.");return response()->json(['ok'=>true]);} $d=$r['data']; $text="🧠 <b>Analisis AI</b>\n━━━━━━━━━━━━━━━\n\n"; if(isset($d['analisis']))$text.="{$d['analisis']}\n\n"; if(isset($d['skor_kesehatan']))$text.="📊 Skor: <b>{$d['skor_kesehatan']}/10</b>\n\n"; if(!empty($d['kelebihan'])){$text.="✅ ";foreach($d['kelebihan'] as $x)$text.="{$x}. ";$text.="\n\n";} if(!empty($d['kekurangan'])){$text.="⚠️ ";foreach($d['kekurangan'] as $x)$text.="{$x}. ";$text.="\n\n";} if(!empty($d['saran'])){$text.="💡 ";foreach($d['saran'] as $x)$text.="{$x}. ";} $this->telegram->sendMessage($profile->telegram_chat_id,$text); return response()->json(['ok'=>true]); }

    // === LOGGING HELPERS ===
    private function cbSetSmartReminder(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse
    {
        $sd = $profile->state_data ?? [];
        $suggested = $sd['suggested_reminders'] ?? [];

        if ($data === 'setreminder_all') {
            // Set semua reminder yang disarankan
            $count = 0;
            foreach ($suggested as $r) {
                $waktu = $r['waktu'] ?? '08:00';
                $profile->reminders()->create([
                    'tipe' => $r['tipe'] ?? 'custom',
                    'judul' => $r['judul'] ?? 'Reminder',
                    'pesan' => $r['pesan'] ?? 'Jangan lupa!',
                    'waktu' => $waktu . ':00',
                    'hari_aktif' => [1,2,3,4,5,6,7],
                    'aktif' => true,
                ]);
                $count++;
            }
            $profile->update(['state_data' => null]);
            $this->telegram->sendMessage($profile->telegram_chat_id, "✅ {$count} reminder berhasil diset!");
            return response()->json(['ok' => true]);
        }

        // Set single: setreminder_minum_08:00
        $parts = explode('_', str_replace('setreminder_', '', $data), 2);
        $tipe = $parts[0] ?? 'custom';
        $waktu = $parts[1] ?? '08:00';

        // Cari dari suggested
        $matched = null;
        foreach ($suggested as $r) {
            if (($r['tipe'] ?? '') === $tipe && ($r['waktu'] ?? '') === $waktu) {
                $matched = $r;
                break;
            }
        }

        $profile->reminders()->create([
            'tipe' => $tipe,
            'judul' => $matched['judul'] ?? ucfirst($tipe),
            'pesan' => $matched['pesan'] ?? 'Jangan lupa!',
            'waktu' => $waktu . ':00',
            'hari_aktif' => [1,2,3,4,5,6,7],
            'aktif' => true,
        ]);

        $this->telegram->sendMessage($profile->telegram_chat_id, "✅ Reminder {$waktu} diset!");
        return response()->json(['ok' => true]);
    }

    // === LOGGING HELPERS ===
    private function logWater(UserProfile $profile, int $ml): \Illuminate\Http\JsonResponse { $today=now('Asia/Singapore')->toDateString(); WaterLog::create(['profile_id'=>$profile->id,'tanggal'=>$today,'jumlah_ml'=>$ml,'waktu'=>now('Asia/Singapore')->format('H:i:s')]); $tot=WaterLog::where('profile_id',$profile->id)->whereDate('tanggal',$today)->sum('jumlah_ml'); $tar=$profile->getAirTarget();$pct=min(100,round(($tot/$tar)*100)); $text="💧 +{$ml}ml\n\n{$tot}/{$tar}ml ({$pct}%)\n".$this->progressBar($pct); if($pct>=100){$text.="\n🎉 Target!";Badge::firstOrCreate(['profile_id'=>$profile->id,'badge_code'=>'water_champion'],['badge_name'=>'Water Champion!','badge_icon'=>'💧','deskripsi'=>'Target air','earned_at'=>now()]);} DailySummary::recalculate($profile->id,$today); $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,$text,[[['text'=>'💧 +250','callback_data'=>'water_250'],['text'=>'💧 +500','callback_data'=>'water_500']]]); return response()->json(['ok'=>true]); }

    private function logWeight(UserProfile $profile, float $berat): \Illuminate\Http\JsonResponse
    {
        if($berat<20||$berat>300){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ 20-300kg.");return response()->json(['ok'=>true]);}
        $today=now('Asia/Singapore')->toDateString(); $last=WeightLog::where('profile_id',$profile->id)->orderByDesc('tanggal')->first();
        $bmi=$profile->tinggi_cm?round($berat/(($profile->tinggi_cm/100)**2),1):null;
        WeightLog::updateOrCreate(['profile_id'=>$profile->id,'tanggal'=>$today],['berat_kg'=>$berat,'bmi'=>$bmi]);
        $profile->update(['berat_kg'=>$berat,'bmi'=>$bmi,'state'=>null,'state_data'=>null]); $profile->recalculate();
        $text="⚖️ <b>{$berat}kg</b> ✅\n"; if($bmi) $text.="BMI: {$bmi} (".$this->getBmiCategory($bmi).")\n";
        if($last){$diff=round($berat-$last->berat_kg,1);$text.=($diff>0?"📈 +":"📉 ")."{$diff}kg\n";}
        if($profile->berat_target){$rem=round($berat-$profile->berat_target,1);if(abs($rem)<0.5){$text.="🎉 TARGET!";Badge::firstOrCreate(['profile_id'=>$profile->id,'badge_code'=>'weight_goal'],['badge_name'=>'Goal!','badge_icon'=>'🎯','deskripsi'=>'Target berat','earned_at'=>now()]);}elseif($profile->goal==='cutting'&&$rem>0)$text.="🎯 Sisa: {$rem}kg";}
        $this->telegram->sendMessage($profile->telegram_chat_id,$text);
        return response()->json(['ok' => true]);
    }

    private function parseAndLogExercise(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse { preg_match('/(.+?)\s+(\d+)\s*(menit|min|m)?/i',$text,$m); if(empty($m)){$profile->update(['state'=>'waiting_exercise_duration','state_data'=>['jenis'=>$text]]);$this->telegram->sendMessage($profile->telegram_chat_id,"🏃 <b>{$text}</b>\nMenit?");return response()->json(['ok'=>true]);} $profile->update(['state'=>null,'state_data'=>null]); return $this->saveExercise($profile,trim($m[1]),(int)$m[2]); }

    private function saveExercise(UserProfile $profile, string $jenis, int $durasi): \Illuminate\Http\JsonResponse
    {
        // MET values CONSERVATIVE (lower bound) - agar tidak cepat puas
        // Nilai diambil dari batas bawah range MET Compendium
        $met = match(strtolower($jenis)){
            'lari','jogging','running' => 7.0,      // normal 8-10, kita pakai 7
            'jalan','jalan kaki','walking' => 2.8,   // normal 3-4, kita pakai 2.8
            'gym','angkat beban' => 3.5,             // normal 4-6, kita pakai 3.5
            'renang','swimming' => 5.0,              // normal 6-8, kita pakai 5
            'sepeda','bersepeda','cycling' => 5.5,   // normal 6-8, kita pakai 5.5
            'yoga','stretching' => 2.0,              // normal 2.5-3, kita pakai 2
            'hiit','crossfit' => 8.0,                // normal 10-12, kita pakai 8
            'badminton','tenis' => 5.0,              // normal 6-7, kita pakai 5
            'futsal','sepak bola' => 6.0,            // normal 7-9, kita pakai 6
            'basket' => 5.5,                         // normal 6-8, kita pakai 5.5
            default => 3.5,                          // conservative default
        };

        // Kalori = MET * berat * jam, lalu potong 15% (conservative)
        $kaloriRaw = $met * ($profile->berat_kg ?? 65) * ($durasi / 60);
        $kalori = (int) round($kaloriRaw * 0.85); // -15% safety margin

        $intensitas = $met >= 7 ? 'berat' : ($met >= 4 ? 'sedang' : 'ringan');
        $today = now('Asia/Singapore')->toDateString();

        ExerciseLog::create([
            'profile_id' => $profile->id, 'tanggal' => $today,
            'jenis_olahraga' => ucfirst($jenis), 'durasi_menit' => $durasi,
            'kalori_terbakar' => $kalori, 'intensitas' => $intensitas,
        ]);

        $this->updateStreak($profile);
        DailySummary::recalculate($profile->id, $today);

        $text = "🏃 <b>" . ucfirst($jenis) . "</b> {$durasi} menit\n";
        $text .= "🔥 ~{$kalori} kkal terbakar\n\n";
        $text .= "💡 <i>Estimasi konservatif - kalori aktual bisa lebih tinggi.</i>\n";
        $text .= "💪 Jangan berhenti di sini, push lebih!";

        if (ExerciseLog::where('profile_id', $profile->id)->count() === 1) {
            Badge::firstOrCreate(
                ['profile_id' => $profile->id, 'badge_code' => 'first_exercise'],
                ['badge_name' => 'First Workout!', 'badge_icon' => '💪', 'deskripsi' => 'Olahraga pertama', 'earned_at' => now()]
            );
            $text .= "\n\n🎉 Badge: 💪 First Workout!";
        }

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function parseSleep(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        if(!preg_match('/(\d{1,2}:\d{2})\s+(\d{1,2}:\d{2})/',$text,$m)){$this->telegram->sendMessage($profile->telegram_chat_id,"❌ Format: <code>23:00 06:30</code>");return response()->json(['ok'=>true]);}
        $durasi=SleepLog::calculateDuration($m[1],$m[2]);
        $today=now('Asia/Singapore')->toDateString();
        SleepLog::updateOrCreate(['profile_id'=>$profile->id,'tanggal'=>$today],['jam_tidur'=>$m[1],'jam_bangun'=>$m[2],'durasi_jam'=>$durasi]);
        $profile->update(['state'=>null,'state_data'=>null]);
        $emoji=$durasi>=7?'😊':($durasi>=5?'😐':'😴');
        $text="😴 <b>Tidur Tercatat!</b>\n\n🛏 {$m[1]} → ⏰ {$m[2]}\n⏱ Durasi: <b>{$durasi} jam</b> {$emoji}\n\n";
        if($durasi>=7) $text.="✅ Tidur cukup!"; elseif($durasi>=5) $text.="⚠️ Kurang sedikit. Target 7-8 jam."; else $text.="❌ Kurang tidur! Usahakan 7-8 jam.";
        $this->telegram->sendMessage($profile->telegram_chat_id,$text);
        return response()->json(['ok' => true]);
    }

    // === UTILITY ===
    private function updateStreak(UserProfile $profile): array { $streak=Streak::firstOrCreate(['profile_id'=>$profile->id]); return $streak->recordActivity(); }
    private function autoFavorite(UserProfile $profile, array $item): void { $nama=$item['nama']??null; if(!$nama) return; $fav=FoodFavorite::where('profile_id',$profile->id)->where('nama_makanan',$nama)->first(); if($fav){$fav->incrementUse();}else{FoodFavorite::create(['profile_id'=>$profile->id,'nama_makanan'=>$nama,'kalori'=>$item['kalori']??0,'protein'=>$item['protein']??0,'karbohidrat'=>$item['karbohidrat']??0,'lemak'=>$item['lemak']??0,'porsi'=>$item['porsi']??1,'satuan_porsi'=>$item['satuan_porsi']??'porsi','use_count'=>1]);} }
    private function detectMealTime(): string { $h=(int)now('Asia/Singapore')->format('H'); if($h>=5&&$h<10) return 'sarapan'; if($h>=10&&$h<15) return 'makan_siang'; if($h>=15&&$h<18) return 'snack'; return 'makan_malam'; }
    private function getMealLabel(string $w): string { return match($w){'sarapan'=>'🌅 Sarapan','makan_siang'=>'☀️ Siang','makan_malam'=>'🌙 Malam','snack'=>'🍪 Snack',default=>$w}; }
    private function getBmiCategory(float $bmi): string { if($bmi<18.5) return 'Underweight'; if($bmi<25) return 'Normal'; if($bmi<30) return 'Overweight'; return 'Obese'; }
    private function getGreeting(): string { $h=(int)now('Asia/Singapore')->format('H'); if($h>=5&&$h<11) return 'Selamat pagi'; if($h>=11&&$h<15) return 'Selamat siang'; if($h>=15&&$h<18) return 'Selamat sore'; return 'Selamat malam'; }
    private function progressBar(int $pct, int $len=20): string { $f=(int)round($pct/100*$len); return str_repeat('█',min($f,$len)).str_repeat('░',max(0,$len-$f)); }
    private function miniBar(float $pct): string { $f=(int)round(min(100,$pct)/100*8); $b=str_repeat('▓',$f).str_repeat('░',8-$f); return $pct>100?$b.'⚠️':($pct>=80?$b.'✅':$b); }
    private function getFallbackRecommendation(int $sisa): string { if($sisa>500) return "• Makan lengkap: nasi + lauk + sayur\n• Pilih protein: ayam bakar, ikan\n"; if($sisa>200) return "• Snack sehat: buah, yogurt\n• Hindari gorengan\n"; return "• Target hampir! Minum air saja\n"; }
}
