@extends('restaurant.layouts.app')
@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            border: #259a38 solid 2px;
            height: 500px; /* ya da istediğin başka bir yükseklik */
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
    </style>


    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kuryeler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/restaurant/couriers">Kuryeler</a></li>
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

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Kurye Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{ route('restaurant.couriers.update') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{$courier->id}}">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label for="form-text" class="form-label fs-14 text-dark">Kurye Adı</label>
                                        <input required type="text" class="form-control" value="{{$courier->name}}" name="name" id="form-text" placeholder="Kurye Adı">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Telefonu</label>
                                        <input required type="text" class="form-control" value="{{$courier->phone}}" name="phone" id="form-text" placeholder="0532 532 0000">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Şifresi</label>
                                        <input type="text" class="form-control" value="" name="password" id="form-text" placeholder="Şifre belirleyin">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="price-type" class="form-label fs-14 text-dark">Ödeme Türü </label>
                                        <select class="form-control" name="price_type" id="price-type">
                                            <option {{$courier->price_type == 'package' ? 'selected' : null}} value="package">Paket Başı</option>
                                            <option {{$courier->price_type == 'fixed' ? 'selected' : null}} value="fixed">Sabit + Km Ücreti</option>
                                        </select>
                                    </div>

                                    <div id="fixed-fields" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Sabit Ücret</label>
                                        <input value="{{$courier->fixed_price}}" required type="text" class="form-control" name="fixed_price" placeholder="25.000">
                                    </div>
                                    <div id="fixed-fields2" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Km Ücreti (1 km göre giriniz)</label>
                                        <input value="{{$courier->km_price}}" required type="text" class="form-control" name="km_price" placeholder="4,00">
                                    </div>

                                    <div id="package-fields" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Paket Baş. Ücreti </label>
                                        <input value="{{$courier->price}}" required type="text" class="form-control" name="price" placeholder="10,00">
                                    </div>

                                    <div class="mt-5 mb-3">
                                        <p class="text-danger fw-bold">Lütfen haritadan konum işaratlemesi yapınız.</p>
                                        <div id="map"></div>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Enlem</label>
                                        <input required  value="{{$courier->latitude}}" type="text" class="form-control" name="latitude" id="lat"
                                               placeholder="Enlem Giriniz">
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Boylam</label>
                                        <input required  value="{{$courier->longitude}}" type="text" class="form-control" name="longitude" id="lng"
                                               placeholder="Boylam Giriniz">
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Durum</label>
                                        <select class="form-control" name="status" id="">
                                            <option  {{$courier->status == App\Helpers\CourierStatus::active ? 'selected' : null}} value="{{App\Helpers\CourierStatus::active}}">Aktif</option>
                                            <option {{$courier->status == App\Helpers\CourierStatus::passive ? 'selected' : null}} value="{{App\Helpers\CourierStatus::passive}}">Pasif</option>
                                            <option {{$courier->status == App\Helpers\CourierStatus::break ? 'selected' : null}} value="{{App\Helpers\CourierStatus::break}}">Molada</option>
                                            <option {{$courier->status == App\Helpers\CourierStatus::service ? 'selected' : null}} value="{{App\Helpers\CourierStatus::service}}">Serviste</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="special-button mt-4 float-end">Kaydı Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const priceTypeSelect = document.getElementById('price-type');
            const packageFields = document.getElementById('package-fields');
            const fixedFields = document.getElementById('fixed-fields');
            const fixedFields2 = document.getElementById('fixed-fields2');

            const packageInput = packageFields.querySelector('input');
            const fixedInput1 = fixedFields.querySelector('input');
            const fixedInput2 = fixedFields2.querySelector('input');

            function toggleFields() {
                const selectedType = priceTypeSelect.value;

                if (selectedType === 'package') {
                    // Göster
                    packageFields.style.display = 'block';
                    // Gizle
                    fixedFields.style.display = 'none';
                    fixedFields2.style.display = 'none';

                    // Required ayarları
                    packageInput.required = true;
                    fixedInput1.required = false;
                    fixedInput2.required = false;
                } else {
                    // Göster
                    fixedFields.style.display = 'block';
                    fixedFields2.style.display = 'block';
                    // Gizle
                    packageFields.style.display = 'none';

                    // Required ayarları
                    packageInput.required = false;
                    fixedInput1.required = true;
                    fixedInput2.required = true;
                }
            }

            // Seçim değiştiğinde çalıştır
            priceTypeSelect.addEventListener('change', toggleFields);

            // Sayfa ilk yüklendiğinde çalıştır
            toggleFields();
        });
    </script>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var existingLat = {{ auth()->user()->latitude ?? '37.15026069044849' }};
        var existingLng = {{ auth()->user()->longitude ?? '38.77905463205474' }};
        var map;

        if (existingLat && existingLng) {
            map = L.map('map').setView([existingLat, existingLng], 13);
            marker = L.marker([existingLat, existingLng]).addTo(map);
        } else {
            map = L.map('map').setView([39.9208, 32.8541], 6); // Türkiye geneli
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng]).addTo(map);

            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        });

    </script>
@endsection
