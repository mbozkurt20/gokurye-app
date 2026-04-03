<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Demo Hesabı Oluştur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pattern-bg {
            background-color: #10b981;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M11 18c0-1.1.9-2 2-2h1a2 2 0 0 1 0 4h-1a2 2 0 0 1-2-2zm25 2c0-1.1.9-2 2-2h1a2 2 0 0 1 0 4h-1a2 2 0 0 1-2-2z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

{{-- Header --}}
<div class="pattern-bg py-10 px-6">
    <div class="max-w-2xl mx-auto text-center">
        <img src="{{config('site.logo')}}" alt="Logo" class="h-10 mx-auto mb-5 brightness-0 invert">
        <div class="inline-flex items-center gap-2 bg-white/20 text-white text-[11px] font-black uppercase tracking-widest px-4 py-2 rounded-full mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Demo Hesabı
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight mb-2">Hemen Başlayın</h1>
        <p class="text-emerald-100 font-bold text-sm">Bilgilerinizi doldurun, sistemi ücretsiz keşfetmeye başlayın.</p>
    </div>
</div>

<div class="max-w-2xl mx-auto px-6 -mt-6 pb-12">

    {{-- Info Card --}}
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-5 mb-6 flex items-start gap-4">
        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-gift text-sm"></i>
        </div>
        <div>
            <p class="font-black text-slate-800 text-sm">Demo hesabınıza 10 kontör yüklenecek</p>
            <p class="text-slate-400 text-xs font-bold mt-1">Her kategoriden 2 kayıt ekleyebilir, sistemi tam anlamıyla deneyebilirsiniz. Şifreniz <span class="text-slate-700 font-black">demo123</span> olarak ayarlanacaktır.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl">
            @foreach($errors->all() as $error)
                <p class="text-rose-700 text-xs font-bold">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('demo.create') }}" id="demoForm">
        @csrf

        {{-- Kişisel Bilgiler --}}
        <div class="bg-white rounded-[28px] shadow-sm border border-slate-100 p-7 mb-5">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Hesap Bilgileri</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Ad Soyad *</label>
                    <input required type="text" name="name" value="{{ old('name') }}"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"
                           placeholder="İşletme Adı / Adınız">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">E-Posta *</label>
                    <input required type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-700 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"
                           placeholder="ornek@email.com">
                </div>
            </div>
        </div>

        {{-- Konum --}}
        <div class="bg-white rounded-[28px] shadow-sm border border-slate-100 p-7 mb-5">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-sm"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Bölge & Konum</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Şehir *</label>
                    <select required name="city_id" id="city_id"
                            class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-600 focus:outline-none focus:border-indigo-400 transition-all">
                        <option value="">Şehir Seçiniz</option>
                        @foreach(\App\Models\City::orderBy('name')->get() as $city)
                            <option value="{{ $city->id }}"
                                    data-lat="{{ $city->lat }}"
                                    data-lng="{{ $city->lng }}"
                                    {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block">İlçe</label>
                    <select name="district_id" id="district_id"
                            class="w-full px-4 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-600 focus:outline-none focus:border-indigo-400 transition-all">
                        <option value="">Önce şehir seçiniz</option>
                    </select>
                </div>
            </div>

            {{-- Harita --}}
            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-3 block">
                Haritadan Merkez Konumu Seçin *
            </label>
            <div class="relative w-full rounded-[20px] overflow-hidden border border-slate-100 shadow-sm" style="height: 350px;">
                <input id="pac-input"
                       class="absolute z-[5] top-4 left-4 w-[calc(100%-2rem)] max-w-sm rounded-xl border-0 shadow-xl px-4 py-3 font-bold text-xs text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                       type="text"
                       placeholder="Adres veya bölge arayın...">
                <div id="map" class="w-full h-full"></div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <label class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1 block">Enlem (Lat)</label>
                    <input required type="text" name="latitude" id="lat" value="{{ old('latitude') }}" readonly
                           class="w-full px-4 py-3 bg-slate-50 border-0 rounded-2xl font-mono text-xs text-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1 block">Boylam (Lng)</label>
                    <input required type="text" name="longitude" id="lng" value="{{ old('longitude') }}" readonly
                           class="w-full px-4 py-3 bg-slate-50 border-0 rounded-2xl font-mono text-xs text-slate-400 focus:outline-none">
                </div>
            </div>
            <p class="text-[10px] text-slate-300 font-bold mt-2">Haritaya tıklayarak veya marker'ı sürükleyerek konumunuzu belirleyin.</p>
        </div>

        {{-- Submit --}}
        <button type="submit" id="submitBtn"
                class="w-full py-5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
            <i class="fas fa-bolt"></i>
            Demo Hesabımı Oluştur &amp; Giriş Yap
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('admin.login') }}" class="text-[11px] font-bold text-slate-400 hover:text-indigo-600 uppercase tracking-widest transition-colors">
                ← Giriş Sayfasına Dön
            </a>
        </div>
    </form>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places&callback=initMap" async defer></script>

