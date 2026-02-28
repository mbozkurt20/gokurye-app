@extends('superadmin.layouts.app')
@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="fw-black text-dark text-uppercase mb-0" style="letter-spacing:-.03em;">Süper Admin Paneli</h4>
            <p class="text-muted mb-0" style="font-size:0.75rem;">{{ now()->format('d.m.Y') }} — Sistem Geneli Özet</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        @php
        $kpis = [
            ['label' => 'Toplam Admin',     'value' => $totalAdmins ?? 0,      'sub' => ($activeAdmins ?? 0).' Aktif',   'icon' => 'fa-user-shield', 'color' => '#4f46e5'],
            ['label' => 'Toplam Restoran',  'value' => $totalRestaurants ?? 0,  'sub' => null,                             'icon' => 'fa-store',       'color' => '#0891b2'],
            ['label' => 'Toplam Kurye',     'value' => $totalCouriers ?? 0,     'sub' => ($idleCouriers ?? 0).' Müsait',  'icon' => 'fa-motorcycle',  'color' => '#059669'],
            ['label' => 'Bugün Sipariş',    'value' => count($tumu),            'sub' => $teslimEdilenSiparisler.' Teslim','icon' => 'fa-receipt',    'color' => '#d97706'],
            ['label' => 'Bugün Ciro',       'value' => $formattedExpense.' ₺',  'sub' => 'Ort: '.$formattedAverageExpense.' ₺', 'icon' => 'fa-coins','color' => '#dc2626'],
            ['label' => 'Toplam Bayi',      'value' => $totalDealers ?? 0,      'sub' => null,                             'icon' => 'fa-handshake',   'color' => '#7c3aed'],
        ];
        @endphp

        @foreach($kpis as $kpi)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid {{ $kpi['color'] }} !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="mb-0 text-muted fw-bold" style="font-size:0.62rem;text-transform:uppercase;letter-spacing:.06em;">{{ $kpi['label'] }}</p>
                        <i class="fas {{ $kpi['icon'] }}" style="color:{{ $kpi['color'] }};font-size:0.9rem;"></i>
                    </div>
                    <p class="mb-0 fw-bold" style="font-size:1.5rem;">{{ $kpi['value'] }}</p>
                    @if($kpi['sub'])
                        <p class="mb-0 text-muted" style="font-size:0.68rem;">{{ $kpi['sub'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Platform & Courier Row --}}
    <div class="row g-3 mb-4">

        {{-- Platform Breakdown --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Platform Dağılımı — Bugün</h6>
                    <div class="row g-3">
                        @php
                        $platforms = [
                            ['title' => 'Telefon',   'count' => count($telefonsiparis), 'icon' => 'fa-phone',  'color' => '#198754', 'is_img' => false],
                            ['title' => 'Getir',     'count' => count($getiryemek),     'img' => 'getir.png',       'is_img' => true],
                            ['title' => 'Trendyol',  'count' => count($trendyol),       'img' => 'trendyol.png',    'is_img' => true],
                            ['title' => 'Y.Sepeti',  'count' => count($yemeksepeti),    'img' => 'yemeksepeti.png', 'is_img' => true],
                            ['title' => 'Migros',    'count' => $migros,                'img' => 'migros.png',      'is_img' => true],
                        ];
                        @endphp

                        @foreach($platforms as $p)
                        <div class="col-4 col-md-2">
                            <div class="text-center p-2 rounded-3 bg-light">
                                <div class="mb-2 d-flex align-items-center justify-content-center bg-white rounded-circle mx-auto shadow-sm" style="width:42px;height:42px;">
                                    @if($p['is_img'])
                                        <img src="{{ asset('theme/images/platforms/'.$p['img']) }}" style="width:24px;height:auto;" alt="{{ $p['title'] }}">
                                    @else
                                        <i class="fas {{ $p['icon'] }}" style="color:{{ $p['color'] }};font-size:1rem;"></i>
                                    @endif
                                </div>
                                <div class="fw-bold fs-5">{{ $p['count'] }}</div>
                                <div class="text-muted fw-semibold" style="font-size:0.6rem;text-transform:uppercase;letter-spacing:.05em;">{{ $p['title'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Courier Status --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Kurye Durumları</h6>
                    <div class="row g-2">
                        @php
                        $courierStats = [
                            ['label' => 'Toplam',   'value' => $totalCouriers ?? 0,   'color' => '#4f46e5'],
                            ['label' => 'Müsait',   'value' => $idleCouriers ?? 0,    'color' => '#059669'],
                            ['label' => 'Serviste', 'value' => $serviceCouriers ?? 0, 'color' => '#d97706'],
                            ['label' => 'Molada',   'value' => $breakCouriers ?? 0,   'color' => '#dc2626'],
                        ];
                        @endphp
                        @foreach($courierStats as $cs)
                        <div class="col-6">
                            <div class="text-center p-3 rounded-3" style="background:{{ $cs['color'] }}15;border-left:3px solid {{ $cs['color'] }};">
                                <div class="fw-bold" style="font-size:1.8rem;color:{{ $cs['color'] }};">{{ $cs['value'] }}</div>
                                <div class="text-muted fw-semibold" style="font-size:0.65rem;text-transform:uppercase;">{{ $cs['label'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Admins Table --}}
    @isset($admins)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-dark mb-0">Adminler</h6>
                <span class="badge rounded-pill" style="background:#4f46e5;">{{ count($admins) }} Admin</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="adminsTable" style="font-size:0.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Admin</th>
                            <th>Kod</th>
                            <th class="text-center">Restoran</th>
                            <th class="text-center">Kurye<small class="text-muted fw-normal"> (müsait/toplam)</small></th>
                            <th class="text-center">Bugün Sipariş</th>
                            <th class="text-center">Bugün Ciro</th>
                            <th class="text-center">Bakiye</th>
                            <th class="text-center">Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $admin->name }}</div>
                                <div class="text-muted" style="font-size:0.7rem;">{{ $admin->email }}</div>
                            </td>
                            <td><code style="font-size:0.72rem;background:#f1f5f9;padding:2px 6px;border-radius:4px;">{{ $admin->code ?? '-' }}</code></td>
                            <td class="text-center">
                                <span class="badge" style="background:#0891b215;color:#0891b2;font-weight:700;">{{ $admin->restaurants_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold" style="color:#059669;">{{ $admin->active_couriers }}</span>
                                <span class="text-muted">/{{ $admin->couriers_count }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $admin->today_orders }}</td>
                            <td class="text-center fw-bold" style="color:#059669;">{{ number_format($admin->today_revenue, 2, ',', '.') }} ₺</td>
                            <td class="text-center fw-bold" style="color:#4f46e5;">{{ number_format($admin->top_up_balance ?? 0, 0, ',', '.') }} ₺</td>
                            <td class="text-center">
                                @if($admin->is_active)
                                    <span class="badge" style="background:#05966915;color:#059669;font-size:0.7rem;">Aktif</span>
                                @else
                                    <span class="badge" style="background:#dc262615;color:#dc2626;font-size:0.7rem;">Pasif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Admin bulunamadı</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endisset

    {{-- Dealers Summary --}}
    @isset($dealers)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-dark mb-0">Bayiler</h6>
                <a href="{{ route('superadmin.dealer') }}" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:0.75rem;">Yönet →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dealersTable" style="font-size:0.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Bayi</th>
                            <th class="text-center">Admin Sayısı</th>
                            <th class="text-center">Komisyon Oranı</th>
                            <th class="text-center">Toplam Kazanç</th>
                            <th class="text-center">Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dealers as $dealer)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $dealer->name }}</div>
                                <div class="text-muted" style="font-size:0.7rem;">{{ $dealer->email }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge" style="background:#7c3aed15;color:#7c3aed;font-weight:700;">{{ $dealer->admins_count }}</span>
                            </td>
                            <td class="text-center fw-bold">%{{ $dealer->commission_rate ?? 20 }}</td>
                            <td class="text-center fw-bold" style="color:#059669;">{{ number_format($dealer->commission_balance ?? 0, 2, ',', '.') }} ₺</td>
                            <td class="text-center">
                                @if($dealer->is_active)
                                    <span class="badge" style="background:#05966915;color:#059669;font-size:0.7rem;">Aktif</span>
                                @else
                                    <span class="badge" style="background:#dc262615;color:#dc2626;font-size:0.7rem;">Pasif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Bayi bulunamadı</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endisset

</div>

@endsection
