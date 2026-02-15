@extends('restaurant.layouts.app')

@section('content')
    <style>
        th {
            font-weight: bold;
        }

        .orderTitle {
            font-size: 14px;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
        }

        .orderProde {
            font-size: 13px;
            font-weight: 500;
        }

        /* Courier Style */
        .courier-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .courier-item {
            flex: 1 1 200px;
            min-width: 200px;
        }

        .courier-box {
            padding: 15px;
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .courier-box p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-assign {
            padding: 5px 15px;
            font-size: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-assign:hover {
            background-color: #218838;
        }

        .courier-box {
            position: relative;
        }

        .courier-box::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 10px;
            border-top-right-radius: 2px;
            border-bottom-right-radius: 2px;
        }

        .red-box::after {
            background-color: #ff0a0a;
        }

        .purple-box::after {
            background-color: #624FD1;
        }

        .avatar {
            vertical-align: middle;
            width: 35px;
            height: 35px;
            border-radius: 0%;
        }

        .bg-default,
        .btn-default {
            background-color: #f2f3f8;
        }

        .btn-error {
            color: #ef5f5f;
        }

        .tabs {
            display: flex;
            flex-wrap: wrap; // make sure it wraps
        }

        .tabs label {
            order: 1;
            display: block;
            padding: 1rem 2rem;
            margin-right: 0.2rem;
            cursor: pointer;
            background: #5c5c5c;
            font-weight: bold;
            transition: background ease 0.2s;
            color: #fff;
        }

        .tabs .tab {
            order: 99;
            flex-grow: 1;
            width: 100%;
            display: none;
            padding: 1rem;
            background: #fff;
        }

        .tabs input[type="radio"] {
            display: none;
        }

        .tabs input[type="radio"]:checked+label {
            background: #edeff1;
            color: #000;
        }

        .tabs input[type="radio"]:checked+label+.tab {
            display: block;
        }

        @media (max-width: 45em) {

            .tabs .tab,
            .tabs label {
                order: initial;
            }

            .tabs label {
                width: 100%;
                margin-right: 0;
                margin-top: 0.8rem;
            }
        }

        .toplusil a i:hover {
            color: red;
        }

        .paymentRol {
            padding: 12px;
            font-size: 18px;
            color: #fff;
            text-align: center;
            font-weight: bold;
            height: 80px;
            border-radius: 10px;
            margin: 0px 2px;
            cursor: pointer;
        }

        .nakit {
            background: #008002;
        }

        .kkkarti {
            background: #f72b50;
        }

        .kkarti {
            background: #624FD1;
        }

        .kayit {
            background: #fd673e;
            padding: 25px;
            font-size: 22px;
        }

        .customer {
            padding: 0px 10px;
            border-left: 3px solid #5c5c5c;
            width: 100%;
            border-radius: 10px;
            border-right: 3px solid #5c5c5c;
            text-align: left;
        }

        .rightbtn {
            padding: 0 !important;
        }

        .rightbtn a {
            padding: 10px 15px;
            height: 50px;
            font-size: 18px;
            color: #fff !important;
            background: #5c5c5c;
        }

        .selectiki {
            height: 50px !important;
            padding: 10px;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            line-height: 40px;
        }

        .select2-container--default .select2-results__option--selected {
            background-color: #e7e7e7;
            line-height: 40px;
        }

        .select2-results__option--selectable {
            cursor: pointer;
            line-height: 40px;
        }


        #loader {
            position: absolute;
            padding: 0;
            width: 100%;
            height: 100vh;
            text-align: center;
            background: #fff;
            z-index: 999;
        }

        #loader2 {
            position: absolute;
            top: 50%;
            display: table-cell;
            vertical-align: middle;
        }

        #loader img {
            position: relative;
            top: 50%;
        }

        .select-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0;
            padding: 10px;
            background-color: #fff;
            border-radius: 10px;
            margin-left: 200px;
            margin-right: 25px;
        }

        .custom-select {
            width: 100%;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 500;
            color: #495057 !important;
            border: 2px solid #ced4da;
            border-radius: 8px;
            background-color: #ffffff;
            transition: border-color 0.3s ease, background-color 0.3s ease;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII, %3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"%3E%3Cpath fill="%23999" d="M2 0L0 2h4z"/%3E%3C/svg%3E') !important;
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 12px;
        }

        .custom-select:focus {
            border-color: #80bdff !important;
            background-color: #f0f8ff !important;
            outline: none !important;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5) !important;
        }

        .custom-select:hover {
            border-color: #5a6268;
            background-color: #e9ecef;
        }

        .custom-select option {
            padding: 10px;
            font-size: 16px;
            font-weight: 500;
        }

        .custom-select option:hover {
            background-color: #007bff !important;
            color: white;
        }

        .custom-select option:checked {
            background-color: #007bff !important;
            color: white;
        }

        .select-container::after {
            content: '⏷';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #999;
            pointer-events: none;
        }

        .order-card {
            border-radius: 10px;
            color: white;
            padding: 5px;
            text-align: center;
            border: none;
        }

        .order-card span {
            font-size: 1rem;
        }

        .order-btn {
            font-size: 0.9rem;
        }

        .orders-section,
        .performance-section {
            background-color: #efefef;
            padding: 20px;
            border-radius: 10px;
        }

        .order-number {
            font-size: 1.5rem;
            font-weight: bold;
        }



        .btn-group-custom {
            border-radius: 10px;
            padding: 10px 20px;
        }

        .date-filter span {
            font-size: 14px;
        }

        .date-filter:hover {
            padding: 1.5px 10px;
            background-color: #FD683E;
            border: #fff;
            border-radius: 15px;
            transition: .5s all;
        }

        .date-filter:hover span {
            color: #fff !important;
            padding-left: 5px;
        }

        .date-filter:hover .fas {
            color: #fff !important;
            transition: .8s all;
        }

        #modal-header-newOrde {
            padding: 0.2rem 1.5rem !important;
        }

        .custom-input {
            border-radius: 12px;
            padding: 8px 12px;
            font-size: .8rem;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
            width: 200px;
            background-color: #f9f9f9;
        }

        .custom-input:focus {
            border-color: #fd683e;
            box-shadow: 0 0 8px rgba(253, 104, 62, 0.4);
            background-color: white;
        }

        .custom-input::placeholder {
            color: #b0b0b0;
            font-style: italic;
        }

        .form-label {
            font-weight: 400;
            color: #555;
        }

        .custom-btn:hover {
            background: #fff;
            color: #fd683e;
            box-shadow: 0 4px 12px rgba(253, 104, 62, 0.3);
            transition: .8s all;
        }

        .custom-link {
            display: flex;
            align-items: center;
            color: #000;
            font-weight: 600;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
            transition: .8s all;
        }

        .custom-link i {
            margin-right: 8px;
            color: #FD683E;
        }

        .custom-link:hover {
            padding: 10px 15px;
            background-color: #FD683E;
            color: white;
            box-shadow: 0 4px 8px rgba(253, 104, 62, 0.3);
        }

        .date-filters {
            margin-left: 20px;
        }

        .custom-dropdown {
            position: relative;
            width: 100%;
        }

        .select-box {
            background-color: #fff;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .select-box::after {
            content: "▼";
            font-size: 12px;
            color: #333;
        }

        .options-container {
            display: none;
            position: absolute;
            top: 50px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            z-index: 10;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .option {
            padding: 12px;
            font-size: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #333;
        }

        .option:hover {
            background-color: #f0f0f0;
        }

        .color-circle {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .option[data-type="restaurant"] .color-circle {
            background-color: #00bcd4;
        }

        .option[data-type="general"] .color-circle {
            background-color: #ff9800;
        }
    </style>

    <div class="container-fluid" style="padding-top: 1.5rem">

        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <div class="w-100 d-flex align-items-center justify-content-between">
                    <form method="GET" action="{{ route('restaurant.filterByDate') }}"
                          class="d-flex  gap-3 align-items-center">
                        <div>
                            <input type="date" class="form-control custom-input" id="start_date" name="start_date"
                                   required>
                        </div>
                        <div>
                            <input type="date" class="form-control custom-input" id="end_date" name="end_date" required>
                        </div>
                        <div class="d-flex align-items-end">
                            <button style="background: #fd683e;color:#fff;font-size: 0.8rem" type="submit"
                                    class="btn custom-btn">
                                <i class="fas fa-calendar-day" style="padding-right:5px"></i>
                                Filtrele</button>
                        </div>
                    </form>
                    <div class="date-filters d-flex align-items-center gap-1 ">
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'today']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day"></i>
                            <span>Bugün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'yesterday']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day"></i>
                            <span>Dün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'this_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week"></i>
                            <span>Bu Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'last_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week"></i>
                            <span>Geçen Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'last_month']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week"></i>
                            <span>Geçen Ay</span>
                        </a>
                    </div>
                    <div class="customer-search mb-sm-0 ">
                        <div class="input-group search-area">
                            <input style="font-size: 0.8rem;width:200px" type="text" class="form-control"
                                   id="custom-filter" placeholder="Sipariş ara..">
                            <span class="input-group-text"><a href="javascript:void(0)"><i
                                        class="flaticon-381-search-2"></i></a></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="orders-section">
                    <h4>Siparişler</h4>
                    <div class="row g-3">
                        <!-- All Orders Button -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom bg-warning order-btn d-flex justify-content-between align-items-center w-100">

                                <span>
                                    <i class="fa-solid fa-box" style="color: #fff;font-size:18px;padding-right:10px"></i>
                                    Tüm Siparişler</span>
                                <span class="badge bg-light text-dark order-number">{{ count($tumu) }}</span>
                            </button>
                        </div>
                        <!-- Getir Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom bg-secondary order-btn d-flex justify-content-between align-items-center w-100">
                                <img src="{{ asset('theme/images/platforms/getir.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="77px"
                                     height="14px" alt="">
                                <span>Getir Siparişleri</span>
                                <span class="badge bg-light text-dark order-number">{{ count($getiryemek) }}</span>
                            </button>
                        </div>
                        <!-- Trendyol Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom bg-primary order-btn d-flex justify-content-between align-items-center w-100">
                                <img src="{{ asset('theme/images/platforms/trendyol.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="71px"
                                     height="14px" alt="">
                                <span>Trendyol Yemek Siparişleri</span>
                                <span class="badge bg-light text-dark order-number">{{ count($trendyol) }}</span>
                            </button>
                        </div>

                        <!-- Yemeksepeti Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #F90050">
                                <img src="{{ asset('theme/images/platforms/yemeksepeti.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="79px"
                                     height="15px" alt="">
                                <span>Yemeksepeti Siparişleri</span>
                                <span class="badge bg-light text-dark order-number">{{ count($yemeksepeti) }}</span>
                            </button>
                        </div>
                        <!-- Migros Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #363A86">
                                <img src="{{ asset('theme/images/platforms/migros.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="69px"
                                     height="28px" alt="">
                                <span>Migros Yemek Siparişleri</span>
                                <span class="badge bg-light text-dark order-number">{{ $migros }}</span>
                            </button>
                        </div>
                        <!-- Phone Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #1da713">
                                <i class="fa-solid fa-phone" style="color: #fff;font-size:18px;padding-left:10px"></i>
                                <span>Telefon Siparişi</span>
                                <span class="badge bg-light text-dark order-number">{{ count($telefonsiparis) }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Section -->
            <div class="col-lg-6">
                <div class="performance-section mb-2">
                    <h4>Satış Performansı</h4>
                    <div class="row g-3">
                        <!-- Revenue Card -->
                        <div class="col-4">
                            <div class="order-card bg-primary text-white">
                                <p>Ciro</p>
                                <span class="order-number">{{ $formattedExpense }} TL</span>
                            </div>
                        </div>
                        <!-- Orders Count Card -->
                        <div class="col-4">
                            <div class="order-card bg-secondary text-white">
                                <p>Sipariş Sayısı</p>
                                <span class="order-number">{{ count($tumu) }} Adet</span>
                            </div>
                        </div>
                        <!-- Average Order Amount Card -->
                        <div class="col-4">
                            <div class="order-card bg-warning text-white">
                                <p>Ortalama Sipariş Tutarı</p>
                                <span class="order-number">{{ $formattedAverageExpense }} TL</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- Modal For New Order --}}
  <div class="modal fade" data-bs-backdrop="false" id="Orders" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header" id="modal-header-newOrde" style="padding: 0.1rem 1.5rem">
                    <h5 class="modal-title">Yeni Sipariş Ekle</h5>
                    <div class="select-container">
                        <select class="custom-select" onchange="customerSelect(event)">
                            <option value="0">Müşteri Seçiniz</option>
                            @foreach ($customers as $customer)
                                <option name="" value="{{ $customer->id }}">{{ $customer->name }} -
                                    {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" style="font-family: 'Poppins', sans-serif;"
                            class="btn btn-secondary btn-rounded" data-bs-toggle="modal"
                            data-bs-target="#yeniMusteri">
                        <i class="fas fa-cash-register"></i> Müşteri Ekle
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 0">
                    <section class="section-content bg-default">
                        <form method="post" id="formPos">
                            <div class="container-fluid" style="padding-top: 10px">
                                <div class="row">
                                    <input type="hidden" name="payment_control" id="payment_control"
                                           value="0">
                                    <input type="hidden" name="customer_id" id="customer_id" value="">
                                    <input type="hidden" name="courier_id" id="courier_id" value="">
                                    <input type="hidden" name="total" id="totalPrice" value="">
                                    <div class="col-md-9 card padding-y-sm card" style="border-radius: 10px">
                                        <!-- Tab and Product Details -->
                                        <div class="tabs">
                                            @php $s = 0; @endphp
                                            @foreach ($categories as $cat)
                                                @php $s++; @endphp
                                                <input type="radio" name="tabs"
                                                       id="tabProduct_{{ $cat->id }}"
                                                       @if ($s == 1) checked="checked" @endif>
                                                <label
                                                    for="tabProduct_{{ $cat->id }}">{{ $cat->name }}</label>
                                                <div class="tab">
                                                    <div class="row">
                                                        @foreach (\App\Models\Product::where('categorie_id', $cat->id)->where('status', 'active')->where('restaurant_id', \Illuminate\Support\Facades\Auth::user()->id)->get() as $pro)
                                                            <div class="col-md-2"
                                                                 style="max-width: 16.3%;border-radius:5px; margin:4px 2px;padding: 0px;height:200px;color:#fff">
                                                                <a onclick="productAdd({{ $pro->id }})"
                                                                   style="cursor: pointer">
                                                                    <figure class="card card-product"
                                                                            style="background:#fd673e">
                                                                        <figcaption class="info-wrap"
                                                                                    style="padding:3px 5px">
                                                                            <div class="row">
                                                                                <div class="col-lg-12"
                                                                                     style="height:90px; text-align: center;padding: 13px 10px;">
                                                                                            <span
                                                                                                style="font-size: 24px;color:#fff;font-weight: 500;"
                                                                                                class="title">{{ $pro->name }}</span>
                                                                                </div>
                                                                                <div class="col-lg-12 text-center"
                                                                                     style="height:40px;padding-bottom:10px">

                                                                                            <span
                                                                                                style="font-size: 30px;font-weight:bold"
                                                                                                class="price-new">{{ number_format($pro->price, 2, ',', '.') }}₺</span>
                                                                                </div>
                                                                            </div>
                                                                        </figcaption>
                                                                    </figure>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Right Cart Section -->
                                    <div class="col-md-3">
                                        <div class="card" style="border-radius: 10px">
                                            <div id="sepetim" class="row">
                                                <div class="col-lg-6 text-left">
                                                    <div class="sepet" style="padding: 10px 30px;">
                                                        <div style="font-size: 22px">
                                                            <i class="fa fa-shopping-bag"></i>
                                                            <span style="font-size: 15px"
                                                                  id="posTotalItem">{{ \Cart::session(\Illuminate\Support\Facades\Auth::user()->id)->getTotalQuantity() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 text-right">
                                                    <div class="toplusil" style="padding: 10px 30px;">
                                                        <a onclick="removePos(1)"
                                                           style="font-size: 20px;cursor:pointer;"><i
                                                                class="fa fa-trash-alt"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="productItems row" style="min-height: 500px;">
                                                <div class="col-lg-12" id="productItemListp"
                                                     style="padding: 20px;height: 460px;">
                                                    @foreach (\Cart::session(\Illuminate\Support\Facades\Auth::user()->id)->getContent() as $basket)
                                                        <div id="posItem_{{ $basket->id }}"
                                                             class="item select-none mb-3 bg-blue-gray-50 rounded-lg w-full text-blue-gray-700 py-2 px-2 flex justify-center"
                                                             style="background: #e7e7e7;border-radius: 10px">
                                                            <div class="row">
                                                                <input type="hidden" name="product_id[]"
                                                                       value="{{ $basket->id }}">
                                                                <div class="col-md-2">
                                                                    <img src="{{ asset($basket->associatedModel['image']) }}"
                                                                         style="height: 45px;width:65px;border-radius: 10px">
                                                                </div>
                                                                <div class="col-md-6 text-left"
                                                                     style="width: 100%;">
                                                                            <span
                                                                                style="width: 100%;">{{ $basket->name }}</span>
                                                                    <br>
                                                                    <input type="hidden" name="price[]"
                                                                           value="{{ $basket->price }}">
                                                                    <span
                                                                        style="width: 100%;">{{ number_format($basket->price, 2, ',', '.') }}</span>
                                                                </div>
                                                                <div class="col-md-4 text-right">
                                                                    <div class="m-btn-group m-btn-group--pill btn-group mr-2"
                                                                         role="group" aria-label="..."
                                                                         style="padding: 3px">
                                                                        <button type="button"
                                                                                onclick="updateMinus({{ $basket->id }})"
                                                                                class="m-btn btn btn-default"
                                                                                style="background: #5c5c5c;color:#fff"><i
                                                                                class="fa fa-minus"></i></button>
                                                                        <input type="button"
                                                                               class="m-btn btn btn-default"
                                                                               name="quantity[]"
                                                                               id="quantity_{{ $basket->id }}"
                                                                               value="{{ $basket->quantity }}"
                                                                               style="background: #fff;color:#000;font-weight: bold"
                                                                               disabled>
                                                                        <button type="button"
                                                                                onclick="updatePlus({{ $basket->id }})"
                                                                                class="m-btn btn btn-default"
                                                                                style="background: #5c5c5c;color:#fff"><i
                                                                                class="fa fa-plus"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div style="padding: 1rem 1.2rem;" class="">
                                                <dl class="dlist-align">
                                                    <dt>Toplam:</dt>
                                                    <dd class="text-right h4 b" id="posTotal">
                                                        {{ number_format(\Cart::session(\Illuminate\Support\Facades\Auth::user()->id)->getTotal(), 2, ',', '.') }}
                                                        TL</dd>
                                                </dl>

                                                <div class="row" style="margin:0px;">
                                                    <div class="col-md-4" style="padding: 10px 0px">
                                                        <div class="paymentRol nakit"
                                                             onclick="PaymentMethodSave('Kapıda Nakit ile Ödeme')"
                                                             style="font-size: 14px;">
                                                            <i class="fas fa-lira-sign"></i><br> Nakit
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px 0px">
                                                        <div class="paymentRol kkarti"
                                                             onclick="PaymentMethodSave('Kapıda Ticket ile Ödeme')"
                                                             style="font-size: 14px;">
                                                            <i class="fas fa-credit-card"></i><br> Ticket
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px 0px">
                                                        <div class="paymentRol kkkarti"
                                                             onclick="PaymentMethodSave('Kapıda Kredi Kartı ile Ödeme')"
                                                             style="font-size: 14px;">
                                                            <i class="fas fa-credit-card"></i><br> Kredi Kartı
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" style="padding: 10px 0px">
                                                        <div class="paymentRol kayit" onclick="CreateOrder()">
                                                            <i class="fas fa-check"></i> Kaydet
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Cart Section -->
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal For New Order End --}}

    {{-- Yeni Customer Modal Start --}}
  <div class="modal fade" data-bs-backdrop="false" id="yeniMusteri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Müşteri Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        {{-- <span aria-hidden="true">&times;</span> --}}
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" style="padding: 20px">
                        <div class="basic-form">
                            <form method="post" id="customerForm">
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Müşteri Adı</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                               placeholder="Müşteri Adı" required>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon Numarası</label>
                                        <input type="text" class="form-control" name="phone"
                                               placeholder="Telefon Numarası" id="phoneNumber" required>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">2.Telefon Numarası</label>
                                        <input type="text" class="form-control" name="mobile"
                                               placeholder="2.Telefon Numarası">
                                    </div>

                                </div>

                                <div class="card-body" style="border-top:1px solid #ddd;padding: 0px 0px">
                                    <div class="clearfix"></div>
                                    <div class="repeater-heading">
                                        <div class="row">
                                            <div class="col-lg-10">
                                                <h5 class="pull-left">Adres Ekle</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="item-content row"
                                         style="background: #f4f4f4;margin: 15px 0px  10px;padding:10px 0px;border-radius: 10px">
                                        <div class="mb-3 col-md-6">
                                            <input type="text" class="form-control" id="adres_name"
                                                   name="adres_name" value="Adres" value="Adres"
                                                   placeholder="Adres Başlğı">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <input type="text" class="form-control" id="sokak_cadde"
                                                   name="sokak_cadde" value="" placeholder="Sokak/Cadde">
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <input type="text" class="form-control" id="bina_no"
                                                   name="bina_no" value="" placeholder="Bina No">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <input type="text" class="form-control" id="kat"
                                                   name="kat" value="" placeholder="Kat">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <input type="text" class="form-control" id="daire_no"
                                                   name="daire_no" value="" placeholder="Daire No">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <input type="text" class="form-control" id="mahalle"
                                                   name="mahalle" value="" placeholder="Mahalle">
                                        </div>


                                        <div class="mb-3 col-md-12">
                                            <input type="text" name="adres_tarifi" id="adres_tarifi"
                                                   class="form-control" placeholder="Adres Tarifi">
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="CreateCustomer()">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Yeni Customer Modal End --}}





    <div class="col-xl-12 col-xxl-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example3" class="order-table shadow-hover card-table text-black"
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
                                            <!-- TODO : Adisyo Platform Order-->
                                        @endif

                                        @if ($order->platform == 'adisyo' || $order->platform == 'Adisyo')
                                            <a style="padding: 10px;background: #ff0a0a;border-color: #fff; font-size: 14px"
                                               class="btn btn-primary btn-rounded"><img
                                                    src="{{ asset('theme/images/adisyoFull.png') }}"
                                                    style="height: 25px;"> </a>
                                        @endif
                                        @if ($order->platform == 'telefonsiparis')
                                            <a class="btn btn-warning btn-rounded"
                                               style="wdith:100%;font-weight: bold;padding:10px 15px;font-size:14px;">
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
                                                $courierOrder = \App\Models\CourierOrder::where(
                                                    'order_id',
                                                    $order->id,
                                                )->first();
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
                                               class="btn btn-secondary sharp me-1">
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
                                                                        <option value="-1">ParsKurye
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
                                                                    data-bs-dismiss="modal">Kapat</button>
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
                                    </td>

                                    <td class="text-ov"><input type="text"
                                                               class="form-control"style="width: 150px;"
                                                               id="message_{{ $order->id }}" value="{{ $order->message }}"
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
                                                HAZIRLANIYOR
                                            </option>
                                            <option value="PREPARED"
                                                    @if ($order->status == 'PREPARED') selected @endif>
                                                HAZIRLANDI
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
                                                    <h5 class="modal-title" id="cancelModalLabel">Siparişi
                                                        İptal
                                                        Et</h5>
                                                    <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label for="cancelReason" class="form-label">Siparişi
                                                        neden
                                                        iptal etmek istiyorsunuz?</label>
                                                    <textarea class="form-control" id="message" rows="4" placeholder="Lütfen iptal nedeninizi yazın..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Geri Dön</button>
                                                    <button type="button" class="btn btn-danger"
                                                            id="confirmCancel">İptal Et</button>
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
                                                                        @php $items = json_decode($order->items); @endphp
                                                                        <table
                                                                            class="table table-responsive-sm"
                                                                            style="min-width: 28rem !important;">
                                                                            <thead>
                                                                            <tr>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Ürün</th>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Adeti</th>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Fiyatı</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                            @foreach ($items as $item)
                                                                                @php $name = $item->name; @endphp

                                                                                <tr>
                                                                                    <th class="orderProde">
                                                                                        {{ $name }}
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
                                                                        class="fa fa-print"></i>Yazdır</button>
                                                                <button type="button"
                                                                        class="btn btn-danger light"
                                                                        data-bs-dismiss="modal">Kapat</button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($order->platform == 'yemeksepeti')
                                              <div class="modal fade" data-bs-backdrop="false" data-bs-backdrop="false" id="Orders{{ $order->id }}">
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
                                                                                    Ürün</th>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Adeti</th>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Fiyatı</th>
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
                                                                        class="fa fa-print"></i>Yazdır</button>
                                                                <button type="button"
                                                                        class="btn btn-danger light"
                                                                        data-bs-dismiss="modal">Kapat</button>
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
                                                                                    Ürün</th>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Adeti</th>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Fiyatı</th>
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
                                                                        class="fa fa-print"></i>Yazdır</button>
                                                                <button type="button"
                                                                        class="btn btn-danger light"
                                                                        data-bs-dismiss="modal">Kapat</button>
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
                                                                                    Ürn</th>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Adeti</th>
                                                                                <th
                                                                                    style="font-size: 14px;font-weight: 600">
                                                                                    Fiyatı</th>
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
                                                                        class="fa fa-print"></i>Yazdr</button>
                                                                <button type="button"
                                                                        class="btn btn-danger light"
                                                                        data-bs-dismiss="modal">Kapat</button>
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
                                    <script>
                                        $(document).ready(function() {

                                            $("#message_" + {{ $order->id }}).change(function() {
                                                var data = $("#message_" + {{ $order->id }}).val();
                                                var tracking_id = $("#tracking_" + {{ $order->id }}).val();
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'restaurant/orders/message' + '?_token=' + '{{ csrf_token() }}',
                                                    data: {
                                                        message: data,
                                                        tracking_id: tracking_id
                                                    },
                                                    success: function(data) {
                                                        Swal.fire('Sipariş notu iletildi!');
                                                    },
                                                    error: function() {
                                                        console.log(data);
                                                    }
                                                });


                                            });
                                        });


                                        $(document).ready(function() {

                                            $("#message2_" + {{ $order->id }}).change(function() {
                                                var data = $("#message2_" + {{ $order->id }}).val();
                                                var tracking_id = $("#tracking_" + {{ $order->id }}).val();
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'restaurant/orders/message2' + '?_token=' + '{{ csrf_token() }}',
                                                    data: {
                                                        message2: data,
                                                        tracking_id: tracking_id
                                                    },
                                                    success: function(data) {
                                                        Swal.fire('Sipariş notu iletildi!');
                                                    },
                                                    error: function() {
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

                                                $('#confirmCancel').off('click').on('click', function() {
                                                    var cancelReason = $('#message').val();

                                                    if (cancelReason.trim() === '') {
                                                        Swal.fire('Lütfen iptal nedenini belirtin.');
                                                        return;
                                                    }

                                                    // Sipariş durumu ve kurye durumu güncellemesi
                                                    sendOrderStatusUpdate(action, tracking_id, platform, cancelReason, id);
                                                });
                                            } else {
                                                // Diğer durumlar için doğrudan sipariş durumu ve kurye durumu güncellemesi
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
                                                success: function(data) {
                                                    if (action === 'DELIVERED' || action === 'UNSUPPLIED') {
                                                        updateCourierStatus(orderId);
                                                    }
                                                    Swal.fire({
                                                        title: 'Sipariş durumu başarıyla değiştirildi.',
                                                        icon: 'success',
                                                        confirmButtonText: 'OK'
                                                    }).then(function() {
                                                        location.reload();
                                                    });
                                                    $('#cancelModal').modal('hide');
                                                },
                                                error: function(xhr, status, error) {
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
                                                success: function(data) {
                                                    console.log('Courier status updated successfully');
                                                },
                                                error: function(xhr, status, error) {
                                                    console.log('Failed to update courier status');
                                                    console.log('Status:', status);
                                                    console.log('Error:', error);
                                                    console.log('Response Text:', xhr.responseText); // Detaylı hata mesajını gösterir
                                                }
                                            });
                                        }

                                        function Courier() {
                                            let selectedOrders = [];
                                            $('input[name="orders[]"]:checked').each(function() {
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
                                                    success: function(data) {
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
                                                            setTimeout(function() {
                                                                location.reload();
                                                            }, 1000);
                                                        } else if (data == "ERR") {
                                                            Swal.fire('Hata oluştu!');
                                                            console.error('Error:', data);
                                                        }
                                                    },
                                                    error: function(xhr, status, error) {
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



                                        function printDiv(orderid) {

                                            $.ajax({
                                                type: 'GET', //THIS NEEDS TO BE GET
                                                url: '/restaurant/orders/printed/' + orderid,
                                                success: function(data) {

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
                                                error: function() {
                                                    console.log(data);
                                                }
                                            });


                                        }
                                    </script>
                                    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
                                    <!--TODO : Pusher Data-->
                                    <script>
                                        // Pusher'ı başlat
                                        var pusher = new Pusher('c68579a24eeaebfd6487', {
                                            cluster: 'eu'
                                        });

                                        // 'orders' kanalını dinle
                                        var channel = pusher.subscribe('sweet-garden-70');
                                        channel.bind('App\\Events\\NewOrderAdded', function(data) {
                                            // Yeni sipariş geldiğinde ekrana verileri ekle
                                            console.log('Yeni sipariş:', data.order);
                                            // Burada DOM manipülasyonu yaparak yeni siparişi sayfaya ekleyebilirsiniz.
                                        });
                                    </script>


                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="12" style="text-align: center;font-weight: bolder;">
                                    Bu tarih
                                    aralığında sipariş
                                    bulunamadı.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.js-example-basic-single').select2({
                selectionCssClass: 'selectiki',
                placeholder: 'Müşteri Arayınız..',
            });

        });

        function productAdd(e) {
            let quant = $('#quantity_' + e).val();

            $('#loader').css('display', 'block');

            $.ajax({
                type: 'GET',
                url: '/restaurant/orders/addPOS/' + e,
                success: function(data) {
                    let src = '{{ url('pos/audio/dot.mp3') }}';
                    let audio = new Audio(src);
                    audio.play();

                    $('#productItemListp').css('display', 'none');
                    $('#productItemLista').css('display', 'block');


                    if (data.durum === "yok") {
                        $('#productItemLista').append(data.items);
                    } else {
                        let newquant = parseInt(quant) + 1;
                        $('#quantity_' + e).val(newquant);
                    }

                    $('#posTotalItem').html(data.posTotalItem);

                    $('#posTotal').html(data.posTotal);

                    $('#totalPrice').val(data.total);

                    $('#loader').css('display', 'none');

                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Ürün başarılı bir şekilde eklendi.',
                        icon: 'success',
                        confirmButtonText: 'Tamam'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = window.location.pathname + "?showModal=Orders";
                        }
                    });
                },
                error: function() {
                    console.log("Product addition error.");
                }
            });
        }


        function updatePlus(id) {

            $('#loader').css('display', 'block');

            let quant = $('#quantity_' + id).val();

            $.ajax({
                type: 'GET', //THIS NEEDS TO BE GET
                url: '/restaurant/orders/updatePlusPOS/' + id,
                success: function(data) {
                    let src = '{{ url('pos/audio/dot.mp3') }}';
                    let audio = new Audio(src);
                    audio.play();

                    let newquant = parseInt(quant) + 1;
                    $('#quantity_' + id).val(newquant);

                    $('#posTotalItem').html(data.posTotalItem);
                    $('#posTotal').html(data.posTotal);
                    $('#totalPrice').val(data.total);

                    $('#loader').css('display', 'none');


                },
                error: function() {
                    console.log(data);
                }
            });


        }

        function updateMinus(id) {

            $('#loader').css('display', 'block');

            let qty = document.getElementById("quantity_" + id).value;

            $.ajax({
                type: 'GET', //THIS NEEDS TO BE GET
                url: '/restaurant/orders/updateMinusPOS/' + id + '/' + qty,
                success: function(data) {
                    let src = '{{ url('pos/audio/dot.mp3') }}';
                    let audio = new Audio(src);
                    audio.play();

                    if (qty <= 1) {
                        $("#posItem_" + id).remove();
                        $('#posTotalItem').html(data.posTotalItem);
                        $('#posTotal').html(data.posTotal);
                        $('#totalPrice').val(data.total);
                    } else {
                        let newquant = parseInt(qty) - 1;
                        $('#quantity_' + id).val(newquant);
                        $('#posTotalItem').html(data.posTotalItem);
                        $('#posTotal').html(data.posTotal);
                        $('#totalPrice').val(data.total);
                    }

                    $('#loader').css('display', 'none');


                },
                error: function() {
                    console.log(data);
                }
            });


        }

        function removePos(e) {
            $.ajax({
                type: 'GET', //THIS NEEDS TO BE GET
                url: '/restaurant/orders/removePOS',
                success: function(data) {
                    let src = '{{ url('pos/audio/trash.mp3') }}';
                    let audio = new Audio(src);
                    audio.play();
                    $('#productItemListp').html("");
                    $('#productItemLista').html("");
                    $('.customer').html('<div style="text-align: center;padding: 15px">Müşteri Seçin</div>');
                    $('#posTotalItem').html("0");
                    $('#posTotal').html("0,00 TL");
                    window.location.href = window.location.pathname + "?showModal=Orders";
                },
                error: function() {
                    console.log(data);
                }
            });
        }

        function PaymentMethodSave(e) {
            $('#payment_control').val(e);

            if (e === "Kapıda Nakit ile Ödeme") {
                let src = '{{ url('pos/audio/beep.mp3') }}';
                let audio = new Audio(src);
                audio.play();
                $('.nakit').css('background', '#5c5c5c');
                $('.kkarti').css('background', '#624FD1');
                $('.kkkarti').css('background', '#f72b50');
            }
            if (e === "Kapıda Ticket ile Ödeme") {
                let src = '{{ url('pos/audio/beep.mp3') }}';
                let audio = new Audio(src);
                audio.play();
                $('.nakit').css('background', '#008002');
                $('.kkarti').css('background', '#5c5c5c');
                $('.kkkarti').css('background', '#f72b50');
            }
            if (e === "Kapıda Kredi Kartı ile Ödeme") {
                let src = '{{ url('pos/audio/beep.mp3') }}';
                let audio = new Audio(src);
                audio.play();
                $('.nakit').css('background', '#008002');
                $('.kkarti').css('background', '#624FD1');
                $('.kkkarti').css('background', '#5c5c5c');
            }
        }

        function CourierSet(e) {
            $('#courier_id').val(e.target.value);
            $('#kuryeAta').modal('hide');

        }

        function ExitPos() {

            window.location.href = 'https://panel.parskurye.net';

        }

        function customerSelect(e) {
            let customerId = e.target.value;
            console.log("Seçilen Müşteri ID'si:", customerId); // ID'yi kontrol et
            $.ajax({
                type: 'GET',
                url: '/restaurant/orders/customerpos/' + customerId,
                success: function(data) {
                    $('#musteriAta').modal('hide');
                    $('.customer').html(data.customer);
                    $('#customer_id').val(customerId); // Doğru şekilde ID'yi saklıyoruz
                },
                error: function() {
                    console.log("Customer selection error");
                }
            });
        }

        function CreateOrder() {
            console.log("Kaydet butonuna basıldı!");

            var payment_control = $('#payment_control').val();
            var customer_id = $('#customer_id').val(); // Eğer modalda müşteri eklendiyse, burayı kontrol et
            var courier_id = $('#courier_id').val();
            let products = [];
            let total = 0;

            // Ürünleri toplama ve toplam tutarı hesaplama işlemi
            $('.item').each(function() {
                let product_id = $(this).find('input[name="product_id[]"]').val();
                let quantity = $(this).find('input[name="quantity[]"]').val();
                let price = $(this).find('input[name="price[]"]').val();
                console.log("Fiyat: ", price);
                if (product_id && quantity && price) {
                    products.push({
                        product_id: product_id,
                        quantity: quantity
                    });
                    total += price * parseInt(quantity);
                }
            });

            $('#totalPrice').val(total.toFixed(2));

            // Yeni eklenen müşteri ID'sini kontrol et
            if (payment_control != 0 && customer_id > 0 && products.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: '/restaurant/orders/addOrder?_token=' + '{{ csrf_token() }}',
                    data: {
                        customer_id: customer_id,
                        payment_method: payment_control,
                        courier_id: courier_id,
                        products: products,
                        amount: total
                    },
                    success: function(response) {
                        console.log("Sipariş başarıyla gönderildi:", response);
                        if (response.status === "OK") {
                            var divToPrint = response.printed;
                            var mywindow = window.open('', 'PRINT', 'height=600,width=800');
                            mywindow.document.write('<html><head><title>' + document.title + '</title>');
                            mywindow.document.write('</head><body >');
                            mywindow.document.write(divToPrint);
                            mywindow.document.write('</body></html>');
                            mywindow.document.close();
                            mywindow.focus();
                            mywindow.print();

                            setTimeout(function() {
                                mywindow.close();
                                window.location.reload();
                            }, 1000);
                        } else {
                            Swal.fire(response.message);
                        }
                    },
                    error: function(response) {
                        console.log("Sipariş gönderme hatası:", response);
                        Swal.fire('Bir hata oluştu, lütfen tekrar deneyin.');
                    }
                });
            } else {
                Swal.fire('Müşteri, ödeme yöntemi veya ürün bilgilerini kontrol edin!');
            }
        }


        // Sayfa yeniden yüklendiğinde URL'deki parametreye göre yeniMusteri modalını açar
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const showModal = urlParams.get('showModal');
            if (showModal === 'Orders') {
                $('#Orders').modal('show');
            }
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#example3').DataTable();
            //DataTable custom search field
            $('#custom-filter').keyup(function() {
                table.search(this.value).draw();
            });
        });
    </script>
    <script>
        const selectBox = document.getElementById("selectedCourier");
        const optionsContainer = document.getElementById("courierOptions");
        const options = document.querySelectorAll(".option");
        const courierIdInput = document.getElementById("courierId");

        selectBox.addEventListener("click", () => {
            optionsContainer.style.display = optionsContainer.style.display === "block" ? "none" : "block";
        });

        options.forEach(option => {
            option.addEventListener("click", (e) => {
                const selectedText = option.innerText;
                const selectedId = option.getAttribute("data-id");

                selectBox.innerHTML = selectedText;
                courierIdInput.value = selectedId;
                optionsContainer.style.display = "none";
            });
        });

        document.addEventListener("click", function(event) {
            if (!selectBox.contains(event.target) && !optionsContainer.contains(event.target)) {
                optionsContainer.style.display = "none";
            }
        });
    </script>
    <!--
<script>
function fetchData() {
            $.ajax({
                url: 'https://panel.parskurye.net/restaurant/getAdisyoOrders', // Replace with the actual API URL
                method: 'GET',
                success: function(textStatus, jqXHR) {

                    if (jqXHR.status === 200) {
                        $.ajax({
                            url: '{{ route("getOrders") }}', // Define this route in web.php
                            method: 'GET',

                            success: function(response) {

                                console.log('Backend operation successful:', response);
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('Backend operation failed:', textStatus, errorThrown);
                            }
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Failed to fetch data:', textStatus, errorThrown);
                }
            });
        }

        // Run the fetchData function every 2 seconds
        setInterval(fetchData, 2000);
</script>
-->
@endsection
