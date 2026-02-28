@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    RESTAURANT <span class="text-slate-400">HAKEDİŞ RAPORU</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Finansal akışı izleyin ve ödeme kayıtlarını yönetin.
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
                        <input type="hidden" name="payable_type" value="restaurant">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Restaurant</label>
                                <select class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none" name="payable_id">
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{$restaurant->id}}">{{$restaurant->restaurant_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Ödeme Tarihi</label>
                                <input class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none" type="date" value="{{date('Y-m-d')}}" name="payment_date" required>
                            </div>
                            <div class="col-md-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Tutar (₺)</label>
                                <input type="text" class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none text-indigo-600" placeholder="0,00" name="amount_display" oninput="formatMoney(this)" inputmode="decimal">
                                <input type="hidden" name="amount">
                            </div>
                            <div class="col-md-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Not</label>
                                <input type="text" class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none" name="note" placeholder="İsteğe bağlı...">
                            </div>
                            <div class="col-md-2 flex items-end">
                                <button class="w-full bg-indigo-600 text-white py-3 !rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-900/10 border-0" type="submit">KAYDET</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-8">
            <div class="col-xl-4">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 p-8 h-100">
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-6">RAPOR SORGULAMA</h4>
                    <div class="space-y-4">
                        <select class="form-control !rounded-xl border-slate-100 font-bold text-xs" id="restaurant">
                            <option value="0">Restaurant Seçiniz</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{$restaurant->id}}">{{$restaurant->restaurant_name}}</option>
                            @endforeach
                        </select>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="date" value="{{date('Y-m-d')}}" class="form-control !rounded-xl border-slate-100 font-bold text-xs" id="start_date">
                            <input type="date" value="{{date('Y-m-d')}}" class="form-control !rounded-xl border-slate-100 font-bold text-xs" id="end_date">
                        </div>
                        <button class="w-full bg-[#0f172a] text-white py-4 !rounded-2xl font-black text-[10px] uppercase tracking-widest border-0" onclick="ReportFilter()">
                            <i class="fa fa-filter me-2"></i> VERİLERİ GETİR
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 p-8 h-100">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest m-0">ÖZET VERİLER</h4>
                        <span id="selected-restaurant" class="text-[10px] font-black text-indigo-600 uppercase"></span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Sipariş Sayısı</p>
                            <h3 class="text-xl font-black text-slate-800" id="order-count">0</h3>
                        </div>
                        <div class="col-md-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Top. Hakediş</p>
                            <h3 class="text-xl font-black text-slate-800" id="total-progress-payment">0.00₺</h3>
                        </div>
                        <div class="col-md-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Yapılan Ödeme</p>
                            <h3 class="text-xl font-black text-emerald-500" id="paid-amount">0.00₺</h3>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-indigo-50 rounded-2xl border border-indigo-100">
                                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">KALAN BAKİYE</p>
                                <h3 class="text-xl font-black text-indigo-600" id="remaining-amount">0.00₺</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
            <div class="table-responsive p-4">
                <table id="paymentsTable" class="table !mb-0 border-0">
                    <thead>
                    <tr class="border-0">
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0">ALICI BİLGİSİ</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">ÖDEME TARİHİ</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">TUTAR</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-right">YÖNETİM</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    @foreach($records as $record)
                        <tr id="data_{{ $record->id }}" class="group transition-all hover:bg-slate-50/50">
                            <td class="py-6 px-8 border-0">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 bg-indigo-50 text-indigo-400 rounded-2xl flex items-center justify-center font-black text-sm uppercase">
                                        {{ substr(\App\Models\Restaurant::find($record->payable_id)->restaurant_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-700 m-0 uppercase tracking-tight">{{ \App\Models\Restaurant::where('id',$record->payable_id)->first()->restaurant_name }}</p>
                                        <p class="text-[10px] font-bold text-slate-300 m-0 uppercase mt-1">{{ $record->note ?? 'Not yok' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <p class="text-xs font-black text-slate-600 m-0">{{ \Carbon\Carbon::parse($record->payment_date)->format('d.m.Y') }}</p>
                                <span class="text-[10px] font-bold text-slate-300 uppercase">BANKA/NAKİT</span>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <span class="bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl text-xs font-black tracking-tight">
                                    {{ number_format($record->amount, 2) }} ₺
                                </span>
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
        /* DataTables Default Gizleme */
        #paymentsTable_wrapper .dataTables_filter,
        #paymentsTable_wrapper .dataTables_info,
        #paymentsTable_wrapper .dataTables_paginate { display: none; }

        /* Paginate Butonları - Indigo Style */
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
        .paginate_button:hover:not(.current) {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            var table = $('#paymentsTable').DataTable({
                order: [[1, 'desc']],
                dom: 'rtip',
                pageLength: 10,
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json" },
                drawCallback: function() {
                    $('#table-pagination-box').html($('.dataTables_paginate').html());
                    const info = table.page.info();
                    $('#table-info-box').html(`Showing ${info.start + 1} to ${info.end} of ${info.recordsTotal} entries`);
                }
            });

            // Pagination Click Event
            $(document).on('click', '#table-pagination-box .paginate_button', function() {
                if($(this).hasClass('next')) table.page('next').draw('page');
                else if($(this).hasClass('previous')) table.page('previous').draw('page');
                else table.page($(this).data('dt-idx')).draw('page');
            });
        });

        function ReportFilter() {
            var restaurant = $('#restaurant').val();
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            if (restaurant === '0') {
                Swal.fire({ icon: 'warning', title: 'Uyarı', text: 'Lütfen bir restoran seçiniz.', confirmButtonColor: '#0f172a' });
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/admin/progress-payment/restaurant' + '?_token=' + '{{ csrf_token() }}',
                data: {restaurant: restaurant, start: start, end: end},
                success: function (response) {
                    $("#selected-restaurant").text(response.restaurant_name);
                    $("#order-count").text(response.order_count);
                    $("#total-progress-payment").text(Number(response.total_progress_payment).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + '₺');
                    $("#paid-amount").text(Number(response.paidAmount).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + '₺');
                    let rem = Number(response.total_progress_payment) - Number(response.paidAmount);
                    $("#remaining-amount").text(rem.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + '₺');

                    // Tabloyu güncelle (Server'dan records_html geldiğini varsayıyorum)
                    $("#paymentsTable tbody").html(response.records_html);
                    $('#paymentsTable').DataTable().draw();
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
                background: '#ffffff',
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
                        success: function(data) {
                            if (data === "OK") {
                                $("#data_" + id).fadeOut();
                            }
                        }
                    });
                }
            });
        }

        function formatMoney(el) {
            let value = el.value.replace(/[^0-9,]/g, '');
            if (value.indexOf(',') !== -1) {
                const parts = value.split(',');
                value = parts[0] + ',' + parts[1].slice(0, 2);
            }
            let parts = value.split(',');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            el.value = parts.join(',');
            const hidden = document.querySelector('input[name="amount"]');
            hidden.value = el.value.replace(/\./g, '').replace(',', '.');
        }
    </script>
@endsection
