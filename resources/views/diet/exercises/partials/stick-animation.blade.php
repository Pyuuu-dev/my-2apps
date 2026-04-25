{{-- Stick Figure Animation - hanya render saat timer aktif --}}
@php $slug = \Illuminate\Support\Str::slug($exercise ?? '', '_'); @endphp

<div class="stick-figure">
<svg viewBox="0 0 80 100" class="w-full h-full">
@switch($slug)

@case('jalan_kaki')
    {{-- Jalan Kaki: langkah pelan --}}
    <circle cx="40" cy="14" r="7" fill="currentColor"/>
    <line x1="40" y1="21" x2="40" y2="52" stroke="currentColor" stroke-width="3"/>
    <line x1="40" y1="33" x2="28" y2="46" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="28;52;28" dur="1s" repeatCount="indefinite"/></line>
    <line x1="40" y1="33" x2="52" y2="46" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="52;28;52" dur="1s" repeatCount="indefinite"/></line>
    <line x1="40" y1="52" x2="30" y2="82" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="30;50;30" dur="1s" repeatCount="indefinite"/></line>
    <line x1="40" y1="52" x2="50" y2="82" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="50;30;50" dur="1s" repeatCount="indefinite"/></line>
@break

@case('jalan_cepat')
    {{-- Jalan Cepat: langkah lebih lebar dan cepat --}}
    <circle cx="40" cy="14" r="7" fill="currentColor"/>
    <line x1="40" y1="21" x2="40" y2="52" stroke="currentColor" stroke-width="3"/>
    <line x1="40" y1="33" x2="24" y2="44" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="24;56;24" dur="0.6s" repeatCount="indefinite"/></line>
    <line x1="40" y1="33" x2="56" y2="44" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="56;24;56" dur="0.6s" repeatCount="indefinite"/></line>
    <line x1="40" y1="52" x2="26" y2="84" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="26;54;26" dur="0.6s" repeatCount="indefinite"/></line>
    <line x1="40" y1="52" x2="54" y2="84" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="54;26;54" dur="0.6s" repeatCount="indefinite"/></line>
@break

@case('jogging')
    {{-- Jogging: berlari sedang, badan sedikit condong --}}
    <circle cx="42" cy="12" r="7" fill="currentColor"><animate attributeName="cy" values="12;10;12" dur="0.5s" repeatCount="indefinite"/></circle>
    <line x1="42" y1="19" x2="40" y2="50" stroke="currentColor" stroke-width="3"/>
    <line x1="41" y1="30" x2="24" y2="40" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="24;58;24" dur="0.5s" repeatCount="indefinite"/></line>
    <line x1="41" y1="30" x2="58" y2="40" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="58;24;58" dur="0.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="50" x2="24" y2="80" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="24;56;24" dur="0.5s" repeatCount="indefinite"/><animate attributeName="y2" values="80;74;80" dur="0.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="50" x2="56" y2="74" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="56;24;56" dur="0.5s" repeatCount="indefinite"/><animate attributeName="y2" values="74;80;74" dur="0.5s" repeatCount="indefinite"/></line>
@break

@case('lari')
    {{-- Lari: sprint cepat --}}
    <circle cx="44" cy="10" r="7" fill="currentColor"><animate attributeName="cy" values="10;7;10" dur="0.35s" repeatCount="indefinite"/></circle>
    <line x1="43" y1="17" x2="38" y2="48" stroke="currentColor" stroke-width="3"/>
    <line x1="40" y1="28" x2="18" y2="36" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="18;62;18" dur="0.35s" repeatCount="indefinite"/></line>
    <line x1="40" y1="28" x2="62" y2="36" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="62;18;62" dur="0.35s" repeatCount="indefinite"/></line>
    <line x1="38" y1="48" x2="20" y2="78" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="20;58;20" dur="0.35s" repeatCount="indefinite"/><animate attributeName="y2" values="78;68;78" dur="0.35s" repeatCount="indefinite"/></line>
    <line x1="38" y1="48" x2="58" y2="68" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="58;20;58" dur="0.35s" repeatCount="indefinite"/><animate attributeName="y2" values="68;78;68" dur="0.35s" repeatCount="indefinite"/></line>
