@extends('restaurant.layouts.app')

@section('content')
    <style>
        #modalMap { height: 450px; width: 100%; border-radius: 20px; background-color: #f8fafc; }

        /* Z-Index Fix: Her şeyin üstünde olmalı */
        .pac-container { z-index: 100000 !important; border-radius: 12px; }
        #mapModal { z-index: 9999 !important; }
        .modal-backdrop { z-index: 9998 !important; }

        /* Modalın body'e taşınması durumunda stil kaybını önlemek için */
        body.modal-open { overflow: hidden; }
    </style>

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

    <div class="container-fluid py-6 px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter uppercase">YENİ MÜŞTERİ KAYDI</h2>
                <p class="text-slate-500 font-medium italic">Haritadan konum seçerek adres bilgilerini otomatik doldurabilirsiniz.</p>
            </div>
        </div>

        <form method="post" class="repeater" id="customerForm" action="{{ route('restaurant.customers.create') }}">
            @csrf

            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Müşteri Adı</label>
                        <input value="{{old('name')}}" type="text" name="name" required class="w-full px-5 py-4 bg-slate-50 border-0 rounded-2xl font-bold text-slate-900 focus:ring-2 focus:ring-brand">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Telefon 1</label>
                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Telefon 2</label>
                        @include('components.phone',['key' => 'mobile', 'required' => false, 'value' => null])
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6 px-2">
                <h5 class="text-xl font-black text-slate-800 uppercase tracking-tight italic text-brand">Adresler</h5>
                <button type="button" data-repeater-create class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-black text-xs tracking-widest hover:bg-brand transition-all shadow-xl active:scale-95">
                    + YENİ ADRES
                </button>
            </div>

            <div data-repeater-list="address" class="space-y-6">
                <div data-repeater-item class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="flex flex-col lg:flex-row">
                        <div class="flex-1 p-8">
                            <div class="flex items-center gap-2 mb-6">
                                <span class="status-badge px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-500 border border-red-100 italic">
                                    Konum Seçilmedi
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Başlık</label>
                                    <input type="text" name="name" required class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">İlçe</label>
                                    <input type="text" name="ilce" class="addr-ilce w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold">
                                </div>
                                <div class="md:col-span-6">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mahalle</label>
                                    <input type="text" name="mahalle" required class="addr-mahalle w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold">
                                </div>
                                <div class="md:col-span-5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sokak/Cadde</label>
                                    <input type="text" name="sokak_cadde" required class="addr-sokak w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Bina</label>
                                    <input type="text" name="bina_no" required class="addr-bina w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kat</label>
                                    <input type="text" name="kat" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Daire</label>
                                    <input type="text" name="daire_no" class="w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tarif</label>
                                    <input type="text" name="adres_tarifi" class="addr-tarif w-full px-4 py-3 bg-slate-50 border-0 rounded-xl font-bold">
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50/50 p-8 flex flex-col justify-center items-center gap-4 min-w-[200px] border-l border-slate-100">
                            <button type="button" class="open-map-modal group w-full py-6 bg-brand/10 border-2 border-dashed border-brand/40 rounded-2xl text-brand hover:bg-brand hover:text-white hover:border-brand transition-all flex flex-col items-center gap-3 animate-pulse hover:animate-none">
                                <i class="fas fa-map-marker-alt text-3xl"></i>
                                <span class="text-[11px] font-black uppercase tracking-wider">Konum Seç</span>
                            </button>
                            <input type="hidden" class="input-lat" name="latitude">
                            <input type="hidden" class="input-lng" name="longitude">
                            <button type="button" class="btn-remove-address text-red-400 hover:text-red-600 font-bold text-[10px] uppercase tracking-widest">
                                KALDIR
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex justify-end">
                <button type="submit" class="bg-brand text-white px-12 py-5 rounded-[24px] font-black text-xl shadow-2xl hover:bg-brand-dark transition-all">
                    KAYDI TAMAMLA
                </button>
            </div>
        </form>
    </div>

  <div class="modal fade" data-bs-backdrop="false" id="mapModal" data-bs-backdrop="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content !rounded-[32px] overflow-hidden border-0">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Adres Konumu</h5>
                        <button type="button" class="text-slate-400" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <input id="modal-search" class="w-full px-5 py-4 bg-slate-50 border-0 rounded-2xl font-bold mb-4 focus:ring-2 focus:ring-brand shadow-inner" type="text" placeholder="Adres arayın...">
                    <div id="modalMap"></div>
                    <div class="mt-6 flex gap-3">
                        <button type="button" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black uppercase" data-bs-dismiss="modal">Kapat</button>
                        <button type="button" class="flex-[2] py-4 bg-brand text-white rounded-2xl font-black uppercase shadow-lg shadow-brand/20" id="confirmLocation">KONUMU ONAYLA</button>
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
            geocoder = new google.maps.Geocoder();

            // Repeater Tanımı (sadece name indexleme için)
            var $repeater = $('.repeater').repeater({
                initEmpty: false,
                show: function () { $(this).slideDown(); },
                hide: function (deleteElement) { $(this).slideUp(deleteElement); }
            });

            // Repeater'ın kendi create handler'ını devre dışı bırak
            $('[data-repeater-create]').removeAttr('data-repeater-create').addClass('btn-add-address');

            // Manuel adres ekleme (tek seferde 1 tane)
            $(document).on('click', '.btn-add-address', function() {
                var $list = $('[data-repeater-list="address"]');
                var $items = $list.children('[data-repeater-item]');
                var newIndex = $items.length;
                var $clone = $items.first().clone();

                // Tüm inputları temizle
                $clone.find('input[type="text"], input[type="hidden"]').val('');

                // Name attribute'larını yeni index ile güncelle
                $clone.find('[name]').each(function() {
                    var name = $(this).attr('name');
                    $(this).attr('name', name.replace(/\[\d+\]/, '[' + newIndex + ']'));
                });

                // Status badge sıfırla
                $clone.find('.status-badge')
                    .removeClass('bg-green-50 text-green-600')
                    .addClass('bg-red-50 text-red-500')
                    .html('<i class="fas fa-exclamation-triangle mr-1"></i> Konum Seçilmedi');

                $clone.hide().appendTo($list).slideDown();
            });

            // Silme: Swal ile onay
            $(document).on('click', '.btn-remove-address', function() {
                var $item = $(this).closest('[data-repeater-item]');
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: 'Bu adresi silmek istediğinize emin misiniz?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Evet, Sil',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $item.slideUp(function() { $(this).remove(); });
                    }
                });
            });

            // Form Gönderim Kontrolü
            $('#customerForm').on('submit', function(e) {
                let allSet = true;
                $('[data-repeater-item]:visible .input-lat').each(function() {
                    if (!$(this).val()) { allSet = false; }
                });
                if (!allSet) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konum Eksik!',
                        text: "Lütfen her adres için haritadan konum seçerek 'Onayla' butonuna basınız!",
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Tamam'
                    });
                }
            });

            // Harita İlk Kurulum
            function initMap() {
                const initialPos = {!! $location !!};
                map = new google.maps.Map(document.getElementById("modalMap"), {
                    center: initialPos,
                    zoom: 14,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false
                });

                marker = new google.maps.Marker({
                    position: initialPos,
                    map: map,
                    draggable: true,
                    animation: google.maps.Animation.DROP
                });

                // Modal içi Arama (Autocomplete)
                autocomplete = new google.maps.places.Autocomplete(document.getElementById("modal-search"));
                autocomplete.bindTo("bounds", map);

                autocomplete.addListener("place_changed", () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry || !place.geometry.location) return;

                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                    marker.setPosition(place.geometry.location);
                });

                // Haritaya tıklandığında marker taşı
                map.addListener("click", (e) => {
                    marker.setPosition(e.latLng);
                });
            }

            initMap();

            // Modal Açma ve Satır Belirleme
            $(document).on('click', '.open-map-modal', function() {
                // Tıklanan butona ait en yakın repeater satırını hafızaya al
                currentRow = $(this).closest('[data-repeater-item]');

                $('#mapModal').modal('show');

                // Haritayı ve Marker'ı satırdaki mevcut konuma (varsa) göre güncelle
                setTimeout(() => {
                    google.maps.event.trigger(map, 'resize');
                    let lat = currentRow.find('.input-lat').val();
                    let lng = currentRow.find('.input-lng').val();

                    if (lat && lng) {
                        let pos = { lat: parseFloat(lat), lng: parseFloat(lng) };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                    } else {
                        const defaultPos = {!! $location !!};
                        map.setCenter(defaultPos);
                        marker.setPosition(defaultPos);
                    }
                }, 350);
            });

            // Modal Fix (Z-Index Sorunu İçin)
            $('#mapModal').on('shown.bs.modal', function () {
                $(this).appendTo('body');
            });

            // KONUMU ONAYLA BUTONU
            $('#confirmLocation').off('click').on('click', function() {
                const pos = marker.getPosition();
                const lat = pos.lat().toFixed(6);
                const lng = pos.lng().toFixed(6);

                // Satırdaki hidden inputlara koordinatları bas
                currentRow.find('.input-lat').val(lat);
                currentRow.find('.input-lng').val(lng);

                // Badge durumunu güncelle
                currentRow.find('.status-badge')
                    .removeClass('bg-red-50 text-red-500')
                    .addClass('bg-green-50 text-green-600')
                    .html(`<i class="fas fa-check-circle mr-1"></i> Konum Onaylandı`);

                // Reverse Geocoding (Koordinattan Adres Çıkarma)
                geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        const components = results[0].address_components;
                        const fullAddress = results[0].formatted_address;

                        let neighborhood = '', street = '', bNo = '', district = '';

                        components.forEach(c => {
                            const types = c.types;
                            if (types.includes("neighborhood") || types.includes("sublocality_level_1")) {
                                neighborhood = c.long_name;
                            }
                            if (types.includes("administrative_area_level_2") || types.includes("district")) {
                                district = c.long_name;
                            }
                            if (types.includes("route")) {
                                street = c.long_name;
                            }
                            if (types.includes("street_number")) {
                                bNo = c.long_name;
                            }
                        });

                        // Fallback (Bulunamazsa adresin başını al)
                        if (!neighborhood && results[0].address_components[0]) {
                            neighborhood = results[0].address_components[0].long_name;
                        }

                        // Inputlara veriyi yerleştir
                        currentRow.find('.addr-mahalle').val(neighborhood);
                        currentRow.find('.addr-ilce').val(district);
                        currentRow.find('.addr-sokak').val(street);
                        currentRow.find('.addr-bina').val(bNo);
                        currentRow.find('.addr-tarif').val(fullAddress);

                        // Eğer başlık boşsa mahalle adını yazalım (opsiyonel)
                        if(!currentRow.find('input[name*="[name]"]').val()){
                            currentRow.find('input[name*="[name]"]').first().val(neighborhood);
                        }
                    }
                });

                $('#mapModal').modal('hide');
            });
        });
    </script>
@endsection
