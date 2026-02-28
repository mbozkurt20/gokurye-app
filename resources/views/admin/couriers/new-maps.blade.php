@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                CANLI <span class="text-slate-400">TAKİP MERKEZİ</span>
            </h1>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                Google Maps · Pusher ile anlık senkronizasyon
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Canlı</span>
            <span id="lastUpdateBadge" class="text-[9px] font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">—</span>
        </div>
    </div>

    {{-- Durum Kartları --}}
    <div class="row g-3 mb-4">
        @php
        $statusCards = [
            ['label' => 'Müsait',  'count' => $data['active'],  'icon' => 'fa-check-double', 'color' => 'emerald', 'status' => 'active'],
            ['label' => 'Molada',  'count' => $data['break'],   'icon' => 'fa-coffee',       'color' => 'amber',   'status' => 'break'],
            ['label' => 'Serviste','count' => $data['service'], 'icon' => 'fa-motorcycle',   'color' => 'indigo',  'status' => 'service'],
            ['label' => 'Kapalı', 'count' => $data['passive'], 'icon' => 'fa-power-off',    'color' => 'slate',   'status' => 'passive'],
        ];
        @endphp
        @foreach($statusCards as $sc)
        <div class="col-xl-3 col-sm-6">
            <div class="bg-white !rounded-[24px] p-5 shadow-sm border border-slate-100 flex items-center gap-4 cursor-pointer hover:border-{{ $sc['color'] }}-200 transition-all group" onclick="filterByStatus('{{ $sc['status'] }}')">
                <div class="w-11 h-11 bg-{{ $sc['color'] }}-50 text-{{ $sc['color'] }}-600 rounded-2xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fas {{ $sc['icon'] }}"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">{{ $sc['label'] }}</p>
                    <h3 class="text-xl font-black text-slate-800 tracking-tighter leading-none">{{ $sc['count'] }} <span class="text-xs text-slate-400 font-bold">Kurye</span></h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Ana İçerik: Sidebar + Harita --}}
    <div class="flex gap-4" style="height: 68vh; min-height: 500px;">

        {{-- Kurye Listesi Sidebar --}}
        <div class="w-72 flex-shrink-0 bg-white !rounded-[28px] shadow-sm border border-slate-100 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-slate-50">
                <div class="relative">
                    <input type="text" id="courierSearch" placeholder="Kurye ara..." onkeyup="searchCourier()"
                           class="w-full bg-slate-50 border-0 !rounded-2xl p-3 pl-9 text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand/20">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                </div>
            </div>
            <div id="courierList" class="flex-1 overflow-y-auto p-2 space-y-1">
                @foreach($allCouriers as $c)
                @php
                    $statusMeta = match($c->status) {
                        'active'  => ['label' => 'Müsait',   'dot' => 'bg-emerald-400'],
                        'service' => ['label' => 'Serviste', 'dot' => 'bg-indigo-500'],
                        'break'   => ['label' => 'Molada',   'dot' => 'bg-amber-400'],
                        'passive' => ['label' => 'Kapalı',   'dot' => 'bg-slate-300'],
                        default   => ['label' => $c->status, 'dot' => 'bg-slate-300'],
                    };
                @endphp
                <div class="courier-card flex items-center gap-3 p-3 rounded-2xl hover:bg-brand/5 cursor-pointer transition-all group"
                     data-name="{{ strtolower($c->name) }}"
                     data-status="{{ $c->status }}"
                     data-lat="{{ $c->latitude }}"
                     data-lng="{{ $c->longitude }}"
                     onclick="focusCourier({{ $c->id }}, {{ $c->latitude ?: 'null' }}, {{ $c->longitude ?: 'null' }})">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-600 text-sm flex-shrink-0 group-hover:bg-brand/10 group-hover:text-brand transition-colors">
                        {{ mb_substr($c->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-black text-slate-700 truncate">{{ $c->name }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusMeta['dot'] }} inline-block"></span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $statusMeta['label'] }}</span>
                        </div>
                    </div>
                    @if($c->latitude && $c->longitude)
                    <i class="fa-solid fa-location-dot text-brand text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    @else
                    <i class="fa-solid fa-location-slash text-slate-300 text-xs"></i>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Google Maps --}}
        <div class="flex-1 bg-white !rounded-[28px] shadow-sm border border-slate-100 overflow-hidden p-2">
            <div id="googleMapCanvas" class="w-full h-full !rounded-[22px]" style="background:#f1f5f9;"></div>
        </div>
    </div>
</div>

<style>
    .courier-card.active-card { background: rgba(99,102,241,0.07); }