@break

@case('bersepeda')
@case('bersepeda_cepat')
    {{-- Bersepeda: duduk kayuh --}}
    @php $spd = $slug === 'bersepeda_cepat' ? '0.4s' : '0.7s'; @endphp
    <circle cx="26" cy="70" r="12" fill="none" stroke="currentColor" stroke-width="2"/>
    <circle cx="54" cy="70" r="12" fill="none" stroke="currentColor" stroke-width="2"/>
    <line x1="26" y1="70" x2="40" y2="55" stroke="currentColor" stroke-width="2"/>
    <line x1="54" y1="70" x2="40" y2="55" stroke="currentColor" stroke-width="2"/>
    <circle cx="40" cy="22" r="6" fill="currentColor"/>
    <line x1="40" y1="28" x2="40" y2="48" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="48" x2="40" y2="55" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="36" x2="50" y2="42" stroke="currentColor" stroke-width="2"/>
    {{-- Kaki kayuh --}}
    <line x1="40" y1="55" x2="32" y2="72" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="32;48;32" dur="{{ $spd }}" repeatCount="indefinite"/></line>
    <line x1="40" y1="55" x2="48" y2="72" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="48;32;48" dur="{{ $spd }}" repeatCount="indefinite"/></line>
@break

@case('renang')
    {{-- Renang: gaya bebas horizontal --}}
    <circle cx="22" cy="48" r="6" fill="currentColor"><animate attributeName="cx" values="22;26;22" dur="1s" repeatCount="indefinite"/></circle>
    <line x1="28" y1="48" x2="60" y2="50" stroke="currentColor" stroke-width="3"/>
    <line x1="60" y1="50" x2="70" y2="56" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="56;44;56" dur="0.8s" repeatCount="indefinite"/></line>
    <line x1="60" y1="50" x2="70" y2="44" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="44;56;44" dur="0.8s" repeatCount="indefinite"/></line>
    <line x1="35" y1="49" x2="30" y2="32" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="30;50;30" dur="1s" repeatCount="indefinite"/><animate attributeName="y2" values="32;38;32" dur="1s" repeatCount="indefinite"/></line>
    <line x1="35" y1="49" x2="50" y2="38" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="50;30;50" dur="1s" repeatCount="indefinite"/><animate attributeName="y2" values="38;32;38" dur="1s" repeatCount="indefinite"/></line>
    <path d="M5,52 Q20,47 35,52 Q50,57 65,52 Q75,47 80,52" fill="none" stroke="#60a5fa" stroke-width="1.5" opacity="0.4"><animate attributeName="d" values="M5,52 Q20,47 35,52 Q50,57 65,52 Q75,47 80,52;M5,52 Q20,57 35,52 Q50,47 65,52 Q75,57 80,52;M5,52 Q20,47 35,52 Q50,57 65,52 Q75,47 80,52" dur="1.5s" repeatCount="indefinite"/></path>
@break

@case('lompat_tali')
    {{-- Lompat Tali --}}
    <circle cx="40" cy="14" r="6" fill="currentColor"><animate attributeName="cy" values="14;6;14" dur="0.45s" repeatCount="indefinite"/></circle>
    <line x1="40" y1="20" x2="40" y2="48" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="20;12;20" dur="0.45s" repeatCount="indefinite"/><animate attributeName="y2" values="48;40;48" dur="0.45s" repeatCount="indefinite"/></line>
    <line x1="40" y1="30" x2="30" y2="38" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="38;30;38" dur="0.45s" repeatCount="indefinite"/></line>
    <line x1="40" y1="30" x2="50" y2="38" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="38;30;38" dur="0.45s" repeatCount="indefinite"/></line>
    <line x1="40" y1="48" x2="36" y2="74" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="48;40;48" dur="0.45s" repeatCount="indefinite"/></line>
    <line x1="40" y1="48" x2="44" y2="74" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="48;40;48" dur="0.45s" repeatCount="indefinite"/></line>
    <path d="M30,38 Q40,88 50,38" fill="none" stroke="#f59e0b" stroke-width="1.5"><animate attributeName="d" values="M30,38 Q40,88 50,38;M30,38 Q40,-5 50,38;M30,38 Q40,88 50,38" dur="0.45s" repeatCount="indefinite"/></path>
