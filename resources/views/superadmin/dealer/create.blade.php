@extends('superadmin.layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    YENİ <span class="text-slate-400">PARTNER KAYDI</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sisteme yeni bir iş ortağı veya bayi tanımlayın.
                </p>
            </div>
            <a href="javascript:void(0);" onclick="window.history.back();" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
        </div>

        @if(session()->has('message'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-emerald-500 shadow-2xl rounded-2xl p-5 animate-bounce-short">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-emerald-50 p-2 rounded-xl"><i class="fas fa-check-circle text-emerald-600 text-lg"></i></div>
                    <div class="ml-4 flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">BAŞARILI</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form method="post" action="{{route('superadmin.dealer_create_request')}}">
            @csrf

            <div class="row g-4">
                <div class="col-xl-7">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden mb-4">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">TEMEL İŞLETME BİLGİLERİ</h4>
                        </div>
                        <div class="p-8">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">PARTNER ADI</label>
                                    <input required type="text" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="name" placeholder="İşletme Adı Giriniz">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">E-POSTA</label>
                                    <input required type="email" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="email" placeholder="partner@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">TELEFON</label>
                                    <input required type="tel" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="phone" placeholder="05xx xxx xx xx">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">GİRİŞ PAROLASI</label>
                                    <input required type="text" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="password" placeholder="Şifre Belirleyin">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">ŞEHİR</label>
                                    <select required class="form-control select2" name="city_id" id="city-select">
                                        <option value="">Şehir Seçin</option>
                                        @foreach($cities as $city)
                                            <option value="{{$city->id}}" data-lat="{{$city->lat}}" data-lng="{{$city->lng}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">İLÇE</label>
                                    <select required class="form-control select2" name="district_id" id="district-select">
                                        <option value="">Önce Şehir Seçin</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">KOMİSYON ORANI (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" name="commission_rate" value="20" placeholder="20">
                                    <p class="text-[9px] text-slate-400 mt-1 ml-1">Her kontör yüklemesinden alınacak komisyon yüzdesi</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">TAM ADRES (OPSİYONEL)</label>
                                    <textarea rows="4" name="address" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" placeholder="Detaylı adres bilgisi..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden h-full">
                        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">KONUM İŞARETLEME</h4>
                            <span class="text-[9px] font-black text-rose-500 uppercase animate-pulse">Konum Seçilmeli</span>
                        </div>
                        <div class="p-8">
                            <div id="map" class="!rounded-[30px] border-4 border-slate-50 shadow-inner mb-6" style="height: 400px; z-index: 1;"></div>

                            <div class="row g-3 mb-8">
                                <div class="col-6">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 block">ENLEM (LAT)</label>
                                    <input required type="text" class="form-control !rounded-xl bg-slate-100 border-0 p-3 text-xs font-black text-slate-600" id="latit" name="lat" readonly placeholder="0.0000">
                                </div>
                                <div class="col-6">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 block">BOYLAM (LNG)</label>
                                    <input required type="text" class="form-control !rounded-xl bg-slate-100 border-0 p-3 text-xs font-black text-slate-600" id="longi" name="lng" readonly placeholder="0.0000">
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-[#0f172a] text-white py-5 !rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:scale-[1.02] transition-all border-0">
                                PARTNER KAYDINI TAMAMLA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        /* Leaflet Marker Görünümü Fix */
        .leaflet-container { font-family: 'Inter', sans-serif; }
        .select2-container--default .select2-selection--single {
            border: 2px solid #f1f5f9 !important;
            background-color: rgba(248, 250, 252, 0.5) !important;
            border-radius: 1rem !important;
            height: 58px !important;
            padding: 14px !important;
            font-weight: 700 !important;
        }
        .select2-dropdown { border: 0 !important; border-radius: 1rem !important; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1) !important; padding: 10px !important; }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            var marker;
            var existingLat = {{ $dealer->latitude ?? '37.1502' }};
            var existingLng = {{ $dealer->longitude ?? '38.7790' }};

            var map = L.map('map').setView([existingLat, existingLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // İlk marker
            marker = L.marker([existingLat, existingLng], {draggable: true}).addTo(map);

            map.on('click', function(e) {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });

            function updateMarker(lat, lng) {
                if (marker) { map.removeLayer(marker); }
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                $('#latit').val(lat.toFixed(6));
                $('#longi').val(lng.toFixed(6));

                marker.on('dragend', function(event) {
                    var m = event.target;
                    var position = m.getLatLng();
                    $('#latit').val(position.lat.toFixed(6));
                    $('#longi').val(position.lng.toFixed(6));
                });
            }

            $('#city-select').on('change', function () {
                var cityId = $(this).val();
                var selectedOption = $(this).find('option:selected');
                var lat = selectedOption.data('lat');
                var lng = selectedOption.data('lng');

                if (lat && lng) {
                    map.setView([lat, lng], 13);
                    updateMarker(lat, lng);
                }

                if (cityId) {
                    $.ajax({
                        url: '/superadmin/get-districts/' + cityId,
                        type: 'GET',
                        success: function (data) {
                            $('#district-select').empty().append('<option value="">İlçe Seçin</option>');
                            $.each(data, function (key, value) {
                                $('#district-select').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
