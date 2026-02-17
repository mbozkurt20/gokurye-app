@extends('admin.layouts.app')
@section('content')
    <style>
        .card-custom { background: #fff; border-radius: 16px; box-shadow: 0 3px 12px rgba(0,0,0,0.05); border: none; margin-bottom: 24px; }
        .bg-primary2 { background: #259a38; color: white; }
        .summary-box { background: #4f46e5; color: white; border-radius: 12px; padding: 20px; text-align: center; font-weight: 600; margin-top: 16px; box-shadow: 0 4px 15px rgba(236,105,30,0.3); }
        .info-alert { background: #e3f2fd; color: #0d47a1; border: none; border-radius: 12px; padding: 15px; margin-bottom: 20px; font-size: 0.95rem; display: flex; align-items: center; }
        .table thead { background: #f8f9fa; color: #333; }
        .badge-payment { font-size: 0.85rem; padding: 5px 10px; border-radius: 8px; }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kurye Raporu</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Kuryeler</a></li>
                    <li class="breadcrumb-item"><a href="#">{{$courier->name}}</a></li>
                </ol>
            </div>
        </div>

        <form method="GET" class="card-custom p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Bitiş Tarihi</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="fa fa-filter me-2"></i>Raporu Güncelle</button>
                </div>
            </div>
        </form>

        <div class="info-alert">
            <i class="fa fa-info-circle fa-lg me-3"></i>
            <div>
                <strong>Hesaplama Detayı:</strong> {{ $summary['info'] }}
            </div>
        </div>

        <div class="row mb-4 text-center">
            <div class="col-md-3">
                <div class="card-custom p-3 border-start border-4 border-success">
                    <h6 class="text-muted mb-1">Toplam Sipariş</h6>
                    <h4 class="mb-0">{{ $summary['order_count'] }} Adet</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-custom p-3 border-start border-4 border-info">
                    <h6 class="text-muted mb-1">Nakit Sipariş</h6>
                    <h4 class="mb-0">{{ $summary['cash_count'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-custom p-3 border-start border-4 border-primary">
                    <h6 class="text-muted mb-1">Kredi Kartı</h6>
                    <h4 class="mb-0">{{ $summary['card_count'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-custom p-3 border-start border-4 border-warning">
                    <h6 class="text-muted mb-1">Yemek Kartı</h6>
                    <h4 class="mb-0">{{ $summary['ticket_count'] }}</h4>
                </div>
            </div>
        </div>

        <div class="card-custom overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th class="ps-4">Sipariş No</th>
                        <th>Platform</th>
                        <th>Müşteri</th>
                        <th>Tutar</th>
                        <th>Ödeme</th>
                        <th>Mesafe</th>
                        <th>Durum</th>
                        <th class="pe-4">Tarih</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-4">#{{$order->tracking_id}}</td>
                            <td><span class="badge bg-light text-dark border">{{$order->platform}}</span></td>
                            <td>{{$order->full_name}}</td>
                            <td class="fw-bold">{{ number_format($order->amount, 2) }} ₺</td>
                            <td>
                                @if(str_contains($order->payment_method, 'Nakit'))
                                    <span class="badge bg-success-subtle text-success badge-payment">Nakit</span>
                                @elseif(str_contains($order->payment_method, 'Kart') || str_contains($order->payment_method, 'Online'))
                                    <span class="badge bg-primary-subtle text-primary badge-payment">Kredi Kartı</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning badge-payment">Yemek Kartı</span>
                                @endif
                            </td>
                            <td>{{ number_format((float)$order->distance, 2) }} km</td>
                            <td>
                                @if($order->status == 'DELIVERED')
                                    <span class="text-success fw-bold"><i class="fa fa-check-circle me-1"></i>Teslim Edildi</span>
                                @else
                                    <span class="text-muted">{{$order->status}}</span>
                                @endif
                            </td>
                            <td class="pe-4 text-muted small">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">Kayıt bulunamadı.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="summary-box">
            <h5 class="mb-1 text-white">Toplam Hakediş Kazancı</h5>
            <h2 class="mb-0 text-white fw-bold">{{ number_format($totalEarnings, 2) }} ₺</h2>
        </div>
    </div>
@endsection
