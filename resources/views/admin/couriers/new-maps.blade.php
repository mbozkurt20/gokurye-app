@extends('admin.layouts.app')
@section('content')

    <div class="row ">
        <div class="col-md-6">
            <div class="card card-body" style="background: #259a38">
                <h5 class="text-white fw-bold"> Müsait: {{$data['active']}} Kurye</h5>
            </div>

        </div>
        <div class="col-md-6">
            <div class="card card-body " style="background: #4f46e5">
                <h5 class="text-white fw-bold"> Molada: {{$data['break']}} Kurye</h5>
            </div>

        </div>
        <div class="col-md-6">
            <div class="card card-body" style="background: rgba(0,184,96,0.43)">
                <h5 class="text-white fw-bold"> Serviste: {{$data['service']}} Kurye</h5>
            </div>

        </div>
        <div class="col-md-6">
            <div class="card card-body" style="background: #4f46e5">
                <h5 class="text-white fw-bold"> Kapalı: {{$data['passive']}} Kurye</h5>
            </div>
        </div>
    </div>

    <!-- Start::row-1 -->
    <div class="row">
        <div class="col-lg-12">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif

            <div class="card custom-card">
                <div class="card-body">
                    <div id="map" style="width: 100%; height: 75vh;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var map = L.map('map').setView([37.969074, 37.1329995], 7);

        L.tileLayer('https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
            attribution: ''
        }).addTo(map);

        var markers = []; // Tüm marker'ları burada tutacağız

        function addMarkers(locations) {
            // Mevcut marker'ları haritadan sil
            markers.forEach(function (marker) {
                map.removeLayer(marker);
            });
            markers = [];

            // Yeni marker'ları oluştur
            locations.forEach(function (location) {
                var icon = L.icon({
                    iconUrl: '/theme/images/kurye.png',
                    iconSize: [58, 58],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -42]
                });

                var marker = L.marker([location.latitude, location.longitude], {icon: icon}).addTo(map);
                marker.bindPopup('<b>' + location.name + '</b><br>' + location.price);


                if (location.price_type === 'package'){
                    marker.bindTooltip(
                        location.status === 'service'
                            ? `<span class="text-warning">Serviste</span>`
                            : `<span class="text-success">Müsait</span>`,
                        {
                            permanent: false,
                            direction: 'top'
                        }
                    );

                    const tooltipContent = `
    ${location.status === 'service'
                        ? `<span class="fw-bold text-warning">Serviste</span>`
                        : `<span class="fw-bold text-success">Müsait</span>`}
    <br> ${location.name} - Tel: ${location.phone}
    <br> Paket Başı: ${location.price}₺
    <br> Uzaklık: ${location.distance}
`;

                    marker.bindTooltip(tooltipContent, {
                        permanent: false,
                        direction: 'top'
                    });
                }else {
                    const statusHtml = `${location.status === 'service'
                        ? `<span class="fw-bold text-warning">Serviste</span>`
                        : `<span class="fw-bold text-success">Müsait</span>`}`;

                    const tooltipContent = `${statusHtml}
    <br>  ${location.name} - Tel: ${location.phone}

    <br> Sabit Ücret: ${location.fixed_price}₺
    <br> Km Ücret: ${location.km_price}₺
    <br> Uzaklık: ${location.distance}
`;

                    marker.bindTooltip(tooltipContent, {
                        permanent: false,
                        direction: 'top'
                    });
                }


                markers.push(marker); // Yeni marker'ı diziye ekle
            });
        }

        // İlk yüklemede kurye verilerini ekle
        var initialData = {!! $courierss !!};
        addMarkers(initialData);

        // Pusher kurulumu
        Pusher.logToConsole = false;

        var courierPusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
            cluster: '{{env('PUSHER_APP_CLUSTER')}}'
        });

        var channelT = courierPusher.subscribe('courier-channel');
        channelT.bind('courier-{{ auth()->id() }}', function (data) {
            addMarkers(data); // Gelen verilerle marker'ları güncelle
        });
    </script>
@endsection