@break

@case('senam_aerobik')
    {{-- Senam Aerobik: jumping jack --}}
    <circle cx="40" cy="12" r="6" fill="currentColor"><animate attributeName="cy" values="12;8;12" dur="0.6s" repeatCount="indefinite"/></circle>
    <line x1="40" y1="18" x2="40" y2="48" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="18;14;18" dur="0.6s" repeatCount="indefinite"/></line>
    <line x1="40" y1="28" x2="22" y2="22" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="22;32;22" dur="0.6s" repeatCount="indefinite"/><animate attributeName="y2" values="22;38;22" dur="0.6s" repeatCount="indefinite"/></line>
    <line x1="40" y1="28" x2="58" y2="22" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="58;48;58" dur="0.6s" repeatCount="indefinite"/><animate attributeName="y2" values="22;38;22" dur="0.6s" repeatCount="indefinite"/></line>
    <line x1="40" y1="48" x2="26" y2="80" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="26;38;26" dur="0.6s" repeatCount="indefinite"/></line>
    <line x1="40" y1="48" x2="54" y2="80" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="54;42;54" dur="0.6s" repeatCount="indefinite"/></line>
@break

@case('naik_tangga')
    {{-- Naik Tangga --}}
    <rect x="35" y="80" width="20" height="5" fill="currentColor" opacity="0.2"/>
    <rect x="25" y="68" width="20" height="5" fill="currentColor" opacity="0.2"/>
    <rect x="15" y="56" width="20" height="5" fill="currentColor" opacity="0.2"/>
    <circle cx="35" cy="20" r="6" fill="currentColor"><animate attributeName="cy" values="20;14;20" dur="0.8s" repeatCount="indefinite"/><animate attributeName="cx" values="35;28;35" dur="0.8s" repeatCount="indefinite"/></circle>
    <line x1="35" y1="26" x2="35" y2="50" stroke="currentColor" stroke-width="3"><animate attributeName="x1" values="35;28;35" dur="0.8s" repeatCount="indefinite"/><animate attributeName="x2" values="35;28;35" dur="0.8s" repeatCount="indefinite"/></line>
    <line x1="35" y1="50" x2="30" y2="75" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="75;62;75" dur="0.8s" repeatCount="indefinite"/></line>
    <line x1="35" y1="50" x2="40" y2="68" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="68;75;68" dur="0.8s" repeatCount="indefinite"/></line>
@break

@case('push_up')
    {{-- Push Up: naik turun horizontal --}}
    <circle cx="18" cy="40" r="5" fill="currentColor"><animate attributeName="cy" values="40;50;40" dur="1.5s" repeatCount="indefinite"/></circle>
    <line x1="23" y1="42" x2="58" y2="46" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="42;52;42" dur="1.5s" repeatCount="indefinite"/><animate attributeName="y2" values="46;52;46" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="58" y1="46" x2="65" y2="72" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="46;52;46" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="18" y1="46" x2="16" y2="62" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="46;56;46" dur="1.5s" repeatCount="indefinite"/><animate attributeName="x2" values="16;12;16" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="18" y1="46" x2="22" y2="62" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="46;56;46" dur="1.5s" repeatCount="indefinite"/><animate attributeName="x2" values="22;26;22" dur="1.5s" repeatCount="indefinite"/></line>
@break

