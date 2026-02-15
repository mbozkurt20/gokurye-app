@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4 px-md-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none italic">
                    SİPARİŞ <span class="text-indigo-500">RAPORLARI</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    İşletmenizin ciro ve performans verilerini analiz edin.
                </p>
            </div>
        </div>

        <div class="bg-white !rounded-[35px] border border-slate-50 shadow-xl shadow-slate-200/50 p-6 mb-8">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Platform</label>
                    <select class="form-select !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner text-xs" id="platform">
                        <option value="0">Tüm Platformlar</option>
                        <option value="gpsyemek">GpsYemek</option>
                        <option value="getir">GetirYemek</option>
                        <option value="trendyol">TrendyolYemek</option>
                        <option value="yemeksepeti">Yemeksepeti</option>
                        <option value="migros">MigrosYemek</option>
                        <option value="telefonsiparis">Telefon Sipariş</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Durum</label>
                    <select class="form-select !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner text-xs" id="status_filter">
                        <option value="delivered">Teslim Edilenler</option>
                        <option value="cancelled">İptal Edilenler</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Başlangıç</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner text-xs" id="start_date">
                </div>
                <div class="col-md-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ps-1">Bitiş</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control !rounded-2xl border-0 bg-slate-100 p-3 font-bold text-slate-700 shadow-inner text-xs" id="end_date">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="flex-1 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest border-0 shadow-lg shadow-slate-200 transition-transform active:scale-95" onclick="ReportFilter()">
                        LİSTELE
                    </button>
                    <button class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center border-0 transition-all hover:bg-red-500 hover:text-white" id="downloadPDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-8">
            <div class="col-md-3 col-6">
                <div class="bg-white p-5 rounded-[30px] border border-slate-50 shadow-sm text-center">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Sipariş Adedi</span>
                    <h3 class="text-2xl font-black text-slate-800 m-0 tracking-tighter" id="res-count">0</h3>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white p-5 rounded-[30px] border border-slate-50 shadow-sm text-center border-b-4 border-b-emerald-500">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Nakit Toplam</span>
                    <h3 class="text-2xl font-black text-slate-800 m-0 tracking-tighter" id="res-nakit">0.00 TL</h3>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white p-5 rounded-[30px] border border-slate-50 shadow-sm text-center border-b-4 border-b-indigo-500">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Kredi Kartı</span>
                    <h3 class="text-2xl font-black text-slate-800 m-0 tracking-tighter" id="res-kkarti">0.00 TL</h3>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white p-5 rounded-[30px] border border-slate-50 shadow-sm text-center border-b-4 border-b-blue-400">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Online Ödeme</span>
                    <h3 class="text-2xl font-black text-slate-800 m-0 tracking-tighter" id="res-online">0.00 TL</h3>
                </div>
            </div>

            <div class="col-md-2 col-4">
                <div class="bg-slate-50/50 p-4 rounded-[25px] border border-slate-100 text-center">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Ticket</span>
                    <h4 class="text-sm font-black text-slate-700 m-0" id="res-ticket">0.00 TL</h4>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="bg-slate-50/50 p-4 rounded-[25px] border border-slate-100 text-center">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Sodexo</span>
                    <h4 class="text-sm font-black text-slate-700 m-0" id="res-sodexo">0.00 TL</h4>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="bg-slate-50/50 p-4 rounded-[25px] border border-slate-100 text-center">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Multinet/Plux</span>
                    <h4 class="text-sm font-black text-slate-700 m-0" id="res-multi_plux">0.00 TL</h4>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="bg-slate-900 p-5 rounded-[30px] shadow-xl shadow-slate-200 flex justify-between items-center px-8 overflow-hidden relative group">
                    <div class="relative z-10">
                        <span class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.3em] block mb-1">GENEL TOPLAM CİRO</span>
                        <h2 class="text-3xl font-black text-white m-0 tracking-tighter" id="res-grand_total">0.00 TL</h2>
                    </div>
                    <i class="fas fa-wallet text-slate-800 text-6xl absolute right-[-10px] bottom-[-10px] transition-transform group-hover:scale-110"></i>
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
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Müşteri</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4">Ödeme Tipi</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 text-end">Tutar</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-4 text-center">Saat</th>
                    </tr>
                    </thead>
                    <tbody id="report" class="border-0">
                    <tr><td colspan="6" class="text-center py-5 text-slate-300 font-bold italic">Lütfen filtreleme yapın...</td></tr>
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

    {{-- PDF Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>

    <script>
        function ReportFilter() {
            const params = {
                start: $('#start_date').val(),
                end: $('#end_date').val(),
                platform: $('#platform').val(),
                status: $('#status_filter').val(),
                _token: '{{ csrf_token() }}'
            };

            $.post('/restaurant/reports/globalFilterOrder', params, function (res) {
                let html = '';
                res.data.forEach(order => {
                    html += `<tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-4"><span class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-500 text-[9px] font-black uppercase tracking-widest">${order.platform}</span></td>
                        <td class="px-4 text-xs font-black text-slate-400">#${order.tracking_id}</td>
                        <td class="px-4 text-xs font-black text-slate-700 uppercase">${order.full_name}</td>
                        <td class="px-4 text-[10px] font-bold text-slate-500 italic">${order.payment}</td>
                        <td class="px-4 text-end text-xs font-black text-slate-900">${order.amount} ₺</td>
                        <td class="px-4 text-center text-[10px] font-bold text-slate-400">${order.time}</td>
                    </tr>`;
                });
                $('#report').html(html || '<tr><td colspan="6" class="text-center py-5 font-bold text-slate-300">Sonuç bulunamadı.</td></tr>');

                // Update Totals
                if(res.totals) {
                    Object.keys(res.totals).forEach(key => {
                        let val = res.totals[key];
                        let formatted = key === 'count' ? val : new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(val) + " TL";
                        $(`#res-${key}`).text(formatted);
                    });
                }
            });
        }

        document.getElementById("downloadPDF").addEventListener("click", function () {
            const tableRows = document.querySelectorAll("#report tr");
            if (tableRows.length === 0 || tableRows[0].cells.length === 1) {
                alert("Önce veri listelemelisiniz.");
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text("Sipariş Raporu - {{ date('d.m.Y') }}", 14, 15);
            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                theme: 'striped',
                headStyles: { fillColor: [79, 70, 229] }
            });
            doc.save('rapor.pdf');
        });
    </script>
@endsection
