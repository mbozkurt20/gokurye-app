@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Teslim Edilen Siparişler</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Siparişler</a></li>
                <li class="breadcrumb-item active">Liste</li>
            </ol>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter-delivered-admin" placeholder="Sipariş ara...">
                    <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="javascript:void(0);" onclick="location.reload();" class="special-ok-button  mb-2">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>

        <div class="card card-body">
            <div class="table-responsive">
                <table id="ordersTableAdmin" class="table table-striped table-hover text-black" style="min-width: 1000px;">
                    <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Telefon</th>
                        <th>Kurye</th>
                        <th>Tutar</th>
                        <th>Ödeme Yön.</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $order)
                        <tr id="data_{{ $order->id }}">
                            <td>
                                @php
                                    $platformImages = [
                                        'yemeksepeti' => 'yemeksepeti.png',
                                        'getir' => 'getiryemek.png',
                                        'trendyol' => 'trendyolyemek.png',
                                        'adisyo' => 'adisyoFull.png',
                                        'migros' => 'MigrosYemek_White_logo.png',
                                    ];
                                    $img = $platformImages[$order->platform] ?? null;
                                @endphp
                                @if ($img)
                                    <span class="btn btn-primary btn-rounded p-2" style="background-color: transparent;">
                                        <img src="{{ asset('theme/images/' . $img) }}" style="height: 20px;">
                                    </span>
                                @elseif ($order->platform === 'telefonsiparis')
                                    <span class="btn btn-warning btn-rounded fw-bold px-3">POS</span>
                                @endif
                            </td>
                            <td>{{ $order->tracking_id }}</td>
                            <td>{{ $order->full_name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>
                                {{ optional(\App\Models\Courier::find($order->courier_id))->name ?? config('site.name') }}
                            </td>
                            <td>{{ number_format($order->amount, 2) }} TL</td>
                            <td>
                                {{ $order->payment_method === 'PAY_WITH_CARD' ? 'Kredi Kartı' : $order->payment_method }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $order->status }}</span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <!-- GÖRÜNTÜLE (MODAL) -->
                                    <button data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}" class="btn btn-secondary btn-sm me-1">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- YAZDIR -->
                                    <button onclick="printDiv({{ $order->id }})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>

                                <!-- MODAL -->
                              <div class="modal fade" data-bs-backdrop="false" id="orderModal{{ $order->id }}">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content" id="Printed{{ $order->id }}">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Sipariş Bilgileri</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2"><strong>Sipariş Kodu:</strong> {{ $order->tracking_id }}</div>
                                                    <div class="col-md-6 mb-2"><strong>Müşteri:</strong> {{ $order->full_name }}</div>
                                                    <div class="col-md-4 mb-2"><strong>Telefon:</strong> {{ $order->phone }}</div>
                                                    <div class="col-md-4 mb-2"><strong>Tutar:</strong> {{ $order->amount }} TL</div>
                                                    <div class="col-md-4 mb-2"><strong>Ödeme:</strong>
                                                        {{ $order->payment_method === 'PAY_WITH_CARD' ? 'Kredi Kartı' : $order->payment_method }}
                                                    </div>
                                                    <div class="col-12 mb-3"><strong>Adres:</strong> {{ $order->address }}</div>
                                                    <div class="col-12">
                                                        <table class="table table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th>Ürün</th>
                                                                <th>Adet</th>
                                                                <th>Fiyat</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach(json_decode($order->items) as $item)
                                                                <tr>
                                                                    <td>{{ $item->name }}</td>
                                                                    <td>1</td>
                                                                    <td>{{ $item->price }} TL</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button onclick="printDiv({{ $order->id }})" class="btn btn-primary"><i class="fa fa-print"></i> Yazdır</button>
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- MODAL END -->
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        $(document).ready(function () {
            var table = $('#ordersTableAdmin').DataTable({
                order: [[1, 'desc']], // Sipariş No'ya göre (tracking_id) sıralama
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

            $('#custom-filter-delivered-admin').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function printDiv(id) {
            var printContents = document.getElementById('Printed' + id).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload(); // geri dönünce sayfayı tazele
        }
    </script>
@endsection
