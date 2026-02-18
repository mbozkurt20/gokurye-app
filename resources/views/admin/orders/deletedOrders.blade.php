@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    İPTAL EDİLEN <span class="text-slate-400">SİPARİŞLER</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Toplam {{ count($orders) }} adet iptal edilmiş sipariş kaydı bulunmaktadır.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="location.reload()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                    <i class="fa-solid fa-rotate text-sm"></i>
                </button>
            </div>
        </div>

        <div class="mb-8">
            <div class="relative group">
                <input type="text" id="custom-filter-delete-admin"
                       class="w-full !rounded-[24px] border-0 bg-white shadow-sm p-5 pl-14 font-bold text-slate-500 focus:ring-2 focus:ring-indigo-100 transition-all placeholder:text-slate-300"
                       placeholder="Sipariş No, Müşteri Adı veya Telefon ile hızlı sorgulama yapın...">
                <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-400 transition-colors"></i>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
            <div class="table-responsive p-4">
                <table id="example56" class="table !mb-0 border-0">
                    <thead>
                    <tr class="border-0">
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0">Platform & Sipariş</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0">Müşteri Detay</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">Tutar</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">İptal Sebebi</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-right">Yönetim</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    @foreach ($orders as $order)
                        <tr id="data_{{ $order->id }}" class="group transition-all hover:bg-slate-50/50">
                            <td class="py-6 px-8 border-0">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center p-2 border border-slate-100 group-hover:bg-white transition-colors">
                                        @switch($order->platform)
                                            @case('yemeksepeti') <img src="{{ asset('theme/images/platforms/yemeksepeti.png') }}" class="w-full object-contain"> @break
                                            @case('getir') <img src="{{ asset('theme/images/platforms/getir.png') }}" class="w-full object-contain"> @break
                                            @case('migros') <img src="{{ asset('theme/images/platforms/migros.png') }}" class="w-full object-contain"> @break
                                            @case('trendyol') <img src="{{ asset('theme/images/platforms/trendyol.png') }}" class="w-full object-contain"> @break
                                            @case('adisyo') <img src="{{ asset('theme/images/adisyoFull.png') }}" class="w-full object-contain"> @break
                                            @case('telefonsiparis') <span class="text-[9px] font-black text-indigo-600">POS</span> @break
                                        @endswitch
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-slate-700 m-0 uppercase tracking-tight">#{{ $order->tracking_id }}</p>
                                        <p class="text-[10px] font-bold text-indigo-400 m-0 uppercase mt-1">{{ $order->payment_method === 'PAY_WITH_CARD' ? 'Kredi Kartı' : $order->payment_method }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-8 border-0">
                                <p class="text-sm font-black text-slate-700 m-0 uppercase tracking-tight">{{ $order->full_name }}</p>
                                <p class="text-[10px] font-bold text-slate-300 m-0 uppercase mt-1">{{ $order->phone }}</p>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <span class="text-xs font-black text-slate-700 bg-slate-100 px-4 py-2 rounded-xl">
                                    {{ number_format($order->amount, 2) }} TL
                                </span>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100">
                                    {{ $order->message ?? 'Belirtilmedi' }}
                                </span>
                            </td>
                            <td class="py-6 px-8 border-0">
                                <div class="flex justify-end gap-3">
                                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all border-0" data-bs-toggle="modal" data-bs-target="#Orders{{ $order->id }}">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-800 hover:text-white transition-all border-0" onclick="printDiv({{ $order->id }})">
                                        <i class="fas fa-print text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="Orders{{ $order->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content !rounded-[40px] border-0 shadow-2xl overflow-hidden">
                                    <div class="modal-header bg-slate-50 border-0 p-8">
                                        <h5 class="text-xl font-black tracking-tighter text-slate-800 uppercase m-0">SİPARİŞ <span class="text-slate-400">DETAYI</span></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-8" id="Printed{{ $order->id }}">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Müşteri</p>
                                                <h4 class="text-base font-black text-slate-800 m-0 uppercase">{{ $order->full_name }}</h4>
                                                <p class="text-xs font-bold text-slate-400 mt-1">{{ $order->phone }}</p>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Sipariş No</p>
                                                <h4 class="text-base font-black text-indigo-600 m-0">#{{ $order->tracking_id }}</h4>
                                                <p class="text-xs font-bold text-slate-400 mt-1">{{ $order->payment_method }}</p>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-4 bg-slate-50 rounded-3xl border border-slate-100">
                                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Teslimat Adresi</p>
                                                    <p class="text-xs font-bold text-slate-700 m-0 leading-relaxed">{{ $order->address }}</p>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <table class="table table-sm border-0 align-middle">
                                                    <thead>
                                                    <tr class="border-0">
                                                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-0 py-3">Ürün Adı</th>
                                                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-0 py-3 text-center">Adet</th>
                                                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-0 py-3 text-end">Fiyat</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-50">
                                                    @foreach (json_decode($order->items) as $item)
                                                        <tr class="border-0">
                                                            <td class="py-3 border-0 text-xs font-black text-slate-700 uppercase">{{ $item->name }}</td>
                                                            <td class="py-3 border-0 text-center text-xs font-bold text-slate-400">1</td>
                                                            <td class="py-3 border-0 text-end text-xs font-black text-slate-700">{{ number_format($item->price, 2) }} TL</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-12 mt-4">
                                                <div class="flex justify-between items-center p-6 bg-indigo-600 rounded-[24px] text-white">
                                                    <span class="text-xs font-black uppercase tracking-widest">TOPLAM TUTAR</span>
                                                    <span class="text-2xl font-black">{{ number_format($order->amount, 2) }} TL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        /* DataTable UI Cleanup */
        #example56_wrapper .dataTables_filter,
        #example56_wrapper .dataTables_info,
        #example56_wrapper .dataTables_paginate { display: none; }

        /* Paginate Buttons */
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
        .paginate_button:hover:not(.current) { background: #f1f5f9 !important; color: #0f172a !important; }

        .modal-backdrop { opacity: 0.2 !important; }
    </style>

    <script>
        $(document).ready(function () {
            let table = $('#example56').DataTable({
                order: [[1, 'desc']],
                dom: 'rtip',
                pageLength: 10,
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json" },
                drawCallback: function() {
                    $('#table-pagination-box').html($('.dataTables_paginate').html());
                    const info = table.page.info();
                    $('#table-info-box').html(`LİSTELENEN: ${info.start + 1} - ${info.end} / TOPLAM: ${info.recordsTotal}`);
                }
            });

            $('#custom-filter-delete-admin').on('keyup', function () {
                table.search(this.value).draw();
            });

            $(document).on('click', '#table-pagination-box .paginate_button', function() {
                if($(this).hasClass('next')) table.page('next').draw('page');
                else if($(this).hasClass('previous')) table.page('previous').draw('page');
                else table.page($(this).data('dt-idx')).draw('page');
            });
        });

        function printDiv(id) {
            const content = document.getElementById('Printed' + id).innerHTML;
            const printWindow = window.open('', '_blank', 'width=1000,height=800');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Sipariş Yazdır #${id}</title>
                    <style>
                        body { font-family: 'Inter', sans-serif; padding: 40px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
                        .total-box { background: #000; color: #fff; padding: 20px; margin-top: 30px; text-align: right; }
                        @media print { .no-print { display: none; } }
                    </style>
                </head>
                <body>${content}</body>
                <script>window.onload = function() { window.print(); window.close(); };<\/script>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
@endsection
