@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4 px-md-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none italic">
                    İPTAL <span class="text-red-500">KAYITLARI</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sistem üzerinden veya restoran tarafından iptal edilen siparişler.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="text" id="custom-filter-delete"
                           class="bg-white border-0 shadow-sm rounded-2xl py-3 px-5 ps-11 text-xs font-bold text-slate-600 focus:ring-2 focus:ring-red-100 transition-all w-64"
                           placeholder="İptal edilenlerde ara...">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                </div>
                <button onclick="location.reload();" class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center text-slate-400 hover:text-red-500 hover:rotate-180 transition-all duration-500 shadow-sm border-0">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] border border-slate-50 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="table-responsive p-4">
                <table id="example5" class="table table-hover align-middle border-0">
                    <thead>
                    <tr class="border-0">
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 py-4">Kanal</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Sipariş No</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Müşteri</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Tutar</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">İptal Sebebi</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 text-center">İşlem</th>
                    </tr>
                    </thead>
                    <tbody class="border-0">
                    @foreach ($orders as $order)
                        <tr id="data_{{ $order->id }}" class="group transition-all hover:bg-red-50/30 border-b border-slate-50 last:border-0">
                            <td class="px-4 py-4">
                                @php
                                    $platformStyles = [
                                        'getir' => ['img' => 'getiryemek.png', 'h' => '24px'],
                                        'yemeksepeti' => ['img' => 'yemeksepeti.png', 'h' => '12px'],
                                        'trendyol' => ['img' => 'trendyolyemek.png', 'h' => '14px'],
                                        'migros' => ['img' => 'MigrosYemek_White_logo.png', 'h' => '14px'],
                                        'adisyo' => ['img' => 'adisyoFull.png', 'h' => '14px']
                                    ];
                                    $style = $platformStyles[$order->platform] ?? null;
                                @endphp
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-100 group-hover:bg-white transition-colors grayscale">
                                    @if($style)
                                        <img src="{{ asset('theme/images/platforms/' . $style['img']) }}" style="height: {{ $style['h'] }}; max-width: 80%;">
                                    @else
                                        <span class="text-[10px] font-black text-slate-400">POS</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4"><span class="text-xs font-black text-slate-400 tracking-tighter">#{{ $order->tracking_id }}</span></td>
                            <td class="px-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-800 uppercase tracking-tighter">{{ $order->full_name }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 italic">{{ $order->phone }}</span>
                                </div>
                            </td>
                            <td class="px-4"><span class="text-xs font-black text-slate-800">{{ number_format($order->amount, 2) }} ₺</span></td>
                            <td class="px-4">
                                <div class="max-w-[200px]">
                                    <span class="text-[10px] font-bold text-red-400 italic leading-tight block truncate group-hover:whitespace-normal">
                                        <i class="fas fa-info-circle me-1"></i> {{ $order->message ?? 'Sebep belirtilmedi' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button data-bs-toggle="modal" data-bs-target="#Orders{{ $order->id }}" class="w-9 h-9 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-800 hover:border-slate-800 transition-all shadow-sm">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    <button onclick="printDiv({{ $order->id }})" class="w-9 h-9 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-500 hover:border-red-500 transition-all shadow-sm">
                                        <i class="fas fa-print text-xs"></i>
                                    </button>
                                </div>

                                <div class="modal fade" id="Orders{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content !rounded-[35px] border-0 shadow-2xl overflow-hidden">

                                            <div id="Printed{{ $order->id }}" class="p-8 bg-white">
                                                <div class="flex justify-between items-start mb-6">
                                                    <div>
                                                        <h5 class="text-sm font-black text-slate-800 uppercase tracking-widest m-0">İptal Edilen Sipariş</h5>
                                                        <span class="text-[10px] font-bold text-red-500 italic">#{{ $order->tracking_id }}</span>
                                                    </div>
                                                </div>

                                                <div class="bg-red-50 rounded-2xl p-4 border border-red-100 mb-6">
                                                    <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-1">İptal Açıklaması</p>
                                                    <p class="text-xs font-bold text-red-700 m-0 italic">"{{ $order->message ?? 'Açıklama bulunmuyor.' }}"</p>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4 mb-6">
                                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Müşteri</p>
                                                        <p class="text-xs font-black text-slate-700 uppercase m-0">{{ $order->full_name }}</p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-right">
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">İptal Edilen Tutar</p>
                                                        <p class="text-lg font-black text-slate-900 m-0">{{ number_format($order->amount, 2) }} ₺</p>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 ps-1">Sipariş İçeriği</p>
                                                    <div class="space-y-2">
                                                        @foreach(json_decode($order->items) as $item)
                                                            <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
                                                                <span class="text-xs font-bold text-slate-500 uppercase tracking-tighter">1x {{ $item->name }}</span>
                                                                <span class="text-xs font-black text-slate-400 tracking-tighter">{{ number_format($item->price, 2) }} TL</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-8 pt-0 flex gap-3">
                                                <button onclick="printDiv({{ $order->id }})" class="flex-1 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] border-0 transition-transform active:scale-95 shadow-lg shadow-slate-200">
                                                    FİŞİ YAZDIR
                                                </button>
                                                <button class="px-6 py-4 bg-slate-100 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] border-0" data-bs-dismiss="modal">
                                                    KAPAT
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* Backdrop Tıklanma Sorunu Çözümü */
        .modal-backdrop { display: none !important; }
        .modal { background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #ef4444 !important; color: white !important; border-radius: 12px; border: 0; font-weight: 900; font-size: 11px;
        }

        /* Yazıcı için özel ayarlar */
        @media print {
            body * { visibility: hidden; }
            #PrintedContent, #PrintedContent * { visibility: visible; }
            #PrintedContent { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>

    <script>
        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#example5')) {
                $('#example5').DataTable().destroy();
            }
            let table = $('#example5').DataTable({
                order: [[1, 'desc']],
                dom: 'rtip',
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json" }
            });

            $('#custom-filter-delete').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function printDiv(id) {
            const printContents = document.getElementById('Printed' + id).innerHTML;
            const originalContents = document.body.innerHTML;

            // Yazdırma penceresi oluştur
            const printWindow = window.open('', '', 'height=700,width=900');
            printWindow.document.write('<html><head><title>Sipariş Fişi</title>');
            printWindow.document.write('<style>body{font-family:sans-serif;padding:30px; line-height:1.5;} table{width:100%; border-collapse:collapse;} .mb-6{margin-bottom:20px;} .flex{display:flex; justify-content:space-between;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();

            printWindow.onload = function() {
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            };
        }
    </script>
@endsection
