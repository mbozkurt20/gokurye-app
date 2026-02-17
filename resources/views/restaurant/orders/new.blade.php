<div class="h-full flex flex-col bg-slate-50 overflow-hidden shadow-2xl">
    <div class="bg-brand px-6 py-5 flex items-center justify-between shadow-lg z-10">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg text-white">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
                <h4 class="text-white font-black uppercase text-sm tracking-tighter m-0">Sipariş Ekranı</h4>
                <p class="text-white/60 text-[10px] uppercase font-bold tracking-widest">{{ Auth::user()->restaurant_name }}</p>
            </div>
        </div>
        <button onclick="toggleDrawer()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-all">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="bg-white border-b border-slate-200 p-4">
        <div class="flex flex-wrap gap-3 items-center justify-between">
            <div class="flex gap-2">
                <button type="button" onclick="openCustomerModal()"
                        class="bg-brand/10 text-brand px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-brand hover:text-white transition-all flex items-center gap-2">
                    <i class="fas fa-user-plus"></i> Müşteri Seç
                </button>
            </div>
            <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100">
                <i class="fas fa-user-circle text-brand text-xl opacity-50"></i>
                <div>
                    <span class="block text-[9px] uppercase font-black text-slate-400 tracking-tighter leading-none">Aktif Müşteri</span>
                    <span class="customer block text-xs font-bold text-slate-700">Seçili Müşteri Bulunmuyor</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">

        <div class="flex-1 overflow-y-auto p-4 border-r border-slate-200 custom-scrollbar">
            <form id="formPos" name="formPos" onsubmit="event.preventDefault(); CreateOrder();">
                <input type="hidden" name="payment_control" id="payment_control" value="0">
                <input type="hidden" name="user_id" id="customer_id" value="0">
                <input type="hidden" name="courier_id" id="courier_id" value="">
                <input type="hidden" name="total" id="totalPrice" value="">

                <div class="flex gap-2 overflow-x-auto pb-4 no-scrollbar">
                    @foreach($categories as $cat)
                        <button type="button"
                                class="category-tab whitespace-nowrap px-5 py-2.5 rounded-xl text-xs font-bold transition-all
                                       {{ $loop->first ? 'bg-brand text-white shadow-lg shadow-brand/20' : 'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50' }}"
                                onclick="switchCategory('cat_{{$cat->id}}', this)">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 mt-2">
                    @foreach($categories as $cat)
                        @foreach($cat->products->where('status','active') as $pro)
                            <div class="product-card group relative bg-white border border-slate-100 p-3 rounded-2xl hover:border-brand hover:shadow-xl hover:shadow-brand/10 transition-all cursor-pointer cat-content cat_{{$cat->id}} {{ $loop->parent->first ? '' : 'hidden' }}"
                                 onclick="productAdd({{$pro->id}})">
                                <div class="aspect-square rounded-xl mb-3 overflow-hidden bg-slate-50 flex items-center justify-center group-hover:bg-brand/5 transition-colors">
                                    @if($pro->image)
                                        <img src="{{ asset($pro->image) }}" alt="{{ $pro->name }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <i class="fas fa-utensils text-slate-300 group-hover:text-brand/40 text-2xl"></i>
                                    @endif
                                </div>
                                <h5 class="text-slate-800 font-bold text-sm leading-tight mb-1 truncate">{{$pro->name}}</h5>
                                <p class="text-brand font-black text-sm">{{ number_format($pro->price, 2, ',', '.') }} ₺</p>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </form>
        </div>

        <div class="w-full lg:w-[420px] bg-white flex flex-col border-t lg:border-t-0 border-slate-200 shadow-2xl">
            <div class="p-4 flex items-center justify-between border-b border-slate-50">
                <h5 class="font-black text-slate-800 text-sm flex items-center gap-2 uppercase">
                    <i class="fas fa-shopping-basket text-brand"></i> Sepetim
                    <span class="text-[10px] font-bold text-slate-400 normal-case" id="posTotalItem">0</span>
                </h5>
                <button type="button" onclick="removePos()" class="text-[10px] font-bold text-rose-500 hover:text-rose-700 uppercase tracking-tighter">
                    <i class="fas fa-trash-alt mr-1"></i> Temizle
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar min-h-[300px]" id="productItemLista">
            </div>

            {{-- Müşteri Seçim Modalı --}}
            <div class="modal fade" data-bs-backdrop="false" id="musteriAta" tabindex="-1" aria-labelledby="musteriAtaLabel" aria-hidden="true" style="z-index: 9999; background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(8px);">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-2xl" style="border-radius: 20px; background: #ffffff;">

                        <div class="px-8 pt-8 pb-4 flex justify-between items-center" style="background: linear-gradient(to right, #ffffff, #f8fafc); border-radius: 20px 20px 0 0;">
                            <div>
                                <h5 class="text-xl font-bold text-slate-900 tracking-tight mb-1" id="musteriAtaLabel" style="color: #4338ca;">Müşteri Atama</h5>
                                <div style="height: 3px; width: 40px; background: #4f46e5; border-radius: 10px;"></div>
                            </div>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Kapat" style="opacity: 0.5;"></button>
                        </div>

                        <div class="px-8 py-6">
                            <div class="mb-4">
                                <label for="customerSelect" class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 d-block">Sistem Kayıtlı Müşteriler</label>
                                <div class="position-relative">
                                    <select id="customerSelect" class="form-control js-example-basic-single w-full" onchange="customerSelect(event)" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 12px; transition: all 0.2s ease;">
                                        <option value="0">Müşteri arayın veya seçin...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3 p-4" style="background: #eef2ff; border-radius: 14px; border-left: 4px solid #4f46e5;">
                                <i class="fas fa-id-card mt-1" style="color: #4338ca; font-size: 14px;"></i>
                                <div>
                                    <p style="font-size: 12px; font-weight: 600; color: #3730a3; margin: 0;">Müşteri Bilgilendirme</p>
                                    <p style="font-size: 11px; color: #4338ca; opacity: 0.8; margin: 0; line-height: 1.4;">
                                        İşlem yapılacak müşteriyi seçtiğinizde profil bilgileri otomatik olarak forma aktarılacaktır.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 pb-8 pt-2 d-flex gap-3">
                            <a href="/restaurant/customers/new" class="btn flex-fill d-flex align-items-center justify-content-center gap-2" style="background: #ffffff; color: #475569; border: 2px solid #e2e8f0; border-radius: 12px; padding: 14px; font-size: 12px; font-weight: 700; transition: all 0.2s;">
                                <i class="fas fa-plus-circle"></i> YENİ KAYIT
                            </a>
                            <button type="button" class="btn flex-fill text-white shadow-lg shadow-indigo-200" style="background: #4f46e5; border: none; border-radius: 12px; padding: 14px; font-size: 12px; font-weight: 700; letter-spacing: 0.5px;" data-bs-dismiss="modal">
                                SEÇİMİ ONAYLA
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-200">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-xs font-bold text-slate-500 uppercase">Ödenecek</span>
                    <span class="text-3xl font-black text-slate-900 tracking-tighter" id="posTotal">0,00 TL</span>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-6">
                    @php
                        $methods = [
                            ['name' => 'Nakit', 'icon' => 'fa-money-bill-wave'],
                            ['name' => 'Kredi Kartı', 'icon' => 'fa-credit-card'],
                            ['name' => 'Ticket', 'icon' => 'fa-ticket-alt'],
                            ['name' => 'Sodexo', 'icon' => 'fa-utensils'],
                            ['name' => 'Pluxee', 'icon' => 'fa-star'],
                            ['name' => 'Multinet', 'icon' => 'fa-wallet'],
                        ];
                    @endphp
                    @foreach($methods as $m)
                        <button type="button" onclick="PaymentMethodSave('{{$m['name']}}', this)"
                                class="paymentRol group flex flex-col items-center justify-center p-3 rounded-2xl border-2 border-white bg-white hover:border-brand hover:bg-brand/5 hover:-translate-y-1 transition-all duration-300">
                            <i class="fas {{$m['icon']}} text-slate-400 group-hover:text-brand text-lg mb-1 transition-colors"></i>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-600 group-hover:text-brand">{{$m['name']}}</span>
                        </button>
                    @endforeach
                </div>

                <button type="button" onclick="CreateOrder()"
                        class="kayit w-full bg-brand text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-brand/30 hover:bg-brand-dark transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-check-circle text-lg"></i> SİPARİŞİ TAMAMLA
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .paymentRol.active {
        background-color: #5850ec !important;
        border-color: #4338ca !important;
        box-shadow: 0 8px 15px rgba(88, 80, 236, 0.3);
        transform: translateY(-2px);
    }
    .paymentRol.active i, .paymentRol.active span { color: white !important; }
    .select2-container--open { z-index: 10000 !important; }
    .select2-container .select2-selection--single {
        height: 50px !important;
        background-color: #f8fafc !important;
        border: none !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        loadCustomers();
        refreshBasket();
    });

    // Müşteri modalını aç (backdrop: false ile)
    function openCustomerModal() {
        const modalEl = document.getElementById('musteriAta');
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) modal = new bootstrap.Modal(modalEl, { backdrop: false });
        modal.show();
    }

    // Kategori Değiştirme
    function switchCategory(catId, btn) {
        document.querySelectorAll('.cat-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.' + catId).forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.category-tab').forEach(el => {
            el.classList.remove('bg-brand', 'text-white', 'shadow-lg', 'shadow-brand/20');
            el.classList.add('bg-white', 'text-slate-500', 'border-slate-200');
        });
        btn.classList.add('bg-brand', 'text-white', 'shadow-lg', 'shadow-brand/20');
        btn.classList.remove('bg-white', 'text-slate-500', 'border-slate-200');
    }

    // Ödeme Yöntemi Seçimi
    function PaymentMethodSave(methodName, element) {
        $('#payment_control').val(methodName);
        new Audio('{{url("pos/audio/beep.mp3")}}').play().catch(function(){});
        $('.paymentRol').removeClass('active');
        $(element).addClass('active');
    }

    // Sepeti Yenileme
    function refreshBasket() {
        $.ajax({
            type: 'GET',
            url: '/restaurant/get-pos-items',
            success: function (data) {
                $('#productItemLista').html(data.items);
                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                $('#totalPrice').val(data.total);
            }
        });
    }

    // Sipariş Oluşturma
    function CreateOrder() {
        const btn = $('.kayit');
        const originalContent = btn.html();

        let products = [];
        $('.item').each(function () {
            let p_id = $(this).find('input[name="product_id"]').val();
            let p_qty = $(this).find('input[name="quantity"]').val();
            if (p_id) {
                products.push({ product_id: p_id, quantity: p_qty });
            }
        });

        // Doğrulamalar
        if ($('#payment_control').val() == "0") {
            Swal.fire({
                title: 'ÖDEME YÖNTEMİ EKSİK',
                text: 'Lütfen devam etmeden önce bir ödeme yöntemi seçin.',
                icon: 'warning',
                confirmButtonText: 'ANLADIM',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[32px] border-0 shadow-2xl p-8',
                    title: 'text-xl font-black text-slate-800 tracking-tighter uppercase',
                    htmlContainer: 'text-slate-500 font-medium',
                    confirmButton: 'bg-slate-900 text-white px-8 py-3 rounded-2xl font-black text-xs tracking-widest hover:bg-brand transition-all outline-none'
                }
            });
            return;
        }

        if ($('#customer_id').val() == "0" || !$('#customer_id').val()) {
            Swal.fire({
                title: 'MÜŞTERİ SEÇİLMEDİ',
                text: 'Siparişi tamamlamak için bir müşteri atamanız gerekiyor.',
                icon: 'warning',
                confirmButtonText: 'MÜŞTERİ SEÇ',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[32px] border-0 shadow-2xl p-8',
                    title: 'text-xl font-black text-slate-800 tracking-tighter uppercase',
                    htmlContainer: 'text-slate-500 font-medium',
                    confirmButton: 'bg-brand text-white px-8 py-3 rounded-2xl font-black text-xs tracking-widest hover:bg-brand-dark transition-all outline-none shadow-lg shadow-brand/20'
                }
            }).then(function(result) {
                if (result.isConfirmed) openCustomerModal();
            });
            return;
        }

        if (products.length === 0) {
            Swal.fire({ title: 'Sepetinizde ürün bulunmuyor!', icon: 'warning', confirmButtonText: 'Tamam' });
            return;
        }

        // Buton Kilitle
        btn.prop('disabled', true).addClass('opacity-50').html('<i class="fas fa-spinner fa-spin"></i> İşleniyor...');

        $.ajax({
            type: 'POST',
            url: '/restaurant/orders/addOrder',
            data: {
                _token: '{{ csrf_token() }}',
                customer_id: $('#customer_id').val(),
                payment_method: $('#payment_control').val(),
                courier_id: $('#courier_id').val(),
                coupon_id: $('#coupon_id').val(),
                products: products,
                amount: $('#totalPrice').val()
            },
            success: function (response) {
                if (response.status === "OK") {
                    removePos();
                    toastr.success("Sipariş Başarıyla Eklendi", "Harika!", {
                        timeOut: 4000,
                        extendedTimeOut: 1000,
                        progressBar: true,
                        newestOnTop: true,
                        closeButton: true,
                        positionClass: "toast-top-right",
                        showMethod: "slideDown",
                        hideMethod: "fadeOut",
                        showDuration: 400,
                        hideDuration: 400,
                        closeHtml: '<button><i class="fa fa-times"></i></button>',
                        tapToDismiss: false
                    });
                } else {
                    Swal.fire({
                        title: 'HATA OLUŞTU',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'TAMAM',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'rounded-[32px] border-0 shadow-2xl p-8',
                            title: 'text-xl font-black text-red-600 tracking-tighter uppercase',
                            htmlContainer: 'text-slate-500 font-medium',
                            confirmButton: 'bg-slate-900 text-white px-10 py-3 rounded-2xl font-black text-xs tracking-widest hover:bg-red-600 transition-all outline-none'
                        }
                    });
                }
            },
            error: function() {
                toastr.error("Sunucu hatası oluştu!");
            },
            complete: function() {
                btn.prop('disabled', false).removeClass('opacity-50').html(originalContent);
            }
        });
    }

    // Ürün Ekleme
    function productAdd(e) {
        $.ajax({
            type: 'GET',
            url: '/restaurant/orders/addPOS/' + e,
            success: function (data) {
                new Audio('{{url("pos/audio/dot.mp3")}}').play().catch(function(){});
                if (data.durum === "yok") {
                    $('#productItemLista').append(data.items);
                } else {
                    let currentQty = parseInt($('#quantity_' + e).val());
                    $('#quantity_' + e).val(currentQty + 1);
                }
                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                $('#totalPrice').val(data.total);
            }
        });
    }

    // Adet Artır
    function updatePlus(id) {
        $.ajax({
            type: 'GET',
            url: '/restaurant/orders/updatePlusPOS/' + id,
            success: function (data) {
                new Audio('{{url("pos/audio/dot.mp3")}}').play().catch(function(){});
                let currentQty = parseInt($('#quantity_' + id).val());
                $('#quantity_' + id).val(currentQty + 1);
                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                $('#totalPrice').val(data.total);
            }
        });
    }

    // Adet Azalt
    function updateMinus(id) {
        let qty = parseInt($('#quantity_' + id).val());
        $.ajax({
            type: 'GET',
            url: '/restaurant/orders/updateMinusPOS/' + id + '/' + qty,
            success: function (data) {
                new Audio('{{url("pos/audio/dot.mp3")}}').play().catch(function(){});
                if (qty <= 1) {
                    $("#posItem_" + id).remove();
                } else {
                    $('#quantity_' + id).val(qty - 1);
                }
                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                $('#totalPrice').val(data.total);
            }
        });
    }

    // Sepeti Temizle
    function removePos() {
        $.ajax({
            type: 'GET',
            url: '/restaurant/orders/removePOS',
            success: function () {
                new Audio('{{url("pos/audio/trash.mp3")}}').play().catch(function(){});
                $('#productItemLista').html('');
                $('.customer').html('Seçili Müşteri Bulunmuyor');
                $('#customer_id').val(0);
                $('#posTotalItem').html('0');
                $('#posTotal').html('0,00 TL');
                $('#totalPrice').val(0);
                $('.paymentRol').removeClass('active');
                $('#payment_control').val(0);
                // Select2 sıfırla
                $('#customerSelect').val('0').trigger('change');
            }
        });
    }

    // Müşterileri Yükle
    function loadCustomers() {
        $.ajax({
            type: 'GET',
            url: '/restaurant/customers/get-customers',
            success: function (data) {
                var select = $('#customerSelect');
                select.empty().append('<option value="0">Müşteri Seçiniz...</option>');
                $.each(data.customers, function(i, c) {
                    select.append('<option value="' + c.id + '">' + c.name + ' - ' + c.phone + '</option>');
                });
                select.select2({ dropdownParent: $('#musteriAta'), width: '100%' });
            }
        });
    }

    // Müşteri Seçildiğinde
    function customerSelect(e) {
        var cid = e.target.value;
        if (cid == "0") return;
        $.ajax({
            type: 'GET',
            url: '/restaurant/orders/customerpos/' + cid,
            success: function (data) {
                var modalEl = document.getElementById('musteriAta');
                var modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
                $('.customer').html(data.customer);
                $('#customer_id').val(cid);
            }
        });
    }
</script>
