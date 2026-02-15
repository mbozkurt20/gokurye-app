@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4 px-md-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none italic">
                    SİPARİŞ <span class="text-emerald-500">ARŞİVİ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Tamamlanmış ve teslim edilmiş sipariş geçmişi.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative group">
                    <input type="text" id="custom-filter-delivered"
                           class="bg-white border-0 shadow-sm rounded-2xl py-3 px-5 ps-11 text-xs font-bold text-slate-600 focus:ring-2 focus:ring-emerald-100 transition-all w-64"
                           placeholder="Siparişlerde ara...">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                </div>
                <button onclick="location.reload();" class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center text-slate-400 hover:text-emerald-500 hover:rotate-180 transition-all duration-500 shadow-sm border-0">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] border border-slate-50 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="table-responsive p-4">
                <table id="ordersTable" class="table table-hover align-middle border-0">
                    <thead>
                    <tr class="border-0">
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 py-4">Kanal</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Sipariş No</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Müşteri</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Kurye</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Tutar</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Ödeme</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 text-center">İşlem</th>
                    </tr>
                    </thead>
                    <tbody class="border-0">
                    @foreach ($orders as $order)
                        <tr id="data_{{ $order->id }}" class="group transition-all hover:bg-slate-50/50 border-b border-slate-50 last:border-0">
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
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-100 group-hover:bg-white transition-colors">
                                    @if($style)
                                        <img src="{{ asset('theme/images/' . $style['img']) }}" style="height: {{ $style['h'] }}; max-width: 80%;">
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
                            <td class="px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    <span class="text-[11px] font-black text-slate-600 uppercase">{{ optional(\App\Models\Courier::find($order->courier_id))->name ?? 'Restoran' }}</span>
                                </div>
                            </td>
                            <td class="px-4"><span class="text-xs font-black text-slate-800">{{ number_format($order->amount, 2) }} ₺</span></td>
                            <td class="px-4">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-500 text-[9px] font-black uppercase tracking-widest">
                                    {{ $order->payment_method === 'PAY_WITH_CARD' ? 'Kart' : 'Nakit' }}
                                </span>
                            </td>
                            <td class="px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}" class="w-9 h-9 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-500 hover:border-indigo-500 transition-all shadow-sm">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    <button onclick="printDiv({{ $order->id }})" class="w-9 h-9 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-emerald-500 hover:border-emerald-500 transition-all shadow-sm">
                                        <i class="fas fa-print text-xs"></i>
                                    </button>
                                </div>

                                <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content !rounded-[35px] border-0 shadow-2xl overflow-hidden" id="Printed{{ $order->id }}">
                                            <div class="p-8">
                                                <div class="flex justify-between items-start mb-6">
                                                    <div>
                                                        <h5 class="text-sm font-black text-slate-800 uppercase tracking-widest m-0">Sipariş Detayı</h5>
                                                        <span class="text-[10px] font-bold text-slate-400 italic">#{{ $order->tracking_id }}</span>
                                                    </div>
                                                    <button type="button" class="btn-close !bg-none border-0 p-0 m-0" data-bs-dismiss="modal"><i class="fa-solid fa-xmark text-slate-300"></i></button>
                                                </div>

                                                <div class="grid grid-cols-2 gap-6 mb-8">
                                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Müşteri Bilgisi</p>
                                                        <p class="text-xs font-black text-slate-700 uppercase m-0">{{ $order->full_name }}</p>
                                                        <p class="text-[10px] font-bold text-slate-500 m-0 mt-1">{{ $order->phone }}</p>
                                                    </div>
                                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-right">
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Toplam Tutar</p>
                                                        <p class="text-lg font-black text-slate-900 m-0">{{ number_format($order->amount, 2) }} ₺</p>
                                                        <span class="text-[9px] font-black text-emerald-500 uppercase italic">Teslim Edildi</span>
                                                    </div>
                                                </div>

                                                <div class="mb-8">
                                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 ps-1">Adres Bilgisi</p>
                                                    <div class="bg-white border-2 border-dashed border-slate-100 rounded-2xl p-4">
                                                        <p class="text-xs font-bold text-slate-600 leading-relaxed m-0 italic">{{ $order->address }}</p>
                                                    </div>
                                                </div>

                                                <div class="mb-8">
                                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 ps-1">Ürünler</p>
                                                    <div class="space-y-2">
                                                        @foreach(json_decode($order->items) as $item)
                                                            <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
                                                                <div class="flex items-center gap-3">
                                                                    <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center text-[10px] font-black">1x</span>
                                                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-tighter">{{ $item->name }}</span>
                                                                </div>
                                                                <span class="text-xs font-black text-slate-800 tracking-tighter">{{ $item->price }} TL</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="flex gap-3">
                                                    <button onclick="printDiv({{ $order->id }})" class="flex-1 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg shadow-slate-200 border-0 transition-transform active:scale-95">
                                                        Siparişi Yazdır
                                                    </button>
                                                    <button class="px-6 py-4 bg-slate-100 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] border-0" data-bs-dismiss="modal">
                                                        Kapat
                                                    </button>
                                                </div>
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
        /* Backdrop Sorunu İçin Kesin Çözüm */
        .modal-backdrop { display: none !important; }
        .modal { background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); }

        .table > :not(caption) > * > * { border-bottom-width: 0; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0f172a !important; color: white !important; border-radius: 12px; border: 0; font-weight: 900; font-size: 11px;
        }
    </style>

    <script>
        $(document).ready(function () {
            var table = $('#ordersTable').DataTable({
                order: [[1, 'desc']],
                dom: 'rtip', // Varsayılan arama çubuğunu gizledik, kendimizinkini bağladık
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json" }
            });

            $('#custom-filter-delivered').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function printDiv(id) {
            var printContents = document.getElementById('Printed' + id).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endsection
