@extends('admin.layouts.app')
@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>

    <style>
        #map {
            height: 450px;
            width: 100%;
            border-radius: 24px;
            border: 4px solid #fff;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.08);
        }
        #map-search {
            margin-top: 15px;
            margin-left: 15px;
            width: 350px;
            height: 45px;
            border-radius: 12px;
            border: none;
            padding: 0 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 5;
            font-weight: 600;
            font-size: 13px;
        }
        .gm-style-mtc, .gm-svpc { display: none !important; } /* Harita butonlarını temizle */
    </style>

    <div class="container-fluid">
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Profil Bilgileri</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Hesap ve Konum Ayarlarınızı Yönetin</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-slate-100 px-4 py-2 !rounded-2xl m-0">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest"><a href="/admin/couriers" class="text-slate-500">Profil</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest text-indigo-600 active" aria-current="page">Düzenle</li>
                </ol>
            </nav>
        </div>

        @if(session()->has('message'))
            <div class="bg-indigo-600 text-white p-4 !rounded-2xl mb-4 flex items-center justify-between shadow-lg shadow-indigo-100">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-xs font-black uppercase tracking-widest">{{ session()->get('message') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" onclick="this.parentElement.style.display='none';"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-xl-6 col-lg-12">
                <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden h-100">
                    <div class="card-body p-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 uppercase m-0">İşletme Konumu</h4>
                                <p class="text-[10px] font-bold text-slate-400 m-0">Haritadan seçim yaparak güncelleyin</p>
                            </div>
                        </div>

                        <input id="map-search" type="text" placeholder="Adres veya mekan ara..." class="form-control">
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-12">
                <div class="card border-0 shadow-sm !rounded-[32px] h-100">
                    <div class="card-body p-5">
                        <form action="{{ route('admin.profile.update') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Tam İsim</label>
                                    <input type="text" name="name" class="form-control !rounded-xl border-slate-200 py-3 font-bold text-slate-700" value="{{ old('name', auth()->user()->name) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Telefon Numarası</label>
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => auth()->user()->phone])
                                </div>

                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Şifre Güncelleme</label>
                                    <input type="password" name="password" class="form-control !rounded-xl border-slate-200 py-3 font-bold text-slate-700" placeholder="Aynı kalacaksa boş bırakın">
                                </div>

                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Şehir</label>
                                    <select required class="form-select select2 !rounded-xl" name="city_id" id="city_id">
                                        <option value="">Şehir Seç</option>
                                        @foreach(\App\Models\City::all() as $city)
                                            <option {{$city->id == auth()->user()->city_id ? 'selected' : '' }}
                                                    value="{{$city->id}}"
                                                    data-lat="{{$city->lat}}"
                                                    data-lng="{{$city->lng}}">
                                                {{$city->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">İlçe</label>
                                    <select required class="form-select select2 !rounded-xl" name="district_id" id="district_id">
                                        <option value="">İlçe Seç</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Enlem (LAT)</label>
                                    <input type="text" required name="latitude" id="latitude" class="form-control !rounded-xl bg-slate-50 border-slate-100 font-bold text-indigo-600" readonly
                                           value="{{ old('latitude', auth()->user()->latitude) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Boylam (LNG)</label>
                                    <input required type="text" name="longitude" id="longitude" class="form-control !rounded-xl bg-slate-50 border-slate-100 font-bold text-indigo-600" readonly
                                           value="{{ old('longitude', auth()->user()->longitude) }}">
                                </div>

                                <div class="mb-8">
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-s-4 border-indigo-500 ps-3">OPERASYONEL LİMİTLER</h4>

                                    <div class="mb-6 p-5 bg-indigo-50/30 rounded-[25px] border border-indigo-50">
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-[10px] font-black text-indigo-900 uppercase tracking-widest">Hizmet Yarıçapı</span>
                                            <span id="dist_val" class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black tracking-tighter shadow-lg shadow-indigo-200 transition-all duration-300">{{ old('distance_limit', auth()->user()->distance_limit ?? 20) }} km</span>
                                        </div>
                                        <input type="range" name="distance_limit" id="distance_limit" min="1" max="100" step="1"
                                               value="{{ old('distance_limit', auth()->user()->distance_limit ?? 20) }}"
                                               oninput="updateVal('distance_limit', 'dist_val', ' km')"
                                               class="form-range custom-slider-indigo w-full cursor-pointer">
                                    </div>

                                    <div class="mb-6 p-5 bg-slate-50/50 rounded-[25px] border border-slate-100">
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Maks. Paket Ataması</span>
                                            <span id="pkg_val" class="px-3 py-1 bg-slate-900 text-white rounded-lg text-[10px] font-black tracking-tighter shadow-lg transition-all duration-300">
                                                {{ old('max_package_limit', auth()->user()->max_package_limit ?? 4) }} Paket
                                            </span>
                                        </div>
                                        <input type="range" name="max_package_limit" id="max_package_limit" min="1" max="10" step="1"
                                               value="{{ old('max_package_limit', auth()->user()->max_package_limit ?? 4) }}"
                                               oninput="updateVal('max_package_limit', 'pkg_val', ' Paket')"
                                               class="form-range custom-slider-dark w-full cursor-pointer">
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-indigo-100 border-0 transition-all hover:scale-[1.01] active:scale-95">
                                        DEĞİŞİKLİKLERİ KAYDET
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map, marker, autocomplete;

        function initMap() {
            const existingLat = parseFloat("{{ auth()->user()->latitude }}") || 37.1502;
            const existingLng = parseFloat("{{ auth()->user()->longitude }}") || 38.7790;
            const initialPos = { lat: existingLat, lng: existingLng };

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 14,
                mapTypeControl: false,
                streetViewControl: false,
                styles: [ /* Google Maps Silver Theme Style opsiyonel olarak buraya eklenebilir */ ]
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true,
                icon: {
                    url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                }
            });

            map.addListener("click", (e) => updatePosition(e.latLng));
            marker.addListener("dragend", (e) => updatePosition(marker.getPosition()));

            const input = document.getElementById("map-search");
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            autocomplete = new google.maps.places.Autocomplete(input);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;
                map.setCenter(place.geometry.location);
                map.setZoom(17);
                updatePosition(place.geometry.location);
            });
        }

        function updatePosition(latLng) {
            marker.setPosition(latLng);
            $('#latitude').val(latLng.lat().toFixed(8));
            $('#longitude').val(latLng.lng().toFixed(8));
        }

        $(document).ready(function () {
            $('.select2').select2();

            $('#city_id').on('change', function () {
                const selected = $(this).find('option:selected');
                const lat = parseFloat(selected.data('lat'));
                const lng = parseFloat(selected.data('lng'));

                if (lat && lng) {
                    const newPos = { lat: lat, lng: lng };
                    map.setCenter(newPos);
                    updatePosition(new google.maps.LatLng(lat, lng));
                }
                loadDistricts($(this).val());
            });

            function loadDistricts(cityId, selectedDistrictId = null) {
                if (cityId) {
                    $.get('/admin/get-districts/' + cityId, function (data) {
                        $('#district_id').empty().append('<option value="">İlçe Seç</option>');
                        $.each(data, function (key, value) {
                            var selected = (value.id == selectedDistrictId) ? 'selected' : '';
                            $('#district_id').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                        });
                    });
                }
            }

            loadDistricts($('#city_id').val(), "{{ auth()->user()->district_id }}");
            initMap();
        });
    </script>

    <style>
        /* Slider Başlıkları için Custom Tasarım */
        .form-range { height: 6px; -webkit-appearance: none; background: #e2e8f0; border-radius: 10px; }

        /* Indigo Slider Thumb */
        .custom-slider-indigo::-webkit-slider-thumb {
            -webkit-appearance: none; width: 20px; height: 20px;
            background: #4f46e5; border: 4px solid white; border-radius: 50%;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.4); cursor: pointer; transition: 0.2s;
        }
        .custom-slider-indigo::-webkit-slider-thumb:hover { transform: scale(1.2); }

        /* Dark Slider Thumb */
        .custom-slider-dark::-webkit-slider-thumb {
            -webkit-appearance: none; width: 20px; height: 20px;
            background: #0f172a; border: 4px solid white; border-radius: 50%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2); cursor: pointer; transition: 0.2s;
        }
        .custom-slider-dark::-webkit-slider-thumb:hover { transform: scale(1.2); }
    </style>

    <script>
        /**
         * Slider değerini anlık güncelleyen fonksiyon
         * @param inputId Slider'ın ID'si
         * @param targetId Değerin yazılacağı span ID'si
         * @param suffix Birim (km, Paket vb.)
         */
        function updateVal(inputId, targetId, suffix) {
            const slider = document.getElementById(inputId);
            const output = document.getElementById(targetId);
            output.innerText = slider.value + suffix;

            // Değişim anında ufak bir animasyon efekti
            output.style.transform = 'scale(1.1)';
            setTimeout(() => { output.style.transform = 'scale(1)'; }, 100);
        }

        // Sayfa yüklendiğinde mevcut değerleri bir kez kontrol et (Opsiyonel)
        document.addEventListener('DOMContentLoaded', function() {
            updateVal('distance_limit', 'dist_val', ' km');
            updateVal('max_package_limit', 'pkg_val', ' Paket');
        });
    </script>
@endsection
