@extends('dealer.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/admin/home/index.css')}}">
    <div class="container-fluid">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" >
                <div class="w-100 d-flex align-items-center justify-content-between">
                    <form method="GET" action="{{ route('admin.filterByDate') }}"
                          class="d-flex  gap-3 align-items-center">
                        <div>
                            <input type="date" class="form-control custom-input" id="start_date" name="start_date"
                                   required>
                        </div>
                        <div>
                            <input type="date" class="form-control custom-input" id="end_date" name="end_date" required>
                        </div>
                        <div class="d-flex align-items-end">
                            <button style="background: #4f46e5;color:#fff;font-size: 0.8rem" type="submit"
                                    class="btn custom-btn">
                                <i class="fas fa-calendar-day" style="padding-right:5px"></i>
                                Filtrele</button>
                        </div>
                    </form>
                    <div class="date-filters d-flex align-items-center gap-1 ">
                        <a style="font-size:0.8rem;font-weight: 300" href="{{ route('admin.filter', ['date' => 'today']) }}"
                           class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Bugün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'yesterday']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Dün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'this_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Bu Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'last_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Geçen Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'last_month']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
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
                <div class="orders-section p-4 border-0 rounded-4 bg-white shadow">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <h4 class="fw-bold text-dark m-0">Sipariş Paneli</h4>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                            Bugün: <strong>{{ count($tumu) }}</strong>
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-secondary-light text-dark fw-bolder shadow-sm transition-hover">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-white bg-opacity-10 p-2 rounded-3 me-3">
                                        <i class="fa-solid fa-layer-group fs-5"></i>
                                    </div>
                                    <span class="fw-semibold ">Genel Toplam</span>
                                </div>
                                <span class="fs-3 fw-bold">{{ count($tumu) }}</span>
                            </div>
                        </div>

                        @php
                            $platforms = [
                                ['title' => 'Telefon', 'count' => count($telefonsiparis), 'icon' => 'fa-phone', 'color' => '#198754', 'is_img' => false],
                                ['title' => 'GpsYemek', 'count' => count($gpsyemek), 'img' => 'gpsyemek.png', 'is_img' => true],
                                ['title' => 'Getir Yemek', 'count' => count($getiryemek), 'img' => 'getir.png', 'is_img' => true],
                                ['title' => 'Trendyol', 'count' => count($trendyol), 'img' => 'trendyol.png', 'is_img' => true],
                                ['title' => 'Y.Sepeti', 'count' => count($yemeksepeti), 'img' => 'yemeksepeti.png', 'is_img' => true],
                                ['title' => 'Migros', 'count' => $migros, 'img' => 'migros.png', 'is_img' => true],
                            ];
                        @endphp

                        @foreach($platforms as $p)
                            <div class="col-4 col-md-4 col-xl-2">
                                <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 rounded-4 platform-card">
                                    <div class="platform-logo-container mb-4 d-flex align-items-center justify-content-center bg-white shadow-sm rounded-circle mx-auto" style="width: 45px; height: 45px;">
                                        @if($p['is_img'])
                                            <img class="rounded-circle" src="{{ asset('theme/images/platforms/'.$p['img']) }}" style="width: 28px; height: auto;" alt="{{ $p['title'] }}">
                                        @else
                                            <i class="fa-solid {{ $p['icon'] }} fs-5" style="color: {{ $p['color'] }}"></i>
                                        @endif
                                    </div>
                                    <div class="fw-bold fs-5 text-dark">{{ $p['count'] }}</div>
                                    <div class="text-muted  text-primary fw-bold" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $p['title'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Performance Section -->
            <div class="col-lg-6">
                <!-- Satış Performansı -->
                <div class="performance-section mb-4">
                    <h4 class="mb-3">Satış Performansı</h4>
                    <div class="row g-3">
                        <!-- Ciro -->
                        <div class="col-4">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Ciro</p>
                                <p class="card-value text-black fw-bold h5 mb-0">{{ $formattedExpense }} ₺</p>
                            </div>
                        </div>
                        <!-- Sipariş Sayısı -->
                        <div class="col-4">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Sipariş Sayısı</p>
                                <p class="card-value text-black fw-bold h5 mb-0">{{ count($tumu) }} Adet</p>
                            </div>
                        </div>
                        <!-- Ortalama Sipariş Tutarı -->
                        <div class="col-4">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Ortalama Sipariş Tutarı</p>
                                <p class="card-value text-black fw-bold h5 mb-0">{{ $formattedAverageExpense }} ₺</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kurye Performansı -->
                <div class="performance-section">
                    <h4 class="mb-3">Kurye Performansı</h4>
                    <div class="row g-3">
                        <!-- Toplam -->
                        <div class="col-3">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Toplam</p>
                                <p class="card-value text-black fw-bold h6 mb-0">{{ $totalCouriers }} Kurye</p>
                            </div>
                        </div>
                        <!-- Müsait -->
                        <div class="col-3">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Müsait</p>
                                <p class="card-value text-black fw-bold h6 mb-0">{{ $idleCouriers }} Kurye</p>
                            </div>
                        </div>
                        <!-- Serviste -->
                        <div class="col-3">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Serviste</p>
                                <p class="card-value text-black fw-bold h6 mb-0">{{ $serviceCouriers }} Kurye</p>
                            </div>
                        </div>
                        <!-- Molada -->
                        <div class="col-3">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Molada</p>
                                <p class="card-value text-black fw-bold h6 mb-0">{{ $breakCouriers }} Kurye</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
