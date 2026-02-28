@extends('admin.layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    YENİ <span class="text-slate-400">RESTAURANT</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sisteme yeni bir işletme kaydı oluşturuyorsunuz.
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-4 py-2 !rounded-2xl m-0 shadow-sm border border-slate-50">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest"><a href="/admin/restaurants" class="text-slate-400">Restaurantlar</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest text-indigo-600 active">Yeni</li>
                </ol>
            </nav>
        </div>

        @if(session()->has('success'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-indigo-500 shadow-2xl rounded-2xl p-4 animate-bounce-short">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-50 p-2 rounded-xl text-indigo-600"><i class="fas fa-check-circle"></i></div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase">BAŞARILI</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 p-2 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Hata Oluştu</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">
                            {{ session()->get('error') }}
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        @endif

        <form method="post" action="{{route('admin.restaurants.create')}}">
            @csrf
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 p-8 md:p-12">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-store text-sm"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">İşletme Bilgileri</h4>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">İşyeri Adı <span class="text-indigo-500">*</span></label>
                                <input required type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 shadow-none transition-all" name="restaurant_name" placeholder="Örn: Gurme Restaurant">
                            </div>
                            <div class="col-md-6">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Yetkili Adı <span class="text-indigo-500">*</span></label>
                                <input required type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 shadow-none" name="name" placeholder="Ad Soyad">
                            </div>
                            <div class="col-md-6">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">E-posta <span class="text-indigo-500">*</span></label>
                                <input required type="email" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 shadow-none" name="email" placeholder="iletisim@restaurant.com">
                            </div>
                            <div class="col-md-6">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Telefon <span class="text-indigo-500">*</span></label>
                                @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                            </div>
                            <div class="col-md-6">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Şifre <span class="text-indigo-500">*</span></label>
                                <input required type="password" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 shadow-none" name="password" placeholder="••••••••">
                            </div>
                            <div class="col-md-6">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Paket Fiyatı (₺)</label>
                                <input type="text" required class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 shadow-none" name="package_price" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Vergi Dairesi</label>
                                <input type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 shadow-none" name="tax_name" placeholder="Daire Adı">
                            </div>
                            <div class="col-md-6">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Vergi Numarası</label>
                                <input type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500 shadow-none" name="tax_number" placeholder="1234567890">
                            </div>
                        </div>

                        <div class="mt-12">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-map-marked-alt text-sm"></i>
                                </div>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Konum & Adres</h4>
                            </div>

                            <div class="mt-8 mb-3 col-md-12">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-4 block">
                                    <i class="fas fa-map-marker-alt text-indigo-500 me-2"></i> Harita Konumu
                                </label>

                                <div class="relative w-full !rounded-[32px] overflow-hidden border border-slate-100 shadow-sm" style="height: 500px;">
                                    <input id="pac-input"
                                           class="absolute z-[5] top-5 left-5 w-full max-w-md !rounded-xl border-0 shadow-2xl p-4 font-bold text-xs text-slate-600 focus:ring-2 focus:ring-indigo-500"
                                           type="text"
                                           placeholder="Adres veya mekan arayın...">

                                    <div id="map" class="w-full h-full"></div>
                                </div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-300 uppercase tracking-widest ml-1 mb-2 block">Enlem (Latitude)</label>
                                    <input required type="text" name="latitude" id="lat" class="form-control !rounded-xl bg-slate-50 border-0 text-slate-400 font-mono text-xs" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-300 uppercase tracking-widest ml-1 mb-2 block">Boylam (Longitude)</label>
                                    <input required type="text" name="longitude" id="lng" class="form-control !rounded-xl bg-slate-50 border-0 text-slate-400 font-mono text-xs" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Tam Adres (Otomatik)</label>
                                    <textarea id="address_field" rows="3" name="address" class="form-control !rounded-[24px] border-slate-100 font-bold text-slate-700 focus:border-indigo-500" required placeholder="Haritadan işaretleme yapın..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="sticky top-4">
                        <div class="bg-[#0f172a] !rounded-[40px] p-10 text-white shadow-2xl mb-6 relative overflow-hidden">
                            <i class="fas fa-store absolute -right-4 -bottom-4 text-white/5 text-8xl transform -rotate-12"></i>
                            <h3 class="text-xl text-white tracking-tighter uppercase mb-2">Hızlı Kayıt</h3>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest leading-relaxed mb-8">Tüm zorunlu alanları (*) doldurduğunuzdan emin olun.</p>

                            <button type="submit" class="w-full bg-indigo-600 text-white py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-indigo-900/20 hover:scale-[1.02] active:scale-95 transition-all mb-4">
                                <i class="fas fa-save me-2 text-indigo-300"></i> RESTAURANTI KAYDET
                            </button>
                            <a href="/admin/restaurants" class="w-full inline-flex items-center justify-center bg-slate-800 text-slate-400 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-700 transition-all border border-slate-700">
                                VAZGEÇ
                            </a>
                        </div>

                        @php
                            $admin = \App\Models\Admin::find(auth()->id());
                            $city = \App\Models\City::find($admin->city_id);
                        @endphp
                        <div class="d-none">
                            <select id="city-select">
                                <option value="{{$city->id}}" data-lat="{{$city->lat}}" data-lng="{{$city->lng}}" selected>{{$city->name}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap" async defer></script>

    <script>
        // JS Kodlarında değişiklik yapmadım, sadece map stili için entegrasyon sağladım.
        let map, marker, autocomplete, geocoder;

        function initMap() {
            geocoder = new google.maps.Geocoder();
            const cityOption = document.querySelector('#city-select option');
            const initialPos = {
                lat: parseFloat(cityOption.dataset.lat) || 39.9208,
                lng: parseFloat(cityOption.dataset.lng) || 32.8541
            };

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 13,
                mapTypeControl: false,
                styles: [
                    { "featureType": "all", "elementType": "geometry.fill", "stylers": [{ "weight": "2.00" }] },
                    { "featureType": "all", "elementType": "geometry.stroke", "stylers": [{ "color": "#9c9c9c" }] },
                    { "featureType": "all", "elementType": "labels.text", "stylers": [{ "visibility": "on" }] },
                    { "featureType": "landscape", "elementType": "all", "stylers": [{ "color": "#f2f2f2" }] },
                    { "featureType": "poi", "elementType": "all", "stylers": [{ "visibility": "off" }] },
                    { "featureType": "road", "elementType": "all", "stylers": [{ "saturation": -100 }, { "lightness": 45 }] },
                    { "featureType": "transit", "elementType": "all", "stylers": [{ "visibility": "off" }] },
                    { "featureType": "water", "elementType": "all", "stylers": [{ "color": "#cbd5e1" }, { "visibility": "on" }] }
                ]
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true,
                icon: 'https://maps.google.com/mapfiles/ms/icons/red-pushpin.png'
            });

            updateInputs(initialPos.lat, initialPos.lng);

            const input = document.getElementById("pac-input");
            input.addEventListener("keydown", (e) => { if (e.key === "Enter") e.preventDefault(); });

            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;
                if (place.geometry.viewport) map.fitBounds(place.geometry.viewport);
                else { map.setCenter(place.geometry.location); map.setZoom(17); }
                marker.setPosition(place.geometry.location);
                updateInputs(place.geometry.location.lat(), place.geometry.location.lng());
                if (place.formatted_address) document.getElementById("address_field").value = place.formatted_address;
            });

            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                updateInputs(e.latLng.lat(), e.latLng.lng());
                geocodeAddress(e.latLng);
            });

            marker.addListener("dragend", () => {
                const pos = marker.getPosition();
                updateInputs(pos.lat(), pos.lng());
                geocodeAddress(pos);
            });
        }

        function updateInputs(lat, lng) {
            document.getElementById("lat").value = lat;
            document.getElementById("lng").value = lng;
        }

        function geocodeAddress(latLng) {
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    document.getElementById("address_field").value = results[0].formatted_address;
                }
            });
        }

        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
@endsection
