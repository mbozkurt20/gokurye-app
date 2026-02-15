<div class="col-xl-12 col-xxl-12 mt-5">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example344" class="order-table shadow-hover card-table text-black"
                       style="min-width: 845px">
                    <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Sipariş Numarası</th>
                        <th>Müşteri</th>
                        <th>Telefon</th>
                        <th style="width: 280px;">Kurye</th>
                        <th>İndirim</th>
                        <th>Tutar</th>
                        <th style="width:12%;">Paket Mesafesi</th>
                        <th>Ödeme Yön.</th>
                        <th>Durum</th>
                        <th>Saati</th>
                        <th>İşlem</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (isset($tumu) && count($tumu) > 0)
                        @foreach ($tumu as $order)
                            <tr id="data_{{ $order->id }}">
                                <td>
                                    @if ($order->platform == 'yemeksepeti' || $order->platform == 'Yemeksepeti')
                                        <a class="btn btn-primary btn-rounded"
                                           style="padding: 10px;background: #fb0050;border-color: #fb0050; font-size:14px;"><img
                                                src="{{ asset('theme/images/yemeksepeti.png') }}"
                                                style="height: 15px"> </a>
                                    @endif
                                    @if ($order->platform == 'getir' || $order->platform == 'Getir')
                                        <a class="btn btn-primary btn-rounded"
                                           style="padding: 10px;background: #6244be;border-color: #6244be; font-size:14px;">
                                            <img src="{{ asset('theme/images/getiryemek.png') }}"
                                                 style="height: 15px"> </a>
                                    @endif
                                    @if ($order->platform == 'trendyol' || $order->platform == 'Trendyol')
                                        <a style="padding: 10px" class="btn btn-primary btn-rounded"><img
                                                src="{{ asset('theme/images/trendyolyemek.png') }}"
                                                style="height: 15px"> </a>
                                    @endif
                                    @if ($order->platform == 'migros' || $order->platform == 'Migros')
                                        <a style="padding: 10px;background: #000080;border-color: #6244be; font-size: 14px"
                                           class="btn btn-primary btn-rounded"><img
                                                src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/aff9ed163620751.6556613f80c21.png"
                                                style="height: 25px;"> </a>
                                    @endif

                                    @if ($order->platform == 'adisyo' || $order->platform == 'Adisyo')
                                        <a style="padding: 10px;background: #ff0a0a;border-color: #fff; font-size: 14px"
                                           class="btn btn-primary btn-rounded"><img
                                                src="{{ asset('theme/images/adisyoFull.png') }}"
                                                style="height: 25px;"> </a>
                                    @endif
                                    @if ($order->platform == 'telefonsiparis')
                                        <a class="special-ok-button btn-rounded"
                                           style="width:100%;font-weight: bold;padding:10px 15px;font-size:14px;">
                                            POS</a>
                                    @endif


                                    <input type="hidden" value="{{ $order->tracking_id }}"
                                           id="tracking_{{ $order->tracking_id }}">

                                </td>
                                <td style="text-align: center">{{ $order->tracking_id }}</td>
                                <td>{{ $order->full_name }}</td>
                                <td>{{ $order->phone }}</td>
                                <td>
                                    @if ($order->courier_id == -1)
                                        @php
                                            $courierOrder = \App\Models\CourierOrder::where('order_id', $order->id)->first();
                                            $courierName = 'Kurye Bekleniyor';
                                            if ($courierOrder) {
                                                $courier = \App\Models\Courier::find(
                                                    $courierOrder->courier_id,
                                                );
                                                if ($courier) {
                                                    $courierName = $courier->name;
                                                }
                                            }
                                        @endphp
                                        {{ $courierName }}
                                        <a href="#""
                                        style="    color: #ffffff;
                                        background: #f72b50;
                                        border-radius: 50%;
                                        padding: 8px;
                                        cursor: pointer;">
                                        <i class="fas fa-truck"></i>
                                        </a>
                                    @endif

                                    @if ($order->courier_id >= 1)
                                        @php
                                            $courier = \App\Models\Courier::where(
                                                'id',
                                                $order->courier_id,
                                            )->first();
                                        @endphp
                                        @if ($courier)
                                            {{ $courier->name }}
                                        @else
                                            <span>Kurye Bekleniyor</span> <!-- Optional fallback message -->
                                        @endif
                                    @endif

                                    @if ($order->courier_id == 0)
                                        <a data-bs-toggle="modal"
                                           data-bs-target="#Courier{{ $order->id }}"
                                           style="padding:10px 15px;height: 50px;"
                                           class="special-ok-button sharp me-1">
                                            <i class="fas fa-truck"></i>
                                        </a>

                                      <div class="modal fade" data-bs-backdrop="false" id="Courier{{ $order->id }}">
                                            <div class="modal-dialog modal-dialog-centered"
                                                 role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            ({{ $order->tracking_id }})
                                                            Siparişe Kurye Ata</h5>
                                                        <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" style="padding: 1rem;">
                                                        <div class="row">
                                                            <div class="mb-1 col-md-12">
                                                                <select
                                                                    class="single-select-placeholder js-states"
                                                                    onchange="Courier(event,{{ $order->id }})">
                                                                    <option value="0">Kurye Seç
                                                                    </option>
                                                                    <option value="-1">{{config('site.name')}}
                                                                    </option>
                                                                    @foreach ($couriers as $courier)
                                                                        <option
                                                                            value="{{ $courier->id }}">
                                                                            {{ $courier->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button"
                                                                class="btn btn-danger light"
                                                                data-bs-dismiss="modal">Kapat
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                @php
                                    $total = 0;

                                    if ($order->platform == 'trendyol' || $order->platform == 'Trendyol') {
                                        if ($order->promotions) {
                                            $promotions = json_decode($order->promotions);
                                            foreach ($promotions as $promotion) {
                                                $total += $promotion->totalSellerAmount ?? 0;
                                            }
                                        }

                                          if ($order->coupon) {
                                            $coupon = json_decode($order->coupon);
                                            $total += $coupon->totalSellerAmount ?? 0;
                                        }
                                    }
                                @endphp

                                <td class="text-ov">{{ number_format($total, 2) }} ₺</td>
                                <td class="text-ov">{{ number_format($order->amount - $total, 2) }} ₺</td>
                                <td class="text-ov">
                                    <input type="text"
                                           class="form-control" style="width: 150px;"
                                           id="message_{{ $order->id }}"
                                           value="{{ $order->message }}"
                                           placeholder="Mesafe">
                                </td>
                                <td>
                                    {{ $order->payment_method }}
                                </td>
                                <td>
                                    <input type="hidden" id="tracking_{{ $order->id }}"
                                           value="{{ $order->tracking_id }}">
                                    <input type="hidden" id="platform_{{ $order->id }}"
                                           value="{{ $order->platform }}">

                                    <select class="default-select  form-control wide"
                                            onchange="StatusOrderChange(event, {{ $order->id }})"
                                            @if ($order->status == 'DELIVERED') disabled @endif>

                                        <option value="PENDING"
                                                @if ($order->status == 'PENDING') selected @endif>
                                            BEKLİYOR
                                        </option>
                                        <option value="PREPARED"
                                                @if ($order->status == 'PREPARED') selected @endif>
                                            HAZIRLANIYOR
                                        </option>
                                        <option value="HANDOVER"
                                                @if ($order->status == 'HANDOVER') selected @endif>
                                            KURYEYE VERİLDİ
                                        </option>
                                        <option value="DELIVERED"
                                                @if ($order->status == 'DELIVERED') selected @endif>
                                            TESLİM EDİLDİ
                                        </option>
                                        <option value="UNSUPPLIED"
                                                @if ($order->status == 'UNSUPPLIED') selected @endif>
                                            İPTAL EDİLDİ
                                        </option>
                                    </select>
                                </td>
                                <!-- cancelModal Modal -->
                              <div class="modal fade" data-bs-backdrop="false" id="cancelModal" tabindex="-1"
                                     aria-labelledby="cancelModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="cancelModalLabel">Siparişi İptal Et</h5>
                                                <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label for="cancelReason" class="form-label">
                                                    Siparişi neden iptal etmek istiyorsunuz?
                                                </label>
                                                <textarea class="form-control" id="message" rows="4"
                                                          placeholder="Lütfen iptal nedeninizi yazınız..."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Geri Dön
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                        id="confirmCancel">İptal Et
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a data-bs-toggle="modal"
                                           data-bs-target="#Orders{{ $order->id }}"
                                           class="btn btn-secondary shadow btn-xs sharp me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if ($order->platform == 'getir')
                                          <div class="modal fade" data-bs-backdrop="false" id="Orders{{ $order->id }}">
                                                <div class="modal-dialog modal-dialog-centered"
                                                     role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Sipariş Bilgileri</h5>
                                                            <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal">
                                                            </button>
                                                        </div>
                                                        <div class="modal-body" style="padding: 1rem;">
                                                            <div class="row">
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Sipariş Kodu</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->tracking_id }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Müşteri Adı</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->full_name }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Telefon</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->phone }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Tutar</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->amount }}₺</p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Ödeme Yön.</p>
                                                                    <p class="orderProde">

                                                                        {{ $order->payment_method }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-2 col-md-12">
                                                                    <p class="orderTitle">Adres</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->address }}</p>
                                                                </div>
                                                                <div class="mb-3 col-md-12">
                                                                    <table
                                                                        class="table table-responsive-sm"
                                                                        style="min-width: 28rem !important;">
                                                                        <thead>
                                                                        <tr>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Ürün
                                                                            </th>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Adeti
                                                                            </th>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Fiyatı
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        @foreach (json_decode($order->items) as $item)
                                                                            <tr>
                                                                                <th class="orderProde">
                                                                                    {{ $item->name->tr }}
                                                                                </th>
                                                                                <th class="orderProde">
                                                                                    {{ $item->count }}
                                                                                </th>
                                                                                <th class="orderProde">
                                                                                    {{ $item->price }}
                                                                                    ₺
                                                                                </th>
                                                                            </tr>
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                    class="btn btn-primary light"
                                                                    onclick="printDiv({{ $order->id }})"><i
                                                                    class="fa fa-print"></i>Yazdır
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-danger light"
                                                                    data-bs-dismiss="modal">Kapat
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($order->platform == 'yemeksepeti')
                                          <div class="modal fade" data-bs-backdrop="false" id="Orders{{ $order->id }}">
                                                <div class="modal-dialog modal-dialog-centered"
                                                     role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Sipariş Bilgileri</h5>
                                                            <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal">
                                                            </button>
                                                        </div>
                                                        <div class="modal-body" style="padding: 1rem;">
                                                            <div class="row">
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Sipariş Kodu</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->tracking_id }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Müşteri Adı</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->full_name }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Telefon</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->phone }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Tutar</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->amount }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Ödeme Yn.</p>
                                                                    <p class="orderProde">

                                                                        {{ $order->payment_method }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-2 col-md-12">
                                                                    <p class="orderTitle">Adres</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->address }}</p>
                                                                </div>
                                                                <div class="mb-3 col-md-12">
                                                                    @php $items = json_decode($order->items); @endphp
                                                                    <table
                                                                        class="table table-responsive-sm"
                                                                        style="min-width: 28rem !important;">
                                                                        <thead>
                                                                        <tr>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Ürün
                                                                            </th>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Adeti
                                                                            </th>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Fiyatı
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach ($items as $item)
                                                                            <tr>
                                                                                <th class="orderProde">
                                                                                    {{ $item->name }}
                                                                                </th>
                                                                                <th class="orderProde">
                                                                                    {{ $item->count }}
                                                                                </th>
                                                                                <th class="orderProde">
                                                                                    {{ $item->price }}
                                                                                    ₺
                                                                                </th>
                                                                            </tr>
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                    class="btn btn-primary light"
                                                                    onclick="printDiv({{ $order->id }})"><i
                                                                    class="fa fa-print"></i>Yazdır
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-danger light"
                                                                    data-bs-dismiss="modal">Kapat
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($order->platform == 'adisyo')
                                          <div class="modal fade" data-bs-backdrop="false" id="Orders{{ $order->id }}">
                                                <div class="modal-dialog modal-dialog-centered"
                                                     role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Sipariş Bilgileri</h5>
                                                            <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal">
                                                            </button>
                                                        </div>
                                                        <div class="modal-body" style="padding: 1rem;">
                                                            <div class="row">
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Sipariş Kodu</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->tracking_id }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Müşteri Adı</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->full_name }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Telefon</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->phone }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Tutar</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->amount }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Ödeme Yn.</p>
                                                                    <p class="orderProde">

                                                                        {{ $order->payment_method }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-2 col-md-12">
                                                                    <p class="orderTitle">Adres</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->address }}</p>
                                                                </div>
                                                                <div class="mb-3 col-md-12">
                                                                    @php $items = json_decode($order->items); @endphp
                                                                    <table
                                                                        class="table table-responsive-sm"
                                                                        style="min-width: 28rem !important;">
                                                                        <thead>
                                                                        <tr>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Ürün
                                                                            </th>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Adeti
                                                                            </th>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Fiyatı
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach ($items as $item)
                                                                            <tr>
                                                                                <th class="orderProde">
                                                                                    {{ $item->name }}
                                                                                </th>
                                                                                <th class="orderProde">
                                                                                    {{ $item->count }}
                                                                                </th>
                                                                                <th class="orderProde">
                                                                                    {{ $item->price }}
                                                                                    ₺
                                                                                </th>
                                                                            </tr>
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                    class="btn btn-primary light"
                                                                    onclick="printDiv({{ $order->id }})"><i
                                                                    class="fa fa-print"></i>Yazdır
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-danger light"
                                                                    data-bs-dismiss="modal">Kapat
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                          <div class="modal fade" data-bs-backdrop="false" id="Orders{{ $order->id }}">
                                                <div class="modal-dialog modal-dialog-centered"
                                                     role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Sipariş Bilgileri</h5>
                                                            <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal">
                                                            </button>
                                                        </div>
                                                        <div class="modal-body" style="padding: 1rem;">
                                                            <div class="row">
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Sipariş Kodu</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->tracking_id }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Müşteri Ad</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->full_name }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Telefon</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->phone }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Tutar</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->amount }}
                                                                    </p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Ödeme Yn.</p>
                                                                    <p class="orderProde">

                                                                        {{ $order->payment_method }}

                                                                    </p>
                                                                </div>
                                                                <div class="mb-2 col-md-12">
                                                                    <p class="orderTitle">Adres</p>
                                                                    <p class="orderProde">
                                                                        {{ $order->address }}</p>
                                                                </div>
                                                                <div class="mb-3 col-md-12">
                                                                    @php $items = json_decode($order->items); @endphp
                                                                    <table
                                                                        class="table table-responsive-sm"
                                                                        style="min-width: 28rem !important;">
                                                                        <thead>
                                                                        <tr>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Ürn
                                                                            </th>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Adeti
                                                                            </th>
                                                                            <th
                                                                                style="font-size: 14px;font-weight: 600">
                                                                                Fiyatı
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach ($items as $item)
                                                                            <tr>
                                                                                <th class="orderProde">
                                                                                    {{ $item->name }}
                                                                                </th>
                                                                                <th class="orderProde">
                                                                                    {{ count($item->items) }}
                                                                                </th>
                                                                                <th class="orderProde">
                                                                                    {{ $item->price }}
                                                                                    ₺
                                                                                </th>
                                                                            </tr>
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                    class="btn btn-primary light"
                                                                    onclick="printDiv({{ $order->id }})"><i
                                                                    class="fa fa-print"></i>Yazdr
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-danger light"
                                                                    data-bs-dismiss="modal">Kapat
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <a onclick="printDiv({{ $order->id }})"
                                           class="btn btn-danger shadow btn-xs sharp me-1">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>


                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="12" style="text-align: center;font-weight: bolder;">
                                Sipariş Bulunmamaktadır.
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Enable pusher logging - sadece test için
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: 'mt1', // kendi cluster'ını yaz
        encrypted: true
    });

    var channel = pusher.subscribe('orders-'+ {{auth()->id()}});

    channel.bind('order.created', function(data) {
        console.log('Yeni sipariş geldi:', data.orders);

        $("#example344 tbody").html(data.orders_html);
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#example344').DataTable();

        $('#custom-filter').keyup(function () {
            table.search(this.value).draw();
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#message_" + {{ $order->id }}).change(function () {
            var data = $("#message_" + {{ $order->id }}).val();
            var tracking_id = $("#tracking_" + {{ $order->id }}).val();
            $.ajax({
                type: 'POST',
                url: 'restaurant/orders/message' + '?_token=' + '{{ csrf_token() }}',
                data: {
                    message: data,
                    tracking_id: tracking_id
                },
                success: function (data) {
                    Swal.fire('Sipariş notu iletildi!');
                },
                error: function () {
                    console.log(data);
                }
            });
        });
    });

    $(document).ready(function () {
        $("#message2_" + {{ $order->id }}).change(function () {
            var data = $("#message2_" + {{ $order->id }}).val();
            var tracking_id = $("#tracking_" + {{ $order->id }}).val();
            $.ajax({
                type: 'POST',
                url: 'restaurant/orders/message2' + '?_token=' + '{{ csrf_token() }}',
                data: {
                    message2: data,
                    tracking_id: tracking_id
                },
                success: function (data) {
                    Swal.fire('Sipariş notu iletildi!');
                },
                error: function () {
                    console.log(data);
                }
            });
        });
    });

    function StatusOrderChange(e, id) {
        var action = e.target.value;
        var tracking_id = $('#tracking_' + id).val();
        var platform = $('#platform_' + id).val();

        // İptal işlemi için modal açılır
        if (action === 'UNSUPPLIED') {
            $('#cancelModal').modal('show');

            $('#confirmCancel').off('click').on('click', function () {
                var cancelReason = $('#message').val();

                if (cancelReason.trim() === '') {
                    Swal.fire('Lütfen iptal nedenini belirtin.');
                    return;
                }

                // Sipariş durumu ve kurye durumu güncellemesi
                sendOrderStatusUpdate(action, tracking_id, platform, cancelReason, id);
            });
        } else {
            sendOrderStatusUpdate(action, tracking_id, platform, null, id);
        }
    }


    function sendOrderStatusUpdate(action, tracking_id, platform, message, orderId) {
        var requestData = {
            action: action,
            tracking_id: tracking_id,
            _token: '{{ csrf_token() }}'
        };

        if (message) {
            requestData.message = message;
        }

        $.ajax({
            type: 'POST',
            url: '/restaurant/' + platform + '/updateOrderStatus',
            data: requestData,
            success: function (data) {
                if (action === 'DELIVERED' || action === 'UNSUPPLIED') {
                    updateCourierStatus(orderId);
                }
                Swal.fire({
                    title: 'Sipariş durumu başarıyla değiştirildi.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function () {

                });
                $('#cancelModal').modal('hide');
            },
            error: function (xhr, status, error) {
                console.log('Failed to update order status');
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response Text:', xhr.responseText); // Daha detaylı hata mesajını gösterir
                Swal.fire({
                    title: 'Hata oluştu!',
                    text: xhr.responseText, // Sunucudan gelen hata mesajını göstermek için
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    }

    function updateCourierStatus(orderId) {
        $.ajax({
            type: 'POST',
            url: '/restaurant/updateCourierStatus',
            data: {
                order_id: orderId,
                _token: '{{ csrf_token() }}'
            },
            success: function (data) {
                console.log('Courier status updated successfully');
            },
            error: function (xhr, status, error) {
                console.log('Failed to update courier status');
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response Text:', xhr.responseText); // Detaylı hata mesajını gösterir
            }
        });
    }

    function Courier() {
        let selectedOrders = [];
        $('input[name="orders[]"]:checked').each(function () {
            selectedOrders.push($(this).val());
        });

        let courierId = $('#courierId').val();
        console.log('Selected Courier ID:', courierId);

        if (selectedOrders.length > 0 && courierId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/restaurant/orders/sendCourier',
                data: {
                    orders: selectedOrders,
                    courier_id: courierId
                },
                success: function (data) {
                    console.log('Response data:', data);
                    if (data == "OK") {
                        // Zil sesi çalınır
                        var audio = new Audio('/pos/audio/bell_small_002.mp3');
                        audio.play().then(() => {
                            console.log("Ses başarıyla çalındı.");
                        }).catch(error => {
                            console.error("Ses çalarken hata oluştu:", error);
                        });

                        Swal.fire('Siparişler başarıyla atandı!');

                        // 2 saniye sonra sayfayı yenile
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else if (data == "ERR") {
                        Swal.fire('Hata oluştu!');
                        console.error('Error:', data);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    Swal.fire('Bir hata oluştu!');
                }
            });
        } else {
            Swal.fire('Lütfen bir kurye ve sipariş seçiniz!');
        }
    }

    function printDiv(orderId) {
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/printed/' + orderId,
            success: function (data) {

                var divToPrint = data.printed;
                var mywindow = window.open('', 'PRINT', 'height=600,width=800');
                mywindow.document.write('<html><head><title>' + document.title + '</title>');
                mywindow.document.write('</head><body >');
                mywindow.document.write(divToPrint);
                mywindow.document.write('</body></html>');
                mywindow.document.close(); // necessary for IE >= 10
                mywindow.focus(); // necessary for IE >= 10*/
                mywindow.print();

            },
            error: function () {
                console.log(data);
            }
        });
    }
</script>

