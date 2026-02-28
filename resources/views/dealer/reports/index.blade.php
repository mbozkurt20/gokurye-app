@extends('dealer.layouts.app')
@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="fw-black text-dark text-uppercase mb-0" style="letter-spacing:-.03em;">Raporlar</h4>
            <p class="text-muted mb-0" style="font-size:0.75rem;">Tarih aralığına göre performans özeti</p>
        </div>
    </div>

    {{-- Filtre --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('dealer.reports') }}" class="d-flex align-items-end gap-3 flex-wrap">
                <div>
                    <label class="text-uppercase fw-bold mb-1" style="font-size:0.65rem;letter-spacing:.06em;color:#94a3b8;">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" class="form-control"
                           value="{{ isset($startDate) ? \Carbon\Carbon::parse($startDate)->toDateString() : \Carbon\Carbon::now()->startOfMonth()->toDateString() }}">
                </div>
                <div>
                    <label class="text-uppercase fw-bold mb-1" style="font-size:0.65rem;letter-spacing:.06em;color:#94a3b8;">Bitiş Tarihi</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ isset($endDate) ? \Carbon\Carbon::parse($endDate)->toDateString() : \Carbon\Carbon::now()->toDateString() }}">
                </div>
                <div>
                    <button class="btn text-white fw-bold" type="submit"
                            style="background:#7c3aed;border-radius:1.5rem;padding:.5rem 1.25rem;font-size:.82rem;">
                        <i class="fas fa-filter me-1"></i> Filtrele
                    </button>
                </div>
                <div>
                    <a href="{{ route('dealer.reports.download', request()->query()) }}"
                       class="btn btn-outline-secondary fw-bold" style="border-radius:1.5rem;padding:.5rem 1.25rem;font-size:.82rem;">
                        <i class="fas fa-download me-1"></i> İndir
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        @php
        $kpis = [
            ['label' => 'Toplam Sipariş',    'value' => $totalOrders,                              'icon' => 'fa-receipt',     'color' => '#7c3aed'],
            ['label' => 'Toplam Restoran',   'value' => $totalRestaurants,                         'icon' => 'fa-store',       'color' => '#0891b2'],
            ['label' => 'Toplam Kurye',      'value' => $totalCouriers,                            'icon' => 'fa-motorcycle',  'color' => '#059669'],
            ['label' => 'Toplam Admin',      'value' => $totalAdmins,                              'icon' => 'fa-user-shield', 'color' => '#d97706'],
            ['label' => 'Ciro',              'value' => number_format($totalAmount, 2, ',', '.').' ₺',  'icon' => 'fa-coins',      'color' => '#dc2626'],
            ['label' => 'Net Tutar',         'value' => number_format($totalSubAmount, 2, ',', '.').' ₺', 'icon' => 'fa-money-bill', 'color' => '#4f46e5'],
            ['label' => 'Toplam İndirim',    'value' => number_format($totalDiscount, 2, ',', '.').' ₺', 'icon' => 'fa-tag',        'color' => '#64748b'],
            ['label' => 'Toplam Müşteri',    'value' => $totalCustomers,                           'icon' => 'fa-users',       'color' => '#7c3aed'],
        ];
        @endphp

        @foreach($kpis as $kpi)
        <div class="col-6 col-md-3 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid {{ $kpi['color'] }} !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="mb-0 text-muted fw-bold" style="font-size:0.62rem;text-transform:uppercase;letter-spacing:.06em;">{{ $kpi['label'] }}</p>
                        <i class="fas {{ $kpi['icon'] }}" style="color:{{ $kpi['color'] }};font-size:.9rem;"></i>
                    </div>
                    <p class="mb-0 fw-bold" style="font-size:1.4rem;">{{ $kpi['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Chart --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3">Günlük Sipariş Grafiği</h6>
            <canvas id="ordersChart" height="80"></canvas>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($ordersByDate->toArray())) !!},
            datasets: [{
                label: 'Günlük Sipariş',
                data: {!! json_encode(array_values($ordersByDate->toArray())) !!},
                fill: true,
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124,58,237,0.08)',
                borderWidth: 2.5,
                tension: 0.4,
                pointBackgroundColor: '#7c3aed',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