<script>
    let map, marker, autocomplete;

    function initMap() {
        const defaultPos = { lat: 39.9334, lng: 32.8597 };

        const mapStyles = [
            { "featureType": "all", "elementType": "geometry.fill", "stylers": [{ "weight": "2.00" }] },
            { "featureType": "landscape", "elementType": "all", "stylers": [{ "color": "#f2f2f2" }] },
            { "featureType": "poi", "elementType": "all", "stylers": [{ "visibility": "off" }] },
            { "featureType": "road", "elementType": "all", "stylers": [{ "saturation": -100 }, { "lightness": 45 }] },
            { "featureType": "water", "elementType": "all", "stylers": [{ "color": "#cbd5e1" }, { "visibility": "on" }] }
        ];

        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultPos,
            zoom: 6,
            styles: mapStyles,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false
        });

        marker = new google.maps.Marker({
            position: defaultPos,
            map: map,
            draggable: true
        });

        const input = document.getElementById("pac-input");
        input.addEventListener("keydown", (e) => { if (e.key === "Enter") e.preventDefault(); });

        autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo("bounds", map);

        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;
            map.setCenter(place.geometry.location);
            map.setZoom(15);
            marker.setPosition(place.geometry.location);
            updateInputs(place.geometry.location.lat(), place.geometry.location.lng());
        });

        map.addListener("click", (e) => {
            marker.setPosition(e.latLng);
            updateInputs(e.latLng.lat(), e.latLng.lng());
        });

        marker.addListener("dragend", () => {
            const pos = marker.getPosition();
            updateInputs(pos.lat(), pos.lng());
        });
    }

    function updateInputs(lat, lng) {
        document.getElementById("lat").value = lat;
        document.getElementById("lng").value = lng;
    }

    // Şehir değişince haritayı merkezle ve ilçeleri yükle
    $('#city_id').on('change', function () {
        const selected = $(this).find('option:selected');
        const lat = parseFloat(selected.data('lat'));
        const lng = parseFloat(selected.data('lng'));

        if (lat && lng && map) {
            const newPos = { lat, lng };
            map.setCenter(newPos);
            map.setZoom(11);
            marker.setPosition(newPos);
            updateInputs(lat, lng);
        }

        loadDistricts($(this).val());
    });

    function loadDistricts(cityId) {
        if (cityId) {
            $.get('/get-districts/' + cityId, function (data) {
                $('#district_id').empty().append('<option value="">İlçe Seçiniz</option>');
                $.each(data, function (key, value) {
                    $('#district_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            });
        } else {
            $('#district_id').empty().append('<option value="">Önce şehir seçiniz</option>');
        }
    }

    // Sayfa yüklenince önceki seçim varsa yükle
    $(document).ready(function () {
        const cityId = $('#city_id').val();
        if (cityId) loadDistricts(cityId);
    });
</script>
</body>
</html>
