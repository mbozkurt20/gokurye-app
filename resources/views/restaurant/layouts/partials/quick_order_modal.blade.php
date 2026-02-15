<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>

<div id="quickOrderModal" class="custom-modal">
    <div class="custom-modal-content border-0 shadow-2xl overflow-hidden !rounded-[32px]">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <form id="quickOrderForm" class="p-0">
            @csrf
            <div class="bg-slate-900 p-6">
                <h4 class="text-white font-black tracking-tighter uppercase mb-0 flex items-center gap-2">
                    <i class="fa fa-rocket text-brand"></i> HIZLI SİPARİŞ
                </h4>
            </div>

            <div class="p-8 max-h-[75vh] overflow-y-auto">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Telefon <small class="text-brand">*</small></label>
                        <div class="modern-input-wrapper">
                            @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Müşteri Adı</label>
                        <input type="text" name="full_name" id="full_name" class="form-control !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner" placeholder="Ad Soyad" required>
                    </div>

                    <div id="existing-addresses-area" class="col-12" style="display: none;">
                        <label class="text-[10px] font-black text-green-500 uppercase tracking-widest mb-2 block"><i class="fa fa-list-ul me-1"></i> Kayıtlı Adresleri</label>
                        <select id="address_selector" class="form-select !rounded-2xl border-2 border-green-100 bg-green-50/30 font-bold text-slate-700 py-3">
                            <option value="new">+ Yeni Adres Kullan / Haritadan Seç</option>
                        </select>
                    </div>

                    <div class="col-12"><div class="h-px bg-slate-100 w-full"></div></div>

                    <div class="col-md-7">
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block"><i class="fa fa-map-marked-alt me-1"></i> Konum Seçimi</label>
                            <span id="location-badge" class="px-3 py-1 bg-red-50 text-red-500 text-[9px] font-black rounded-lg uppercase tracking-tighter">Konum Seçilmedi</span>
                        </div>

                        <div class="relative mb-3">
                            <input id="quick-map-search" class="form-control !rounded-2xl border-0 bg-slate-100 p-3 pl-10 font-bold text-slate-700 shadow-inner" type="text" placeholder="Adres veya bina ara...">
                            <i class="fa fa-search absolute left-4 top-4 text-slate-400"></i>
                        </div>

                        <div id="quickOrderMap" class="shadow-sm border-4 border-slate-50" style="height: 300px; width: 100%; border-radius: 24px;"></div>

                        <div class="row g-2 mt-3">
                            <div class="col-6">
                                <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 flex items-center gap-2">
                                    <span class="text-[9px] font-black text-slate-400 uppercase">Lat:</span>
                                    <input type="text" name="latitude" id="quick-lat" class="bg-transparent border-0 font-bold text-slate-700 text-xs w-full outline-none" readonly required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 flex items-center gap-2">
                                    <span class="text-[9px] font-black text-slate-400 uppercase">Lng:</span>
                                    <input type="text" name="longitude" id="quick-lng" class="bg-transparent border-0 font-bold text-slate-700 text-xs w-full outline-none" readonly required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Adres Detayları</label>
                        <div class="space-y-3">
                            <input type="text" name="mahalle" id="quick-mahalle" class="form-control border-0 border-b-2 border-slate-100 rounded-0 bg-transparent font-bold text-slate-700 px-0 focus:border-brand transition-colors" placeholder="Mahalle" required>
                            <input type="text" name="sokak_cadde" id="quick-sokak" class="form-control border-0 border-b-2 border-slate-100 rounded-0 bg-transparent font-bold text-slate-700 px-0 focus:border-brand transition-colors" placeholder="Sokak / Cadde" required>

                            <div class="row g-2">
                                <div class="col-6"><input type="text" name="bina_no" id="quick-bina" class="form-control border-0 border-b-2 border-slate-100 rounded-0 bg-transparent font-bold text-slate-700 px-0" placeholder="Bina No" required></div>
                                <div class="col-3"><input type="text" name="kat" id="quick-kat" class="form-control border-0 border-b-2 border-slate-100 rounded-0 bg-transparent font-bold text-slate-700 px-0" placeholder="Kat"></div>
                                <div class="col-3"><input type="text" name="daire_no" id="quick-daire" class="form-control border-0 border-b-2 border-slate-100 rounded-0 bg-transparent font-bold text-slate-700 px-0" placeholder="Daire"></div>
                            </div>

                            <textarea name="adress_tarifi" id="quick-tarif" class="form-control !rounded-2xl border-0 bg-slate-50 p-3 font-bold text-slate-700 shadow-inner mt-3" rows="4" placeholder="Adres Tarifi..."></textarea>
                        </div>
                        <input type="hidden" name="ilce" id="quick-ilce">
                        <input type="hidden" name="customer_id" id="quick-customer-id">
                    </div>

                    <div class="col-12"><div class="h-px bg-slate-100 w-full my-2"></div></div>

                    <div class="col-md-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Ödeme Yöntemi</label>
                        <select name="payment_method" class="form-select !rounded-2xl border-0 bg-slate-100 font-bold text-slate-700 py-3">
                            <option value="Kapıda Nakit İle Ödeme">Kapıda Nakit</option>
                            <option value="Kapıda Kredi Kartı İle Ödeme">Kapıda Kredi Kartı</option>
                            <option value="Kapıda Ticket İle Ödeme">Kapıda Ticket</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Tutar (₺)</label>
                        <div class="amount-wrapper">
                            <x-money-input
                                name="amount"
                                label=""
                                required="true"
                                class="!rounded-2xl border-0 bg-slate-900 text-white font-black text-center py-3 shadow-lg"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-100">
                <button id="submitOrderBtn" type="submit" class="w-full py-4 bg-slate-200 text-slate-400 !rounded-[20px] font-black text-xs uppercase tracking-[2px] transition-all shadow-lg shadow-slate-200/50" disabled>
                    KONUM SEÇİNİZ
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); justify-content: center; align-items: center; padding: 20px; }
    .custom-modal-content { background: #fff; width: 100%; max-width: 900px; position: relative; animation: modalSlideUp 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); max-height: 90vh; }

    @keyframes modalSlideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .close-btn { position: absolute; top: 20px; right: 25px; font-size: 24px; cursor: pointer; color: #fff; z-index: 100; transition: transform 0.2s; }
    .close-btn:hover { transform: rotate(90deg); color: #ef4444; }

    /* Submit Button States */
    .btn-active-premium { background: #1e293b !important; color: white !important; cursor: pointer !important; box-shadow: 0 10px 25px -5px rgba(30, 41, 59, 0.4) !important; }
    .btn-active-premium:hover { transform: translateY(-2px); }
    .btn-processing-premium { background: #5850ec !important; color: white !important; cursor: wait !important; }

    /* Custom Scrollbar */
    .p-8::-webkit-scrollbar { width: 6px; }
    .p-8::-webkit-scrollbar-track { background: transparent; }
    .p-8::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    /* Form Overrides */
    .modern-input-wrapper input { border-radius: 1rem !important; border: 0 !important; background: #f1f5f9 !important; padding: 0.75rem !important; font-weight: 700 !important; }
</style>

<script>
    let quickMap, quickMarker, quickGeocoder, quickAutocomplete;
    let isMapInitialized = false;

    function updateLocationStatus() {
        const lat = $('#quick-lat').val();
        const btn = $('#submitOrderBtn');
        const badge = $('#location-badge');
        if (lat && lat !== "") {
            btn.prop('disabled', false)
                .text('SİPARİŞİ ONAYLA')
                .addClass('btn-active-premium');
            badge.removeClass('bg-red-50 text-red-500').addClass('bg-green-50 text-green-500').html('Konum Alındı <i class="fa fa-check ml-1"></i>');
        } else {
            btn.prop('disabled', true)
                .text('KONUM SEÇİNİZ')
                .removeClass('btn-active-premium');
            badge.removeClass('bg-green-50 text-green-500').addClass('bg-red-50 text-red-500').text('Konum Seçilmedi');
        }
    }

    function initQuickOrderMap() {
        if (typeof google === 'undefined') return;
        const initialPos = { lat: parseFloat("{{ auth()->user()->admin->latitude }}") || 37.1502, lng: parseFloat("{{ auth()->user()->admin->longitude }}") || 38.7790 };

        quickMap = new google.maps.Map(document.getElementById("quickOrderMap"), {
            center: initialPos,
            zoom: 15,
            mapTypeControl: false,
            streetViewControl: false,
            styles: [/* İstersen buraya Google Maps Silver Theme ekleyebilirsin */]
        });

        quickMarker = new google.maps.Marker({ position: initialPos, map: quickMap, draggable: true });
        quickGeocoder = new google.maps.Geocoder();

        quickAutocomplete = new google.maps.places.Autocomplete(document.getElementById("quick-map-search"));
        quickAutocomplete.addListener("place_changed", () => {
            const place = quickAutocomplete.getPlace();
            if (!place.geometry) return;
            quickMap.setCenter(place.geometry.location);
            quickMarker.setPosition(place.geometry.location);
            fillAddressFromPos(place.geometry.location);
        });

        quickMap.addListener("click", (e) => { quickMarker.setPosition(e.latLng); fillAddressFromPos(e.latLng); });
        quickMarker.addListener("dragend", (e) => { fillAddressFromPos(e.latLng); });
        isMapInitialized = true;
    }

    function fillAddressFromPos(pos) {
        const lat = pos.lat();
        const lng = pos.lng();
        $('#quick-lat').val(lat.toFixed(6));
        $('#quick-lng').val(lng.toFixed(6));
        updateLocationStatus();

        quickGeocoder.geocode({ location: pos }, (results, status) => {
            if (status === "OK" && results[0]) {
                const components = results[0].address_components;
                const fullAddr = results[0].formatted_address;
                let mahalle = '', ilce = '', sokak = '', bNo = '';

                components.forEach(c => {
                    const types = c.types;
                    if (types.includes("neighborhood") || types.includes("sublocality_level_1")) mahalle = c.long_name;
                    if (types.includes("administrative_area_level_2")) ilce = c.long_name;
                    if (types.includes("route")) sokak = c.long_name;
                    if (types.includes("street_number")) bNo = c.long_name;
                });

                if (!mahalle && fullAddr) {
                    const parts = fullAddr.split(',');
                    mahalle = parts[0].trim().replace(' Mahallesi', '').replace(' Mah.', '');
                }

                $('#quick-mahalle').val(mahalle);
                $('#quick-ilce').val(ilce);
                $('#quick-sokak').val(sokak);
                $('#quick-bina').val(bNo);
                $('#quick-tarif').val(fullAddr);
            }
        });
    }

    document.getElementById("quickOrderForm").addEventListener("submit", async function(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = document.getElementById("submitOrderBtn");

        submitBtn.disabled = true;
        submitBtn.textContent = "İŞLENİYOR...";
        submitBtn.classList.add('btn-processing-premium');

        const data = {
            restaurant_id: {{ auth()->user()->id }},
            full_name: form.full_name.value,
            phone: form.phone.value,
            ilce: form.ilce.value,
            mahalle: form.mahalle.value,
            sokak_cadde: form.sokak_cadde.value,
            bina_no: form.bina_no.value,
            kat: form.kat.value,
            daire_no: form.daire_no.value,
            adress_tarifi: form.adress_tarifi.value,
            latitude: form.latitude.value,
            longitude: form.longitude.value,
            verify_code: Math.floor(100000 + Math.random() * 900000),
            payment_method: form.payment_method.value,
            amount: form.amount.value,
            items: JSON.stringify([{ price: 0.00, unitSellingPrice: 0.00, quantity: 1, productId: 0, name: "Hızlı Sipariş" }])
        };

        try {
            const response = await fetch("{{ route('quick.order.store') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                form.reset();
                $('#quick-lat').val('');
                $('#quick-lng').val('');
                updateLocationStatus();
                $('#quickOrderModal').fadeOut(300);

                Swal.fire({
                    title: 'BAŞARILI',
                    text: 'Siparişiniz sisteme işlendi.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: '!rounded-[32px] border-0', title: '!font-black tracking-tighter' }
                });

                if ($.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable().ajax.reload(null, false);
                }
            } else {
                Swal.fire({ title: 'HATA', text: 'Bilgileri kontrol ediniz.', icon: 'error', customClass: { popup: '!rounded-[32px]' } });
            }
        } catch (error) {
            Swal.fire({ title: 'SİSTEM HATASI', text: 'Sunucuya ulaşılamadı.', icon: 'error', customClass: { popup: '!rounded-[32px]' } });
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = "SİPARİŞİ ONAYLA";
            submitBtn.classList.remove('btn-processing-premium');
        }
    });

    $(document).on('click', '#openModalBtn2', function() {
        $('#quickOrderModal').css('display', 'flex').hide().fadeIn(400);
        setTimeout(() => { if (!isMapInitialized) initQuickOrderMap(); else google.maps.event.trigger(quickMap, "resize"); }, 400);
    });

    $(document).on('click', '#closeModalBtn', function() { $('#quickOrderModal').fadeOut(300); });
</script>
