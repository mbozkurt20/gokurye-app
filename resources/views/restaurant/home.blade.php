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

        <div class="row g-4">
            @php
                $topPlatform = collect([
                    ['title' => 'Telefon', 'count' => count($telefonsiparis)],
                    ['title' => 'Getir',   'count' => count($getiryemek)],
                    ['title' => 'Trendyol','count' => count($trendyol)],
                    ['title' => 'Y.Sepeti','count' => count($yemeksepeti)],
                    ['title' => 'Migros',  'count' => $migros],
                ])->sortByDesc('count')->first();
                $teslimEdilenCount = $tumu->where('status', 'DELIVERED')->count();
                $bekleyenCount     = $tumu->whereIn('status', ['PENDING','ASSIGNED','PREPARED','HANDOVER'])->count();
                $platforms = [
                    ['title' => 'Telefon',     'count' => count($telefonsiparis), 'icon' => 'fa-phone',    'bg' => '#6366f112', 'color' => '#6366f1'],
                    ['title' => 'Getir',       'count' => count($getiryemek),    'img' => 'getir.png',       'bg' => '#ff690012', 'color' => '#ff6900'],
                    ['title' => 'Trendyol',    'count' => count($trendyol),      'img' => 'trendyol.png',    'bg' => '#f2711512', 'color' => '#f27115'],
                    ['title' => 'Yemeksepeti', 'count' => count($yemeksepeti),   'img' => 'yemeksepeti.png', 'bg' => '#fa000012', 'color' => '#fa0000'],
                    ['title' => 'Migros',      'count' => $migros,               'img' => 'migros.png',      'bg' => '#ef444412', 'color' => '#ef4444'],
                ];
            @endphp

            {{-- Kart 1: Bugünkü Sipariş --}}
            <div class="col-xl-4 mb-6">
                <div class="group h-full rounded-[2rem] border border-slate-100 bg-white p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10">
                    <div class="flex items-start justify-between mb-8">
                        <div>
                            <h6 class="text-[11px] font-extrabold uppercase tracking-[0.1em] text-slate-400 mb-1">Bugünkü Sipariş</h6>
                            <div class="text-6xl font-black tracking-tighter text-slate-900">{{ count($tumu) }}</div>
                        </div>
                        <div class="flex flex-col gap-2 pt-2">
                <span class="inline-flex items-center rounded-lg bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-600 border border-emerald-100">
                    {{ $teslimEdilenCount }} Teslim
                </span>
                            <span class="inline-flex items-center rounded-lg bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-600 border border-amber-100">
                    {{ $bekleyenCount }} Aktif
                </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 divide-x divide-slate-100 rounded-2xl bg-slate-50/50 p-4 border border-slate-100">
                        <div class="text-center px-2">
                            <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Ciro</div>
                            <div class="text-sm font-black text-slate-900">{{ $formattedExpense }}₺</div>
                        </div>
                        <div class="text-center px-2">
                            <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Ort.</div>
                            <div class="text-sm font-black text-indigo-600">{{ $formattedAverageExpense }}₺</div>
                        </div>
                        <div class="text-center px-2">
                            <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-1">Lider</div>
                            <div class="truncate text-sm font-black text-slate-900 px-1">{{ $topPlatform['title'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kart 2: Platformlar --}}
            <div class="col-xl-4 mb-6">
                <div class="h-full rounded-[2rem] border border-slate-100 bg-white p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10">
                    <h6 class="text-[11px] font-extrabold uppercase tracking-[0.1em] text-slate-400 mb-6">Platform Dağılımı</h6>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($platforms as $p)
                            <div class="flex flex-col items-center rounded-2xl border border-transparent p-4 transition-all duration-200 hover:border-slate-100 hover:bg-white hover:shadow-sm" style="background-color: {{ $p['bg'] }}15">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm shadow-black/5 border border-slate-50">
                                    @if(isset($p['img']))
                                        <img src="{{ asset('theme/images/platforms/'.$p['img']) }}" class="h-5 w-5 object-contain">
                                    @else
                                        <i class="fa-solid {{ $p['icon'] }} text-xs" style="color: {{ $p['color'] }}"></i>
                                    @endif
                                </div>
                                <div class="text-2xl font-black tracking-tight text-slate-900">{{ $p['count'] }}</div>
                                <div class="text-[8px] font-bold uppercase tracking-wider text-slate-400 text-center leading-tight mt-1">{{ $p['title'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kart 3: Operasyonel Hız --}}
            <div class="col-xl-4 mb-6">
                <div class="h-full rounded-[2rem] border border-slate-100 bg-white p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10">
                    <h6 class="text-[11px] font-extrabold uppercase tracking-[0.1em] text-slate-400 mb-8">Operasyonel Hız</h6>
                    @php
                        $metrics = [
                            ['l' => 'Mutfak Hazırlık', 'v' => $stats['prepared']['avg'], 'm' => 60, 'c' => 'bg-indigo-500', 't' => 'text-indigo-600'],
                            ['l' => 'Kurye Atama',     'v' => $stats['handover']['avg'],  'm' => 20, 'c' => 'bg-amber-500',  't' => 'text-amber-600'],
                            ['l' => 'Saha Teslimat',   'v' => $stats['delivery']['avg'],  'm' => 45, 'c' => 'bg-emerald-500','t' => 'text-emerald-600'],
                        ];
                    @endphp
                    @foreach($metrics as $m)
                        @php $pct = $m['m'] > 0 ? min(100, round(($m['v'] / $m['m']) * 100)) : 0; @endphp
                        <div class="mb-6 last:mb-0">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-700">{{ $m['l'] }}</span>
                                <span class="text-sm font-black {{ $m['t'] }}">{{ $m['v'] }} <small class="text-[10px] font-bold text-slate-400">dk</small></span>
                            </div>
                            <div class="relative h-2 w-full overflow-visible rounded-full bg-slate-100">
                                <div class="relative h-full rounded-full {{ $m['c'] }} transition-all duration-500" style="width: {{ $pct }}%">
                                    <div class="absolute -right-1 top-1/2 h-3 w-3 -translate-y-1/2 rounded-full border-2 border-white bg-inherit shadow-sm"></div>
                                </div>
                            </div>
                            <div class="mt-2 flex justify-between text-[9px] font-bold text-slate-300 uppercase tracking-tighter">
                                <span>Hemen</span>
                                <span>Hedef: {{ $m['m'] }} dk</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 30 Günlük Performans — Toggle --}}
            <div class="col-12 fade-in-up" style="animation-delay: 0.35s">
                <div class="neo-surface overflow-hidden">
                    {{-- Toggle Header --}}
                    <button type="button" id="perfToggleBtn"
                            class="w-100 border-0 bg-transparent d-flex justify-content-between align-items-center px-4 py-3"
                            style="cursor:pointer;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="background:#4f46e515; width:36px; height:36px;">
                                <i class="fas fa-chart-line" style="color:#4f46e5; font-size:14px;"></i>
                            </div>
                            <div class="text-start">
                                <span class="fw-black text-dark" style="font-size:13px; text-transform:uppercase; letter-spacing:.04em;">30 Günlük Performans</span>
                                <small class="d-block text-muted fw-bold" style="font-size:10px; text-transform:uppercase;">{{ $startDate }} — {{ $endDate }}</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-muted" id="perfChevron" style="font-size:12px; transition:transform .3s;"></i>
                    </button>

                    {{-- İçerik — kapalı başlar --}}
                    <div id="perfContent" style="display:none;">
                        <div class="px-4 pb-4 pt-2">
                            <div class="row g-3 mb-4">
                                @php
                                    $perfCards = [
                                        ['label' => 'Mutfak Hazırlık', 'key' => 'prepared', 'color' => '#4f46e5', 'icon' => 'fa-utensils'],
                                        ['label' => 'Kurye Teslim',    'key' => 'handover', 'color' => '#f59e0b', 'icon' => 'fa-motorcycle'],
                                        ['label' => 'Teslimat Süresi', 'key' => 'delivery', 'color' => '#10b981', 'icon' => 'fa-map-marker-alt'],
                                    ];
                                @endphp
                                @foreach($perfCards as $card)
                                <div class="col-md-4">
                                    <div class="p-3 rounded-4 h-100" style="background:{{ $card['color'] }}10; border:1.5px solid {{ $card['color'] }}22;">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="background:{{ $card['color'] }}20; width:32px; height:32px;">
                                                <i class="fas {{ $card['icon'] }}" style="color:{{ $card['color'] }}; font-size:13px;"></i>
                                            </div>
                                            <span class="fw-black" style="font-size:10px; color:#475569; text-transform:uppercase; letter-spacing:.04em;">{{ $card['label'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-end gap-1 mb-2">
                                            <span class="fw-black" style="color:{{ $card['color'] }}; font-size:32px; line-height:1; letter-spacing:-2px;">{{ $stats[$card['key']]['avg'] }}</span>
                                            <span class="fw-bold text-muted mb-1" style="font-size:11px;">dk ort.</span>
                                        </div>
                                        <div class="d-flex gap-3">
                                            <div>
                                                <small class="d-block text-muted fw-bold" style="font-size:8px; text-transform:uppercase;">En İyi</small>
                                                <span class="fw-black text-success" style="font-size:13px;">{{ $stats[$card['key']]['min'] }} dk</span>
                                            </div>
                                            <div>
                                                <small class="d-block text-muted fw-bold" style="font-size:8px; text-transform:uppercase;">Sipariş</small>
                                                <span class="fw-black text-dark" style="font-size:13px;">{{ $stats[$card['key']]['total_orders'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <canvas id="perfTrendChart" height="70"></canvas>
                        </div>
                    </div>{{-- #perfContent --}}
                </div>
            </div>

            {{-- SİPARİŞ AKIŞI — Odak Bölüm --}}
            <div class="col-12 fade-in-up" style="animation-delay: 0.4s">
                <div style="background: #0f172a; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 60px rgba(15,23,42,0.15);">
                    <div class="d-flex justify-content-between align-items-center px-5 py-4" style="position:relative;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="background: #4f46e5; width:40px; height:40px;">
                                <i class="fas fa-stream text-white" style="font-size:16px;"></i>
                            </div>
                            <div>
                                <h4 class="fw-black m-0 text-white tracking-tighter" style="letter-spacing:-0.5px;">Sipariş Akışı</h4>
                                <small class="fw-bold" style="color:#64748b; font-size:10px; text-transform:uppercase; letter-spacing:.05em;">Canlı durumlar</small>
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
                            <span class="fw-black text-white" style="font-size:28px; font-family:'JetBrains Mono',monospace; letter-spacing:-2px;">{{ count($tumu) }}</span>
                            <a href="#" class="fw-bold text-decoration-none px-3 py-2 rounded-3" style="background:rgba(255,255,255,0.08); color:#94a3b8; font-size:11px;">
                                Tümü <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div style="background: white; border-radius: 24px; margin: 0 8px 8px; padding: 8px;">
                        @include('restaurant.partials.home_table')
                    </div>
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
        // 30 Günlük Performans — jQuery slideToggle ile aç/kapat
        let perfChart = null;
        const perfChartData = {!! json_encode($chartData) !!};

        $('#perfToggleBtn').on('click', function () {
            const $content = $('#perfContent');
            const $chevron = $('#perfChevron');
            const isOpen   = $content.is(':visible');

            $content.slideToggle(280, function () {
                $chevron.css('transform', isOpen ? 'rotate(0deg)' : 'rotate(180deg)');

                // İlk açılışta chart'ı başlat
                if (!isOpen && !perfChart) {
                    perfChart = new Chart(document.getElementById('perfTrendChart'), {
                        type: 'line',
                        data: perfChartData,
                        options: {
                            responsive: true,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16, font: { weight: '900', size: 10 } } },
                                tooltip: { callbacks: { label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' dk' } }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    ticks: { font: { weight: 'bold' }, callback: v => v + ' dk' }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { weight: 'bold', size: 9 },
                                        maxTicksLimit: 10,
                                        callback: function (val) {
                                            const d = this.getLabelForValue(val);
                                            return d ? d.slice(5) : '';
                                        }
                                    }
                                }
                            },
                            elements: { line: { borderWidth: 2, tension: 0.4 }, point: { radius: 3 } }
                        }
                    });
                } else if (!isOpen && perfChart) {
                    perfChart.resize();
                }
            });
        });

        $('#dateModal').on('shown.bs.modal', function () {
            $(this).appendTo('body');
        });
    </script>

@endsection
