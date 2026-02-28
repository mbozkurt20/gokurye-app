@extends('admin.layouts.app')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

    <style>
        :root { --ultra-indigo: #4f46e5; --glass-bg: rgba(255,255,255,0.7); --dark-surface: #0f172a; }
        body { background: radial-gradient(circle at top right, #f8fafc, #eff6ff); font-family: 'Inter', sans-serif; }
        .modal-content.neo-surface { border-radius: 40px !important; }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(0.3) sepia(1) saturate(5) hue-rotate(220deg); cursor: pointer; }
        .fade-in-up { animation: fadeInUp 0.6s ease-out; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .neo-surface { background: var(--glass-bg); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.4); border-radius: 32px; box-shadow: 0 4px 30px rgba(0,0,0,0.03); }
        .counter-display { font-family: 'JetBrains Mono', monospace; font-weight: 900; letter-spacing: -4px; background: linear-gradient(180deg, var(--dark-surface) 0%, var(--ultra-indigo) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .nav-pill-group { background: rgba(15,23,42,0.05); padding: 6px; border-radius: 20px; display: inline-flex; }
        .nav-link-custom { padding: 8px 15px; border-radius: 15px; font-size: 11px; font-weight: 700; color: #64748b; transition: 0.3s; text-decoration: none !important; }
        .nav-link-custom.active { background: var(--dark-surface); color: white; }
        .toggle-btn { width:100%; border:0; background:transparent; display:flex; justify-content:space-between; align-items:center; padding:16px 20px; cursor:pointer; }
        .toggle-chevron { font-size:12px; transition:transform .3s; color:#94a3b8; }
    </style>

    <div class="container-fluid py-5 px-4">

        <div class="row mb-5 fade-in-up">
            <div class="col-lg-6">
                <span class="badge text-white px-3 py-2 rounded-pill mb-2" style="background:#4f46e5;">Canlı Operasyon</span>
                <h1 class="fw-black text-dark tracking-tighter display-5 mb-0">Genel Bakış</h1>
            </div>
            <div class="col-lg-7 d-flex align-items-center justify-content-end gap-2 flex-wrap">
                <div class="nav-pill-group">
                    <a href="{{ route('admin.filter', ['date' => 'today']) }}"      class="nav-link-custom {{ request()->date == 'today'      ? 'active' : '' }}">BUGÜN</a>
                    <a href="{{ route('admin.filter', ['date' => 'yesterday']) }}"  class="nav-link-custom {{ request()->date == 'yesterday'  ? 'active' : '' }}">DÜN</a>
                    <a href="{{ route('admin.filter', ['date' => 'this_week']) }}"  class="nav-link-custom {{ request()->date == 'this_week'  ? 'active' : '' }}">BU HAFTA</a>
                    <a href="{{ route('admin.filter', ['date' => 'last_month']) }}" class="nav-link-custom {{ request()->date == 'last_month' ? 'active' : '' }}">GEÇEN AY</a>
                </div>
                <button class="btn neo-surface p-3 border-0 shadow-sm" onclick="$('#dateModal').modal('show')">
                    <i class="fas fa-calendar-alt text-indigo"></i>
                </button>
            </div>
        </div>

        <div class="row g-4">

            {{-- Toplam Sipariş --}}
            <div class="col-xl-4 fade-in-up" style="animation-delay:.1s">
                @php
                    $adminTopPlatform = collect([
                        ['title' => 'Telefon',  'count' => count($telefonsiparis)],
                        ['title' => 'Getir',    'count' => count($getiryemek)],
                        ['title' => 'Trendyol', 'count' => count($trendyol)],
                        ['title' => 'Y.Sepeti', 'count' => count($yemeksepeti)],
                        ['title' => 'Migros',   'count' => $migros],
                    ])->sortByDesc('count')->first();
                    $teslimEdilenAdmin = $tumu->where('status','DELIVERED')->count();
                    $bekleyenAdmin     = $tumu->whereIn('status',['PENDING','ASSIGNED','PREPARED','HANDOVER'])->count();
                @endphp
                <div class="neo-surface p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-black text-muted text-uppercase mb-1" style="font-size:10px; letter-spacing:.08em;">Toplam Sipariş</h6>
                        <div class="counter-display mb-1" style="font-size:72px; line-height:1; letter-spacing:-4px;">{{ count($tumu) }}</div>
                        <p class="text-muted fw-bold mb-0" style="font-size:11px;">{{ $teslimEdilenAdmin }} teslim · {{ $bekleyenAdmin }} aktif</p>
                    </div>
                    <div class="mt-4 p-3 rounded-4 bg-white shadow-sm border border-light">
                        <div class="row g-0 text-center">
                            <div class="col-4 border-end">
                                <small class="d-block text-muted fw-bold mb-1" style="font-size:9px; text-transform:uppercase;">CİRO</small>
                                <span class="fw-black text-indigo" style="font-size:12px;">{{ $formattedExpense }}₺</span>
                            </div>
                            <div class="col-4 border-end">
                                <small class="d-block text-muted fw-bold mb-1" style="font-size:9px; text-transform:uppercase;">ORT.</small>
                                <span class="fw-black text-success" style="font-size:12px;">{{ $formattedAverageExpense }}₺</span>
                            </div>
                            <div class="col-4">
                                <small class="d-block text-muted fw-bold mb-1" style="font-size:9px; text-transform:uppercase;">EN ÇOK</small>
                                <span class="fw-black text-dark" style="font-size:12px;">{{ $adminTopPlatform['title'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Platformlar --}}
            <div class="col-xl-4 fade-in-up" style="animation-delay:.2s">
                <div class="neo-surface p-4 h-100">
                    <h6 class="fw-black mb-3 text-muted text-uppercase" style="font-size:10px; letter-spacing:.08em;">Platformlar</h6>
                    @php
                        $adminPlatforms = [
                            ['title' => 'Telefon Sipariş', 'count' => count($telefonsiparis), 'icon' => 'fa-phone',  'bg' => '#6366f115', 'color' => '#6366f1'],
                            ['title' => 'Getir',           'count' => count($getiryemek),    'img' => 'getir.png',        'bg' => '#ff690015', 'color' => '#ff6900'],
                            ['title' => 'Trendyol',        'count' => count($trendyol),      'img' => 'trendyol.png',     'bg' => '#f2711515', 'color' => '#f27115'],
                            ['title' => 'Yemeksepeti',     'count' => count($yemeksepeti),   'img' => 'yemeksepeti.png',  'bg' => '#fa000015', 'color' => '#fa0000'],
                            ['title' => 'Migros',          'count' => $migros,               'img' => 'migros.png',       'bg' => '#ef444415', 'color' => '#ef4444'],
                        ];
                        $adminPlatformTotal = array_sum(array_column($adminPlatforms, 'count'));
                    @endphp
                    <div class="d-flex flex-column gap-2">
                        @foreach($adminPlatforms as $p)
                        @php $pPct = $adminPlatformTotal > 0 ? round(($p['count'] / $adminPlatformTotal) * 100) : 0; @endphp
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:{{ $p['bg'] }};">
                            <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width:36px;height:36px;background:white;box-shadow:0 2px 8px rgba(0,0,0,.06);">
                                @if(isset($p['img']))
                                    <img src="{{ asset('theme/images/platforms/'.$p['img']) }}" style="width:20px;height:20px;object-fit:contain;">
                                @else
                                    <i class="fa-solid {{ $p['icon'] }}" style="color:{{ $p['color'] }};font-size:14px;"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark" style="font-size:12px;">{{ $p['title'] }}</span>
                                    <span class="fw-black" style="font-size:18px;color:{{ $p['color'] }};letter-spacing:-1px;line-height:1;">{{ $p['count'] }}</span>
                                </div>
                                <div style="height:4px;background:#e2e8f0;border-radius:100px;overflow:hidden;">
                                    <div style="width:{{ $pPct }}%;height:100%;background:{{ $p['color'] }};border-radius:100px;opacity:.6;transition:.6s ease;"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kurye Operasyonu --}}
            <div class="col-xl-4 fade-in-up" style="animation-delay:.3s">
                <div class="neo-surface p-4 h-100 d-flex flex-column">
                    <h6 class="fw-black text-muted text-uppercase mb-3" style="font-size:10px; letter-spacing:.08em;">Kurye Operasyonu</h6>

                    @php
                        $courierItems = [
                            ['label' => 'Toplam',   'val' => $totalCouriers,  'color' => '#475569', 'bg' => '#47556910', 'icon' => 'fa-users'],
                            ['label' => 'Müsait',   'val' => $idleCouriers,   'color' => '#10b981', 'bg' => '#10b98110', 'icon' => 'fa-circle-check'],
                            ['label' => 'Serviste', 'val' => $serviceCouriers,'color' => '#6366f1', 'bg' => '#6366f110', 'icon' => 'fa-motorcycle'],
                            ['label' => 'Molada',   'val' => $breakCouriers,  'color' => '#f59e0b', 'bg' => '#f59e0b10', 'icon' => 'fa-mug-hot'],
                        ];
                    @endphp

                    <div class="row g-2 mb-3">
                        @foreach($courierItems as $ci)
                        <div class="col-6">
                            <div class="p-3 rounded-4 d-flex align-items-center gap-3" style="background:{{ $ci['bg'] }}; border:1.5px solid {{ $ci['color'] }}18;">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px;height:32px;background:{{ $ci['color'] }}20;">
                                    <i class="fas {{ $ci['icon'] }}" style="color:{{ $ci['color'] }};font-size:13px;"></i>
                                </div>
                                <div>
                                    <small class="d-block fw-bold" style="font-size:9px;color:#94a3b8;text-transform:uppercase;">{{ $ci['label'] }}</small>
                                    <span class="fw-black" style="font-size:22px;color:{{ $ci['color'] }};letter-spacing:-1px;line-height:1.1;">{{ $ci['val'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-auto p-3 rounded-4" style="background:#0f172a;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="d-block fw-bold" style="font-size:9px;color:#475569;text-transform:uppercase;">Aktiflik Oranı</small>
                                <span class="fw-black text-white" style="font-size:22px;letter-spacing:-1px;">
                                    {{ $totalCouriers > 0 ? round(($serviceCouriers / $totalCouriers) * 100) : 0 }}%
                                </span>
                            </div>
                            <i class="fa-solid fa-map-location-dot" style="color:#6366f1;font-size:28px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Toggle 1: Haftalık Sipariş Trendi + Kurye Bugün Durumu --}}
            <div class="col-12 fade-in-up" style="animation-delay:.4s">
                <div class="neo-surface overflow-hidden">
                    <button type="button" id="weeklyToggleBtn" class="toggle-btn">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="background:#4f46e515;width:36px;height:36px;">
                                <i class="fas fa-chart-line" style="color:#4f46e5;font-size:14px;"></i>
                            </div>
                            <div class="text-start">
                                <span class="fw-black text-dark" style="font-size:13px;text-transform:uppercase;letter-spacing:.04em;">Haftalık Trend & Kurye Durumu</span>
                                <small class="d-block text-muted fw-bold" style="font-size:10px;text-transform:uppercase;">Son 7 gün sipariş · Bugün kurye aktivitesi</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down toggle-chevron" id="weeklyChevron"></i>
                    </button>
                    <div id="weeklyContent" style="display:none;">
                        <div class="px-4 pb-4 pt-2">
                            <div class="row g-4">
                                <div class="col-xl-6">
                                    <h6 class="fw-black text-muted text-uppercase mb-3" style="font-size:10px; letter-spacing:.08em;">Haftalık Sipariş Trendi</h6>
                                    <canvas id="weeklyTrendChart" height="140"></canvas>
                                </div>
                                <div class="col-xl-6">
                                    <h6 class="fw-black text-muted text-uppercase mb-3" style="font-size:10px; letter-spacing:.08em;">
                                        Kurye Bugün Durumu
                                        <a href="{{ route('admin.courier.performance') }}" class="text-indigo ms-2" style="font-size:10px;text-decoration:none;">Detay →</a>
                                    </h6>
                                    @if($courierStatusToday->isEmpty())
                                        <p class="text-muted fw-bold text-center py-4" style="font-size:12px;">Bugün kurye hareketi kaydı yok.</p>
                                    @else
                                        <canvas id="courierTodayChart" height="140"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Toggle 2: Restoran Performansı --}}
            <div class="col-12 fade-in-up" style="animation-delay:.45s">
                <div class="neo-surface overflow-hidden">
                    <button type="button" id="restPerfToggleBtn" class="toggle-btn">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="background:#10b98115;width:36px;height:36px;">
                                <i class="fas fa-store" style="color:#10b981;font-size:14px;"></i>
                            </div>
                            <div class="text-start">
                                <span class="fw-black text-dark" style="font-size:13px;text-transform:uppercase;letter-spacing:.04em;">Restoran Performansı</span>
                                <small class="d-block text-muted fw-bold" style="font-size:10px;text-transform:uppercase;">Son 30 gün — sipariş & hız analizi</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down toggle-chevron" id="restPerfChevron"></i>
                    </button>
                    <div id="restPerfContent" style="display:none;">
                        <div class="px-4 pb-4 pt-0">
                            @if($restaurantStats->isEmpty())
                                <p class="text-muted fw-bold text-center py-4" style="font-size:12px;">Henüz sipariş verisi yok.</p>
                            @else
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="font-size:10px;font-weight:900;color:#94a3b8;text-transform:uppercase;">Restoran</th>
                                            <th style="font-size:10px;font-weight:900;color:#94a3b8;text-transform:uppercase;">Sipariş</th>
                                            <th style="font-size:10px;font-weight:900;color:#94a3b8;text-transform:uppercase;">Ort. Hazırlık</th>
                                            <th style="font-size:10px;font-weight:900;color:#94a3b8;text-transform:uppercase;">Ort. Teslimat</th>
                                            <th style="font-size:10px;font-weight:900;color:#94a3b8;text-transform:uppercase;">Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($restaurantStats as $rs)
                                        @php
                                            $prepMin = $rs->avg_prepared_min ?? 0;
                                            $delMin  = $rs->avg_delivery_min ?? 0;
                                            $score   = max(0, 100 - round((($prepMin + $delMin) / 2) * 1.5));
                                            $scoreColor = $score >= 70 ? 'text-success' : ($score >= 40 ? 'text-warning' : 'text-danger');
                                        @endphp
                                        <tr class="border-bottom border-light">
                                            <td class="py-3 fw-bold" style="font-size:13px;">{{ $rs->restaurant_name }}</td>
                                            <td><span class="badge fw-black px-3 py-2 rounded-pill" style="background:#4f46e515;color:#4f46e5;">{{ $rs->total_orders }}</span></td>
                                            <td><span class="fw-black text-dark" style="font-size:13px;">{{ $prepMin ?: '—' }}{{ $prepMin ? ' dk' : '' }}</span></td>
                                            <td><span class="fw-black text-dark" style="font-size:13px;">{{ $delMin  ?: '—' }}{{ $delMin  ? ' dk' : '' }}</span></td>
                                            <td><span class="fw-black {{ $scoreColor }}" style="font-size:15px;">{{ $score }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- SİPARİŞ AKIŞI — Odak Bölüm --}}
            <div class="col-12 fade-in-up" style="animation-delay:.5s">
                <div style="background:#0f172a;border-radius:32px;overflow:hidden;box-shadow:0 20px 60px rgba(15,23,42,0.15);">
                    <div class="d-flex justify-content-between align-items-center px-5 py-4" style="position:relative;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="background:#4f46e5;width:40px;height:40px;">
                                <i class="fas fa-stream text-white" style="font-size:16px;"></i>
                            </div>
                            <div>
                                <h4 class="fw-black m-0 text-white tracking-tighter" style="letter-spacing:-.5px;">Sipariş Akışı</h4>
                                <small class="fw-bold" style="color:#64748b;font-size:10px;text-transform:uppercase;letter-spacing:.05em;">Canlı durumlar</small>
                            </div>
                        </div>

                        @if($pakettGelinceBildir)
                        <div id="siparisAkisiBadge" style="display:none;position:absolute;left:50%;transform:translateX(-50%);align-items:center;gap:8px;background:#ef4444;border-radius:12px;padding:8px 16px;" class="order-alert-blink">
                            <span style="width:7px;height:7px;border-radius:50%;background:white;flex-shrink:0;display:block;"></span>
                            <span style="font-size:11px;font-weight:900;color:white;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">Yeni Sipariş</span>
                            <span id="siparisAkisiCount" style="font-size:10px;font-weight:900;background:white;color:#ef4444;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;line-height:1;">0</span>
                        </div>
                        @endif

                        <div class="d-flex align-items-center gap-3">
                            <span class="fw-black text-white" style="font-size:28px;font-family:'JetBrains Mono',monospace;letter-spacing:-2px;">{{ count($tumu) }}</span>
                            <a href="{{ route('admin.deliveredOrders') }}" class="fw-bold text-decoration-none px-3 py-2 rounded-3" style="background:rgba(255,255,255,.08);color:#94a3b8;font-size:11px;">
                                Tümü <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div style="background:white;border-radius:24px;margin:0 8px 8px;padding:8px;">
                        @include('admin.partials.home_table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="false" id="dateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content neo-surface border-0 shadow-lg" style="background:rgba(255,255,255,.95);backdrop-filter:blur(20px);">
                <div class="modal-header border-0 p-4 pb-0">
                    <h4 class="fw-black text-dark tracking-tighter m-0 uppercase">Filtrele</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="GET" action="{{ route('admin.filterByDate') }}">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="small fw-bold text-muted mb-2 uppercase tracking-widest" style="font-size:10px;">Başlangıç</label>
                                <input type="date" class="form-control border-0 bg-light p-3 rounded-4 fw-bold shadow-sm" name="start_date" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted mb-2 uppercase tracking-widest" style="font-size:10px;">Bitiş</label>
                                <input type="date" class="form-control border-0 bg-light p-3 rounded-4 fw-bold shadow-sm" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn w-100 p-3 rounded-4 fw-black text-white shadow-lg" style="background:var(--dark-surface);">
                            UYGULA <i class="fas fa-filter ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Haftalık Trend + Kurye Bugün — lazy init
        let weeklyChart = null, courierTodayChart = null;

        function makeToggle(btnId, contentId, chevronId, onOpen) {
            $('#' + btnId).on('click', function () {
                const $c = $('#' + contentId);
                const $ch = $('#' + chevronId);
                const isOpen = $c.is(':visible');
                $c.slideToggle(280, function () {
                    $ch.css('transform', isOpen ? 'rotate(0deg)' : 'rotate(180deg)');
                    if (!isOpen && onOpen) onOpen();
                });
            });
        }

        makeToggle('weeklyToggleBtn', 'weeklyContent', 'weeklyChevron', function () {
            if (!weeklyChart) {
                weeklyChart = new Chart(document.getElementById('weeklyTrendChart'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($weeklyLabels) !!},
                        datasets: [{
                            label: 'Sipariş',
                            data: {!! json_encode($weeklyOrders) !!},
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79,70,229,0.08)',
                            borderWidth: 3, pointBackgroundColor: '#4f46e5', pointRadius: 5,
                            fill: true, tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true, plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { weight: 'bold' }, stepSize: 1 } },
                            x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 11 } } }
                        }
                    }
                });
            } else { weeklyChart.resize(); }

            @if($courierStatusToday->isNotEmpty())
            if (!courierTodayChart) {
                courierTodayChart = new Chart(document.getElementById('courierTodayChart'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($courierStatusToday->pluck('courier_name')) !!},
                        datasets: [
                            { label: 'Müsait',   data: {!! json_encode($courierStatusToday->pluck('active_min'))  !!}, backgroundColor: '#10b981', borderRadius: 4 },
                            { label: 'Serviste', data: {!! json_encode($courierStatusToday->pluck('service_min')) !!}, backgroundColor: '#6366f1', borderRadius: 4 },
                            { label: 'Molada',   data: {!! json_encode($courierStatusToday->pluck('break_min'))   !!}, backgroundColor: '#f59e0b', borderRadius: 4 },
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { stacked: true, grid: { display: false }, ticks: { font: { weight: 'bold', size: 10 } } },
                            y: { stacked: true, grid: { color: '#f1f5f9' }, ticks: { font: { weight: 'bold' }, callback: v => v + ' dk' } }
                        },
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 14, font: { weight: '900', size: 10 } } } }
                    }
                });
            } else { courierTodayChart.resize(); }
            @endif
        });

        makeToggle('restPerfToggleBtn', 'restPerfContent', 'restPerfChevron', null);

        $('#dateModal').on('shown.bs.modal', function () { $(this).appendTo('body'); });
    </script>

@endsection