@case('sit_up')
    {{-- Sit Up: badan naik dari berbaring --}}
    <line x1="50" y1="68" x2="60" y2="68" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="62" x2="50" y2="68" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="62" x2="55" y2="75" stroke="currentColor" stroke-width="2.5"/>
    <circle cx="28" cy="48" r="5" fill="currentColor"><animate attributeName="cy" values="62;38;62" dur="1.5s" repeatCount="indefinite"/><animate attributeName="cx" values="20;35;20" dur="1.5s" repeatCount="indefinite"/></circle>
    <line x1="24" y1="52" x2="40" y2="62" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="65;42;65" dur="1.5s" repeatCount="indefinite"/><animate attributeName="x1" values="24;38;24" dur="1.5s" repeatCount="indefinite"/></line>
@break

@case('plank')
    {{-- Plank: posisi statis dengan getaran --}}
    <circle cx="16" cy="44" r="5" fill="currentColor"/>
    <line x1="21" y1="46" x2="60" y2="48" stroke="currentColor" stroke-width="3"><animate attributeName="y2" values="48;46;48" dur="2s" repeatCount="indefinite"/></line>
    <line x1="60" y1="48" x2="66" y2="72" stroke="currentColor" stroke-width="2.5"/>
    <line x1="16" y1="50" x2="16" y2="66" stroke="currentColor" stroke-width="2.5"/>
    {{-- Trembling --}}
    <line x1="30" y1="47" x2="50" y2="48" stroke="currentColor" stroke-width="0.5" opacity="0.3"><animate attributeName="y1" values="47;46;47" dur="0.3s" repeatCount="indefinite"/></line>
@break

@case('squat')
    {{-- Squat: naik turun --}}
    <circle cx="40" cy="14" r="6" fill="currentColor"><animate attributeName="cy" values="14;32;14" dur="1.5s" repeatCount="indefinite"/></circle>
    <line x1="40" y1="20" x2="40" y2="48" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="20;38;20" dur="1.5s" repeatCount="indefinite"/><animate attributeName="y2" values="48;56;48" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="32" x2="54" y2="36" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="36;48;36" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="32" x2="26" y2="36" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="36;48;36" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="48" x2="30" y2="80" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="48;56;48" dur="1.5s" repeatCount="indefinite"/><animate attributeName="x2" values="30;24;30" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="48" x2="50" y2="80" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="48;56;48" dur="1.5s" repeatCount="indefinite"/><animate attributeName="x2" values="50;56;50" dur="1.5s" repeatCount="indefinite"/></line>
@break

@case('angkat_beban')
    {{-- Angkat Beban: bicep curl --}}
    <circle cx="40" cy="14" r="6" fill="currentColor"/>
    <line x1="40" y1="20" x2="40" y2="52" stroke="currentColor" stroke-width="3"/>
    <line x1="40" y1="52" x2="34" y2="80" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="52" x2="46" y2="80" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="32" x2="24" y2="46" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="46;28;46" dur="1.2s" repeatCount="indefinite"/></line>
    <line x1="40" y1="32" x2="56" y2="46" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="46;28;46" dur="1.2s" repeatCount="indefinite"/></line>
    {{-- Dumbbells --}}
    <rect x="20" y="44" width="8" height="4" rx="1" fill="currentColor"><animate attributeName="y" values="44;26;44" dur="1.2s" repeatCount="indefinite"/></rect>
    <rect x="52" y="44" width="8" height="4" rx="1" fill="currentColor"><animate attributeName="y" values="44;26;44" dur="1.2s" repeatCount="indefinite"/></rect>
@break

@case('pull_up')
    {{-- Pull Up: naik turun dari bar --}}
    <line x1="15" y1="10" x2="65" y2="10" stroke="currentColor" stroke-width="3"/>
    <circle cx="40" cy="24" r="6" fill="currentColor"><animate attributeName="cy" values="24;16;24" dur="1.5s" repeatCount="indefinite"/></circle>
    <line x1="40" y1="30" x2="40" y2="55" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="30;22;30" dur="1.5s" repeatCount="indefinite"/><animate attributeName="y2" values="55;42;55" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="32" x2="32" y2="12" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="32;22;32" dur="1.5s" repeatCount="indefinite"/><animate attributeName="x2" values="32;34;32" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="32" x2="48" y2="12" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="32;22;32" dur="1.5s" repeatCount="indefinite"/><animate attributeName="x2" values="48;46;48" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="55" x2="34" y2="78" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="55;42;55" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="55" x2="46" y2="78" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="55;42;55" dur="1.5s" repeatCount="indefinite"/></line>
