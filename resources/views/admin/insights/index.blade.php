@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    {{-- Başlık --}}
    <div class="mb-6">
        <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Akıllı Öneriler</h2>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Restoran & Kurye Performans Analizi — Son 30 Gün</p>
    </div>

    {{-- Özet Kartlar --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Restoran Sayısı</p>
            <p class="text-2xl font-black text-slate-800">{{ $restaurantStats->count() }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ort. Hazırlanma</p>
            <p class="text-2xl font-black text-brand">{{ round($overallAvgPrepared, 1) }} <span class="text-sm font-bold text-slate-400">dk</span></p>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kurye (7 Gün)</p>
            <p class="text-2xl font-black text-slate-800">{{ $courierStats->count() }}</p>
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

        {{-- Sağ: Tablolar --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Restoran Karşılaştırma --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5 border-b border-slate-50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Restoran Hazırlanma Süreleri</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Son 30 gün · Genel ort: {{ round($overallAvgPrepared, 1) }} dk</p>
                    </div>
                    <i class="fa-solid fa-shop text-slate-200 text-2xl"></i>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($restaurantStats as $r)
                        @php
                            $maxMin = $restaurantStats->max('avg_prepared_min') ?: 1;
                            $barWidth = $maxMin > 0 ? round(($r->avg_prepared_min / $maxMin) * 100) : 0;
                            $isAbove = $overallAvgPrepared > 0 && $r->avg_prepared_min > $overallAvgPrepared * 1.3;
                            $barColor = $isAbove ? 'bg-rose-400' : ($r->avg_prepared_min > $overallAvgPrepared ? 'bg-amber-400' : 'bg-emerald-400');
                        @endphp
                        <div class="px-5 py-3.5">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    @if($isAbove)
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 flex-shrink-0"></span>
                                    @elseif($r->avg_prepared_min > $overallAvgPrepared)
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                    @endif
                                    <p class="text-xs font-bold text-slate-700 truncate max-w-[180px]">{{ $r->restaurant_name }}</p>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="text-[10px] font-bold text-slate-400">{{ $r->total_orders }} sipariş</span>
                                    <span class="text-xs font-black {{ $isAbove ? 'text-rose-600' : ($r->avg_prepared_min > $overallAvgPrepared ? 'text-amber-600' : 'text-emerald-600') }}">{{ $r->avg_prepared_min }} dk</span>
                                </div>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $barColor }}" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2 block"></i>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Son 30 günde kayıt bulunamadı</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kurye Mola/Aktif Dağılımı --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5 border-b border-slate-50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Kurye Aktivite Dağılımı</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Son 7 gün · Aktif / Mola / Servis</p>
                    </div>
                    <i class="fa-solid fa-motorcycle text-slate-200 text-2xl"></i>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($courierStats as $c)
                        @php
                            $total = $c->active_min + $c->break_min + $c->service_min;
                            $activePct  = $total > 0 ? round(($c->active_min  / $total) * 100) : 0;
                            $breakPct   = $total > 0 ? round(($c->break_min   / $total) * 100) : 0;
                            $servicePct = $total > 0 ? round(($c->service_min / $total) * 100) : 0;
                            $isHighBreak = $breakPct > 40;
                        @endphp
                        <div class="px-5 py-3.5">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    @if($isHighBreak)
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 flex-shrink-0"></span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                    @endif
                                    <p class="text-xs font-bold text-slate-700">{{ $c->courier_name }}</p>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400">{{ $total }} dk toplam</span>
                            </div>
                            <div class="flex h-2 rounded-full overflow-hidden gap-px">
                                @if($activePct > 0)
                                    <div class="bg-emerald-400 h-full" style="width: {{ $activePct }}%" title="Aktif: {{ $c->active_min }} dk"></div>
                                @endif
                                @if($breakPct > 0)
                                    <div class="{{ $isHighBreak ? 'bg-rose-400' : 'bg-amber-300' }} h-full" style="width: {{ $breakPct }}%" title="Mola: {{ $c->break_min }} dk"></div>
                                @endif
                                @if($servicePct > 0)
                                    <div class="bg-blue-300 h-full" style="width: {{ $servicePct }}%" title="Servis: {{ $c->service_min }} dk"></div>
                                @endif
                            </div>
                            <div class="flex gap-3 mt-1.5">
                                <span class="text-[9px] font-bold text-emerald-600"><i class="fa-solid fa-circle text-[5px] mr-1"></i>Aktif {{ $c->active_min }}dk</span>
                                <span class="text-[9px] font-bold {{ $isHighBreak ? 'text-rose-500' : 'text-amber-500' }}"><i class="fa-solid fa-circle text-[5px] mr-1"></i>Mola {{ $c->break_min }}dk</span>
                                <span class="text-[9px] font-bold text-blue-500"><i class="fa-solid fa-circle text-[5px] mr-1"></i>Servis {{ $c->service_min }}dk</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2 block"></i>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Son 7 günde kurye hareketi bulunamadı</p>
                        </div>
                    @endforelse
                </div>
                {{-- Legend --}}
                <div class="px-5 py-3 bg-slate-50/50 border-t border-slate-50 flex gap-4">
                    <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest"><span class="inline-block w-2 h-2 bg-emerald-400 rounded-full mr-1"></span>Aktif</span>
                    <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest"><span class="inline-block w-2 h-2 bg-amber-300 rounded-full mr-1"></span>Mola</span>
                    <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest"><span class="inline-block w-2 h-2 bg-blue-300 rounded-full mr-1"></span>Servis</span>
                    <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest ml-auto"><span class="inline-block w-2 h-2 bg-rose-400 rounded-full mr-1"></span>Yüksek Mola (&gt;%40)</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
