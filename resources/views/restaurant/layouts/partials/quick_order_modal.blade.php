<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>

<div id="quickOrderModal" class="custom-modal">
    <div class="custom-modal-content border-0 shadow-2xl overflow-hidden !rounded-[32px]">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <form id="quickOrderForm" class="p-0">
            @csrf
            <div class="bg-slate-900 p-6 flex justify-between items-center">
                <h4 class="text-white font-black tracking-tighter uppercase mb-0 flex items-center gap-2">
                    <i class="fa fa-rocket text-brand text-xl"></i> HIZLI SİPARİŞ
                </h4>
            </div>

            <div class="p-0 overflow-y-auto" style="flex:1; min-height:0;">
                <div class="p-6">
                    <div class="row g-4">

                        {{-- Müşteri Bilgileri --}}
                        <div class="col-12">
                            <div class="bg-slate-50 p-5 rounded-[24px] border border-slate-100">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block ml-1">Telefon <small class="text-brand">*</small></label>
                                        <div class="modern-input-wrapper">
                                            @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block ml-1">Müşteri Adı</label>
                                        <input type="text" name="full_name" id="full_name" class="form-control !rounded-2xl border-0 bg-white p-3 font-bold text-slate-700 shadow-sm" placeholder="Ad Soyad" required>
                                    </div>
                                    <div id="existing-addresses-area" class="col-12 mt-2" style="display: none;">
                                        <label class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-2 block ml-1"><i class="fa fa-list-ul me-1"></i> Kayıtlı Adresleri</label>
                                        <select id="address_selector" class="form-select !rounded-2xl border-2 border-green-100 bg-white font-bold text-slate-700 py-3 cursor-pointer">
                                            <option value="new">+ Yeni Adres Kullan / Haritadan Seç</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Harita --}}
                        <div class="col-md-7">
                            <div class="flex justify-between items-center mb-3 px-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block"><i class="fa fa-map-marked-alt me-1"></i> Konum Seçimi</label>
                                <span id="location-badge" class="px-3 py-1 bg-red-50 text-red-500 text-[9px] font-black rounded-lg uppercase tracking-tighter border border-red-100">Konum Seçilmedi</span>
                            </div>
                            <div class="relative mb-3">
                                <input id="quick-map-search" class="form-control !rounded-2xl border-2 border-slate-100 bg-white p-3 pl-10 font-bold text-slate-700 transition-all focus:border-brand shadow-sm" type="text" placeholder="Adres veya bina ara...">
                                <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                            <div id="quickOrderMap" class="shadow-md border-4 border-white" style="height: 320px; width: 100%; border-radius: 24px;"></div>
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <div class="bg-slate-100/50 p-2 rounded-xl border border-slate-200/60 flex items-center gap-2">
                                        <span class="text-[9px] font-black text-slate-400 uppercase">Lat:</span>
                                        <input type="text" name="latitude" id="quick-lat" class="bg-transparent border-0 font-bold text-slate-600 text-xs w-full outline-none" readonly required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-slate-100/50 p-2 rounded-xl border border-slate-200/60 flex items-center gap-2">
                                        <span class="text-[9px] font-black text-slate-400 uppercase">Lng:</span>
                                        <input type="text" name="longitude" id="quick-lng" class="bg-transparent border-0 font-bold text-slate-600 text-xs w-full outline-none" readonly required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Adres Detayları --}}
                        <div class="col-md-5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block px-1">
                                <i class="fa fa-location-dot me-1"></i> Adres Detayları
                            </label>
                            <div class="space-y-2">

                                <div class="addr-field-group">
                                    <span class="addr-field-label">Mahalle</span>
                                    <input type="text" name="mahalle" id="quick-mahalle"
                                           class="addr-field-input"
                                           placeholder="Mahalle adı" required>
                                </div>

                                <div class="addr-field-group">
                                    <span class="addr-field-label">Sokak / Cadde</span>
                                    <input type="text" name="sokak_cadde" id="quick-sokak"
                                           class="addr-field-input"
                                           placeholder="Sokak veya cadde adı" required>
                                </div>

                                <div class="row g-2">
                                    <div class="col-5">
                                        <div class="addr-field-group">
                                            <span class="addr-field-label">Bina No</span>
                                            <input type="text" name="bina_no" id="quick-bina"
                                                   class="addr-field-input"
                                                   placeholder="— ">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="addr-field-group">
                                            <span class="addr-field-label">Kat</span>
                                            <input type="text" name="kat" id="quick-kat"
                                                   class="addr-field-input"
                                                   placeholder="—">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="addr-field-group">
                                            <span class="addr-field-label">Daire</span>
                                            <input type="text" name="daire_no" id="quick-daire"
                                                   class="addr-field-input"
                                                   placeholder="—">
                                        </div>
                                    </div>
                                </div>

                                <div class="addr-field-group">
                                    <span class="addr-field-label">Adres Tarifi</span>
                                    <textarea name="adress_tarifi" id="quick-tarif"
                                              class="addr-field-input !rounded-2xl resize-none"
                                              rows="4"
                                              placeholder="Örn: Parkın karşısındaki mavi bina, zil çalışmıyor..."></textarea>
                                </div>

                            </div>
                            <input type="hidden" name="ilce" id="quick-ilce">
                            <input type="hidden" name="customer_id" id="quick-customer-id">
                        </div>

                        {{-- Ödeme --}}
                        <div class="col-12 mt-2">
                            <div class="bg-slate-900 p-5 rounded-[24px] shadow-xl">
                                <input type="hidden" name="payment_method" id="payment_method_input" value="Kapıda Nakit İle Ödeme">

                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Ödeme Yöntemi</label>
                                <div class="grid grid-cols-3 gap-3 mb-5">
                                    <label class="pay-method-card pay-method-active" data-value="Kapıda Nakit İle Ödeme">
                                        <div class="pay-method-icon bg-emerald-500/20 text-emerald-400">
                                            <i class="fa-solid fa-money-bill-wave"></i>
                                        </div>
                                        <span class="pay-method-label">Nakit</span>
                                        <div class="pay-method-check"><i class="fa-solid fa-check text-[9px]"></i></div>
                                    </label>
                                    <label class="pay-method-card" data-value="Kapıda Kredi Kartı İle Ödeme">
                                        <div class="pay-method-icon bg-blue-500/20 text-blue-400">
                                            <i class="fa-solid fa-credit-card"></i>
                                        </div>
                                        <span class="pay-method-label">Kredi Kartı</span>
                                        <div class="pay-method-check"><i class="fa-solid fa-check text-[9px]"></i></div>
                                    </label>
                                    <label class="pay-method-card" data-value="Kapıda Ticket İle Ödeme">
                                        <div class="pay-method-icon bg-amber-500/20 text-amber-400">
                                            <i class="fa-solid fa-ticket"></i>
                                        </div>
                                        <span class="pay-method-label">Ticket / Yemek Kartı</span>
                                        <div class="pay-method-check"><i class="fa-solid fa-check text-[9px]"></i></div>
                                    </label>
                                </div>

                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Toplam Tutar</label>
                                <x-money-input
                                    name="amount"
                                    label=""
                                    required="true"
                                    class="!rounded-xl border-0 bg-white text-slate-900 font-black text-center py-3 shadow-lg text-lg"
                                />
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="p-6 bg-white border-t border-slate-100 px-8">
                <button id="submitOrderBtn" type="submit" class="w-full py-4 bg-slate-200 text-slate-400 !rounded-[20px] font-black text-sm uppercase tracking-[2px] transition-all shadow-lg shadow-slate-200/50" disabled>
                    <i class="fa fa-check-circle me-2"></i> KONUM SEÇİNİZ
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-modal {
        display: none; position: fixed; z-index: 9999; left: 0; top: 0;
        width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        justify-content: center; align-items: center; padding: 20px;
    }
    .custom-modal-content {
        background: #f8fafc; width: 100%; max-width: 1060px; position: relative;
        animation: modalSlideUp 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
        max-height: 92vh;
        display: flex; flex-direction: column;
    }
    @keyframes modalSlideUp {
        from { transform: translateY(40px) scale(0.98); opacity: 0; }
        to   { transform: translateY(0) scale(1); opacity: 1; }
    }
    .close-btn {
        position: absolute; top: 20px; right: 25px;
        font-size: 24px; cursor: pointer; color: #fff; z-index: 100; transition: transform 0.2s;
    }
    .close-btn:hover { transform: rotate(90deg); color: #ef4444; }

    /* Address field */
    .addr-field-group {
        background: #fff;
        border: 2px solid #f1f5f9;
        border-radius: 16px;
        padding: 10px 14px 8px;
        transition: border-color 0.2s;
    }
    .addr-field-group:focus-within {
        border-color: #6366f1;
    }
    .addr-field-label {
        display: block;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #94a3b8;
        margin-bottom: 3px;
    }
    .addr-field-input {
        display: block;
        width: 100%;
        background: transparent;
        border: none;
        outline: none;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        padding: 0;
        box-shadow: none !important;
    }
    .addr-field-input::placeholder { color: #cbd5e1; font-weight: 600; }
    textarea.addr-field-input { padding-top: 2px; }

    /* Submit Button States */
    .btn-active-premium {
        background: #1e293b !important; color: white !important;
        cursor: pointer !important; box-shadow: 0 10px 25px -5px rgba(30, 41, 59, 0.4) !important;
    }
    .btn-active-premium:hover { transform: translateY(-2px); }
    .btn-processing-premium { background: #5850ec !important; color: white !important; cursor: wait !important; }

    /* Payment Method Cards */
    .pay-method-card {
        display: flex; flex-direction: column; align-items: center;
        gap: 8px; padding: 14px 10px 12px;
        border-radius: 16px;
        border: 2px solid rgba(255,255,255,0.07);
        background: rgba(255,255,255,0.04);
        cursor: pointer; transition: all 0.18s ease;
        user-select: none; text-align: center; position: relative;
    }
    .pay-method-card:hover { background: rgba(255,255,255,0.09); border-color: rgba(255,255,255,0.15); }
    .pay-method-card.pay-method-active { background: rgba(255,255,255,0.11); border-color: rgba(255,255,255,0.28); }
    .pay-method-icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
    }
    .pay-method-label {
        font-size: 10px; font-weight: 800;
        color: #fff; text-transform: uppercase; letter-spacing: 0.06em;
        line-height: 1.3;
    }
    .pay-method-check {
        position: absolute; top: 7px; right: 7px;
        width: 18px; height: 18px; border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        color: transparent; transition: all 0.18s;
        font-size: 8px;
    }
    .pay-method-card.pay-method-active .pay-method-check {
        background: #22c55e; border-color: #22c55e; color: #fff;
    }

    /* Form Overrides */
    .modern-input-wrapper input {
        border-radius: 1rem !important; border: 0 !important;
        background: #f1f5f9 !important; padding: 0.75rem !important; font-weight: 700 !important;
    }
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

                showOrderToast('success', 'Sipariş Verildi', 'Siparişiniz başarıyla sisteme işlendi.');

                if ($.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable().ajax.reload(null, false);
                }
            } else {
                const body = await response.json().catch(() => ({}));
                if (body.status === 'BalanceError') {
                    showOrderToast('error', 'Yetersiz Bakiye', body.message || 'Kontör bakiyeniz yetersiz.');
                } else {
                    showOrderToast('error', 'Hata', 'Bilgileri kontrol ediniz.');
                }
            }
        } catch (error) {
            showOrderToast('error', 'Sistem Hatası', 'Sunucuya ulaşılamadı.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = "SİPARİŞİ ONAYLA";
            submitBtn.classList.remove('btn-processing-premium');
        }
    });

    // Payment method card selection
    $(document).on('click', '.pay-method-card', function() {
        $('.pay-method-card').removeClass('pay-method-active');
        $(this).addClass('pay-method-active');
        $('#payment_method_input').val($(this).data('value'));
    });

    $(document).on('click', '#openModalBtn2', function() {
        $('#quickOrderModal').css('display', 'flex').hide().fadeIn(400);
        setTimeout(() => { if (!isMapInitialized) initQuickOrderMap(); else google.maps.event.trigger(quickMap, "resize"); }, 400);
    });

    $(document).on('click', '#closeModalBtn', function() { $('#quickOrderModal').fadeOut(300); });

    function showOrderToast(type, title, message) {
        const existing = document.getElementById('orderToast');
        if (existing) existing.remove();

        const isSuccess = type === 'success';
        const toast = document.createElement('div');
        toast.id = 'orderToast';
        toast.style.cssText = `
            position: fixed; top: 24px; right: 24px; z-index: 99999;
            display: flex; align-items: center; gap: 14px;
            background: #fff; border-radius: 20px;
            padding: 16px 20px;
            box-shadow: 0 20px 60px -10px rgba(0,0,0,0.18), 0 0 0 1px rgba(0,0,0,0.04);
            min-width: 300px; max-width: 380px;
            animation: toastIn 0.4s cubic-bezier(0.165,0.84,0.44,1);
            border-left: 4px solid ${isSuccess ? '#22c55e' : '#ef4444'};
        `;
        toast.innerHTML = `
            <div style="width:40px;height:40px;border-radius:12px;background:${isSuccess ? '#f0fdf4' : '#fef2f2'};
                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas ${isSuccess ? 'fa-rocket' : 'fa-triangle-exclamation'}"
                   style="color:${isSuccess ? '#22c55e' : '#ef4444'};font-size:16px;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="margin:0 0 2px;font-size:11px;font-weight:900;text-transform:uppercase;
                    letter-spacing:.08em;color:${isSuccess ? '#16a34a' : '#dc2626'};">${title}</p>
                <p style="margin:0;font-size:13px;font-weight:700;color:#334155;line-height:1.4;">${message}</p>
            </div>
            <div onclick="this.parentElement.remove()" style="cursor:pointer;color:#cbd5e1;font-size:18px;line-height:1;padding:2px 4px;">×</div>
        `;

        const style = document.getElementById('toastAnimStyle');
        if (!style) {
            const s = document.createElement('style');
            s.id = 'toastAnimStyle';
            s.textContent = `
                @keyframes toastIn { from{transform:translateX(120%) scale(.9);opacity:0} to{transform:translateX(0) scale(1);opacity:1} }
                @keyframes toastOut { from{transform:translateX(0);opacity:1} to{transform:translateX(120%);opacity:0} }
            `;
            document.head.appendChild(s);
        }

        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'toastOut 0.35s ease forwards';
            setTimeout(() => toast.remove(), 350);
        }, 3500);
    }
</script>