@break

@case('lunges')
    {{-- Lunges: kaki maju turun --}}
    <circle cx="40" cy="14" r="6" fill="currentColor"><animate attributeName="cy" values="14;26;14" dur="1.5s" repeatCount="indefinite"/></circle>
    <line x1="40" y1="20" x2="40" y2="48" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="20;32;20" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="32" x2="28" y2="38" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="32" x2="52" y2="38" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="48" x2="28" y2="80" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="28;24;28" dur="1.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="48" x2="54" y2="80" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="54;58;54" dur="1.5s" repeatCount="indefinite"/><animate attributeName="y2" values="80;72;80" dur="1.5s" repeatCount="indefinite"/></line>
@break

@case('yoga')
    {{-- Yoga: tree pose --}}
    <circle cx="40" cy="14" r="6" fill="currentColor"/>
    <line x1="40" y1="20" x2="40" y2="52" stroke="currentColor" stroke-width="3"/>
    <line x1="40" y1="28" x2="26" y2="16" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="16;14;16" dur="2s" repeatCount="indefinite"/></line>
    <line x1="40" y1="28" x2="54" y2="16" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="16;14;16" dur="2s" repeatCount="indefinite"/></line>
    <line x1="40" y1="52" x2="40" y2="82" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="52" x2="48" y2="62" stroke="currentColor" stroke-width="2.5"/>
    <line x1="48" y1="62" x2="42" y2="52" stroke="currentColor" stroke-width="2"/>
@break

@case('stretching')
    {{-- Stretching: raih jari kaki --}}
    <circle cx="30" cy="28" r="6" fill="currentColor"><animate attributeName="cy" values="28;34;28" dur="2s" repeatCount="indefinite"/></circle>
    <line x1="34" y1="32" x2="48" y2="52" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="32;38;32" dur="2s" repeatCount="indefinite"/></line>
    <line x1="48" y1="52" x2="60" y2="52" stroke="currentColor" stroke-width="2.5"/>
    <line x1="60" y1="52" x2="62" y2="52" stroke="currentColor" stroke-width="2.5"/>
    <line x1="34" y1="36" x2="52" y2="42" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="52;58;52" dur="2s" repeatCount="indefinite"/><animate attributeName="y2" values="42;48;42" dur="2s" repeatCount="indefinite"/></line>
@break

@case('pilates')
    {{-- Pilates: the hundred --}}
    <circle cx="22" cy="48" r="5" fill="currentColor"/>
    <line x1="27" y1="50" x2="52" y2="52" stroke="currentColor" stroke-width="3"/>
    <line x1="52" y1="52" x2="60" y2="38" stroke="currentColor" stroke-width="2.5"/>
    <line x1="22" y1="54" x2="18" y2="40" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="40;36;40" dur="0.3s" repeatCount="indefinite"/></line>
    <line x1="22" y1="54" x2="26" y2="40" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="40;36;40" dur="0.3s" repeatCount="indefinite"/></line>
@break

@case('hiit_workout')
    {{-- HIIT: high knees --}}
    <circle cx="40" cy="10" r="6" fill="currentColor"><animate attributeName="cy" values="10;6;10" dur="0.3s" repeatCount="indefinite"/></circle>
    <line x1="40" y1="16" x2="40" y2="44" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="16;12;16" dur="0.3s" repeatCount="indefinite"/></line>
    <line x1="40" y1="28" x2="28" y2="36" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="36;30;36" dur="0.3s" repeatCount="indefinite"/></line>
    <line x1="40" y1="28" x2="52" y2="36" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="30;36;30" dur="0.3s" repeatCount="indefinite"/></line>
    <line x1="40" y1="44" x2="32" y2="78" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="78;50;78" dur="0.3s" repeatCount="indefinite"/></line>
    <line x1="40" y1="44" x2="48" y2="50" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="50;78;50" dur="0.3s" repeatCount="indefinite"/></line>
