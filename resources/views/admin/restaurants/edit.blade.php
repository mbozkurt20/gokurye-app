@extends('admin.layouts.app')
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
            <h2 class="mb-3 me-auto">Restaurantlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/restaurants">Restaurantlar</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $restaurant->restaurant_name }}</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Düzenle</a></li>
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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">İşyeri Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{ route('admin.restaurants.update') }}">
                                @csrf
                                <input type="hidden" value="{{ $restaurant->id }}" name="id">

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">İşyeri Adı</label>
                                        <input type="text" class="form-control" name="restaurant_name"
                                               value="{{ $restaurant->restaurant_name }}" placeholder="İşyeri Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Yetkili Adı</label>
                                        <input type="text" class="form-control" name="name"
                                               value="{{ $restaurant->name }}" placeholder="Yetkili Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">E-posta Adresi</label>
                                        <input type="email" class="form-control" name="email"
                                               value="{{ $restaurant->email }}" placeholder="E-posta Adresi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon</label>
                                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => $restaurant->phone])
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Şifre</label>
                                        <input type="password" class="form-control" name="password"
                                               placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Vergi Dairesi</label>
                                        <input type="text" class="form-control" name="tax_name"
                                               value="{{ $restaurant->tax_name }}" placeholder="Vergi Dairesi">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Vergi Numarası</label>
                                        <input type="text" class="form-control" name="tax_number"
                                               value="{{ $restaurant->tax_number }}" placeholder="Vergi Numarası">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Paket Fiyatı</label>
                                        <input type="text" class="form-control" name="package_price"
                                               value="{{ $restaurant->package_price }}" placeholder="Paket Fiyatı">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Durum</label>
                                        <select class="form-control" name="status">
                                            <option value="active" {{ $restaurant->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="deactive" {{ $restaurant->status == 'deactive' ? 'selected' : '' }}>Pasif</option>
                                        </select>
                                    </div>

                                    <div class="mt-4 mb-3 col-md-12">
                                        <label class="form-label fw-bold text-primary">Haritada Konum Güncelle</label>
                                        <div class="map-search-container">
                                            <input id="pac-input" class="form-control mb-2" type="text" placeholder="Adres ara veya konumu sürükle...">
                                        </div>
                                        <div id="map"></div>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label text-dark">Enlem (Latitude)</label>
                                        <input required value="{{$restaurant->latitude}}" type="text" class="form-control" name="latitude" id="lat" readonly>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label text-dark">Boylam (Longitude)</label>
                                        <input required value="{{$restaurant->longitude}}" type="text" class="form-control" name="longitude" id="lng" readonly>
                                    </div>

                                    <div class="mb-3 col-md-12">
                                        <label class="form-label text-black">Açık Adres (Düzenleyebilirsiniz)</label>
                                        <textarea id="address_field" rows="4" name="address" class="form-control" required placeholder="İşyeri Adresi">{{ $restaurant->address }}</textarea>
                                    </div>
                                </div>

                                <button type="submit" class="special-button btn btn-primary float-end">Bilgileri Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap" async defer></script>

    <script>
        let map, marker, autocomplete, geocoder;

        function initMap() {
            geocoder = new google.maps.Geocoder();

            // Mevcut koordinatlar
            const existingPos = {
                lat: parseFloat("{{ $restaurant->latitude }}") || 37.1502,
                lng: parseFloat("{{ $restaurant->longitude }}") || 38.7790
            };

            map = new google.maps.Map(document.getElementById("map"), {
                center: existingPos,
                zoom: 15,
                mapTypeControl: true
            });

            marker = new google.maps.Marker({
                position: existingPos,
                map: map,
                draggable: true // İşaretçi sürüklenebilir
            });

            // Arama Kutusu (Autocomplete)
            const input = document.getElementById("pac-input");

            // Enter tuşuyla formun yanlışlıkla gönderilmesini engelle
            input.addEventListener("keydown", function(e) {
                if (e.key === "Enter") { e.preventDefault(); }
            });

            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                marker.setPosition(place.geometry.location);
                updateInputs(place.geometry.location.lat(), place.geometry.location.lng());

                if (place.formatted_address) {
                    document.getElementById("address_field").value = place.formatted_address;
                }
            });

            // Haritaya tıklandığında konumu güncelle
            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                updateInputs(e.latLng.lat(), e.latLng.lng());
                geocodeLatLng(e.latLng);
            });

            // İşaretçi sürüklendiğinde konumu güncelle
            marker.addListener("dragend", () => {
                const pos = marker.getPosition();
                updateInputs(pos.lat(), pos.lng());
                geocodeLatLng(pos);
            });
        }

        function updateInputs(lat, lng) {
            document.getElementById("lat").value = lat;
            document.getElementById("lng").value = lng;
        }

        function geocodeLatLng(latLng) {
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === "OK" && results[0]) {
                    document.getElementById("address_field").value = results[0].formatted_address;
                }
            });
        }
    </script>
@endsection
