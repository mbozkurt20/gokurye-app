@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid pb-5">
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Global Raporlar</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Veriye Dayalı Performans Analizi</p>
            </div>
            <div class="d-flex gap-2">
                <button class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-red-500 hover:bg-red-50 transition-all shadow-sm" id="pdfBtn" title="PDF İndir">
                    <i class="fa fa-file-pdf"></i>
                </button>
                <button class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-green-600 hover:bg-green-50 transition-all shadow-sm" id="excelBtn" title="Excel İndir">
                    <i class="fa fa-file-excel"></i>
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden mb-4">
            <div class="card-body p-5">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Kurye Seçimi</label>
                        <select class="form-select select2 !rounded-xl border-slate-200" id="courier">
                            <option value="0">Tümü</option>
                            @foreach ($couriers as $courier)
                                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Restoran</label>
                        <select class="form-select select2 !rounded-xl border-slate-200" id="restaurant">
                            <option value="0">Tümü</option>
                            @foreach ($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}">{{ $restaurant->restaurant_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Sipariş Durumu</label>
                        <select class="form-select !rounded-xl border-slate-200 font-bold text-xs" id="status_filter">
                            <option value="all">Tümü (İptaller Dahil)</option>
                            <option value="delivered" selected>Sadece Teslim Edilenler</option>
                            <option value="cancelled">Sadece İptal Edilenler</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Başlangıç Tarihi</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control !rounded-xl border-slate-200 font-bold text-xs" id="start_date">
                    </div>
                    <div class="col-md-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Bitiş Tarihi</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control !rounded-xl border-slate-200 font-bold text-xs" id="end_date">
                    </div>
                    <div class="col-md-2">
                        <label class="mb-2 block invisible">Buton</label>
                        <button class="w-full py-2.5 bg-indigo-600 text-white rounded-xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-indigo-100 border-0 transition-all hover:scale-[1.02] active:scale-95" onclick="ReportFilter()">
                            FİLTRELE
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="summaryArea" style="display: none;">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="bg-white p-5 !rounded-[32px] shadow-sm flex items-center gap-4 border-b-4 border-indigo-600">
                        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600"><i class="fas fa-shopping-basket"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest m-0">Toplam Sipariş</p>
                            <h3 class="font-black text-slate-800 m-0" id="topsiparis">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-5 !rounded-[32px] shadow-sm flex items-center gap-4 border-b-4 border-green-500">
                        <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest m-0">Teslim Edilen</p>
                            <h3 class="font-black text-slate-800 m-0" id="count_delivered">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-5 !rounded-[32px] shadow-sm flex items-center gap-4 border-b-4 border-red-500">
                        <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600"><i class="fas fa-times-circle"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest m-0">İptal Edilen</p>
                            <h3 class="font-black text-slate-800 m-0" id="count_cancelled">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 !rounded-[32px] shadow-sm mb-4 border border-slate-50">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-4">Ödeme Yöntemi Dağılımı</p>
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-7 g-3">
                    @foreach(['online' => 'Online', 'nakit' => 'Nakit', 'kkarti' => 'K.Kartı', 'ticket' => 'Ticket', 'sodexo' => 'Sodexo', 'multinet' => 'Multinet', 'pluxee' => 'Pluxee'] as $key => $label)
                        <div class="col">
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                                <small class="text-[9px] font-black text-slate-400 uppercase block mb-1">{{ $label }}</small>
                                <strong class="text-xs font-black text-slate-800 block" id="top{{ $key }}">0.00 TL</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 p-4 bg-indigo-600 rounded-2xl flex justify-between items-center shadow-lg shadow-indigo-100">
                    <span class="text-xs font-black text-white uppercase tracking-widest">GENEL TOPLAM CİRO</span>
                    <h2 class="text-2xl font-black text-white m-0" id="topciro">0.00 TL</h2>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive" id="reportList">
                    <table class="table align-middle m-0">
                        <thead class="bg-slate-50">
                        <tr>
                            <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0">Platform</th>
                            <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0">Sipariş No</th>
                            <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0">Kurye / Müşteri</th>
                            <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0">Ödeme</th>
                            <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0">Tutar</th>
                            <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0 text-end">Tarih</th>
                        </tr>
                        </thead>
                        <tbody id="report" class="border-0 text-xs">
                        <tr id="no-data">
                            <td colspan="6" class="text-center py-10">
                                <div class="flex flex-col items-center opacity-20">
                                    <i class="fas fa-filter fa-3x mb-3"></i>
                                    <p class="font-black uppercase tracking-widest">Verileri Görmek İçin Filtreleyin</p>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

    <script type="text/javascript">
        function ReportFilter() {
            let data = {
                _token: '{{ csrf_token() }}',
                courier: $('#courier').val(),
                restaurant: $('#restaurant').val(),
                status: $('#status_filter').val(),
                start: $('#start_date').val(),
                end: $('#end_date').val()
            };

            $.ajax({
                type: 'POST',
                url: '/admin/reports/globalFilter',
                data: data,
                beforeSend: function () {
                    $('#report').html('<tr><td colspan="6" class="text-center py-10 font-bold text-slate-400 uppercase tracking-widest animate-pulse">Veriler Hazırlanıyor...</td></tr>');
                    Swal.showLoading();
                },
                success: function (response) {
                    Swal.close();
                    $('#summaryArea').fadeIn(400);
                    $('#report').empty();

                    if (response.data.length === 0) {
                        $('#report').html('<tr><td colspan="6" class="text-center py-10 font-black text-red-400 uppercase">Kriterlere Uygun Sonuç Bulunamadı.</td></tr>');
                    } else {
                        response.data.forEach((item) => {
                            $('#report').append(`
                                <tr class="hover:bg-slate-50/50 transition-colors border-b border-slate-50">
                                    <td class="py-4 px-4"><span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[9px] font-black uppercase tracking-tighter border border-slate-200">${item.platform}</span></td>
                                    <td class="py-4 px-4 font-black text-slate-800">#${item.tracking_id}</td>
                                    <td class="py-4 px-4">
                                        <div class="font-black text-slate-800 uppercase text-[10px]">${item.courier}</div>
                                        <div class="text-[9px] text-slate-400 font-bold">${item.full_name}</div>
                                    </td>
                                    <td class="py-4 px-4 text-[10px] font-bold text-slate-500 uppercase">${item.payment}</td>
                                    <td class="py-4 px-4 font-black text-indigo-600">${item.amount}</td>
                                    <td class="py-4 px-4 text-end text-[10px] font-bold text-slate-400 uppercase">${item.time}</td>
                                </tr>
                            `);
                        });
                    }

                    const fmt = (v) => Number(v).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' ₺';
                    let t = response.totals;

                    $('#topsiparis').text(t.topsiparis);
                    $('#count_delivered').text(t.count_delivered || 0);
                    $('#count_cancelled').text(t.count_cancelled || 0);

                    ['online', 'nakit', 'kkarti', 'ticket', 'sodexo', 'multinet', 'pluxee'].forEach(key => {
                        $(`#top${key}`).text(fmt(t[key]));
                    });
                    $('#topciro').text(fmt(t.topciro));
                }
            });
        }

        // PDF ve Excel butonları aynı kalıyor
        document.getElementById("pdfBtn").addEventListener("click", function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            doc.autoTable({ html: '#reportList table', theme: 'grid', styles: { fontSize: 7 } });
            doc.save('global_rapor.pdf');
        });

        document.getElementById("excelBtn").addEventListener("click", function() {
            let table = document.querySelector("#reportList table");
            let wb = XLSX.utils.table_to_book(table);
            XLSX.writeFile(wb, "global_rapor.xlsx");
        });
    </script>
@endsection
