@extends('restaurant.layouts.app')
@section('content')
    <style>
        #modalMap { height: 450px; width: 100%; border-radius: 20px; background-color: #f8fafc; }
        .pac-container { z-index: 100000 !important; border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 8px; }
        #mapModal { z-index: 9999 !important; }
        .modal-backdrop { z-index: 9998 !important; }
    </style>

    <div class="container-fluid py-6 px-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tighter uppercase leading-none mb-2">Müşteri Düzenle</h2>
                <p class="text-slate-500 font-medium italic">{{ $customer->name }} kullanıcısını güncelliyorsunuz.</p>
            </div>
            <a href="/restaurant/customers" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-chevron-left text-xs"></i> Geri Dön
            </a>
        </div>

        <form method="post" class="repeater" id="customerForm" action="{{ route('restaurant.customers.update') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $customer->id }}">

            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8 mb-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Müşteri Adı <small class="text-red-500">*</small></label>
                        <input type="text" class="w-full px-5 py-4 bg-slate-50 border-0 rounded-2xl font-bold text-slate-900 focus:ring-2 focus:ring-brand outline-none transition-all" name="name" value="{{ old('name', $customer->name) }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Telefon <small class="text-red-500">*</small></label>
                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => old('phone', $customer->phone)])
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Telefon 2</label>
                        @include('components.phone',['key' => 'mobile', 'required' => false, 'value' => old('mobile', $customer->mobile)])
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-end mb-6 px-2">
                <div>
                    <h5 class="text-2xl font-black text-slate-800 uppercase tracking-tight italic text-brand">Adres Bilgileri</h5>
                    <p class="text-slate-400 text-sm font-medium mt-1">Konumu düzenleyerek adres detaylarını otomatik doldurabilirsiniz.</p>
                </div>
                <button type="button" class="px-6 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-xs tracking-widest hover:bg-brand transition-all shadow-xl active:scale-95" data-repeater-create>
                    <i class="fa fa-plus me-2"></i> YENİ ADRES EKLE
                </button>
            </div>

            <div data-repeater-list="address" class="space-y-6">
                @php
                    $addresses = old('address') ? old('address') : \App\Models\CustomerAddress::where('customer_id', $customer->id)->get();
                @endphp

                @foreach ($addresses as $index => $address)
                    <div data-repeater-item class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden transition-all hover:border-slate-200">
                        <input type="hidden" name="id" value="{{ is_array($address) ? ($address['id'] ?? '') : $address->id }}">

                        <div class="bg-slate-50/50 px-8 py-4 border-b border-slate-100 flex justify-between items-center text-xs">
                            <div class="flex items-center gap-2">
                                <i class="fa fa-map-pin text-red-500"></i>
                                <span class="font-black text-slate-700 uppercase tracking-widest italic">Adres Kaydı</span>
                            </div>
                            @php
                                $lat = is_array($address) ? ($address['latitude'] ?? '') : $address->latitude;
                                $lng = is_array($address) ? ($address['longitude'] ?? '') : $address->longitude;
                            @endphp
                            <span class="status-badge px-4 py-1.5 rounded-full font-black uppercase tracking-tighter {{ $lat ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $lat ? "Onaylandı (".round($lat, 4).")" : 'Konum Seçilmedi' }}
                        </span>
                        </div>

                        <div class="p-8 flex flex-col lg:flex-row gap-8">
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-5">
                                <div class="md:col-span-4 space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Başlık (Ev/İş)</label>
                                    <input type="text" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold text-slate-800" name="name" value="{{ is_array($address) ? $address['name'] : $address->name }}" required>
                                </div>
                                <div class="md:col-span-4 space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-brand">İlçe</label>
                                    <select class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold text-slate-800 addr-ilce-select cursor-pointer" name="district_id" required>
                                        <option value="">İlçe Seçiniz</option>
                                        @foreach($districts as $dist)
                                            <option value="{{ $dist->id }}" {{ (is_array($address) ? ($address['district_id'] ?? '') : $address->district_id) == $dist->id ? 'selected' : '' }}>
                                                {{ $dist->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-4 space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mahalle</label>
                                    <input type="text" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold text-slate-800 addr-mahalle" name="mahalle" value="{{ is_array($address) ? $address['mahalle'] : $address->mahalle }}" required>
                                </div>
                                <div class="md:col-span-5 space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sokak/Cadde</label>
                                    <input type="text" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold text-slate-800 addr-sokak" name="sokak_cadde" value="{{ is_array($address) ? $address['sokak_cadde'] : $address->sokak_cadde }}" required>
                                </div>
                                <div class="md:col-span-3 space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Bina/No</label>
                                    <input type="text" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold text-slate-800 addr-bina" name="bina_no" value="{{ is_array($address) ? $address['bina_no'] : $address->bina_no }}" required>
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kat</label>
                                    <input type="text" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold text-slate-800" name="kat" value="{{ is_array($address) ? ($address['kat'] ?? '') : $address->kat }}">
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Daire</label>
                                    <input type="text" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold text-slate-800" name="daire_no" value="{{ is_array($address) ? ($address['daire_no'] ?? '') : $address->daire_no }}">
                                </div>
                                <div class="md:col-span-12 space-y-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Adres Tarifi</label>
                                    <input type="text" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold text-slate-800 addr-tarif" name="adres_tarifi" value="{{ is_array($address) ? ($address['adres_tarifi'] ?? '') : $address->adres_tarifi }}">
                                </div>
                            </div>

                            <div class="lg:w-48 flex flex-col items-center justify-center gap-4 bg-slate-50/50 rounded-2xl p-6 border border-dashed border-slate-200">
                                <button type="button" class="w-full py-4 bg-white border border-slate-200 rounded-2xl text-slate-600 font-black text-[10px] uppercase tracking-widest hover:border-brand hover:text-brand transition-all shadow-sm open-map-modal flex flex-col items-center gap-2">
                                    <i class="fa fa-map-marker-alt text-lg"></i> Konumu Düzenle
                                </button>

                                <input type="hidden" class="input-lat" name="latitude" value="{{ $lat }}">
                                <input type="hidden" class="input-lng" name="longitude" value="{{ $lng }}">

                                <button type="button" class="text-red-400 hover:text-red-600 font-black text-[10px] uppercase tracking-widest transition-colors flex items-center gap-1" data-repeater-delete>
                                    <i class="fa fa-trash-alt text-xs"></i> Adresi Kaldır
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 mb-10 flex justify-end">
                <button type="submit" class="px-12 py-5 bg-brand text-white rounded-[24px] font-black text-xl tracking-tighter shadow-2xl shadow-brand/30 hover:bg-brand-dark transition-all active:scale-95">
                    GÜNCELLEMEYİ TAMAMLA
                </button>
            </div>
        </form>
    </div>

  <div class="modal fade" data-bs-backdrop="false" id="mapModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content !rounded-[32px] overflow-hidden border-0 shadow-2xl">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Konum Seçiniz</h5>
                        <button type="button" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input id="modal-search" class="w-full pl-11 pr-4 py-4 bg-slate-50 border-0 rounded-2xl font-bold text-slate-900 focus:ring-2 focus:ring-brand outline-none shadow-inner" type="text" placeholder="Adres veya mekan arayın...">
                    </div>
                    <div id="modalMap" class="shadow-inner border border-slate-100"></div>
                    <div class="mt-8 flex gap-3">
                        <button type="button" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black uppercase tracking-widest" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="button" class="flex-[2] py-4 bg-brand text-white rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-brand/20 hover:bg-brand-dark transition-all" id="confirmLocation">Konumu Onayla</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>

    <script>

        let map, marker, autocomplete, currentRow, geocoder;

        $(document).ready(function () {
            $('#mapModal').on('shown.bs.modal', function () {
                $(this).appendTo('body');
            });

            geocoder = new google.maps.Geocoder();

            $('.repeater').repeater({
                initEmpty: false,
                show: function () {
                    $(this).slideDown();
                    $(this).find('.status-badge').removeClass('coord-ok').addClass('coord-missing').text('Konum Seçilmedi');
                    $(this).find('input[type="hidden"]').val('');
                    $(this).find('input[type="text"]').val('');
                },
                hide: function (deleteElement) {
                    if(confirm('Bu adresi silmek istediğinize emin misiniz?')) { $(this).slideUp(deleteElement); }
                }
            });

            function initMap() {
                const initialPos = { lat: 37.1502, lng: 38.7790 };
                map = new google.maps.Map(document.getElementById("modalMap"), { center: initialPos, zoom: 13 });
                marker = new google.maps.Marker({ position: initialPos, map: map, draggable: true });

                autocomplete = new google.maps.places.Autocomplete(document.getElementById("modal-search"));
                autocomplete.addListener("place_changed", () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) return;
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                    marker.setPosition(place.geometry.location);
                });
                map.addListener("click", (e) => { marker.setPosition(e.latLng); });
            }
            initMap();

            $(document).on('click', '.open-map-modal', function() {
                currentRow = $(this).closest('[data-repeater-item]');
                $('#mapModal').modal('show');
                setTimeout(() => {
                    google.maps.event.trigger(map, 'resize');
                    let lat = currentRow.find('.input-lat').val();
                    if (lat) {
                        let pos = { lat: parseFloat(lat), lng: parseFloat(currentRow.find('.input-lng').val()) };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                    }
                }, 300);
            });

            $('#confirmLocation').click(function() {
                const pos = marker.getPosition();
                const lat = pos.lat().toFixed(6);
                const lng = pos.lng().toFixed(6);

                currentRow.find('.input-lat').val(lat);
                currentRow.find('.input-lng').val(lng);
                currentRow.find('.status-badge')
                    .removeClass('coord-missing').addClass('coord-ok')
                    .html(`<i class="fa fa-check"></i> Onaylandı (${lat}, ${lng})`);

                geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        const components = results[0].address_components;
                        const fullAddress = results[0].formatted_address;

                        // Değişkenleri en başta güvenli tanımlıyoruz
                        let neighborhood = '';
                        let street = '';
                        let bNo = '';
                        let districtName = '';

                        components.forEach(c => {
                            const types = c.types;

                            // MAHALLE: Google'ın dönebileceği tüm mahalle varyasyonlarını tara
                            if (types.includes("neighborhood") ||
                                types.includes("sublocality_level_1") ||
                                types.includes("administrative_area_level_4")) {
                                neighborhood = c.long_name;
                            }

                            // İLÇE
                            if (types.includes("administrative_area_level_2")) {
                                districtName = c.long_name;
                            }

                            // SOKAK & NO
                            if (types.includes("route")) street = c.long_name;
                            if (types.includes("street_number")) bNo = c.long_name;
                        });

                        // 1. GARANTİ: İlçe (Karaköprü vb.) bulunamazsa tam adresten çek
                        if (!districtName) {
                            const districtMatch = fullAddress.match(/(\w+)\/Şanlıurfa/);
                            if (districtMatch) districtName = districtMatch[1];
                        }

                        // 2. GARANTİ: Mahalle bulunamazsa (Bazen Google vermez)
                        // Genelde tam adresin ilk parçası mahalledir (Örn: "Batıkent, 63320 Karaköprü...")
                        if (!neighborhood) {
                            let addrParts = fullAddress.split(',');
                            if (addrParts.length > 0) neighborhood = addrParts[0].trim();
                        }

                        // İLÇE SELECT BOX EŞLEŞTİRME
                        if (districtName) {
                            let districtSelect = currentRow.find('.addr-ilce-select');
                            districtSelect.find('option').each(function() {
                                let optionText = $(this).text().trim().toLocaleLowerCase('tr');
                                let foundText = districtName.toLocaleLowerCase('tr');
                                if (optionText === foundText) {
                                    districtSelect.val($(this).val()).trigger('change');
                                }
                            });
                        }

                        // FORM ALANLARINA BAS
                        currentRow.find('.addr-mahalle').val(neighborhood);
                        currentRow.find('.addr-sokak').val(street);
                        currentRow.find('.addr-bina').val(bNo);
                        currentRow.find('.addr-tarif').val(fullAddress);
                    }
                });

                $('#mapModal').modal('hide');
            });
        });
    </script>
@endsection
