@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    YENİ <span class="text-slate-400">KURYE KAYDI</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sisteme yeni bir kurye personeli tanımlıyorsunuz.
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-4 py-2 !rounded-2xl m-0 shadow-sm border border-slate-50">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest"><a href="/admin/couriers" class="text-slate-400">Kuryeler</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest text-indigo-600 active">Yeni</li>
                </ol>
            </nav>
        </div>

        @if(session()->has('success'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-indigo-500 shadow-2xl rounded-2xl p-4 animate-bounce-short">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-50 p-2 rounded-xl text-indigo-600"><i class="fas fa-check-circle"></i></div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase">BAŞARILI</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('success') }}</p>
                    </div>
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

        <form method="post" action="{{route('admin.couriers.create')}}" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 p-8 md:p-12 mb-6">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Kurye Bilgileri</h4>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Kurye Adı</label>
                                <input value="{{old('name')}}" required type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500" name="name" placeholder="Ad Soyad">
                            </div>
                            <div class="col-md-4">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Şifresi</label>
                                <input required type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500" name="password" placeholder="••••••">
                            </div>
                            <div class="col-md-4">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Telefonu</label>
                                @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                            </div>

                            <div class="col-md-12">
                                <div class="bg-slate-50 p-6 !rounded-[24px] border border-slate-100">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Ödeme Çalışma Şekli</label>
                                            <select class="form-control !rounded-xl border-slate-200 font-bold text-slate-600" name="price_type" id="price-type">
                                                <option value="package">Paket Başı Ücretlendirme</option>
                                                <option value="fixed">Sabit Maaş + Km Ücreti</option>
                                            </select>
                                        </div>

                                        <div id="package-fields" class="col-md-12">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Paket Başı Ücret (₺)</label>
                                            <input type="text" class="form-control !rounded-xl border-slate-200 font-bold text-slate-700" name="price" placeholder="10.00">
                                        </div>

                                        <div id="fixed-fields" class="col-md-4">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Sabit Ücret (₺)</label>
                                            <input value="{{old('fixed_price')}}" type="text" class="form-control !rounded-xl border-slate-200 font-bold text-slate-700" name="fixed_price" placeholder="25.000">
                                        </div>
                                        <div id="fixed-fields2" class="col-md-4">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Km Ücreti (₺)</label>
                                            <input value="{{old('km_price')}}" type="text" class="form-control !rounded-xl border-slate-200 font-bold text-slate-700" name="km_price" placeholder="4.00">
                                        </div>
                                        <div id="fixed-fields3" class="col-md-4">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Km Eşiği (Sonrası Hesapla)</label>
                                            <input value="{{old('km_distance_later')}}" type="number" class="form-control !rounded-xl border-slate-200 font-bold text-slate-700" name="km_distance_later" placeholder="2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Ek Bilgiler --}}
                        <div class="mt-10">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-id-card text-sm"></i>
                                </div>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Ek Bilgiler</h4>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">TC Kimlik No *</label>
                                    <input required value="{{old('tc_id')}}" type="text" maxlength="11" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500" name="tc_id" placeholder="12345678901">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Yaş *</label>
                                    <input required value="{{old('age')}}" type="number" min="18" max="70" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500" name="age" placeholder="25">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Kan Grubu *</label>
                                    <select required class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-600" name="blood_type">
                                        <option value="">Seçiniz</option>
                                        @foreach(['A+','A-','B+','B-','AB+','AB-','0+','0-'] as $bt)
                                            <option value="{{$bt}}" {{old('blood_type') == $bt ? 'selected' : ''}}>{{$bt}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Araç Tipi *</label>
                                    <select required class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-600" name="vehicle_type">
                                        <option value="">Seçiniz</option>
                                        <option value="motor" {{old('vehicle_type') == 'motor' ? 'selected' : ''}}>Motor</option>
                                        <option value="otomobil" {{old('vehicle_type') == 'otomobil' ? 'selected' : ''}}>Otomobil</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Plaka *</label>
                                    <input required value="{{old('plate')}}" type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500" name="plate" placeholder="34 ABC 123">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Profil Fotoğrafı *</label>
                                    <input required type="file" accept="image/*" class="form-control !rounded-2xl !py-3 border-slate-100 font-bold text-slate-700 focus:border-indigo-500" name="profile_photo">
                                </div>

                                <div class="col-md-6">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Banka *</label>
                                    <input required value="{{old('bank')}}" type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500" name="bank" placeholder="Ziraat Bankası">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">IBAN *</label>
                                    <input required value="{{old('iban')}}" type="text" maxlength="32" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700 focus:border-indigo-500" name="iban" placeholder="TR00 0000 0000 0000 0000 0000 00">
                                </div>
                            </div>
                        </div>

                        <div class="mt-12">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-4 block">
                                <i class="fas fa-map-marker-alt text-indigo-500 me-2"></i> Başlangıç Konumu
                            </label>

                            <div class="relative w-full !rounded-[32px] overflow-hidden border border-slate-100 shadow-sm" style="height: 450px;">
                                <input id="pac-input"
                                       class="absolute z-[5] top-5 left-5 w-full max-w-md !rounded-xl border-0 shadow-2xl p-4 font-bold text-xs text-slate-600 focus:ring-2 focus:ring-indigo-500"
                                       type="text"
                                       placeholder="Bölge veya sokak arayın...">
                                <div id="map" class="w-full h-full"></div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-300 uppercase tracking-widest ml-1 mb-2 block">Enlem (Lat)</label>
                                    <input required type="text" name="latitude" id="lat" value="{{old('latitude')}}" class="form-control !rounded-xl bg-slate-50 border-0 text-slate-400 font-mono text-xs" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-300 uppercase tracking-widest ml-1 mb-2 block">Boylam (Lng)</label>
                                    <input required type="text" name="longitude" id="lng" value="{{old('longitude')}}" class="form-control !rounded-xl bg-slate-50 border-0 text-slate-400 font-mono text-xs" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="sticky top-4">
                        <div class="bg-[#0f172a] !rounded-[40px] p-10 text-white shadow-2xl mb-6 relative overflow-hidden">
                            <i class="fas fa-motorcycle absolute -right-4 -bottom-4 text-white/5 text-8xl transform -rotate-12"></i>
                            <h3 class="text-xl text-white tracking-tighter uppercase mb-2">Kurye Kaydı</h3>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest leading-relaxed mb-8">Kuryenin başlangıç konumunu haritadan işaretlemeyi unutmayın.</p>

                            <button type="submit" class="w-full bg-indigo-600 text-white py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-indigo-900/20 hover:scale-[1.02] active:scale-95 transition-all mb-4">
                                <i class="fas fa-save me-2 text-indigo-300"></i> KURYEYİ KAYDET
                            </button>
                            <a href="/admin/couriers" class="w-full inline-flex items-center justify-center bg-slate-800 text-slate-400 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest border border-slate-700 hover:bg-slate-700 transition-all">
                                VAZGEÇ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google.maps_key')}}&libraries=places&callback=initMap" async defer></script>

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

            const mapStyles = [
                { "featureType": "all", "elementType": "geometry.fill", "stylers": [{ "weight": "2.00" }] },
                { "featureType": "all", "elementType": "geometry.stroke", "stylers": [{ "color": "#9c9c9c" }] },
                { "featureType": "landscape", "elementType": "all", "stylers": [{ "color": "#f2f2f2" }] },
                { "featureType": "poi", "elementType": "all", "stylers": [{ "visibility": "off" }] },
                { "featureType": "road", "elementType": "all", "stylers": [{ "saturation": -100 }, { "lightness": 45 }] },
                { "featureType": "water", "elementType": "all", "stylers": [{ "color": "#cbd5e1" }, { "visibility": "on" }] }
            ];

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 13,
                styles: mapStyles,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true
            });

            updateInputs(initialPos.lat, initialPos.lng);

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

            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                updateInputs(e.latLng.lat(), e.latLng.lng());
            });

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
