@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    KURYE <span class="text-slate-400">GÜNCELLE</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    {{ $courier->name }} personeline ait bilgileri düzenliyorsunuz.
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-4 py-2 !rounded-2xl m-0 shadow-sm border border-slate-50">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest"><a href="/admin/couriers" class="text-slate-400">Kuryeler</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest text-indigo-600 active">Düzenle</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ route('admin.couriers.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$courier->id}}">

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 p-8 md:p-12 mb-6">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-edit text-sm"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Personel Detayları</h4>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Kurye Adı</label>
                                <input required type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700" value="{{$courier->name}}" name="name">
                            </div>
                            <div class="col-md-4">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Telefon</label>
                                @include('components.phone',['key' => 'phone', 'required' => true, 'value' => $courier->phone])
                            </div>
                            <div class="col-md-4">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Şifre (Değişmeyecekse Boş)</label>
                                <input type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700" name="password" placeholder="••••••">
                            </div>

                            <div class="col-md-12">
                                <div class="bg-slate-50 p-8 !rounded-[32px] border border-slate-100">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Çalışma Şekli</label>
                                            <select class="form-control !rounded-xl border-slate-200 font-bold text-slate-600 shadow-none" name="price_type" id="price-type">
                                                <option {{$courier->price_type == 'package' ? 'selected' : ''}} value="package">Paket Başı</option>
                                                <option {{$courier->price_type == 'fixed' ? 'selected' : ''}} value="fixed">Sabit + Km Ücreti</option>
                                            </select>
                                        </div>
                                        <div id="package-fields" class="col-md-12">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Paket Başı Ücret</label>
                                            <input value="{{$courier->price}}" type="text" class="form-control !rounded-xl border-slate-200 font-bold text-slate-700" name="price">
                                        </div>
                                        <div id="fixed-fields" class="col-md-4">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Sabit Ücret</label>
                                            <input value="{{$courier->fixed_price}}" type="text" class="form-control !rounded-xl border-slate-200 font-bold text-slate-700" name="fixed_price">
                                        </div>
                                        <div id="fixed-fields2" class="col-md-4">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Km Ücreti</label>
                                            <input value="{{$courier->km_price}}" type="text" class="form-control !rounded-xl border-slate-200 font-bold text-slate-700" name="km_price">
                                        </div>
                                        <div id="fixed-fields3" class="col-md-4">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Limit (Km Sonrası)</label>
                                            <input value="{{$courier->km_distance_later}}" type="number" class="form-control !rounded-xl border-slate-200 font-bold text-slate-700" name="km_distance_later">
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
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">TC Kimlik No</label>
                                    <input value="{{$courier->tc_id}}" type="text" maxlength="11" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700" name="tc_id" placeholder="12345678901">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Yaş</label>
                                    <input value="{{$courier->age}}" type="number" min="18" max="70" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700" name="age" placeholder="25">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Kan Grubu</label>
                                    <select class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-600" name="blood_type">
                                        <option value="">Seçiniz</option>
                                        @foreach(['A+','A-','B+','B-','AB+','AB-','0+','0-'] as $bt)
                                            <option value="{{$bt}}" {{$courier->blood_type == $bt ? 'selected' : ''}}>{{$bt}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Araç Tipi</label>
                                    <select class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-600" name="vehicle_type">
                                        <option value="">Seçiniz</option>
                                        <option value="motor" {{$courier->vehicle_type == 'motor' ? 'selected' : ''}}>Motor</option>
                                        <option value="otomobil" {{$courier->vehicle_type == 'otomobil' ? 'selected' : ''}}>Otomobil</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Plaka</label>
                                    <input value="{{$courier->plate}}" type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700" name="plate" placeholder="34 ABC 123">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Profil Fotoğrafı</label>
                                    @if($courier->profile_photo)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $courier->profile_photo) }}" alt="Profil" class="w-16 h-16 rounded-2xl object-cover border border-slate-100">
                                        </div>
                                    @endif
                                    <input type="file" accept="image/*" class="form-control !rounded-2xl !py-3 border-slate-100 font-bold text-slate-700" name="profile_photo">
                                    <p class="text-[10px] text-slate-300 font-bold mt-1 ml-1">Boş bırakırsanız mevcut fotoğraf korunur.</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Banka</label>
                                    <input value="{{$courier->bank}}" type="text" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700" name="bank" placeholder="Ziraat Bankası">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">IBAN</label>
                                    <input value="{{$courier->iban}}" type="text" maxlength="32" class="form-control !rounded-2xl !py-3.5 border-slate-100 font-bold text-slate-700" name="iban" placeholder="TR00 0000 0000 0000 0000 0000 00">
                                </div>
                            </div>
                        </div>

                        <div class="mt-12">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-4 block">
                                <i class="fas fa-map-marked-alt text-indigo-500 me-2"></i> Kurye Mevcut Konumu
                            </label>
                            <div class="relative w-full !rounded-[32px] overflow-hidden border border-slate-100 shadow-sm" style="height: 450px;">
                                <input id="pac-input"
                                       class="absolute z-[5] top-5 left-5 w-full max-w-md !rounded-xl border-0 shadow-2xl p-4 font-bold text-xs text-slate-600 focus:ring-2 focus:ring-indigo-500"
                                       type="text"
                                       placeholder="Konum güncellemek için arayın...">
                                <div id="map" class="w-full h-full"></div>
                            </div>
                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-300 uppercase tracking-widest ml-1 mb-2 block">Enlem (LAT)</label>
                                    <input required value="{{$courier->latitude}}" type="text" name="latitude" id="lat" class="form-control !rounded-xl bg-slate-50 border-0 text-slate-400 font-mono text-xs" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-300 uppercase tracking-widest ml-1 mb-2 block">Boylam (LNG)</label>
                                    <input required value="{{$courier->longitude}}" type="text" name="longitude" id="lng" class="form-control !rounded-xl bg-slate-50 border-0 text-slate-400 font-mono text-xs" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="sticky top-4">
                        <div class="bg-[#0f172a] !rounded-[40px] p-10 text-white shadow-2xl mb-6 relative overflow-hidden">
                            <i class="fas fa-id-card absolute -right-4 -bottom-4 text-white/5 text-8xl transform rotate-12"></i>
                            <h3 class="text-xl text-white tracking-tighter uppercase mb-2">Kurye Durumu</h3>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest leading-relaxed mb-8">Personelin anlık çalışma durumunu seçin.</p>

                            <div class="space-y-3 mb-10">
                                @php $statuses = [\App\Helpers\CourierStatus::active => 'Müsait', \App\Helpers\CourierStatus::service => 'Serviste', \App\Helpers\CourierStatus::break => 'Molada', \App\Helpers\CourierStatus::passive => 'Kapalı']; @endphp
                                @foreach($statuses as $val => $label)
                                    <div class="relative">
                                        <input class="hidden peer" type="radio" name="status" id="status_{{$val}}" value="{{$val}}" {{ $courier->status == $val ? 'checked' : '' }}>
                                        <label for="status_{{$val}}" class="flex items-center justify-between w-full p-4 text-slate-400 bg-slate-800/50 border border-slate-700 !rounded-2xl cursor-pointer peer-checked:border-indigo-500 peer-checked:text-white peer-checked:bg-indigo-600/20 transition-all font-bold text-xs uppercase tracking-widest">
                                            {{$label}}
                                            <i class="fas fa-check-circle opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-indigo-900/20 hover:scale-[1.02] transition-all mb-4">
                                <i class="fas fa-sync me-2"></i> GÜNCELLEMEYİ KAYDET
                            </button>
                            <a href="/admin/couriers" class="w-full inline-flex items-center justify-center bg-slate-800 text-slate-400 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest border border-slate-700 hover:bg-slate-700 transition-all">
                                LİSTEYE DÖN
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap" async defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const priceTypeSelect = document.getElementById('price-type');
            const packageFields = document.getElementById('package-fields');
            const fixedFields = [document.getElementById('fixed-fields'), document.getElementById('fixed-fields2'), document.getElementById('fixed-fields3')];

            function toggleFields() {
                const isPackage = priceTypeSelect.value === 'package';
                packageFields.style.display = isPackage ? 'block' : 'none';
                fixedFields.forEach(field => field.style.display = isPackage ? 'none' : 'block');
            }
            priceTypeSelect.addEventListener('change', toggleFields);
            toggleFields();
        });

        let map, marker, autocomplete;

        function initMap() {
            const existingPos = {
                lat: parseFloat("{{ $courier->latitude }}") || 37.1502,
                lng: parseFloat("{{ $courier->longitude }}") || 38.7790
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
                center: existingPos,
                zoom: 15,
                styles: mapStyles,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });

            marker = new google.maps.Marker({
                position: existingPos,
                map: map,
                draggable: true
            });

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
