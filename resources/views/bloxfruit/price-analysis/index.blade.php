@extends('layouts.app')
@section('title', 'Analisa Harga')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <x-page-header eyebrow="Tools" title="Analisa Harga" subtitle="Saran harga jual yang masih untung tapi tetap kompetitif">
    </x-page-header>

    {{-- Tabs --}}
    <div class="card overflow-hidden">
        <div class="flex border-b border-[var(--border)] overflow-x-auto">
            @php
                $tabs = [
                    ['key' => 'fruit', 'label' => 'Fruit'],
                    ['key' => 'skin', 'label' => 'Skin'],
                    ['key' => 'gamepass', 'label' => 'Gamepass'],
                    ['key' => 'permanent', 'label' => 'Permanent'],
                ];
            @endphp
            @foreach($tabs as $t)
            <a href="{{ route('bloxfruit.price-analysis', ['tab' => $t['key'], 'min_margin' => $minMargin, 'ideal_margin' => $idealMargin]) }}"
               class="px-4 sm:px-5 py-3 text-xs sm:text-sm font-semibold border-b-2 whitespace-nowrap transition-colors {{ $tab === $t['key'] ? 'border-[var(--accent)] text-[var(--accent)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]' }}">
                {{ $t['label'] }}
            </a>
            @endforeach
        </div>

        {{-- Margin Settings --}}
        <form method="GET" action="{{ route('bloxfruit.price-analysis') }}" class="px-5 py-4 bg-[var(--surface-2)]/50 border-b border-[var(--border)]">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="form-label">Margin Minimum</label>
                    <div class="flex items-center gap-1.5">
                        <input type="number" name="min_margin" value="{{ $minMargin }}" min="0" max="200" step="5"
                               class="w-20 h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm num focus:border-[var(--accent)] focus:ring-0 focus:outline-none">
                        <span class="text-sm text-[var(--text-muted)]">%</span>
                    </div>
                </div>
                <div>
                    <label class="form-label">Margin Ideal</label>
                    <div class="flex items-center gap-1.5">
                        <input type="number" name="ideal_margin" value="{{ $idealMargin }}" min="0" max="300" step="5"
                               class="w-20 h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm num focus:border-[var(--accent)] focus:ring-0 focus:outline-none">
                        <span class="text-sm text-[var(--text-muted)]">%</span>
                    </div>
                </div>
                <x-btn type="submit" variant="primary">Terapkan</x-btn>
                <p class="text-[11px] text-[var(--text-subtle)] ml-auto max-w-xs">
                    Item dianggap <span class="text-[var(--success)] font-semibold">sehat</span> jika margin ≥ {{ $minMargin }}%, dan <span class="text-[var(--accent)] font-semibold">optimal</span> jika ≥ {{ $idealMargin }}%.
                </p>
            </div>
        </form>
    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stat-card label="Total {{ $title }}" :value="$stats['total_items']" :sub="format_angka($stats['total_stok']) . ' total stok'" tone="accent" icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        <x-stat-card label="Total Modal" :value="format_rupiah($stats['total_modal'])" sub="Berdasarkan harga beli" tone="warning" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8" />
        <x-stat-card label="Potensi Revenue" :value="format_rupiah($stats['total_potensi_revenue'])" sub="Jika semua terjual" tone="info" icon="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" />
        <x-stat-card label="Potensi Profit" :value="format_rupiah($stats['total_potensi_profit'])" :sub="'Margin overall ' . $stats['margin_overall'] . '%'" :tone="$stats['total_potensi_profit'] >= 0 ? 'success' : 'danger'" :trend="$stats['total_potensi_profit'] >= 0 ? 'up' : 'down'" icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
    </div>

    {{-- Status Distribution --}}
    @if($stats['total_items'] > 0)
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-[var(--text)] section-bar">Distribusi Status Harga</h3>
            <p class="text-[11px] text-[var(--text-subtle)] num">{{ $stats['total_items'] }} item</p>
        </div>
        @php
            $optimalCount = $rows->where('status', 'optimal')->count();
            $sehatCount = $stats['item_margin_sehat'];
            $tipisCount = $stats['item_margin_tipis'];
            $belumCount = $stats['item_belum_set'];
            $total = max(1, $stats['total_items']);
        @endphp
        <div class="flex h-3 rounded-full overflow-hidden bg-[var(--surface-2)]">
            @if($optimalCount > 0)<div style="width: {{ ($optimalCount / $total) * 100 }}%; background: var(--accent)" title="Optimal: {{ $optimalCount }}"></div>@endif
            @if($sehatCount > 0)<div style="width: {{ ($sehatCount / $total) * 100 }}%; background: var(--success)" title="Sehat: {{ $sehatCount }}"></div>@endif
            @if($tipisCount > 0)<div style="width: {{ ($tipisCount / $total) * 100 }}%; background: var(--warning)" title="Tipis: {{ $tipisCount }}"></div>@endif
            @if($belumCount > 0)<div style="width: {{ ($belumCount / $total) * 100 }}%; background: var(--text-subtle)" title="Belum set: {{ $belumCount }}"></div>@endif
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 text-xs">
            <div class="flex items-center gap-2"><span class="dot dot-accent"></span><span class="text-[var(--text-muted)]">Optimal</span><span class="num font-semibold text-[var(--text)]">{{ $optimalCount }}</span></div>
            <div class="flex items-center gap-2"><span class="dot dot-success"></span><span class="text-[var(--text-muted)]">Sehat</span><span class="num font-semibold text-[var(--text)]">{{ $sehatCount }}</span></div>
            <div class="flex items-center gap-2"><span class="dot dot-warning"></span><span class="text-[var(--text-muted)]">Tipis</span><span class="num font-semibold text-[var(--text)]">{{ $tipisCount }}</span></div>
            <div class="flex items-center gap-2"><span class="dot"></span><span class="text-[var(--text-muted)]">Belum set</span><span class="num font-semibold text-[var(--text)]">{{ $belumCount }}</span></div>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[var(--border)]">
            <h3 class="text-sm font-semibold text-[var(--text)]">Daftar Harga {{ $title }}</h3>
            <p class="text-[11px] text-[var(--text-subtle)] mt-1">Saran harga: <span class="text-[var(--warning)] font-semibold">Min</span> = modal+{{ $minMargin }}%, <span class="text-[var(--accent)] font-semibold">Ideal</span> = modal+{{ $idealMargin }}%, <span class="text-[var(--text-muted)] font-semibold">Market</span> = ceiling kompetitif</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[var(--surface-2)]">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Nama</th>
                        <th class="px-3 py-2.5 text-right text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Stok</th>
                        <th class="px-3 py-2.5 text-right text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Modal</th>
                        <th class="px-3 py-2.5 text-right text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Harga Saat Ini</th>
                        <th class="px-3 py-2.5 text-right text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Margin</th>
                        <th class="px-3 py-2.5 text-right text-[10px] uppercase tracking-wider text-[var(--warning)]">Saran Min</th>
                        <th class="px-3 py-2.5 text-right text-[10px] uppercase tracking-wider text-[var(--accent)]">Saran Ideal</th>
                        <th class="px-3 py-2.5 text-right text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Market</th>
                        <th class="px-3 py-2.5 text-center text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Status</th>
                        <th class="px-3 py-2.5 text-center text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($rows as $r)
                    <tr class="hover:bg-[var(--surface-2)]/50 transition-colors">
                        <td class="px-3 py-2.5">
                            <p class="font-semibold text-[var(--text)]">{{ $r['nama'] }}</p>
                            <p class="text-[10px] {{ $r['meta_class'] }} font-medium uppercase tracking-wide mt-0.5">{{ $r['meta'] }}</p>
                        </td>
                        <td class="px-3 py-2.5 text-right">
                            @if($r['stok'] > 0)
                            <span class="num font-semibold text-[var(--text)]">{{ format_angka($r['stok']) }}</span>
                            @else
                            <span class="text-[var(--text-subtle)]">0</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-right text-[var(--text-muted)] num">{{ $r['harga_beli'] > 0 ? format_angka($r['harga_beli']) : '-' }}</td>
                        <td class="px-3 py-2.5 text-right num">
                            @if($r['harga_jual'] > 0)
                            <span class="font-semibold text-[var(--text)]">{{ format_angka($r['harga_jual']) }}</span>
                            @else
                            <span class="text-[var(--text-subtle)]">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-right">
                            @if($r['status'] === 'belum')
                            <span class="text-[var(--text-subtle)]">-</span>
                            @else
                            @php
                                $marginClass = match($r['status']) {
                                    'tipis'   => 'text-[var(--warning)]',
                                    'sehat'   => 'text-[var(--success)]',
                                    'optimal' => 'text-[var(--accent)]',
                                    default   => 'text-[var(--text-muted)]',
                                };
                            @endphp
                            <span class="num font-bold {{ $marginClass }}">{{ $r['current_margin'] }}%</span>
                            <p class="text-[10px] text-[var(--text-subtle)] num">+{{ format_angka($r['profit_per_unit']) }}/unit</p>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-right num text-[var(--warning)] font-semibold">{{ $r['min_suggest'] > 0 ? format_angka($r['min_suggest']) : '-' }}</td>
                        <td class="px-3 py-2.5 text-right num text-[var(--accent)] font-semibold">{{ $r['ideal_suggest'] > 0 ? format_angka($r['ideal_suggest']) : '-' }}</td>
                        <td class="px-3 py-2.5 text-right num text-[var(--text-subtle)]">{{ $r['market_ceiling'] > 0 ? format_angka($r['market_ceiling']) : '-' }}</td>
                        <td class="px-3 py-2.5 text-center">
                            @php
                                $statusBadge = match($r['status']) {
                                    'optimal' => ['Optimal', 'bg-[var(--accent-soft)] text-[var(--accent)]'],
                                    'sehat'   => ['Sehat', 'bg-[var(--success-soft)] text-[var(--success)]'],
                                    'tipis'   => ['Tipis', 'bg-[var(--warning-soft)] text-[var(--warning)]'],
                                    default   => ['Belum set', 'bg-[var(--surface-2)] text-[var(--text-subtle)]'],
                                };
                            @endphp
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusBadge[1] }}">{{ $statusBadge[0] }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <a href="{{ $r['edit_url'] }}" class="text-[11px] font-medium text-[var(--accent)] hover:underline">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12">
                            <x-empty-state icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2" message="Belum ada data {{ strtolower($title) }}" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Insights --}}
    @if($stats['total_items'] > 0 && ($stats['item_margin_tipis'] > 0 || $stats['item_belum_set'] > 0))
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-[var(--text)] mb-3 section-bar">Rekomendasi Aksi</h3>
        <ul class="space-y-2 text-sm text-[var(--text-muted)]">
            @if($stats['item_margin_tipis'] > 0)
            <li class="flex items-start gap-2">
                <span class="dot dot-warning mt-1.5 shrink-0"></span>
                <span>Ada <span class="font-semibold text-[var(--warning)] num">{{ $stats['item_margin_tipis'] }}</span> {{ strtolower($title) }} dengan margin di bawah {{ $minMargin }}%. Pertimbangkan naikkan harga ke saran <span class="text-[var(--warning)]">Min</span> untuk menjaga keuntungan.</span>
            </li>
            @endif
            @if($stats['item_belum_set'] > 0)
            <li class="flex items-start gap-2">
                <span class="dot mt-1.5 shrink-0"></span>
                <span>Ada <span class="font-semibold text-[var(--text-subtle)] num">{{ $stats['item_belum_set'] }}</span> {{ strtolower($title) }} yang belum punya harga modal/jual. Lengkapi dulu agar bisa dianalisa.</span>
            </li>
            @endif
            @if($stats['margin_overall'] < $minMargin && $stats['total_potensi_revenue'] > 0)
            <li class="flex items-start gap-2">
                <span class="dot dot-danger mt-1.5 shrink-0"></span>
                <span>Margin overall <span class="num font-semibold text-[var(--danger)]">{{ $stats['margin_overall'] }}%</span> masih di bawah target minimum {{ $minMargin }}%. Review item-item bermargin tipis.</span>
            </li>
            @elseif($stats['margin_overall'] >= $idealMargin)
            <li class="flex items-start gap-2">
                <span class="dot dot-accent mt-1.5 shrink-0"></span>
                <span>Margin overall <span class="num font-semibold text-[var(--accent)]">{{ $stats['margin_overall'] }}%</span> sudah optimal. Pertahankan strategi harga saat ini.</span>
            </li>
            @endif
        </ul>
    </div>
    @endif
</div>
@endsection