</style>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    const COURIER_DATA = @json($courierss);
    const ALL_COURIERS = @json($allCouriers);

    const STATUS_COLORS = {
        active:  '#10b981',
        service: '#6366f1',
        break:   '#f59e0b',
        passive: '#94a3b8',
    };

    window.mapsData = { map: null, markers: {}, infoWindow: null };
    let activeFilter = null;

    window.initMap = function () {
        window.mapsData.map = new google.maps.Map(document.getElementById('googleMapCanvas'), {
            center: { lat: 39.92, lng: 32.85 },
            zoom: 12,
            mapTypeControl: false,
            streetViewControl: false,
            styles: [
                { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] }
            ]
        });
        window.mapsData.infoWindow = new google.maps.InfoWindow();

        if (COURIER_DATA.length > 0) {
            updateMarkers(COURIER_DATA);
            fitAll();
        }
    };

    function makeMarkerIcon(status) {
        const color = STATUS_COLORS[status] || '#64748b';
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="36" height="44" viewBox="0 0 36 44">
            <path d="M18 0C8.06 0 0 8.06 0 18c0 12 18 26 18 26S36 30 36 18C36 8.06 27.94 0 18 0z" fill="${color}"/>
            <circle cx="18" cy="18" r="9" fill="white" opacity="0.9"/>
        </svg>`;
        return { url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg), scaledSize: new google.maps.Size(36, 44), anchor: new google.maps.Point(18, 44) };
    }

    function updateMarkers(locations) {
        // Clear existing
        Object.values(window.mapsData.markers).forEach(m => m.setMap(null));
        window.mapsData.markers = {};

        locations.forEach(loc => {
            const lat = parseFloat(loc.latitude);
            const lng = parseFloat(loc.longitude);
            if (isNaN(lat) || isNaN(lng)) return;

            const marker = new google.maps.Marker({
                position: { lat, lng },
                map: window.mapsData.map,
                icon: makeMarkerIcon(loc.status || 'active'),
                title: loc.name
            });

            const statusLabel = { active: 'Müsait', service: 'Serviste', break: 'Molada', passive: 'Kapalı' }[loc.status] || loc.status;
            const color = STATUS_COLORS[loc.status] || '#64748b';

            marker.addListener('click', () => {
                const photo = loc.profile_photo
                    ? `<img src="/storage/${loc.profile_photo}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-sm flex-shrink-0">`
                    : `<div style="width:40px;height:40px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;color:#64748b;flex-shrink:0;">${loc.name.charAt(0)}</div>`;

                window.mapsData.infoWindow.setContent(`
                    <div style="font-family:system-ui;min-width:200px;padding:4px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                            ${photo}
                            <div>
                                <p style="font-size:13px;font-weight:900;color:#1e293b;margin:0;">${loc.name}</p>
                                <span style="font-size:10px;font-weight:800;color:${color};background:${color}15;padding:2px 8px;border-radius:8px;text-transform:uppercase;">${statusLabel}</span>
                            </div>
                        </div>
                        <div style="font-size:11px;font-weight:700;color:#64748b;border-top:1px solid #f1f5f9;padding-top:8px;">
                            <div style="margin-bottom:4px;">📍 Mesafe: <strong>${loc.distance || '—'}</strong></div>
                            <div>📞 ${loc.phone || '—'}</div>
                        </div>
                    </div>
                `);
                window.mapsData.infoWindow.open(window.mapsData.map, marker);
            });

            window.mapsData.markers[loc.id] = marker;
        });

        updateLastUpdateTime();
    }

    function fitAll() {
        const bounds = new google.maps.LatLngBounds();
        let valid = false;
        COURIER_DATA.forEach(loc => {
            if (loc.latitude && loc.longitude) {
                bounds.extend({ lat: parseFloat(loc.latitude), lng: parseFloat(loc.longitude) });
                valid = true;
            }
        });
        if (valid) window.mapsData.map.fitBounds(bounds);
    }

    function focusCourier(id, lat, lng) {
        document.querySelectorAll('.courier-card').forEach(el => el.classList.remove('active-card'));
        event.currentTarget.classList.add('active-card');

        if (!lat || !lng) return;
        window.mapsData.map.setCenter({ lat: parseFloat(lat), lng: parseFloat(lng) });
        window.mapsData.map.setZoom(16);

        const marker = window.mapsData.markers[id];
        if (marker) google.maps.event.trigger(marker, 'click');
    }

    function filterByStatus(status) {
        const cards = document.querySelectorAll('.courier-card');
        if (activeFilter === status) {
            activeFilter = null;
            cards.forEach(c => c.style.display = 'flex');
        } else {
            activeFilter = status;
            cards.forEach(c => {
                c.style.display = c.dataset.status === status ? 'flex' : 'none';
            });
        }
    }

    function searchCourier() {
        const q = document.getElementById('courierSearch').value.toLowerCase();
        document.querySelectorAll('.courier-card').forEach(c => {
            c.style.display = c.dataset.name.includes(q) ? 'flex' : 'none';
        });
    }

    function updateLastUpdateTime() {
        const now = new Date();
        document.getElementById('lastUpdateBadge').textContent =
            'Son: ' + now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0') + ':' + now.getSeconds().toString().padStart(2,'0');
    }

    // Pusher
    if (typeof window.pusherInstance === 'undefined') {
        window.pusherInstance = new Pusher('{{ env("PUSHER_APP_KEY") }}', { cluster: '{{ env("PUSHER_APP_CLUSTER") }}' });
    }
    if (typeof window.courierChannel === 'undefined') {
        window.courierChannel = window.pusherInstance.subscribe('courier-channel');
        window.courierChannel.bind('courier-{{ auth()->guard("admin")->id() }}', function (data) {
            updateMarkers(data);
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
@endsection
