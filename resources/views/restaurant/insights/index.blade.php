@extends('restaurant.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    {{-- Başlık --}}
    <div class="mb-6">
        <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Akıllı Öneriler</h2>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Hazırlanma Süresi Analizi — Son 30 Gün</p>
    </div>

    {{-- Özet Kartlar --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">30 Gün Ortalaması</p>
            <p class="text-2xl font-black text-brand">{{ $overallAvg }} <span class="text-sm font-bold text-slate-400">dk</span></p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Son 7 Gün</p>
            <div class="flex items-end gap-2">
                <p class="text-2xl font-black text-slate-800">{{ $last7Avg }} <span class="text-sm font-bold text-slate-400">dk</span></p>
                @if($trendChange > 5)
                    <span class="text-[10px] font-black text-rose-500 mb-1">▲ %{{ $trendChange }}</span>
                @elseif($trendChange < -5)
                    <span class="text-[10px] font-black text-emerald-500 mb-1">▼ %{{ abs($trendChange) }}</span>
                @else
                    <span class="text-[10px] font-black text-slate-400 mb-1">≈ Sabit</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">En Yoğun Saat</p>
            @php
                $slowestHour = $hourlyPattern->sortByDesc('avg_minutes')->first();
            @endphp
            <p class="text-2xl font-black text-slate-800">
                {{ $slowestHour ? $slowestHour->hour . ':00' : '—' }}
                <span class="text-sm font-bold text-slate-400">{{ $slowestHour ? $slowestHour->avg_minutes . ' dk' : '' }}</span>
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Öneri Sayısı</p>
            <p class="text-2xl font-black {{ count($suggestions) === 1 && $suggestions[0]['type'] === 'success' ? 'text-emerald-500' : 'text-amber-500' }}">{{ count($suggestions) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Sol: Öneriler --}}
        <div class="xl:col-span-1 space-y-3">
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Öneriler & Uyarılar</h3>

            @foreach($suggestions as $s)
                @php
                    $colors = [
                        'success' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-600', 'title_color' => 'text-emerald-800'],
                        'warning' => ['bg' => 'bg-amber-50',   'border' => 'border-amber-100',   'icon_bg' => 'bg-amber-100',   'icon_color' => 'text-amber-600',   'title_color' => 'text-amber-800'],
                        'danger'  => ['bg' => 'bg-rose-50',    'border' => 'border-rose-100',    'icon_bg' => 'bg-rose-100',    'icon_color' => 'text-rose-600',    'title_color' => 'text-rose-800'],
                        'info'    => ['bg' => 'bg-blue-50',    'border' => 'border-blue-100',    'icon_bg' => 'bg-blue-100',    'icon_color' => 'text-blue-600',    'title_color' => 'text-blue-800'],
                    ];
                    $c = $colors[$s['type']] ?? $colors['info'];
                @endphp
                <div class="rounded-2xl border p-4 flex gap-3 {{ $c['bg'] }} {{ $c['border'] }}">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $c['icon_bg'] }}">
                        <i class="fa-solid {{ $s['icon'] }} text-sm {{ $c['icon_color'] }}"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-tight mb-1 {{ $c['title_color'] }}">{{ $s['title'] }}</p>
                        <p class="text-[11px] text-slate-600 leading-relaxed">{!! $s['body'] !!}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sağ: Grafikler & Tablolar --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Saatlik Isı Haritası --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5 border-b border-slate-50">
                    <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Saatlik Hazırlanma Süresi</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Son 30 gün ortalaması</p>
                </div>
                <div class="p-5">
                    <div class="flex items-end gap-1 h-28">
                        @php
                            $maxHourVal = $hourlyPattern->max('avg_minutes') ?: 1;
                        @endphp
                        @for($h = 0; $h < 24; $h++)
                            @php
                                $hData = $hourlyPattern->get($h);
                                $val = $hData ? $hData->avg_minutes : 0;
                                $barH = $maxHourVal > 0 ? round(($val / $maxHourVal) * 100) : 0;
                                $isHot = $overallAvg > 0 && $val > $overallAvg * 1.3;
                                $barCol = $isHot ? 'bg-rose-400' : ($val > $overallAvg ? 'bg-amber-300' : 'bg-brand/30');
                                $barCol = $val == 0 ? 'bg-slate-100' : $barCol;
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1 group relative">
                                @if($val > 0)
                                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] font-bold px-1.5 py-0.5 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                        {{ $val }}dk
                                    </div>
                                @endif
                                <div class="w-full rounded-t-sm {{ $barCol }} transition-all" style="height: {{ max($barH, $val > 0 ? 4 : 0) }}%"></div>
                                @if($h % 4 === 0)
                                    <span class="text-[8px] font-bold text-slate-400">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</span>
                                @else
                                    <span class="text-[8px] font-bold text-transparent">·</span>
                                @endif
                            </div>
                        @endfor
                    </div>
                    <div class="flex gap-4 mt-3">
                        <span class="text-[9px] font-bold text-slate-400"><span class="inline-block w-2 h-2 bg-brand/30 rounded-sm mr-1"></span>Normal</span>
                        <span class="text-[9px] font-bold text-amber-500"><span class="inline-block w-2 h-2 bg-amber-300 rounded-sm mr-1"></span>Ortalama Üstü</span>
                        <span class="text-[9px] font-bold text-rose-500"><span class="inline-block w-2 h-2 bg-rose-400 rounded-sm mr-1"></span>Yoğun (+%30)</span>
                    </div>
                </div>
            </div>

            {{-- Günlük Dağılım + Platform --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Haftanın Günleri --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-5 border-b border-slate-50">
                        <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Gün Bazlı Ortalama</p>
                    </div>
                    @php
                        $dayNames = [1 => 'Paz', 2 => 'Pzt', 3 => 'Sal', 4 => 'Çar', 5 => 'Per', 6 => 'Cum', 7 => 'Cmt'];
                        $dayFull  = [1 => 'Pazar', 2 => 'Pazartesi', 3 => 'Salı', 4 => 'Çarşamba', 5 => 'Perşembe', 6 => 'Cuma', 7 => 'Cumartesi'];
                        $dayMap   = $dayPattern->keyBy('day');
                        $maxDayVal = $dayPattern->max('avg_minutes') ?: 1;
                    @endphp
                    <div class="p-4 space-y-2">
                        @for($d = 1; $d <= 7; $d++)
                            @php
                                $dData = $dayMap->get($d);
                                $val = $dData ? $dData->avg_minutes : 0;
                                $barW = $maxDayVal > 0 ? round(($val / $maxDayVal) * 100) : 0;
                                $isSlow = $overallAvg > 0 && $val > $overallAvg * 1.25;
                                $barCol = $isSlow ? 'bg-rose-400' : ($val > $overallAvg ? 'bg-amber-300' : 'bg-emerald-400');
                                $barCol = $val == 0 ? 'bg-slate-100' : $barCol;
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-slate-500 w-8">{{ $dayNames[$d] }}</span>
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $barCol }}" style="width: {{ $barW }}%"></div>
                                </div>
                                <span class="text-[10px] font-black {{ $isSlow ? 'text-rose-500' : 'text-slate-500' }} w-10 text-right">{{ $val > 0 ? $val . ' dk' : '—' }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Platform Dağılımı --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-5 border-b border-slate-50">
                        <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Platform Bazlı Ortalama</p>
                    </div>
                    @php
                        $maxPlatformVal = $platformPattern->max('avg_minutes') ?: 1;
                        $platformAvgAll = $platformPattern->avg('avg_minutes') ?: 0;
                    @endphp
                    <div class="p-4 space-y-2">
                        @forelse($platformPattern as $p)
                            @php
                                $barW = $maxPlatformVal > 0 ? round(($p->avg_minutes / $maxPlatformVal) * 100) : 0;
                                $isSlow = $platformAvgAll > 0 && $p->avg_minutes > $platformAvgAll * 1.3;
                                $barCol = $isSlow ? 'bg-rose-400' : 'bg-brand/50';
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-slate-500 w-24 truncate">{{ strtoupper($p->platform) }}</span>
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $barCol }}" style="width: {{ $barW }}%"></div>
                                </div>
                                <span class="text-[10px] font-black {{ $isSlow ? 'text-rose-500' : 'text-slate-500' }} w-10 text-right">{{ $p->avg_minutes }} dk</span>
                            </div>
                        @empty
                            <p class="text-[11px] text-slate-400 text-center py-4">Veri bulunamadı</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
