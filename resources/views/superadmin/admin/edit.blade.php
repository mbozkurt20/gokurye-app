@extends('superadmin.layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    YÖNETİCİ <span class="text-slate-400">PROFİLİNİ GÜNCELLE</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Partner: <span class="text-indigo-600">#{{$admin->code}} - {{$admin->name}}</span>
                </p>
            </div>
            <a href="{{ url('superadmin/admin')  }}" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
        </div>

        @if(session()->has('message'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-indigo-500 shadow-2xl rounded-2xl p-5 animate-bounce-short">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-50 p-2 rounded-xl"><i class="fas fa-check-circle text-indigo-600 text-lg"></i></div>
                    <div class="ml-4 flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">BAŞARILI</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-slate-300"><i class="fas fa-times"></i></button>
                </div>
            </div>
        @endif

        <form method="post" action="{{route('superadmin.admin_update', $admin->id)}}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-xl-7">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden mb-4">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">KİMLİK VE BÖLGE VERİLERİ</h4>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-indigo-600 uppercase">KONTÖR ÜCRETİ:</span>
                                <input type="text" name="top_up_price" value="{{ number_format($admin->top_up_price, 2) }}"
                                       class="w-24 bg-indigo-600 text-white border-0 rounded-lg px-2 py-1 text-[11px] font-black text-center focus:ring-0">
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">AD SOYAD</label>
                                    <input type="text" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="name" value="{{ $admin->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">EMAIL ADRESİ</label>
                                    <input type="email" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="email" value="{{ $admin->email }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">TELEFON NUMARASI</label>
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => $admin->phone])
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">HİZMET ŞEHRİ</label>
                                    <select required class="form-control select2" name="city_id" id="city-select">
                                        <option value="">Şehir Seç</option>
                                        @foreach($cities as $city)
                                            <option {{$admin->city_id == $city->id ? 'selected' : ''}} value="{{$city->id}}" data-lat="{{$city->lat}}" data-lng="{{$city->lng}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">HİZMET İLÇESİ</label>
                                    <select required class="form-control select2" name="district_id" id="district-select">
                                        <option value="">İlçe Seç</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ADRES DETAYI</label>
                                    <textarea rows="3" name="address" id="address_field" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all">{{ $admin->address }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden h-full">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">KONUM VE GPS VERİLERİ</h4>
                        </div>
                        <div class="p-8">
                            <div class="relative mb-6">
                                <input id="pac-input" class="form-control !rounded-2xl border-slate-200 bg-white p-4 pl-12 font-bold text-slate-600 shadow-sm focus:ring-2 focus:ring-indigo-100 transition-all" type="text" placeholder="Haritada adres ara...">
                                <i class="fa-solid fa-location-dot absolute left-5 top-1/2 -translate-y-1/2 text-indigo-500"></i>
                            </div>

                            <div id="map" class="!rounded-[30px] border-4 border-slate-50 shadow-inner mb-6" style="height: 350px;"></div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 block">ENLEM (LAT)</label>
                                    <input type="text" class="form-control !rounded-xl bg-slate-100 border-0 p-3 text-xs font-black text-slate-500" id="latit" name="lat" value="{{$admin->latitude}}" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 block">BOYLAM (LNG)</label>
                                    <input type="text" class="form-control !rounded-xl bg-slate-100 border-0 p-3 text-xs font-black text-slate-500" id="longi" name="lng" value="{{$admin->longitude}}" readonly>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-col gap-3">
                                <button type="submit" class="w-full bg-[#0f172a] text-white py-5 !rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:scale-[1.02] transition-all border-0">
                                    DEĞİŞİKLİKLERİ KAYDET
                                </button>
                                <p class="text-[9px] text-center font-bold text-slate-300 uppercase tracking-widest italic">Son Güncelleme: {{ $admin->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
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
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google.maps_key')}}&libraries=places&callback=initMap" async defer></script>

    <script>
        let map, marker, autocomplete, geocoder;

        function initMap() {
            geocoder = new google.maps.Geocoder();
            const existingPos = {
                lat: parseFloat("{{ $admin->latitude }}") || 37.1502,
                lng: parseFloat("{{ $admin->longitude }}") || 38.7790
            };

            map = new google.maps.Map(document.getElementById("map"), {
                center: existingPos,
                zoom: 14,
                styles: [ /* Google Maps Gece/Modern Teması İstersen Buraya Stil Gelebilir */ ],
                mapTypeControl: false,
                streetViewControl: false
            });

            marker = new google.maps.Marker({
                position: existingPos,
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
                updateInputs(place.geometry.location);
                if(place.formatted_address) document.getElementById("address_field").value = place.formatted_address;
            });

            map.addListener("click", (e) => { updateInputs(e.latLng); geocodeLatLng(e.latLng); });
            marker.addListener("dragend", () => { updateInputs(marker.getPosition()); geocodeLatLng(marker.getPosition()); });
        }

        function updateInputs(latLng) {
            marker.setPosition(latLng);
            document.getElementById("latit").value = latLng.lat().toFixed(6);
            document.getElementById("longi").value = latLng.lng().toFixed(6);
        }

        function geocodeLatLng(latLng) {
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    document.getElementById("address_field").value = results[0].formatted_address;
                }
            });
        }

        $(document).ready(function () {
            $('.select2').select2();

            $('#city-select').on('change', function () {
                const cityId = $(this).val();
                const selected = $(this).find('option:selected');
                const lat = parseFloat(selected.data('lat'));
                const lng = parseFloat(selected.data('lng'));

                if (lat && lng && map) {
                    const newPos = new google.maps.LatLng(lat, lng);
                    map.setCenter(newPos);
                    updateInputs(newPos);
                }

                if (cityId) {
                    let districtId = {{ $admin->district_id ?? 'null' }};
                    $.get('/superadmin/get-districts/' + cityId, function (data) {
                        let html = '<option value="">İlçe Seç</option>';
                        $.each(data, function (i, item) {
                            html += `<option value="${item.id}" ${item.id == districtId ? 'selected' : ''}>${item.name}</option>`;
                        });
                        $('#district-select').html(html).trigger('change');
                    });
                }
            });

            const initialCity = $('#city-select').val();
            if(initialCity) {
                let districtId = {{ $admin->district_id ?? 'null' }};
                $.get('/superadmin/get-districts/' + initialCity, function (data) {
                    let html = '<option value="">İlçe Seç</option>';
                    $.each(data, function (i, item) {
                        html += `<option value="${item.id}" ${item.id == districtId ? 'selected' : ''}>${item.name}</option>`;
                    });
                    $('#district-select').html(html);
                });
            }
        });
    </script>
@endsection
