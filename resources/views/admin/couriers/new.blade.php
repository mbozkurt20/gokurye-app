@extends('admin.layouts.app')
@section('content')
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
            <h2 class="mb-3 me-auto">Yeni Kurye Ekle</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/couriers">Kuryeler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Kurye Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('admin.couriers.create')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Kurye Adı</label>
                                        <input value="{{old('name')}}" required type="text" class="form-control" name="name" placeholder="Kurye Adı">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Şifresi</label>
                                        <input required type="text" class="form-control" name="password" placeholder="Şifre belirleyin">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Telefonu</label>
                                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label for="price-type" class="form-label fs-14 text-dark">Ödeme Türü </label>
                                        <select class="form-control" name="price_type" id="price-type">
                                            <option value="package">Paket Başı</option>
                                            <option value="fixed">Sabit + Km Ücreti</option>
                                        </select>
                                    </div>

                                    <div id="fixed-fields" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Sabit Ücret</label>
                                        <input value="{{old('fixed_price')}}" type="text" class="form-control" name="fixed_price" placeholder="25.000">
                                    </div>
                                    <div id="fixed-fields2" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Km Ücreti</label>
                                        <input value="{{old('km_price')}}" type="text" class="form-control" name="km_price" placeholder="4,00">
                                    </div>
                                    <div id="fixed-fields3" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Km sonrası hesapla</label>
                                        <input value="{{old('km_distance_later')}}" type="number" class="form-control" name="km_distance_later" placeholder="2">
                                    </div>

                                    <div id="package-fields" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Paket Baş. Ücreti </label>
                                        <input type="text" class="form-control" name="price" placeholder="10,00">
                                    </div>

                                    <div class="mt-4 mb-3 col-md-12">
                                        <label class="form-label fw-bold text-primary">Kurye Başlangıç Konumunu Seçin</label>
                                        <div class="map-search-container">
                                            <input id="pac-input" class="form-control mb-2" type="text" placeholder="Bölge, sokak veya yer arayın...">
                                        </div>
                                        <div id="map"></div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark">Enlem (Latitude)</label>
                                        <input value="{{old('latitude')}}" required type="text" name="latitude" id="lat" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark">Boylam (Longitude)</label>
                                        <input value="{{old('longitude')}}" required type="text" name="longitude" id="lng" class="form-control" readonly>
                                    </div>
                                </div>

                                <button type="submit" class="special-button btn btn-success float-end">Kuryeyi Kaydet</button>
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
        // Ücret Türü Alanları Yönetimi
        document.addEventListener('DOMContentLoaded', function () {
            const priceTypeSelect = document.getElementById('price-type');
            const packageFields = document.getElementById('package-fields');
            const fixedFields = document.getElementById('fixed-fields');
            const fixedFields2 = document.getElementById('fixed-fields2');
            const fixedFields3 = document.getElementById('fixed-fields3');

            function toggleFields() {
                const isPackage = priceTypeSelect.value === 'package';
                packageFields.style.display = isPackage ? 'block' : 'none';
                fixedFields.style.display = isPackage ? 'none' : 'block';
                fixedFields2.style.display = isPackage ? 'none' : 'block';
                fixedFields3.style.display = isPackage ? 'none' : 'block';

                // Inputların required durumlarını güncelle
                packageFields.querySelector('input').required = isPackage;
                fixedFields.querySelector('input').required = !isPackage;
                fixedFields2.querySelector('input').required = !isPackage;
                fixedFields3.querySelector('input').required = !isPackage;
            }

            priceTypeSelect.addEventListener('change', toggleFields);
            toggleFields();
        });

        // Google Maps Yönetimi
        let map, marker, autocomplete;

        function initMap() {
            const initialPos = {
                lat: parseFloat("{{ auth()->user()->latitude }}") || 37.1502,
                lng: parseFloat("{{ auth()->user()->longitude }}") || 38.7790
            };

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 13,
                mapTypeControl: true
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true
            });

            updateInputs(initialPos.lat, initialPos.lng);

            // Arama Kutusu
            const input = document.getElementById("pac-input");
            input.addEventListener("keydown", (e) => { if (e.key === "Enter") e.preventDefault(); });

            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);
                updateInputs(place.geometry.location.lat(), place.geometry.location.lng());
            });

            // Harita Tıklama
            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                updateInputs(e.latLng.lat(), e.latLng.lng());
            });

            // Marker Sürükleme
            marker.addListener("dragend", () => {
                const pos = marker.getPosition();
                updateInputs(pos.lat(), pos.lng());
            });
        }

        function updateInputs(lat, lng) {
            document.getElementById("lat").value = lat;
            document.getElementById("lng").value = lng;
        }
    </script>
@endsection
