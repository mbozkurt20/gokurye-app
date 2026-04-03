@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4 px-md-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none italic">
                    PROFİL <span class="text-indigo-600">AYARLARI</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Restoran lokasyon bilgileri ve operasyonel limitlerinizi güncelleyin.
                </p>
            </div>
        </div>

        @if(session()->has('error'))
            <div class="bg-indigo-50 border border-indigo-100 text-indigo-600 px-6 py-4 rounded-[20px] mb-6 flex items-center justify-between shadow-sm shadow-indigo-100/50">
                <span class="text-xs font-black uppercase tracking-widest"><i class="fas fa-check-circle me-2"></i> {{ session()->get('success') }}</span>
                <button type="button" class="border-0 bg-transparent text-indigo-400 hover:text-indigo-600" onclick="this.parentElement.style.display='none';">×</button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-xl-7">
                <div class="bg-white !rounded-[40px] border border-slate-50 shadow-xl shadow-slate-200/50 p-6 overflow-hidden relative">
                    <div class="mb-4">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-map-location-dot text-indigo-600"></i> RESTORAN KONUMU
                        </h4>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 italic">Haritaya tıklayarak veya arama yaparak imleci tam yerinize taşıyın.</p>
                    </div>

                    <div class="relative group">
                        <input id="map-search-input" type="text"
                               class="absolute top-4 left-4 z-10 w-80 bg-white border-0 shadow-2xl rounded-2xl py-3 px-5 text-xs font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all"
                               placeholder="Adres arayın...">
                        <div id="userMap" style="height: 550px; width: 100%; border-radius: 30px;" class="border-4 border-slate-50 shadow-inner"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="bg-white !rounded-[40px] border border-slate-50 shadow-xl shadow-slate-200/50 p-8 h-100">
                    <form action="{{ route('restaurant.profile.update') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-s-4 border-indigo-500 ps-3">TEMEL BİLGİLER</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">İsim</label>
                                    <input type="text" name="name" class="form-control !rounded-2xl border-0 bg-slate-50 p-3 font-bold text-slate-700 shadow-inner text-xs focus:bg-white" value="{{ old('name', auth()->user()->name) }}">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Telefon</label>
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => auth()->user()->phone])
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Yeni Şifre</label>
                                    <input type="password" name="password" class="form-control !rounded-2xl border-0 bg-slate-50 p-3 font-bold text-slate-700 shadow-inner text-xs focus:bg-white" placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-8">
                            <div class="col-6">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Enlem</label>
                                <input readonly type="text" name="latitude" id="latitude" class="form-control !rounded-xl border-0 bg-slate-50 p-3 font-bold text-slate-400 shadow-inner text-xs" value="{{ auth()->user()->latitude }}">
                            </div>
                            <div class="col-6">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Boylam</label>
                                <input readonly type="text" name="longitude" id="longitude" class="form-control !rounded-xl border-0 bg-slate-50 p-3 font-bold text-slate-400 shadow-inner text-xs" value="{{ auth()->user()->longitude }}">
                            </div>
                        </div>

                        <button type="submit" class="w-100 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] border-0 shadow-xl shadow-indigo-100 transition-all hover:bg-indigo-700 active:scale-95">
                            AYARLARI KAYDET
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Slider Indigo Stil */
        .custom-slider-indigo { height: 6px; background: #e0e7ff; border-radius: 5px; -webkit-appearance: none; }
        .custom-slider-indigo::-webkit-slider-thumb { -webkit-appearance: none; width: 18px; height: 18px; border-radius: 50%; background: #4f46e5; cursor: pointer; border: 3px solid #fff; box-shadow: 0 0 12px rgba(79, 70, 229, 0.4); }

        .custom-slider-dark { height: 6px; background: #e2e8f0; border-radius: 5px; -webkit-appearance: none; }
        .custom-slider-dark::-webkit-slider-thumb { -webkit-appearance: none; width: 18px; height: 18px; border-radius: 50%; background: #1e293b; cursor: pointer; border: 3px solid #fff; }

        .form-control:focus { box-shadow: none !important; }
        .gm-style-parent { border-radius: 30px !important; overflow: hidden; }
    </style>

    <script>
        function updateRangeValues() {
            $('#dist_val').text($('#distance_limit_km').val() + ' km');
            $('#pkg_val').text($('#max_package_limit').val() + ' Paket');
        }

        $(document).on('input', '#distance_limit_km, #max_package_limit', function() {
            updateRangeValues();
        });

        function initMap() {
            const existingLat = parseFloat("{{ auth()->user()->latitude }}") || 37.1502;
            const existingLng = parseFloat("{{ auth()->user()->longitude }}") || 38.7790;
            const initialPos = { lat: existingLat, lng: existingLng };

            map = new google.maps.Map(document.getElementById("userMap"), {
                center: initialPos,
                zoom: 15,
                mapTypeControl: false,
                styles: [
                    { "featureType": "poi.business", "stylers": [{"visibility": "off"}] }
                ]
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true,
                icon: {
                    path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                    scale: 8,
                    fillColor: "#4f46e5", // Indigo marker
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: "#ffffff",
                },
                animation: google.maps.Animation.DROP
            });

            const input = document.getElementById("map-search-input");
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                updateInputs(place.geometry.location.lat(), place.geometry.location.lng());
            });

            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                updateInputs(e.latLng.lat(), e.latLng.lng());
            });

            marker.addListener("dragend", (e) => {
                updateInputs(e.latLng.lat(), e.latLng.lng());
            });
        }

        function updateInputs(lat, lng) {
            $('#latitude').val(lat.toFixed(8));
            $('#longitude').val(lng.toFixed(8));
        }

        $(document).ready(function() {
            updateRangeValues();
            initMap();
        });
    </script>
@endsection
