@extends('admin.layouts.app')

@section('content')
    <style type="text/css">
        .tops {
            padding: 20px 10px;
            font-weight: bold;
            color: #fff;
        }

        .tops span {
            font-size: 15px;
        }

        .table thead tr {
            background: #ddd;
        }


        .table thead tr th {
            color: #000;
            font-size: 15px;
            height: 20px;
            overflow: hidden;
        }

        .bg-ok {
            background: #4f46e5;
        }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restaurant Paket Raporu</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Restaurant Hakediş</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Filtrele</a></li>
                </ol>
            </div>
        </div>

        <div class="row gap-4">
            <div class="col-md-6">
                @if(session()->has('message'))
                    <div class="custom-alert success">
                        <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span class="alert-message">{{ session()->get('message') }}</span>
                    </div>
                @endif

                @if(session()->has('test') )
                    <div class="custom-alert error">
                        <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                        <span class="alert-message">{{ session()->get('test') }}</span>
                    </div>
                @endif

                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Ödeme Kaydı Ekle</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <form method="POST" action="{{ route('admin.progress.payments.store') }}">
                                    @csrf
                                    <input style="display: none" type="text" name="payable_type" value="restaurant">

                                    <div class="mb-4 text-dark">
                                        <label>Restaurant Seçiniz</label>
                                        <select class="form-control" name="payable_id">
                                            @foreach($restaurants as $restaurant)
                                                <option value="{{$restaurant->id}}">{{$restaurant->restaurant_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-4 text-dark">
                                        <label>Ödeme Tarihi Seçiniz</label>
                                        <input class="form-control" type="date"  value="{{date('Y-m-d',strtotime(\Carbon\Carbon::now()))}}" name="payment_date" required>
                                    </div>

                                    <div class="mb-4 text-dark">
                                        <label>Ödeme Tutarı</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="0,00"
                                            name="amount_display"
                                            oninput="formatMoney(this)"
                                            inputmode="decimal"
                                            autocomplete="off"
                                        >
                                        <input type="hidden" name="amount">
                                    </div>


                                    <div class="mb-4 text-dark">
                                        <label>Eklemek İstediğiniz Detay (opsiyonel)</label>
                                        <textarea  class="form-control" name="note" placeholder="Not"></textarea>
                                    </div>

                                    <button class="special-button" type="submit">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 card card-body">
                <div class="row" >
                    <div class="col-lg-4">
                        <select class="form-control" id="restaurant">
                            <option value="0">Restaurant Seçiniz</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{$restaurant->id}}">{{$restaurant->restaurant_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <input type="date" value="{{date('Y-m-d')}}" class="form-control" id="start_date">
                    </div>
                    <div class="col-lg-4">
                        <input type="date" value="{{date('Y-m-d')}}" class="form-control" id="end_date">
                    </div>
                    <div class="col-lg-4 mt-4 float-end">
                        <button class="special-button" onclick="ReportFilter()"><i class="fa fa-filter"></i>
                            Filtrele
                        </button>
                    </div>


                    <div class="col-xl-12 mt-5" id="reportList">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h4 class="card-title mb-4 text-center" id="selected-restaurant" style="font-weight: 700;"></h4>
                                <div class="row text-center">

                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded bg-ok">
                                            <h6 class="mb-1 text-white">Sipariş Sayısı</h6>
                                            <h4 class="text-white mb-0" id="order-count">0 Adet</h4>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded bg-ok">
                                            <h6 class="mb-1 text-white">Toplam Hakediş</h6>
                                            <h4 class="text-white mb-0" id="total-progress-payment">0₺</h4>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded bg-ok">
                                            <h6 class="mb-1 text-white">Yapılan Ödeme</h6>
                                            <h4 class="text-white mb-0" id="paid-amount">0₺</h4>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded bg-ok">
                                            <h6 class="mb-1 text-white">Kalan Ödeme</h6>
                                            <h4 class="text-white mb-0" id="remaining-amount">0₺</h4>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="row">
            <div class="row card">
                <div class="col-xl-12 card-body">
                    <div class="table-responsive">
                        <table id="paymentsTable" class="order-table shadow-hover card-table text-black"
                               style="min-width: 845px">
                            <thead>
                            <tr>
                                <th>Alıcı Adı <i class="fa fa-filter text-danger"></i></th>
                                <th>Ödeme Tarihi <i class="fa fa-filter text-danger"></i></th>
                                <th>Tutar <i class="fa fa-filter text-danger"></i></th>
                                <th>Not</th>
                                <th>Kayıt Tarihi</th>
                                <th>İşlem</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($records as $record)
                                <tr id="data_{{ $record->id }}">
                                    <td>{{ \App\Models\Restaurant::where('id',$record->payable_id)->first()->restaurant_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($record->payment_date)->format('d.m.Y') }}</td>
                                    <td>{{ number_format($record->amount, 2) }} ₺</td>
                                    <td>{{ $record->note ?? '-' }}</td>
                                    <td>{{ date('d-m-Y H:i', strtotime($record->created_at)) }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a onclick="DeleteFunction({{ $record->id }})"
                                               class="btn btn-danger shadow btn-xs sharp">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function ReportFilter() {
            var courier = $('#courier').val();
            var restaurant = $('#restaurant').val();
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            if (restaurant === '0') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Uyarı',
                    text: 'Lütfen bir restoran seçiniz.',
                    confirmButtonText: 'Tamam'
                });
                return; // İşlem devam etmesin
            }

            $.ajax({
                type: 'POST',
                url: '/admin/progress-payment/restaurant' + '?_token=' + '{{ csrf_token() }}',
                data: {restaurant: restaurant, start: start, end: end},
                success: function (response) {
                    $("#selected-restaurant").text(response.restaurant_name);
                    $("#order-count").html(response.order_count + ' Adet');
                    $("#total-progress-payment").html(
                        Number(response.total_progress_payment).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₺'
                    );
                    $("#paid-amount").html(
                        Number(response.paidAmount).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₺'
                    );

                    let remaining = Number(response.total_progress_payment) - Number(response.paidAmount);
                    $("#remaining-amount").html(
                        remaining.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₺'
                    );
                    // Tabloyu güncelle
                    $("#paymentsTable tbody").html(response.records_html);

                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı',
                        text: 'Rapor başarıyla getirildi.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error);
                    console.log("Status:", status);
                    console.log("Response:", xhr.responseText);

                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: 'Bir hata oluştu: ' + error,
                        confirmButtonText: 'Tamam'
                    });
                }
            });
        }

    </script>
    <!-- Search & Delete Scripts -->
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#paymentsTable').DataTable({
                order: [[3, "desc"]], // created_at sütunu
                language: {
                    search: "Ara:",
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json",
                    lengthMenu: "Sayfa başına _MENU_ kayıt",
                    info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
                    infoEmpty: "Gösterilecek kayıt yok",
                    paginate: {
                        next: "Sonraki",
                        previous: "Önceki"
                    }
                }
            });

            $('#custom-filter-payments').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function DeleteFunction(id) {
            Swal.fire({
                title: 'Silmek istediğinizden emin misiniz?',
                text: "Bu işlemi geri alamazsınız!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#259a38',
                cancelButtonColor: '#4f46e5',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Silmek istiyorum!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/admin/progress-payment/record/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + id).fadeOut(() => $(this).remove());
                            } else if (data === "NO") {
                                Swal.fire("Uyarı!", "Bu kaydı silemezsiniz.", "warning");
                            }
                        },
                        error: function () {
                            Swal.fire("Hata!", "Silme sırasında bir sorun oluştu.", "error");
                        }
                    });
                }
            });
        }
    </script>

    <script>
        function formatMoney(el) {
            let value = el.value;

            // Sadece rakam ve virgül
            value = value.replace(/[^0-9,]/g, '');

            // Virgülden fazlasını sil
            if (value.indexOf(',') !== -1) {
                const parts = value.split(',');
                value = parts[0] + ',' + parts[1].slice(0, 2);
            }

            // Binlik ayırıcı ekle
            let parts = value.split(',');
            parts[0] = parts[0]
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            el.value = parts.join(',');

            // Backend için: 12.244,55 → 12244.55
            const hidden = document.querySelector('input[name="amount"]');
            hidden.value = el.value
                .replace(/\./g, '')
                .replace(',', '.');
        }
    </script>

@endsection
