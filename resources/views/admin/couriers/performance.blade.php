@extends('admin.layouts.app')
@section('content')
    <div class="card card-body container-fluid py-4">
        <p class="mb-4 size-1 fw-bold text-black">
            <i class="material-icons align-middle">
                {{request('courier_id') ? App\Models\Courier::find(request('courier_id'))->name : ''}}
            </i>
            <strong> Kurye Performansı</strong>
        </p>

        {{-- 🔍 Filtreleme Formu --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.courier.performance') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Kurye Seç</label>
                            <select name="courier_id" class="form-select">
                                <option value="">Tümü</option>
                                @foreach($couriers as $courier)
                                    <option
                                        value="{{ $courier->id }}" {{ request('courier_id') == $courier->id ? 'selected' : '' }}>
                                        {{ $courier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="date" class="form-control"
                                   value="{{ request('date', now()->toDateString()) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Zaman Aralığı</label>
                            <select name="period" class="form-select">
                                <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Günlük
                                </option>
                                <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Haftalık
                                </option>
                                <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Aylık
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="special-ok-button w-100">
                                <i class="fa fa-filter"></i> Filtrele
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 📋 Sekmeler --}}
        <ul class="nav nav-tabs" id="performanceTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#summary">📌 Özet</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#topCouriers">🏆 En İyiler</a></li>
        </ul>

        <div class="tab-content mt-4">
            {{-- 📌 Özet Tabı --}}
            <div class="tab-pane fade show active" id="summary">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>📅 Seçilen Aralık: <strong>{{ $startDate->format('Y-m-d') }}
                                - {{ $endDate->format('Y-m-d') }}</strong></h5>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-primary">
                                <tr>
                                    <th>Kurye</th>
                                    <th>Durum</th>
                                    <th>Süre (dk)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($statusSummary->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            🔍 Kurye Teslimat Performans Verileri Bulunmuyor...
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($statusSummary as $row)
                                        <tr>
                                            <td>
                                                <a href="#" data-bs-toggle="modal"
                                                   data-bs-target="#courierModal{{ $row->courier_id }}">
                                                    {{ $couriers->firstWhere('id', $row->courier_id)?->name ?? 'Bilinmiyor' }}
                                                </a>
                                            </td>
                                            <td>
                                                @php
                                                    $statuses = ['break' => 'Molada', 'service' => 'Serviste', 'active' => 'Müsait', 'passive' => 'Pasif'];
                                                @endphp
                                                {{ $statuses[$row->status] ?? ucfirst($row->status) }}
                                            </td>
                                            <td>{{ round($row->total_duration / 60, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- 📊 Grafik --}}
                        <canvas id="statusChart" style="max-width: 500px; max-height: 500px; margin: auto;"></canvas>
                    </div>
                </div>
            </div>

            {{-- 🏆 En İyi Courierlar Tabı --}}
            <div class="tab-pane fade" id="topCouriers">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="size-3 text-black">🔥 Günlük En Aktif Kurye:
                            <strong>
                                @if ($topActiveCourier)
                                    {{ $couriers->firstWhere('id', $topActiveCourier->courier_id)?->name ?? 'Bilinmiyor' }}
                                    ({{ round($topActiveCourier->active_duration / 60, 2) }} dk)
                                @else
                                    Yok
                                @endif
                            </strong>
                        </p>
                        <hr>
                        <h6>🧩 Durumlara Göre En Fazla Süre Harcayanlar</h6>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped align-middle text-center">
                                <thead class="table-primary">
                                <tr>
                                    <th>Kurye</th>
                                    <th>Durum</th>
                                    <th>Süre (dk)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($topStatusList as $row)
                                    <tr>
                                        <td>
                                            <a href="#" data-bs-toggle="modal"
                                               data-bs-target="#courierModal{{ $row->courier_id }}">
                                                {{ $couriers->firstWhere('id', $row->courier_id)?->name ?? 'Bilinmiyor' }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $statuses[$row->status] ?? ucfirst($row->status) }}
                                        </td>
                                        <td>{{ round($row->total_duration / 60, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- 🥇 Bar Chart --}}
                        <canvas id="topStatusChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔍 Kurye Modalları --}}
        @foreach ($couriers as $courier)
          <div class="modal fade" data-bs-backdrop="false" data-bs-backdrop="false" id="courierModal{{ $courier->id }}" tabindex="-1"
                 aria-labelledby="courierModalLabel{{ $courier->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $courier->name }} - Detaylar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Telefon:</strong> {{ $courier->phone }}</li>
                                <li class="list-group-item"><strong>Doğum
                                        Tarihi:</strong> {{ $courier->birthday ?? '-' }}</li>
                                <li class="list-group-item"><strong>Fiyat Tipi:</strong> {{ $courier->price_type }}</li>
                                <li class="list-group-item"><strong>Fiyat:</strong> {{ $courier->price }}</li>
                                <li class="list-group-item"><strong>Online
                                        Durum:</strong> {{ $courier->online ? 'Online' : 'Offline' }}</li>
                                <li class="list-group-item"><strong>Durum:</strong> {{ ucfirst($courier->status) }}</li>
                                <li class="list-group-item"><strong>Son Atama:</strong> {{ $courier->last_assigned_at }}
                                </li>
                                <li class="list-group-item"><strong>Konum:</strong> {{ $courier->latitude }}
                                    , {{ $courier->longitude }}</li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($statusSummary->pluck('status')->map(fn($s) => $statuses[$s] ?? ucfirst($s))) !!},
                datasets: [{
                    label: 'Durum Süreleri',
                    data: {!! json_encode($statusSummary->pluck('total_duration')->map(fn($s) => round($s / 60, 2))) !!},
                    backgroundColor: ['#4caf50', '#2196f3', '#ff9800', '#9c27b0'],
                    borderWidth: 1
                }]
            }
        });

        const topStatusChart = new Chart(document.getElementById('topStatusChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($topStatusList->map(fn($row) => $couriers->firstWhere('id', $row->courier_id)?->name . ' - ' . ($statuses[$row->status] ?? ucfirst($row->status)))) !!},
                datasets: [{
                    label: 'Süre (dk)',
                    data: {!! json_encode($topStatusList->pluck('total_duration')->map(fn($s) => round($s / 60, 2))) !!},
                    backgroundColor: '#03a9f4'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {display: false},
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {display: true, text: 'Dakika'}
                    }
                }
            }
        });
    </script>
@endsection
