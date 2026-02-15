@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4 px-md-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none italic">
                    KURYE <span class="text-indigo-500">PERFORMANS</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Kurye bazlı sipariş dağılımı ve hakediş takibi.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button id="downloadPDF" class="h-11 px-4 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-red-500 hover:bg-red-50 transition-all shadow-sm text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-file-pdf me-2"></i> PDF
                </button>
                <button id="downloadExcel" class="h-11 px-4 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-emerald-500 hover:bg-emerald-50 transition-all shadow-sm text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-file-excel me-2"></i> EXCEL
                </button>
            </div>
        </div>

        <div class="bg-white !rounded-[35px] border border-slate-50 shadow-xl shadow-slate-200/50 p-6 mb-8">
            <div class="row g-3">
                <div class="col-lg-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Kurye Seçimi</label>
                    <select class="form-select !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner text-xs" id="courier">
                        <option value="-1">Tüm Kuryeler</option>
                        <option value="0">Restaurant Kuryeleri</option>
                        @foreach ($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Başlangıç</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner text-xs" id="start_date">
                </div>
                <div class="col-lg-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Bitiş</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner text-xs" id="end_date">
                </div>
                <div class="col-lg-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Durum</label>
                    <select class="form-select !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner text-xs" id="report_status">
                        <option value="delivered">Teslim Edilenler</option>
                        <option value="cancelled">İptal Edilenler</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex align-items-end">
                    <button class="w-100 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest border-0 shadow-lg shadow-slate-200 transition-transform active:scale-95" onclick="ReportFilter()">
                        VERİLERİ ANALİZ ET
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-8">
            <div class="col-md-2 col-6">
                <div class="bg-white p-5 rounded-[30px] border border-slate-50 shadow-sm text-center">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Toplam Paket</span>
                    <h3 class="text-2xl font-black text-slate-800 m-0 tracking-tighter" id="topsiparis">0</h3>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="bg-white p-5 rounded-[30px] border border-slate-50 shadow-sm text-center border-b-4 border-b-emerald-500">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Nakit Paket</span>
                    <h3 class="text-xl font-black text-slate-800 m-0 tracking-tighter" id="topnakit">0.00 ₺</h3>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="bg-white p-5 rounded-[30px] border border-slate-50 shadow-sm text-center border-b-4 border-b-indigo-500">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Kredi Kartı</span>
                    <h3 class="text-xl font-black text-slate-800 m-0 tracking-tighter" id="topkkarti">0.00 ₺</h3>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white p-5 rounded-[30px] border border-slate-50 shadow-sm text-center border-b-4 border-b-orange-400">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Ticket / Kart</span>
                    <h3 class="text-xl font-black text-slate-800 m-0 tracking-tighter" id="topticket">0.00 ₺</h3>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-slate-900 p-5 rounded-[30px] shadow-lg shadow-slate-200 text-center">
                    <span class="text-[9px] font-black text-indigo-300 uppercase tracking-[0.2em] block mb-2">Online Toplam</span>
                    <h3 class="text-xl font-black text-white m-0 tracking-tighter" id="toponline">0.00 ₺</h3>
                </div>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] border border-slate-50 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="table-responsive p-4">
                <table class="table table-hover align-middle border-0" id="reportTable">
                    <thead>
                    <tr class="border-0">
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 py-4">Kanal</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Sipariş No</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Kurye</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Müşteri</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 text-center">Ödeme</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 text-end">Tutar</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 text-center">Saat</th>
                    </tr>
                    </thead>
                    <tbody id="report" class="border-0">
                    <tr class="no-data-row text-center">
                        <td colspan="7" class="py-5 text-slate-300 font-bold italic">Sorgulama yapmak için filtreleri kullanın.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .table > :not(caption) > * > * { border-bottom-width: 0; }
        .form-select, .form-control { outline: none !important; }
        .form-select:focus, .form-control:focus { box-shadow: none !important; background-color: #f1f5f9 !important; }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        function ReportFilter() {
            const courier = $('#courier').val();
            const start = $('#start_date').val();
            const end = $('#end_date').val();
            const status = $('#report_status').val();

            $.ajax({
                type: 'POST',
                url: '/restaurant/reports/globalFilter?_token={{ csrf_token() }}',
                data: { courier, start, end, status },
                beforeSend: function() {
                    $('#report').html('<tr><td colspan="7" class="text-center py-5"><div class="spinner-border spinner-border-sm text-indigo-500"></div></td></tr>');
                },
                success: function(response) {
                    $('#report').empty();

                    if (!response.data || response.data.length === 0) {
                        $('#report').html('<tr><td colspan="7" class="text-center py-5 font-bold text-slate-300">Kayıt bulunamadı.</td></tr>');
                        return;
                    }

                    response.data.forEach((el) => {
                        $('#report').append(`
                            <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-4"><span class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-500 text-[9px] font-black uppercase tracking-widest">${el.platform}</span></td>
                                <td class="px-4 text-xs font-black text-slate-400">#${el.tracking_id}</td>
                                <td class="px-4"><span class="px-3 py-1 rounded-lg bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest">${el.courier}</span></td>
                                <td class="px-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">${el.full_name}</span>
                                        <span class="text-[9px] font-bold text-slate-400">${el.phone}</span>
                                    </div>
                                </td>
                                <td class="px-4 text-center text-[10px] font-bold text-slate-500 italic">${el.payment}</td>
                                <td class="px-4 text-end text-xs font-black text-slate-900">${el.amount} ₺</td>
                                <td class="px-4 text-center text-[10px] font-bold text-slate-400">${el.time}</td>
                            </tr>
                        `);
                    });

                    $('#topnakit').text(response.totals.kapida_nakit + ' ₺');
                    $('#topkkarti').text(response.totals.kapida_k_karti + ' ₺');
                    $('#topticket').text(response.totals.kapida_ticket + ' ₺');
                    $('#toponline').text(response.totals.online + ' ₺');
                    $('#topsiparis').text(response.totals.topsiparis);
                }
            });
        }

        // PDF & Excel Actions
        document.getElementById("downloadPDF").addEventListener("click", function () {
            if ($("#report tr").length < 2 || $("#report tr:first").hasClass('no-data-row')) return;
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.autoTable({ html: '#reportTable', theme: 'striped', headStyles: { fillColor: [15, 23, 42] } });
            doc.save('kurye_raporu.pdf');
        });

        document.getElementById("downloadExcel").addEventListener("click", function () {
            if ($("#report tr").length < 2 || $("#report tr:first").hasClass('no-data-row')) return;
            const wb = XLSX.utils.table_to_book(document.getElementById('reportTable'), {sheet:"Rapor"});
            XLSX.writeFile(wb, "kurye_raporu.xlsx");
        });
    </script>
@endsection
