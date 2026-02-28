<!DOCTYPE html>
<html lang="tr" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Partner Başvurusu</title>

    <link rel="shortcut icon" type="image/png" href="{{config('site.logo')}}">
    <link href="{{asset('theme/login/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('theme/login/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/pages/restaurants/login/index.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .form-control,
        .select2-container--default .select2-selection--single {
            color: white;
            font-weight: bold;
        }

        .form-control::placeholder {
            color: white;
            font-weight: bold;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white !important;
            font-weight: bold;
        }

        /* Placeholder metni */
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: white !important;
            font-weight: bold;
        }

        input.form-control,
        textarea.form-control {
            color: white !important;
        }
        #map {
            border: #259a38 solid 2px;
            height: 500px; /* ya da istediğin başka bir yükseklik */
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .login-box2 {
            background: rgba(237, 223, 223, 0.2); /* Beyaz ve %20 opak */
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            backdrop-filter: blur(10px); /* Cam efekti için */
            -webkit-backdrop-filter: blur(10px);
        }

        .dealer-layout{
            display:grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap:24px;
        }
        @media(max-width: 992px){ .dealer-layout{grid-template-columns:1fr;} }

        .card{
            background: var(--card);
            border-radius: 16px;
            color: white;
            backdrop-filter: blur(6px);
            box-shadow: 0 10px 30px rgba(0,0,0,.25);
        }
        .card-header{padding:18px 20px; border-bottom:1px solid rgba(255,255,255,0.08); color:var(--text); font-weight:700;}
        .card-body{padding:20px;}

        .form-label{color:var(--text); font-weight:600}
        .form-control, .select2-container .select2-selection--single{
            background: var(--glass) !important;
            border-radius: 12px; border:1px solid rgba(0,0,0,.15);
            color:#111; height:44px; padding:10px 12px;
        }
        textarea.form-control{min-height:140px;}

        #map{width:100%; height:600px; border-radius:14px; background:#1e293b; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-weight:600;}
        .map-hint{ color: #fecaca; font-weight:700; margin-bottom:10px }

        .special-button{background:linear-gradient(135deg,var(--primary),#8b5cf6); color:white; border:none; border-radius:12px; padding:12px 18px; font-weight:700;}
        .special-button:hover{filter:brightness(1.05)}

        .custom-alert {
            display: inline-block; /* yazı uzunluğu kadar genişler */
            padding: 10px 14px;
            border-radius: 12px;
            margin: 10px 0;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        .custom-alert.success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
        }

        .custom-alert.error {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            color: #fff;
        }

        .close-btn {
            position: absolute;
            top: 6px;
            right: 8px;
            font-size: 16px;
            color: rgba(255,255,255,0.8);
            cursor: pointer;
            transition: color 0.2s;
        }
        .close-btn:hover {
            color: #fff;
        }

        /* küçük animasyon */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-box2">
        <div class="logo">
            <a href="{{route('restaurant.login')}}">
                <img src="{{config('site.logo')}}" alt="Logo">
            </a>
        </div>

         @if(session()->has('error'))
        <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-green-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out animate-bounce-short">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 p-2 rounded-xl">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">İşlem Başarılı</p>
                    <p class="text-sm font-bold text-slate-700 leading-tight">
                        {{ session()->get('success') }}
                    </p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
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

        <div class="dealer-layout">
            <div class="card">
                <div class="card-header">
                    <p class=" size-1">  Partner Başvurusu</p>

                    <p class="size-4">
                        <a class="text-white fw-bold" style="text-decoration: underline" href="/dealer/login">Zaten bir hesabım var...</a>
                    </p>
                </div>

                <div class="card-body">
                    <form method="post" action="{{ route('createDealerRequest') }}">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Partner Adı</label>
                                <input required type="text" class="form-control border border-white" name="name" placeholder="Partner Adı">
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Email</label>
                                <input required type="email" class="form-control border border-white" name="email" placeholder="Email">
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Telefon</label>
                                <input required type="tel" class="form-control border border-white" name="phone" placeholder="Telefon Numarası">
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Parola</label>
                                <input required type="password" class="form-control border border-white" name="password" placeholder="Şifre Giriniz">
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Şehir</label>
                                <select required class="form-control border border-white select2" name="city_id" id="city-select">
                                    <option value="">Şehir Seç</option>
                                    @foreach($cities as $city)
                                        <option   data-lat="{{$city->lat}}"
                                                  data-lng="{{$city->lng}}" value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">İlçe</label>
                                <select required class="form-control border border-white select2" name="district_id" id="district-select">
                                    <option value="">İlçe Seç</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Latitude</label>
                                <input required type="text" class="form-control border border-white" id="lati" name="lat">
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Longitude</label>
                                <input required type="text" id="longit" class="form-control border border-white" name="lng">
                            </div>
                        </div>
                        <div class="mb-3 mt-2">
                            <label class="form-label">Adres (opsiyonel)</label>
                            <textarea name="address" class="form-control border border-white" placeholder="Adres bilgisi…"></textarea>
                        </div>
                        <button type="submit" class="float-end special-button">Kaydı Tamamla</button>
                    </form>

                </div>
            </div>

            <div class="card">
                <div class="card-header">Konum Seçimi</div>
                <div class="card-body">
                    <p class="map-hint fw-bold text-white">Lütfen haritadan konum işaretlemesi yapınız.</p>
                    <div id="map">[ Harita Alanı ]</div>
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

            document.getElementById('lati').value = lat;
            document.getElementById('longit').value = lng;
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
                    url: '/get-districts/' + cityId,
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
</script></script>

<script src="{{asset('theme/login/js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>
