@extends('superadmin.layouts.app')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        #map {
            border: #259a38 solid 2px;
            height: 500px;
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .map-search-container {
            margin-bottom: 15px;
        }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Yönetici Güncelle</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yönetici</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Güncelle</a></li>
                </ol>
            </div>
        </div>

         @if(session()->has('message'))
        <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-green-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out animate-bounce-short">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 p-2 rounded-xl">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">İşlem Başarılı</p>
                    <p class="text-sm font-bold text-slate-700 leading-tight">
                        {{ session()->get('message') }}
                    </p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session()->has('test'))
        <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 p-2 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Hata Oluştu</p>
                    <p class="text-sm font-bold text-slate-700 leading-tight">
                        {{ session()->get('test') }}
                    </p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
    @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Yönetici Bilgileri</h4></div>
                    <div class="card-body">
                        <form method="post" action="{{route('superadmin.admin_update', $admin->id)}}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label text-success fw-bold">Tanımlı Kontör Ücreti</label>
                                    <input type="text" class="form-control border-success" name="top_up_price" value="{{ number_format($admin->top_up_price, 2) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control" name="name" value="{{ $admin->name }}">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ $admin->email }}">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Telefon</label>
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => $admin->phone])
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Şehir</label>
                                    <select required class="form-control select2" name="city_id" id="city-select">
                                        <option value="">Şehir Seç</option>
                                        @foreach($cities as $city)
                                            <option {{$admin->city_id == $city->id ? 'selected' : ''}}
                                                    value="{{$city->id}}"
                                                    data-lat="{{$city->lat}}"
                                                    data-lng="{{$city->lng}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">İlçe</label>
                                    <select required class="form-control select2" name="district_id" id="district-select">
                                        <option value="">İlçe Seç</option>
                                    </select>
                                </div>
                            </div>

                            <hr>
                            <div class="mt-4 mb-3">
                                <label class="form-label fw-bold text-primary">Haritada Konum Güncelle</label>
                                <div class="map-search-container">
                                    <input id="pac-input" class="form-control mb-2" type="text" placeholder="Adres ara...">
                                </div>
                                <div id="map"></div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Enlem (Lat)</label>
                                    <input type="text" class="form-control" id="latit" name="lat" value="{{$admin->latitude}}" readonly>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Boylam (Lng)</label>
                                    <input type="text" class="form-control" id="longi" name="lng" value="{{$admin->longitude}}" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Adres Detayı</label>
                                <textarea rows="4" name="address" id="address_field" class="form-control">{{ $admin->address }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary float-end">Güncelle</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap" async defer></script>

    <script>
        let map, marker, autocomplete, geocoder;

        function initMap() {
            geocoder = new google.maps.Geocoder();

            // Mevcut koordinatlar veya varsayılan Urfa
            const existingPos = {
                lat: parseFloat("{{ $admin->latitude }}") || 37.1502,
                lng: parseFloat("{{ $admin->longitude }}") || 38.7790
            };

            map = new google.maps.Map(document.getElementById("map"), {
                center: existingPos,
                zoom: 14,
                mapTypeControl: true
            });

            marker = new google.maps.Marker({
                position: existingPos,
                map: map,
                draggable: true
            });

            // Autocomplete (Arama Kutusu)
            const input = document.getElementById("pac-input");
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);
            input.addEventListener("keydown", function(e) {
                if (e.keyCode === 13) { // 13, Enter tuşunun kodudur
                    e.preventDefault();
                }
            });

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                map.setCenter(place.geometry.location);
                map.setZoom(17);
                updateInputs(place.geometry.location);
                if(place.formatted_address) document.getElementById("address_field").value = place.formatted_address;
            });

            // Harita Tıklama
            map.addListener("click", (e) => {
                updateInputs(e.latLng);
                geocodeLatLng(e.latLng);
            });

            // Marker Sürükleme
            marker.addListener("dragend", () => {
                updateInputs(marker.getPosition());
                geocodeLatLng(marker.getPosition());
            });
        }

        function updateInputs(latLng) {
            marker.setPosition(latLng);
            document.getElementById("latit").value = latLng.lat();
            document.getElementById("longi").value = latLng.lng();
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

            // İlk yüklemede ilçeleri getir ama haritayı adminin konumu bozmasın diye manuel tetikleme yapmıyoruz
            // veya sadece ilçe listesi için ajax çağırıyoruz:
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
