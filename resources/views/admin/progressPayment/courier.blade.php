@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    KURYE <span class="text-slate-400">HAKEDİŞ YÖNETİMİ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Kurye performansını izleyin ve ödeme süreçlerini yönetin.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="location.reload()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                    <i class="fa-solid fa-rotate text-sm"></i>
                </button>
                <button onclick="$('#paymentFormCard').toggle('slow')" class="inline-flex items-center gap-3 bg-[#0f172a] text-white px-8 py-3.5 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-slate-200 hover:scale-105 transition-all border-0">
                    <i class="fas fa-plus text-indigo-400"></i>
                    YENİ ÖDEME EKLE
                </button>
            </div>
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

        <div class="row mb-8" id="paymentFormCard" style="display: none;">
            <div class="col-12">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 p-8">
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-6">ÖDEME KAYDI OLUŞTUR</h4>
                    <form method="POST" action="{{ route('admin.progress.payments.store') }}">
                        @csrf
                        <input type="hidden" name="payable_type" value="courier">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Kurye Seçimi</label>
                                <select class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none" name="payable_id">
                                    @foreach($courierss as $courier)
                                        <option value="{{$courier->id}}">{{$courier->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Ödeme Tarihi</label>
                                <input class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none" type="date" value="{{date('Y-m-d')}}" name="payment_date" required>
                            </div>
                            <div class="col-md-3">
                                <x-money-input name="amount" label="Ödeme Tutarı" required="true" />
                            </div>
                            <div class="col-md-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Not</label>
                                <input type="text" class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none" name="note" placeholder="Örn: Haftalık hakediş">
                            </div>
                            <div class="col-md-12 flex justify-end mt-4">
                                <button class="bg-indigo-600 text-white px-10 py-3.5 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-indigo-900/10 border-0" type="submit">
                                    SİSTEME KAYDET
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-8">
            <div class="col-xl-4">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 p-8 h-100">
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-6">FİLTRELEME</h4>
                    <div class="space-y-4">
                        <select class="form-control !rounded-xl border-slate-100 font-bold text-xs" id="courier">
                            <option value="0">Kurye Seçiniz</option>
                            @foreach($courierss as $courier)
                                <option value="{{$courier->id}}">{{$courier->name}}</option>
                            @endforeach
                        </select>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="date" value="{{date('Y-m-d')}}" class="form-control !rounded-xl border-slate-100 font-bold text-xs" id="start_date">
                            <input type="date" value="{{date('Y-m-d')}}" class="form-control !rounded-xl border-slate-100 font-bold text-xs" id="end_date">
                        </div>
                        <button class="w-full bg-[#0f172a] text-white py-4 !rounded-2xl font-black text-[10px] uppercase tracking-widest border-0" onclick="ReportFilter()">
                            <i class="fa fa-filter me-2"></i> RAPORU GETİR
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 p-8 h-100">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest m-0">KURYE PERFORMANS ÖZETİ</h4>
                        <span id="selected-courier" class="text-[10px] font-black text-indigo-600 uppercase"></span>
                    </div>

                    <div id="calculation_info_box" class="bg-slate-50 border border-slate-100 p-4 rounded-2xl mb-6 flex items-center gap-3" style="display: none;">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-indigo-500 shadow-sm">
                            <i class="fa fa-calculator text-xs"></i>
                        </div>
                        <span id="calculation_info_text" class="text-[10px] font-bold text-slate-500 uppercase tracking-tight"></span>
                    </div>

                    <div class="row g-3">
                        <div id="fixed_price" style="display: none" class="col-md-4">
                            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Sabit Ücret</p>
                                <h4 class="text-base font-black text-slate-800 m-0" id="fixed-amount">0₺</h4>
                            </div>
                        </div>
                        <div id="km_price_card" style="display: none" class="col-md-4">
                            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Km Başı</p>
                                <h4 class="text-base font-black text-slate-800 m-0" id="km-amount">0₺</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Sipariş</p>
                                <h4 class="text-base font-black text-slate-800 m-0" id="order-count">0 Adet</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Top. Hakediş</p>
                                <h4 class="text-base font-black text-slate-800 m-0" id="total-progress-payment">0.00₺</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                                <p class="text-[9px] font-black text-emerald-500 uppercase mb-1">Yapılan Ödeme</p>
                                <h4 class="text-base font-black text-emerald-700 m-0" id="paid-amount">0.00₺</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-900/10">
                                <p class="text-[9px] font-black text-indigo-100 uppercase mb-1">Kalan Bakiye</p>
                                <h4 class="text-base font-black text-white m-0" id="remaining-amount">0.00₺</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden mt-4">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest m-0">ÖDEME GEÇMİŞİ</h4>
                <div class="relative">
                    <input type="text" id="custom-filter-payments" class="text-[10px] font-bold !rounded-xl border-slate-100 px-4 py-2" placeholder="Tabloda ara...">
                </div>
            </div>
            <div class="table-responsive p-4">
                <table id="paymentsTable" class="table !mb-0 border-0">
                    <thead>
                    <tr class="border-0">
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0">KURYE BİLGİSİ</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">ÖDEME TARİHİ</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">TUTAR</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">KAYIT</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-right">İŞLEM</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    @foreach($records as $record)
                        <tr id="data_{{ $record->id }}" class="group transition-all hover:bg-slate-50/50">
                            <td class="py-6 px-8 border-0">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-xl flex items-center justify-center font-black text-xs uppercase">
                                        {{ substr(\App\Models\Courier::find($record->payable_id)->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-700 m-0 uppercase tracking-tight">{{ \App\Models\Courier::where('id',$record->payable_id)->first()->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-300 m-0 uppercase mt-1">{{ $record->note ?? 'Detay yok' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <p class="text-xs font-black text-slate-600 m-0">{{ \Carbon\Carbon::parse($record->payment_date)->format('d.m.Y') }}</p>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <span class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-xs font-black tracking-tight">
                                    {{ number_format($record->amount, 2) }} ₺
                                </span>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ date('d.m.Y H:i', strtotime($record->created_at)) }}</p>
                            </td>
                            <td class="py-6 px-8 border-0">
                                <div class="flex justify-end gap-3">
                                    <button onclick="DeleteFunction({{ $record->id }})"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all border-0">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div id="custom-pagination-container" class="px-10 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div id="table-info-box" class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]"></div>
                <div id="table-pagination-box" class="flex items-center gap-2"></div>
            </div>
        </div>
    </div>

    <style>
        /* DataTables UI Gizleme */
        #paymentsTable_wrapper .dataTables_filter,
        #paymentsTable_wrapper .dataTables_info,
        #paymentsTable_wrapper .dataTables_paginate { display: none; }

        /* Paginate Butonları Styling */
        .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 38px !important;
            height: 38px !important;
            border-radius: 12px !important;
            font-size: 11px !important;
            font-weight: 900 !important;
            background: transparent !important;
            border: 0 !important;
            margin: 0 2px !important;
            cursor: pointer !important;
            color: #cbd5e1 !important;
            transition: all 0.2s;
        }
        .paginate_button.current {
            background: #0f172a !important;
            color: white !important;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2) !important;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#paymentsTable').DataTable({
                order: [[3, "desc"]],
                dom: 'rtip',
                pageLength: 10,
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json" },
                drawCallback: function() {
                    $('#table-pagination-box').html($('.dataTables_paginate').html());
                    const info = table.page.info();
                    $('#table-info-box').html(`GÖSTERİLEN: ${info.start + 1} - ${info.end} / TOPLAM: ${info.recordsTotal}`);
                }
            });

            $('#custom-filter-payments').on('keyup', function () {
                table.search(this.value).draw();
            });

            $(document).on('click', '#table-pagination-box .paginate_button', function() {
                if($(this).hasClass('next')) table.page('next').draw('page');
                else if($(this).hasClass('previous')) table.page('previous').draw('page');
                else table.page($(this).data('dt-idx')).draw('page');
            });
        });

        function ReportFilter() {
            var courier = $('#courier').val();
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            if (courier === '0') {
                Swal.fire({ icon: 'warning', title: 'UYARI', text: 'Lütfen bir kurye seçiniz.', confirmButtonColor: '#0f172a' });
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/admin/progress-payment/courier' + '?_token=' + '{{ csrf_token() }}',
                data: {courier: courier, start: start, end: end},
                success: function (response) {
                    if (response.calculation_info) {
                        $("#calculation_info_text").text(response.calculation_info);
                        $("#calculation_info_box").fadeIn();
                    } else {
                        $("#calculation_info_box").hide();
                    }

                    // Fiyat Tipine Göre Alanlar
                    const isKmBased = response.courier.price_type !== 'package';
                    $("#fixed_price, #km_price_card").toggle(isKmBased);

                    // Verileri Yazdır
                    $("#selected-courier").text(response.courier.name);
                    $("#order-count").text(response.order_count + ' Adet');

                    const formatNum = (num) => Number(num).toLocaleString('tr-TR', { minimumFractionDigits: 2 });

                    $("#fixed-amount").text(formatNum(response.fixed_amount) + ' ₺');
                    $("#km-amount").text(formatNum(response.courier.km_price) + ' ₺');
                    $("#total-progress-payment").text(formatNum(response.total_progress_payment) + ' ₺');
                    $("#paid-amount").text(formatNum(response.paidAmount) + ' ₺');

                    let remaining = Number(response.total_progress_payment) - Number(response.paidAmount);
                    $("#remaining-amount").text(formatNum(remaining) + ' ₺');

                    $("#paymentsTable tbody").html(response.records_html);
                    Swal.fire({ icon: 'success', title: 'BAŞARILI', text: 'Veriler güncellendi.', timer: 1500, showConfirmButton: false });
                }
            });
        }

        function DeleteFunction(id) {
            Swal.fire({
                title: 'SİLME İŞLEMİ',
                html: '<p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest">Bu ödeme kaydını silmek istediğinize emin misiniz?</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'EVET, SİL',
                cancelButtonText: 'VAZGEÇ',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[40px] border-0 p-12 shadow-2xl',
                    title: 'font-black tracking-tighter text-slate-800 text-2xl uppercase',
                    confirmButton: 'bg-slate-900 text-white px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2 hover:bg-rose-500 transition-all',
                    cancelButton: 'bg-slate-100 text-slate-400 px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/admin/progress-payment/record/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                $("#data_" + id).fadeOut();
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
