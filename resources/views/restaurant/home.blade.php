@extends('restaurant.layouts.app')
@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

    <style>
        :root {
            --ultra-indigo: #4f46e5;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --dark-surface: #0f172a;
        }

        body {
            background: radial-gradient(circle at top right, #f8fafc, #eff6ff);
            font-family: 'Inter', sans-serif;
        }
        .modal-content.neo-surface {
            border-radius: 40px !important;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.3) sepia(1) saturate(5) hue-rotate(220deg); /* Takvim ikonunu indigo yapıyoruz */
            cursor: pointer;
        }
        /* Dash Layout Animasyonu */
        .fade-in-up { animation: fadeInUp 0.6s ease-out; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modern Glass Surfaces */
        .neo-surface {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 32px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        }

        /* Sipariş Sayacı - Ultra Vurgu */
        .counter-display {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 900;
            letter-spacing: -4px;
            background: linear-gradient(180deg, var(--dark-surface) 0%, var(--ultra-indigo) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Platform Chip - Minimalist */
        .platform-chip {
            background: white;
            border-radius: 20px;
            padding: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid transparent;
        }
        .platform-chip:hover {
            transform: scale(1.05) rotate(2deg);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.1);
            border-color: var(--ultra-indigo);
        }

        /* Hız Göstergesi Bar */
        .speed-indicator {
            height: 12px;
            background: #e2e8f0;
            border-radius: 100px;
            position: relative;
            overflow: visible;
        }
        .speed-knob {
            height: 24px;
            width: 24px;
            background: white;
            border: 4px solid var(--ultra-indigo);
            border-radius: 50%;
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 15px rgba(79, 70, 229, 0.4);
        }

        /* Menü Pill */
        .nav-pill-group {
            background: rgba(15, 23, 42, 0.05);
            padding: 6px;
            border-radius: 20px;
            display: inline-flex;
        }
        .nav-link-custom {
            padding: 8px 20px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-500);
            transition: 0.3s;
            text-decoration: none !important;
        }
        .nav-link-custom.active {
            background: var(--dark-surface);
            color: white;
        }
    </style>

    <div class="container-fluid py-5 px-4">
        @include('restaurant.partials.home_script_modals')

        <div class="row mb-5 fade-in-up">
            <div class="col-lg-6">
                <span class="badge bg-indigo-700 rounded-b-full text-white px-3 py-2 rounded-pill mb-2">Canlı Operasyon</span>
                <h1 class="fw-black text-dark tracking-tighter display-5 mb-0">Genel Bakış</h1>
            </div>
            <div class="col-lg-6 text-end d-flex align-items-center justify-content-end gap-3">
                <div class="nav-pill-group">
                    <a href="{{ route('orders.filter', ['date' => 'today']) }}" class="nav-link-custom {{request()->date == 'today' ? 'active' : '' }}">BUGÜN</a>
                    <a href="{{ route('orders.filter', ['date' => 'yesterday']) }}" class="nav-link-custom {{request()->date == 'yesterday' ? 'active' : '' }}">DÜN</a>
                    <a href="{{ route('orders.filter', ['date' => 'this_week']) }}" class="nav-link-custom {{request()->date == 'this_week' ? 'active' : '' }}">HAFTA</a>
                </div>
                <button class="btn neo-surface p-3 border-0" onclick="$('#dateModal').modal('show')">
                    <i class="fas fa-calendar-day text-indigo"></i>
                </button>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-xl-4 fade-in-up" style="animation-delay: 0.1s">
                <div class="neo-surface p-5 h-100 d-flex flex-column justify-content-center text-center">
                    <h6 class="text-uppercase fw-black text-muted tracking-widest mb-4">Toplam Sipariş</h6>
                    <div class="counter-display display-1 mb-2">{{ count($tumu) }}</div>
                    <p class="text-slate-400 fw-medium">Şu ana kadar alınan tüm talepler.</p>

                    <div class="mt-5 p-4 rounded-4 bg-white shadow-sm border border-light">
                        <div class="row">
                            <div class="col-6 border-end">
                                <small class="d-block text-muted fw-bold">EN ÇOK</small>
                                <span class="fw-black fs-5">Getir</span>
                            </div>
                            <div class="col-6">
                                <small class="d-block text-muted fw-bold">HIZ</small>
                                <span class="fw-black fs-5 text-success">Stabil</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 fade-in-up" style="animation-delay: 0.2s">
                <div class="row g-3">
                    @php
                        $platforms = [
                            ['title' => 'Telefon', 'count' => count($telefonsiparis), 'icon' => 'fa-phone', 'color' => '#6366f1'],
                            ['title' => 'GpsYemek', 'count' => count($gpsyemek), 'img' => 'gpsyemek.png'],
                            ['title' => 'Getir', 'count' => count($getiryemek), 'img' => 'getir.png'],
                            ['title' => 'Trendyol', 'count' => count($trendyol), 'img' => 'trendyol.png'],
                            ['title' => 'Y.Sepeti', 'count' => count($yemeksepeti), 'img' => 'yemeksepeti.png'],
                            ['title' => 'Migros', 'count' => $migros, 'img' => 'migros.png'],
                        ];
                    @endphp

                    @foreach($platforms as $p)
                        <div class="col-6">
                            <div class="platform-chip d-flex align-items-center gap-3">
                                <div class="bg-light p-2 rounded-3">
                                    @if(isset($p['img']))
                                        <img src="{{ asset('theme/images/platforms/'.$p['img']) }}" style="width: 24px; height: 24px; object-fit: contain;">
                                    @else
                                        <i class="fa-solid {{ $p['icon'] }} text-indigo"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="m-0 fw-black tracking-tighter">{{ $p['count'] }}</h4>
                                    <small class="text-muted fw-bold" style="font-size: 10px;">{{ $p['title'] }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-xl-4 fade-in-up" style="animation-delay: 0.3s">
                <div class="neo-surface p-4 h-100">
                    <h5 class="fw-black mb-5 tracking-tighter uppercase">Performans Skorları</h5>

                    @php
                        $metrics = [
                            ['l' => 'Mutfak Hazırlık', 'v' => $stats['prepared']['avg'], 'm' => 60, 'c' => '#4f46e5'],
                            ['l' => 'Kurye Atama', 'v' => $stats['handover']['avg'], 'm' => 20, 'c' => '#f59e0b'],
                            ['l' => 'Saha Teslimat', 'v' => $stats['delivery']['avg'], 'm' => 45, 'c' => '#10b981'],
                        ];
                    @endphp

                    @foreach($metrics as $m)
                        <div class="mb-5">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold text-dark small">{{ $m['l'] }}</span>
                                <span class="fw-black text-indigo">{{ $m['v'] }} DK</span>
                            </div>
                            <div class="speed-indicator">
                                <div class="speed-knob" style="left: {{ ($m['v'] / $m['m']) * 100 }}%;"></div>
                                <div style="width: {{ ($m['v'] / $m['m']) * 100 }}%; height: 100%; background: {{ $m['c'] }}; border-radius: 100px; opacity: 0.3;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12 fade-in-up" style="animation-delay: 0.4s">
                <div class="neo-surface p-4 overflow-hidden shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                        <h4 class="fw-black m-0 tracking-tighter">SİPARİŞ AKIŞI</h4>
                        <a href="#" class="text-indigo fw-bold small text-decoration-none">Tümünü Gör <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                    @include('restaurant.partials.home_table')
                </div>
            </div>
        </div>
    </div>

  <div class="modal fade" data-bs-backdrop="false" id="dateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content neo-surface border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
                <div class="modal-header border-0 p-4 pb-0">
                    <h4 class="fw-black text-dark tracking-tighter m-0">ZAMAN ARALIĞI</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('restaurant.filterByDate') }}">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="small fw-bold text-muted mb-2 uppercase tracking-widest" style="font-size: 10px;">Başlangıç</label>
                                <input type="date" class="form-control border-0 bg-light p-3 rounded-4 fw-bold shadow-sm" name="start_date" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted mb-2 uppercase tracking-widest" style="font-size: 10px;">Bitiş</label>
                                <input type="date" class="form-control border-0 bg-light p-3 rounded-4 fw-bold shadow-sm" name="end_date" required>
                            </div>
                        </div>
                        <div class="mt-4 p-3 rounded-4 bg-indigo bg-opacity-10 border border-indigo border-opacity-10 text-center">
                            <p class="small text-indigo fw-bold m-0"><i class="fas fa-info-circle me-2"></i>Seçilen tarihler arasındaki tüm veriler analiz edilecektir.</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn w-100 p-3 rounded-4 fw-black text-white shadow-lg tracking-tighter" style="background: var(--dark-surface); transition: 0.3s;">
                            VERİLERİ GÜNCELLE <i class="fas fa-sync-alt ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('#dateModal').on('shown.bs.modal', function () {
            $(this).appendTo('body');
        });
    </script>
@endsection
