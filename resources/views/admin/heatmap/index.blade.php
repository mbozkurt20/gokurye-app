@extends('admin.layouts.app')
@section('content')
<div class="container-fluid py-4">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                BÖLGE <span class="text-slate-400">ISI HARİTASI</span>
            </h1>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                Son {{ $days }} günde {{ $points->count() }} teslimat noktası · En yoğun bölgeler kırmızı
            </p>
        </div>
        <ol class="breadcrumb !bg-transparent p-0 m-0">
            <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter"><a href="/admin" class="text-slate-400">Admin</a></li>
            <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter active text-slate-800">Bölge Haritası</li>
        </ol>
    </div>

    {{-- Filtre --}}
    <form method="GET" action="{{ route('admin.heatmap') }}" class="bg-white !rounded-[28px] p-5 shadow-sm border border-slate-100 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Zaman Aralığı</label>
                <select name="days" class="form-select !rounded-2xl border-slate-100 bg-slate-50 p-3 font-bold text-slate-700 text-sm pr-10">
                    <option value="7"  {{ $days == 7  ? 'selected' : '' }}>Son 7 Gün</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Son 30 Gün</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>Son 90 Gün</option>
                </select>
            </div>
            <button type="submit" class="bg-slate-900 text-white px-8 py-3 !rounded-2xl font-black text-[11px] uppercase tracking-widest">
                <i class="fa-solid fa-filter mr-2"></i> Güncelle
            </button>
        </div>
    </form>

    <div class="row g-4">
        {{-- Harita --}}
        <div class="col-xl-9">
            <div class="bg-white !rounded-[32px] shadow-sm border border-slate-100 overflow-hidden p-3">
                <div id="heatmapCanvas" style="height: 65vh; min-height: 500px; border-radius: 24px; background: #f1f5f9;"></div>
            </div>
        </div>

        {{-- Top Bölgeler --}}
        <div class="col-xl-3">
            <div class="bg-slate-900 !rounded-[32px] p-6 shadow-2xl h-full">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-brand">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div>
                        <h5 class="text-sm font-black text-white uppercase tracking-tighter m-0">En Yoğun Bölgeler</h5>
                        <p class="text-[9px] font-bold text-slate-500 uppercase m-0">Mahalle bazlı</p>
                    </div>
                </div>

                @if($zoneFreq->isEmpty())
                    <p class="text-[11px] font-bold text-slate-500 text-center py-8 uppercase">Veri bulunamadı</p>
                @else
                @php $maxFreq = $zoneFreq->max(); @endphp
                <div class="space-y-3">
                    @foreach($zoneFreq as $zone => $count)
                    @php
                        $pct = $maxFreq > 0 ? round(($count / $maxFreq) * 100) : 0;
                        $rank = $loop->iteration;
                        $rankColor = $rank === 1 ? '#f59e0b' : ($rank === 2 ? '#94a3b8' : ($rank === 3 ? '#cd7c32' : '#475569'));
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black" style="color:{{ $rankColor }};">#{{ $rank }}</span>
                                <span class="text-[11px] font-bold text-white truncate max-w-[140px]">{{ $zone }}</span>
                            </div>
                            <span class="text-[11px] font-black text-brand">{{ $count }}</span>
                        </div>
                        <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-brand transition-all" style="width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="mt-6 pt-4 border-t border-white/10">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">{{ $points->count() }} toplam teslimat</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const heatPoints = @json($points->map(fn($p) => ['lat' => (float)$p->latitude, 'lng' => (float)$p->longitude])->values());

    window.initHeatmap = function () {
        const map = new google.maps.Map(document.getElementById('heatmapCanvas'), {
            center: heatPoints.length > 0
                ? { lat: heatPoints[0].lat, lng: heatPoints[0].lng }
                : { lat: 39.92, lng: 32.85 },
            zoom: 12,
            mapTypeControl: false,
            streetViewControl: false,
        });

        const latLngPoints = heatPoints.map(p => new google.maps.LatLng(p.lat, p.lng));

        const heatmap = new google.maps.visualization.HeatmapLayer({
            data: latLngPoints,
            map: map,
            radius: 30,
            opacity: 0.75,
            gradient: [
                'rgba(0, 255, 255, 0)',
                'rgba(0, 255, 255, 1)',
                'rgba(0, 191, 255, 1)',
                'rgba(0, 127, 255, 1)',
                'rgba(0, 63, 255, 1)',
                'rgba(0, 0, 255, 1)',
                'rgba(0, 0, 223, 1)',
                'rgba(0, 0, 191, 1)',
                'rgba(0, 0, 159, 1)',
                'rgba(0, 0, 127, 1)',
                'rgba(63, 0, 91, 1)',
                'rgba(127, 0, 63, 1)',
                'rgba(191, 0, 31, 1)',
                'rgba(255, 0, 0, 1)'
            ]
        });

        if (latLngPoints.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            latLngPoints.forEach(p => bounds.extend(p));
            map.fitBounds(bounds);
        }
    };
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=visualization&callback=initHeatmap" async defer></script>
@endsection
