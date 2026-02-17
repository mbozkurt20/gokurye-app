@extends('admin.layouts.app')
@section('content')
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .page-header { background: #07004d; color: white; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(7,0,77,0.2); }
        .card-custom { background: #fff; border-radius: 16px; box-shadow: 0 3px 12px rgba(0,0,0,0.1); border: none; margin-bottom: 24px; }
        .btn-primary { background-color: #4f46e5; border: none; border-radius: 8px; font-weight: 600; }
        .table thead { background: #259a38; color: white; }

        /* Özet Kutuları */
        .summary-box { color: white; padding: 16px; text-align: center; font-weight: 700; border-radius: 12px; font-size: 1.1rem; }
        .payment-stat-card { background: #fdfdfd; border: 1px solid #eee; padding: 12px; text-align: center; border-radius: 10px; transition: all 0.3s; height: 100%; }
        .payment-stat-card small { color: #666; font-weight: 600; display: block; margin-bottom: 4px; text-transform: uppercase; font-size: 0.75rem; }
        .payment-stat-card strong { color: #07004d; font-size: 0.95rem; }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Global Raporlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Raporlar</a></li>
                    <li class="breadcrumb-item active">Filtreleme</li>
                </ol>
            </div>
        </div>

        <div class="card card-custom mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Kurye</label>
                        <select class="form-control select2" id="courier">
                            <option value="0">Tümü</option>
                            @foreach ($couriers as $courier)
                                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Restaurant</label>
                        <select class="form-control select2" id="restaurant">
                            <option value="0">Tümü</option>
                            @foreach ($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}">{{ $restaurant->restaurant_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Durum</label>
                        <select class="form-control" id="status_filter">
                            <option value="all">Tümü (İptaller Dahil)</option>
                            <option value="delivered" selected>Sadece Teslim Edilenler</option>
                            <option value="cancelled">Sadece İptal Edilenler</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Başlangıç</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="start_date">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Bitiş</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="end_date">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100 py-2" onclick="ReportFilter()">
                            <i class="fa fa-filter me-1"></i> Filtrele
                        </button>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-danger btn-sm" id="pdfBtn"><i class="fa fa-file-pdf"></i> PDF</button>
                    <button class="btn btn-outline-success btn-sm" id="excelBtn"><i class="fa fa-file-excel"></i> Excel</button>
                </div>
            </div>
        </div>

        <div id="summaryArea" style="display: none;">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="summary-box shadow-sm" style="background: #4f46e5;">
                        <small class="d-block text-white-50 uppercase">Toplam Sipariş</small>
                        <span id="topsiparis">0</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-box shadow-sm" style="background: #259a38;">
                        <small class="d-block text-white-50">Teslim Edilen</small>
                        <span id="count_delivered">0</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-box shadow-sm" style="background: #dc3545;">
                        <small class="d-block text-white-50">İptal Edilen</small>
                        <span id="count_cancelled">0</span>
                    </div>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-7 g-2 mb-4">
                @php
                    $methods = [
                        'online' => 'Online', 'nakit' => 'Nakit', 'kkarti' => 'K.Kartı',
                        'ticket' => 'Ticket', 'sodexo' => 'Sodexo', 'multinet' => 'Multinet', 'pluxee' => 'Pluxee'
                    ];
                @endphp
                @foreach($methods as $key => $label)
                    <div class="col">
                        <div class="payment-stat-card shadow-sm">
                            <small>{{ $label }}</small>
                            <strong id="top{{ $key }}">0.00 TL</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="alert alert-success d-flex justify-content-between align-items-center mb-4">
                <p class="mb-0 fw-bold text-black">GENEL TOPLAM CİRO:</p>
                <h2 class="mb-0 fw-bold" id="topciro">0.00 TL</h2>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-body" id="reportList">
                <div class="table-responsive">
                    <table class="table  align-middle">
                        <thead>
                        <tr>
                            <th>Platform</th>
                            <th>Sipariş No</th>
                            <th>Kurye</th>
                            <th>Müşteri</th>
                            <th>Ödeme</th>
                            <th>Tutar</th>
                            <th>Tarih</th>
                        </tr>
                        </thead>
                        <tbody id="report">
                        <tr id="no-data">
                            <td colspan="8" class="text-center py-5 text-muted italic">Verileri görmek için filtreleme yapın.</td>
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
                    $('#report').html('<tr><td colspan="8" class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Yükleniyor...</td></tr>');
                    Swal.showLoading();
                },
                success: function (response) {
                    Swal.close();
                    $('#summaryArea').fadeIn();
                    $('#report').empty();

                    if (response.data.length === 0) {
                        $('#report').html('<tr><td colspan="8" class="text-center py-4">Sonuç bulunamadı.</td></tr>');
                    } else {
                        response.data.forEach((item) => {
                            let statusBadge = item.status === 'İptal Edildi' ? 'bg-danger' : 'bg-success';
                            $('#report').append(`
                                <tr>
                                    <td><span class="badge border border-dark text-dark border">${item.platform}</span></td>
                                    <td><strong>${item.tracking_id}</strong></td>
                                    <td>${item.courier}</td>
                                    <td>${item.full_name}</td>
                                    <td><small>${item.payment}</small></td>

                                    <td class="fw-bold text-primary">${item.amount}</td>
                                    <td><small>${item.time}</small></td>
                                </tr>
                            `);
                        });
                    }

                    // İstatistikler
                    const fmt = (v) => Number(v).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' TL';
                    let t = response.totals;

                    $('#topsiparis').text(t.topsiparis);
                    $('#count_delivered').text(t.count_delivered || 0);
                    $('#count_cancelled').text(t.count_cancelled || 0);

                    $('#toponline').text(fmt(t.online));
                    $('#topnakit').text(fmt(t.nakit));
                    $('#topkkarti').text(fmt(t.kkarti));
                    $('#topticket').text(fmt(t.ticket));
                    $('#topsodexo').text(fmt(t.sodexo));
                    $('#topmultinet').text(fmt(t.multinet));
                    $('#toppluxee').text(fmt(t.pluxee));
                    $('#topciro').text(fmt(t.topciro));
                }
            });
        }

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
