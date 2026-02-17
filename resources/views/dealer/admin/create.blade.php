@extends('dealer.layouts.app')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
            <h2 class="mb-3 me-auto">Yönetici Ekle</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yönetici</a></li>
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

        @if($errors->any())
            {{ implode('', $errors->all('<div>:message</div>')) }}
        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Yönetici Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('dealer.admin_create_request')}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Yönetici Adı</label>
                                        <input required type="text" class="form-control" name="name" placeholder="Yönetici Adı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Email</label>
                                        <input required type="text" class="form-control" name="email"
                                               placeholder="Email">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Telefon</label>
                                        <input required type="tel" class="form-control" name="phone"
                                               placeholder="Telefon Numarası">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Parola</label>
                                        <input required type="text" class="form-control" name="password"
                                               placeholder="Şifre Giriniz">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Şehir</label>
                                        <select required class="form-control select2" name="city_id" id="city-select">
                                            <option value="">Şehir Seç</option>
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}" data-lat="{{$city->lat}}" data-lng="{{$city->lng}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">İlçe</label>
                                        <select required class="form-control select2" name="district_id" id="district-select">
                                            <option value="">İlçe Seç</option>
                                        </select>
                                    </div>

                                    <div class="mt-5 mb-3">
                                        <p class="text-danger fw-bold">Lütfen haritadan konum işaratlemesi yapınız.</p>
                                        <div id="map"></div>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label text-black">Lokasyon (Latitude)</label>
                                        <input required type="text" class="form-control" id="latit" name="lat" placeholder=""  >
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label text-black">Lokasyon (Longitude)</label>
                                        <input required type="text" id="longi" class="form-control" name="lng" placeholder="" >
                                    </div>
                                </div>

                                <div class="mb-3 mt-3">
                                    <label for="adres" class="form-label text-black">Adres (opsiyonel)</label>
                                    <textarea rows="12" name="address" class="form-control"></textarea>
                                </div>

                                <button type="submit" class="float-end special-button">Kaydı Tamamla</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        $(document).ready(function () {

            var existingLat = {{ $dealer->latitude ?? '37.15026069044849' }};
            var existingLng = {{ $dealer->longitude ?? '38.77905463205474' }};
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

                document.getElementById('latit').value = lat;
                document.getElementById('longi').value = lng;
            });


            $('.select2').select2();

            $('#city-select').on('change', function () {
                var cityId = $(this).val();
                var selectedOption = $(this).find('option:selected');
                var lat = selectedOption.data('lat');
                var lng = selectedOption.data('lng');

                if (lat && lng && map) {
                    map.setView([lat, lng], 13); // Harita o şehre odaklanır

                    if (marker) {
                        map.removeLayer(marker);
                    }
                    marker = L.marker([lat, lng]).addTo(map);

                    $('#latit').val(lat);
                    $('#longi').val(lng);
                }

                if (cityId) {
                    $.ajax({
                        url: '/dealer/get-districts/' + cityId,
                        type: 'GET',
                        success: function (data) {
                            $('#district-select').empty();
                            $('#district-select').append('<option value="">İlçe Seç</option>');
                            $.each(data, function (key, value) {
                                $('#district-select').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#district-select').empty();
                    $('#district-select').append('<option value="">İlçe Seç</option>');
                }
            });
        });
    </script>
@endsection


