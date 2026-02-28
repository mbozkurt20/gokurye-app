@extends('superadmin.layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    YENİ <span class="text-slate-400">YÖNETİCİ KAYDI</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sisteme yeni bir partner veya bölge yöneticisi tanımlayın.
                </p>
            </div>
            <a href="{{ url('superadmin/admin') }}" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
        </div>

        <form method="post" action="{{route('superadmin.admin_create_request')}}">
            @csrf

            <div class="row g-4">
                <div class="col-xl-7">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden mb-4">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">HESAP VE KİMLİK BİLGİLERİ</h4>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-emerald-600 uppercase">KONTÖR BİRİM FİYATI:</span>
                                <input required type="text" name="top_up_price" placeholder="0.00"
                                       class="w-24 bg-emerald-500 text-white border-0 rounded-lg px-2 py-1 text-[11px] font-black text-center focus:ring-0 placeholder:text-emerald-200">
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">YÖNETİCİ / PARTNER ADI</label>
                                    <input required type="text" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="name" placeholder="Örn: Kuzey Bölge Lojistik">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">E-POSTA ADRESİ</label>
                                    <input required type="email" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="email" placeholder="admin@sirket.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">GİRİŞ PAROLASI</label>
                                    <input required type="text" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="password" placeholder="Güçlü bir şifre girin">
                                </div>
                                <div class="col-md-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">İLETİŞİM NUMARASI</label>
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ŞEHİR SEÇİMİ</label>
                                    <select required class="form-control select2" name="city_id" id="city-select">
                                        <option value="">Şehir Seçin</option>
                                        @foreach($cities as $city)
                                            <option value="{{$city->id}}" data-lat="{{$city->lat}}" data-lng="{{$city->lng}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">İLÇE SEÇİMİ</label>
                                    <select required class="form-control select2" name="district_id" id="district-select">
                                        <option value="">Önce Şehir Seçin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden h-full">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">LOKASYON VE ADRES TANIMI</h4>
                        </div>
                        <div class="p-8">
                            <div class="relative mb-6">
                                <input id="pac-input" class="form-control !rounded-2xl border-slate-200 bg-white p-4 pl-12 font-bold text-slate-600 shadow-sm focus:ring-2 focus:ring-indigo-100 transition-all" type="text" placeholder="Adres, mahalle veya mekan arayın...">
                                <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-indigo-500"></i>
                            </div>

                            <div id="map" class="!rounded-[30px] border-4 border-slate-50 shadow-inner mb-6" style="height: 380px;"></div>

                            <div class="row g-3 mb-6">
                                <div class="col-6">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 block">LATITUDE</label>
                                    <input required type="text" class="form-control !rounded-xl bg-slate-100 border-0 p-3 text-xs font-black text-slate-500" id="latit" name="lat" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 block">LONGITUDE</label>
                                    <input required type="text" class="form-control !rounded-xl bg-slate-100 border-0 p-3 text-xs font-black text-slate-500" id="longi" name="lng" readonly>
                                </div>
                            </div>

                            <div class="mb-8">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">TAM ADRES METNİ</label>
                                <textarea id="address_field" rows="3" name="address" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" placeholder="Haritadan otomatik gelir..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#0f172a] text-white py-5 !rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:scale-[1.02] transition-all border-0">
                                KAYDI SİSTEME EKLE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .select2-container--default .select2-selection--single {
            border: 2px solid #f1f5f9 !important;
            background-color: rgba(248, 250, 252, 0.5) !important;
            border-radius: 1rem !important;
            height: 58px !important;
            padding: 14px !important;
            font-weight: 700 !important;
            color: #334155 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { top: 16px !important; right: 15px !important; }
        .select2-dropdown { border: 0 !important; border-radius: 1rem !important; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1) !important; padding: 10px !important; }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap" async defer></script>

    <script>
        let map, marker, autocomplete, geocoder;

        function initMap() {
            geocoder = new google.maps.Geocoder();
            const initialPos = { lat: {{ $dealer->latitude ?? 37.1502 }}, lng: {{ $dealer->longitude ?? 38.7790 }} };

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 13,
                mapTypeControl: false,
                streetViewControl: false
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            const input = document.getElementById("pac-input");
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;
                map.setCenter(place.geometry.location);
                map.setZoom(17);
                updateLocationInputs(place.geometry.location);
                if(place.formatted_address) document.getElementById("address_field").value = place.formatted_address;
            });

            map.addListener("click", (e) => { updateLocationInputs(e.latLng); geocodeAddress(e.latLng); });
            marker.addListener("dragend", () => { updateLocationInputs(marker.getPosition()); geocodeAddress(marker.getPosition()); });
        }

        function geocodeAddress(latLng) {
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    document.getElementById("address_field").value = results[0].formatted_address;
                }
            });
        }

        function updateLocationInputs(location) {
            marker.setPosition(location);
            const lat = typeof location.lat === 'function' ? location.lat() : location.lat;
            const lng = typeof location.lng === 'function' ? location.lng() : location.lng;
            document.getElementById("latit").value = lat.toFixed(6);
            document.getElementById("longi").value = lng.toFixed(6);
        }

        $(document).ready(function () {
            $('.select2').select2();

            $('#city-select').on('change', function () {
                const cityId = $(this).val();
                const selected = $(this).find('option:selected');
                const lat = parseFloat(selected.data('lat'));
                const lng = parseFloat(selected.data('lng'));

                if (lat && lng && map) {
                    const newPos = { lat: lat, lng: lng };
                    map.setCenter(newPos);
                    map.setZoom(12);
                    updateLocationInputs(newPos);
                    geocodeAddress(newPos);
                }

                if (cityId) {
                    $.get('/superadmin/get-districts/' + cityId, function (data) {
                        let options = '<option value="">İlçe Seç</option>';
                        $.each(data, function (key, value) {
                            options += `<option value="${value.id}">${value.name}</option>`;
                        });
                        $('#district-select').html(options).trigger('change');
                    });
                }
            });
        });
    </script>
@endsection
