@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    CANLI <span class="text-slate-400">TAKİP MERKEZİ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Google Maps Altyapısı ile Anlık Kurye Takibi
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Canlı Senkronizasyon</span>
            </div>
        </div>

        <div class="row g-4 mb-8">
            @php
                $metrics = [
                    ['label' => 'Müsait', 'count' => $data['active'], 'icon' => 'fa-check-double', 'color' => 'emerald'],
                    ['label' => 'Molada', 'count' => $data['break'], 'icon' => 'fa-coffee', 'color' => 'amber'],
                    ['label' => 'Serviste', 'count' => $data['service'], 'icon' => 'fa-motorcycle', 'color' => 'indigo'],
                    ['label' => 'Kapalı', 'count' => $data['passive'], 'icon' => 'fa-power-off', 'color' => 'slate']
                ];
            @endphp
            @foreach($metrics as $metric)
                <div class="col-xl-3 col-sm-6">
                    <div class="bg-white !rounded-[32px] p-6 shadow-sm border border-slate-50 relative overflow-hidden group">
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-12 h-12 bg-{{$metric['color']}}-50 text-{{$metric['color']}}-600 rounded-2xl flex items-center justify-center text-xl transition-transform group-hover:scale-110">
                                <i class="fas {{$metric['icon']}}"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{$metric['label']}}</p>
                                <h3 class="text-2xl font-black text-slate-800 tracking-tighter leading-none">{{$metric['count']}} <span class="text-xs text-slate-400 font-bold uppercase">Kurye</span></h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white !rounded-[40px] shadow-2xl border border-slate-100 p-4">
            <div id="googleMapCanvas" class="w-full !rounded-[32px] overflow-hidden shadow-inner" style="height: 75vh; min-height: 500px; background-color: #eee;"></div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script>
        let map;
        let markers = [];
        let infoWindow;

        function initMap() {
            const mapStyles = [
                { "featureType": "all", "elementType": "geometry.fill", "stylers": [{ "weight": "2.00" }] },
                { "featureType": "landscape", "elementType": "all", "stylers": [{ "color": "#f2f2f2" }] },
                { "featureType": "poi", "elementType": "all", "stylers": [{ "visibility": "off" }] },
                { "featureType": "road", "elementType": "all", "stylers": [{ "saturation": -100 }, { "lightness": 45 }] },
                { "featureType": "water", "elementType": "all", "stylers": [{ "color": "#cbd5e1" }, { "visibility": "on" }] }
            ];

            // Canvas ID'sini yukarıdaki ile eşitledim
            map = new google.maps.Map(document.getElementById("googleMapCanvas"), {
                center: { lat: 37.969074, lng: 37.1329995 },
                zoom: 10,
                styles: mapStyles,
                mapTypeControl: false,
                streetViewControl: true,
                fullscreenControl: true
            });

            infoWindow = new google.maps.InfoWindow();

            // İlk veriyi yükle
            const initialData = {!! $courierss !!};
            if(initialData && initialData.length > 0) {
                updateMarkers(initialData);

                // Haritayı kuryelerin olduğu yere odakla (isteğe bağlı)
                const bounds = new google.maps.LatLngBounds();
                initialData.forEach(loc => bounds.extend({lat: parseFloat(loc.latitude), lng: parseFloat(loc.longitude)}));
                map.fitBounds(bounds);
            }
        }

        function updateMarkers(locations) {
            markers.forEach(m => m.setMap(null));
            markers = [];

            locations.forEach(location => {
                if(!location.latitude || !location.longitude) return;

                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                    map: map,
                    icon: {
                        url: '/theme/images/kurye.png',
                        scaledSize: new google.maps.Size(45, 45),
                        anchor: new google.maps.Point(22, 45)
                    },
                    title: location.name
                });

                marker.addListener("click", () => {
                    const statusText = location.status === 'service' ?
                        '<span style="color:#6366f1; font-weight:bold;">● SERVİSTE</span>' :
                        '<span style="color:#10b981; font-weight:bold;">● MÜSAİT</span>';

                    const content = `
                        <div style="padding:10px; font-family:'Inter',sans-serif; min-width:180px;">
                            <div style="font-size:10px; font-weight:900;">${statusText}</div>
                            <div style="font-size:14px; font-weight:900; color:#1e293b; margin:4px 0;">${location.name}</div>
                            <div style="font-size:11px; color:#64748b;">Tel: ${location.phone}</div>
                            <div style="margin-top:8px; padding-top:8px; border-top:1px solid #eee; font-size:11px;">
                                <b>Paket/Sabit:</b> ${location.price || location.fixed_price}₺<br>
                                <b>Mesafe:</b> ${location.distance || '-'}
                            </div>
                        </div>
                    `;
                    infoWindow.setContent(content);
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            });
        }

        // Pusher Realtime
        const pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', { cluster: '{{env('PUSHER_APP_CLUSTER')}}' });
        const channel = pusher.subscribe('courier-channel');
        channel.bind('courier-{{ auth()->id() }}', function(data) {
            updateMarkers(data);
        });
    </script>
@endsection