@break

@case('burpees')
    {{-- Burpees: lompat --}}
    <circle cx="40" cy="10" r="6" fill="currentColor"><animate attributeName="cy" values="10;40;10" dur="0.8s" repeatCount="indefinite"/></circle>
    <line x1="40" y1="16" x2="40" y2="42" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="16;46;16" dur="0.8s" repeatCount="indefinite"/><animate attributeName="y2" values="42;58;42" dur="0.8s" repeatCount="indefinite"/></line>
    <line x1="40" y1="26" x2="24" y2="16" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="16;52;16" dur="0.8s" repeatCount="indefinite"/></line>
    <line x1="40" y1="26" x2="56" y2="16" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="16;52;16" dur="0.8s" repeatCount="indefinite"/></line>
    <line x1="40" y1="42" x2="34" y2="76" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="42;58;42" dur="0.8s" repeatCount="indefinite"/></line>
    <line x1="40" y1="42" x2="46" y2="76" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="42;58;42" dur="0.8s" repeatCount="indefinite"/></line>
@break

@case('mountain_climbers')
    {{-- Mountain Climbers --}}
    <circle cx="18" cy="36" r="5" fill="currentColor"/>
    <line x1="23" y1="38" x2="52" y2="46" stroke="currentColor" stroke-width="3"/>
    <line x1="18" y1="42" x2="18" y2="58" stroke="currentColor" stroke-width="2.5"/>
    <line x1="52" y1="46" x2="62" y2="70" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="62;35;62" dur="0.4s" repeatCount="indefinite"/><animate attributeName="y2" values="70;52;70" dur="0.4s" repeatCount="indefinite"/></line>
    <line x1="52" y1="46" x2="35" y2="52" stroke="currentColor" stroke-width="2.5"><animate attributeName="x2" values="35;62;35" dur="0.4s" repeatCount="indefinite"/><animate attributeName="y2" values="52;70;52" dur="0.4s" repeatCount="indefinite"/></line>
@break

@case('tabata')
    {{-- Tabata: explosive squat jump --}}
    <circle cx="40" cy="10" r="6" fill="currentColor"><animate attributeName="cy" values="10;30;10" dur="0.5s" repeatCount="indefinite"/></circle>
    <line x1="40" y1="16" x2="40" y2="44" stroke="currentColor" stroke-width="3"><animate attributeName="y1" values="16;36;16" dur="0.5s" repeatCount="indefinite"/><animate attributeName="y2" values="44;54;44" dur="0.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="28" x2="26" y2="18" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="18;40;18" dur="0.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="28" x2="54" y2="18" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="18;40;18" dur="0.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="44" x2="30" y2="78" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="44;54;44" dur="0.5s" repeatCount="indefinite"/><animate attributeName="x2" values="30;24;30" dur="0.5s" repeatCount="indefinite"/></line>
    <line x1="40" y1="44" x2="50" y2="78" stroke="currentColor" stroke-width="2.5"><animate attributeName="y1" values="44;54;44" dur="0.5s" repeatCount="indefinite"/><animate attributeName="x2" values="50;56;50" dur="0.5s" repeatCount="indefinite"/></line>
@break

@default
    {{-- Default: gerakan tangan --}}
    <circle cx="40" cy="14" r="7" fill="currentColor"/>
    <line x1="40" y1="21" x2="40" y2="52" stroke="currentColor" stroke-width="3"/>
    <line x1="40" y1="33" x2="26" y2="44" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="44;28;44" dur="1s" repeatCount="indefinite"/></line>
    <line x1="40" y1="33" x2="54" y2="44" stroke="currentColor" stroke-width="2.5"><animate attributeName="y2" values="28;44;28" dur="1s" repeatCount="indefinite"/></line>
    <line x1="40" y1="52" x2="32" y2="80" stroke="currentColor" stroke-width="2.5"/>
    <line x1="40" y1="52" x2="48" y2="80" stroke="currentColor" stroke-width="2.5"/>
@endswitch
</svg>
</div>
