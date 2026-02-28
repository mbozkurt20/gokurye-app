@extends('superadmin.layouts.app')
@section('content')
<style>
    :root {
        --indigo:      #4f46e5;
        --indigo-dark: #3730a3;
        --indigo-light:#eef2ff;
        --indigo-mid:  #c7d2fe;
        --indigo-text: #3730a3;
    }

    /* Genel kart */
    .card-section {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(79,70,229,.06);
    }

    /* Bölüm başlığı */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 12px 12px 0 0;
        background: var(--indigo-light);
    }
    .section-header h5 {
        margin: 0;
        font-weight: 700;
        color: var(--indigo-text);
        font-size: 15px;
    }

    /* İstatistik kartları */
    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 16px;
        text-align: center;
        box-shadow: 0 1px 4px rgba(79,70,229,.06);
        transition: box-shadow .2s;
    }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(79,70,229,.12); }
    .stat-card .stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6b7280;
        margin-bottom: 6px;
    }
    .stat-card .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--indigo);
        line-height: 1;
    }
    .stat-card .stat-sub {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }
    .stat-card.indigo-fill {
        background: var(--indigo);
        border-color: var(--indigo);
    }
    .stat-card.indigo-fill .stat-label { color: #c7d2fe; }
    .stat-card.indigo-fill .stat-value { color: #fff; }
    .stat-card.indigo-fill .stat-sub   { color: #c7d2fe; }

    /* Tablo */
    .detail-table thead th {
        background: var(--indigo-light);
        color: var(--indigo-text);
        font-size: 12px;
        font-weight: 700;
        border-bottom: 2px solid var(--indigo-mid);
        white-space: nowrap;
        padding: 10px 12px;
    }
    .detail-table tbody td {
        font-size: 13px;
        padding: 9px 12px;
        vertical-align: middle;
        border-color: #f3f4f6;
    }
    .detail-table tbody tr:hover { background: var(--indigo-light); }
    .scrollable-body { max-height: 280px; overflow-y: auto; }

    /* Form kart */
    .form-card .card-header-custom {
        background: var(--indigo);
        color: #fff;
        padding: 14px 20px;
        border-radius: 12px 12px 0 0;
        font-weight: 700;
        font-size: 15px;
    }
    .form-card .card-body-custom {
        padding: 24px 20px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 12px 12px;
    }

    /* Kontör istatistik mini kartları */
    .kontor-stat {
        border-radius: 10px;
        padding: 14px;
        text-align: center;
    }
    .kontor-stat h6 { font-size: 11px; margin-bottom: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
    .kontor-stat h4 { font-size: 22px; font-weight: 800; margin: 0; }
    .kontor-stat.white-box { background: var(--indigo-light); border: 1.5px solid var(--indigo-mid); }
    .kontor-stat.white-box h6 { color: var(--indigo-text); }
    .kontor-stat.white-box h4 { color: var(--indigo); }
    .kontor-stat.indigo-box { background: var(--indigo); }
    .kontor-stat.indigo-box h6, .kontor-stat.indigo-box h4 { color: #fff; }

    /* Filtre formu */
    .filter-form-wrap {
        background: var(--indigo-light);
        border: 1px solid var(--indigo-mid);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 16px;
    }

    /* Tab */
    .nav-tabs .nav-link {
        font-weight: 600;
        font-size: 13px;
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 10px 18px;
        border-radius: 0;
    }
    .nav-tabs .nav-link.active {
        color: var(--indigo);
        border-bottom: 2px solid var(--indigo);
        background: transparent;
    }
    .nav-tabs { border-bottom: 1px solid #e5e7eb; }

    /* Breadcrumb */
    .page-title { font-size: 20px; font-weight: 800; color: #111827; }
    .page-subtitle { font-size: 13px; color: #9ca3af; }

    .badge-indigo { background: var(--indigo); color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 20px; }

    /* Detay Offcanvas */
    .offcanvas-detail { width: 400px !important; }
    .offcanvas-detail .offcanvas-header {
        background: var(--indigo);
        color: #fff;
    }
    .offcanvas-detail .btn-close { filter: invert(1); }
    .courier-info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
    .courier-info-row:last-child { border-bottom: none; }
    .courier-info-label { color: #6b7280; font-weight: 600; }
    .courier-info-val { color: #111827; font-weight: 600; }
    .dist-row { display: flex; align-items: center; justify-content: space-between; padding: 9px 12px; border-radius: 8px; margin-bottom: 6px; background: #f9fafb; border: 1px solid #e5e7eb; font-size: 13px; }
    .dist-row:hover { background: var(--indigo-light); }
    .dist-badge { font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
    .dist-near  { background: #dcfce7; color: #16a34a; }
    .dist-mid   { background: #fef9c3; color: #ca8a04; }
    .dist-far   { background: #fee2e2; color: #dc2626; }
    .dist-unknown { background: #f3f4f6; color: #9ca3af; }
    .btn-detail { font-size: 11px; padding: 3px 10px; border-radius: 20px; border: 1.5px solid var(--indigo); color: var(--indigo); background: transparent; cursor: pointer; font-weight: 600; transition: all .15s; }
    .btn-detail:hover { background: var(--indigo); color: #fff; }
    #courierDetailSpinner { display: none; text-align: center; padding: 40px 0; }
</style>

<div class="container-fluid pb-4">

    {{-- Başlık --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h2 class="page-title mb-0">{{ $admin->name }}</h2>
            <span class="page-subtitle">Yönetici Kontör & İstatistik Paneli</span>
        </div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.admin') }}" class="text-decoration-none" style="color:var(--indigo)">Yöneticiler</a></li>
            <li class="breadcrumb-item active">Kontör Hareketleri</li>
        </ol>
    </div>

    {{-- Flash mesajlar --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== 1. SATIR: Genel İstatistik Kartları ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Restoran</div>
                <div class="stat-value">{{ $adminStats['restaurant_count'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Toplam Kurye</div>
                <div class="stat-value">{{ $adminStats['courier_count'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card indigo-fill">
                <div class="stat-label">Aktif Kurye</div>
                <div class="stat-value">{{ $adminStats['active_courier_count'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Toplam Sipariş</div>
                <div class="stat-value">{{ $adminStats['order_count'] }}</div>
                <div class="stat-sub">Bugün: <strong style="color:var(--indigo)">{{ $adminStats['today_order_count'] }}</strong></div>
            </div>
        </div>
    </div>

    {{-- ===== 2. SATIR: Restoran & Kurye Listeleri ===== --}}
    <div class="row g-3 mb-4">
        {{-- Restoranlar --}}
        <div class="col-lg-6">
            <div class="card-section h-100">
                <div class="section-header">
                    <h5>Restoranlar</h5>
                    <span class="badge-indigo">{{ $adminStats['restaurant_count'] }}</span>
                </div>
                <div class="scrollable-body">
                    <table class="table detail-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Restoran Adı</th>
                                <th>Sorumlu</th>
                                <th>Telefon</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($restaurants as $i => $r)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $r->restaurant_name }}</td>
                                    <td class="text-muted">{{ $r->name }}</td>
                                    <td>{{ $r->phone }}</td>
                                    <td>
                                        @if($r->status)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Restoran bulunamadı</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kuryeler --}}
        <div class="col-lg-6">
            <div class="card-section h-100">
                <div class="section-header">
                    <h5>Kuryeler</h5>
                    <span class="badge-indigo">{{ $adminStats['courier_count'] }}</span>
                </div>
                <div class="scrollable-body">
                    <table class="table detail-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ad Soyad</th>
                                <th>Telefon</th>
                                <th>Araç</th>
                                <th>Durum</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($couriers as $i => $c)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $c->name }}</td>
                                    <td>{{ $c->phone }}</td>
                                    <td>{{ $c->vehicle_type ?? '—' }}</td>
                                    <td>
                                        @if($c->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn-detail" onclick="event.preventDefault(); event.stopPropagation(); openCourierDetail({{ $c->id }})">Detay</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Kurye bulunamadı</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== 3. SATIR: Kontör Yükle + Kontör İstatistikleri & Filtre ===== --}}
    <div class="row g-3 mb-4">

        {{-- Kontör Yükle Formu --}}
        <div class="col-lg-5">
            <div class="form-card">
                <div class="card-header-custom">Kontör Yükle</div>
                <div class="card-body-custom">
                    <form method="POST" action="{{ route('superadmin.admin_topup_create') }}">
                        @csrf
                        <input type="hidden" value="{{ $admin->id }}" name="admin_id">
                        <div class="mb-3">
                            <x-money-input name="top_up_price" label="Kontör Birim Ücreti" required="true" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kontör Adet</label>
                            <input required class="form-control" type="number" min="1" placeholder="1 kontör = 1 paket" id="top_up" name="top_up">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Toplam Tutar</label>
                            <input class="form-control fw-bold" style="color:var(--indigo); background:var(--indigo-light);" type="text" id="total_amount" name="total_amount" readonly>
                        </div>
                        <button class="special-button w-100" type="submit">Kontör Yükle</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Filtre + Kontör Özet --}}
        <div class="col-lg-7">
            <div class="card-section h-100">
                <div class="section-header">
                    <h5>Kontör Özeti</h5>
                    <a href="{{ route('superadmin.admin_topup', $admin->id) }}" class="text-decoration-none" style="color:var(--indigo); font-size:13px;">Temizle</a>
                </div>
                <div class="p-3">
                    {{-- Filtre --}}
                    <form method="GET" action="{{ route('superadmin.admin_topup', $admin->id) }}" class="filter-form-wrap">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--indigo-text)">Başlangıç Tarihi</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--indigo-text)">Bitiş Tarihi</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-sm w-100" style="background:var(--indigo);color:#fff;font-weight:600;">Filtrele</button>
                            </div>
                        </div>
                    </form>

                    {{-- Kontör Kartları --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="kontor-stat white-box">
                                <h6>Mevcut Kontör</h6>
                                <h4>{{ $stats['current_balance'] }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="kontor-stat indigo-box">
                                <h6>Toplam Alınan</h6>
                                <h4>{{ $stats['total_topup'] }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="kontor-stat indigo-box">
                                <h6>Yapılan Ödeme</h6>
                                <h4>{{ number_format($stats['paid_amount'], 2) }}₺</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="kontor-stat white-box">
                                <h6>Kalan Ödeme</h6>
                                <h4>{{ number_format($stats['remaining_amount'], 2) }}₺</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== 4. SATIR: Kontör Hareketleri Tabları ===== --}}
    <div class="card-section">
        <div class="section-header">
            <h5>Kontör Hareketleri</h5>
            <a href="{{ route('superadmin.admin_topup', $admin->id) }}" class="text-decoration-none" style="color:var(--indigo);font-size:13px;font-weight:600;">↻ Yenile</a>
        </div>
        <div class="p-3">
            <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#talep" type="button">Talep</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#eklenen" type="button">Yüklenen Kontörler</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#odemeBekleyen" type="button" style="color:#ef4444;">Ödeme Bekliyor</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#odenen" type="button" style="color:#16a34a;">Ödendi</button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- Talep --}}
                <div class="tab-pane fade show active" id="talep" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table detail-table" id="tablo-talepler">
                            <thead>
                                <tr>
                                    <th>Talep Eden</th>
                                    <th>Rol</th>
                                    <th>Kontör Adet</th>
                                    <th>Birim Ücret</th>
                                    <th>Toplam</th>
                                    <th>Onayla</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records->where('is_approved', false) as $record)
                                    <tr id="data_{{ $record->id }}">
                                        <td class="fw-semibold">
                                            @switch($record->created_type)
                                                @case('superadmin') {{ App\Models\SuperAdmin::find($record->created_by_user_id)->name ?? '—' }} @break
                                                @case('admin')      {{ App\Models\Admin::find($record->created_by_user_id)->name ?? '—' }} @break
                                                @case('dealer')     {{ App\Models\User::find($record->created_by_user_id)->name ?? '—' }} @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($record->created_type)
                                                @case('superadmin') <span class="badge" style="background:var(--indigo)">Üst Yönetici</span> @break
                                                @case('admin')      <span class="badge bg-secondary">Yönetici</span> @break
                                                @case('dealer')     <span class="badge bg-info text-dark">Partner</span> @break
                                            @endswitch
                                        </td>
                                        <td>{{ $record->top_up }}</td>
                                        <td>{{ $record->top_up_price }}₺</td>
                                        <td class="fw-semibold">{{ $record->total_amount }}₺</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="s-{{ $record->id }}"
                                                       role="switch" style="height:26px;width:52px;"
                                                       {{ $record->is_approved ? 'checked' : '' }}
                                                       onchange="approveFunction(this, '{{ $record->id }}')">
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ date('d.m.Y H:i', strtotime($record->created_at)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="no-records-talep" style="display:none;text-align:center;padding:20px;color:#9ca3af;">Veri bulunmuyor</div>
                    </div>
                </div>

                {{-- Yüklenenler --}}
                <div class="tab-pane fade" id="eklenen" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table detail-table" id="tablo-eklenen">
                            <thead>
                                <tr>
                                    <th>Ekleyen</th>
                                    <th>Rol</th>
                                    <th>Kontör Adet</th>
                                    <th>Birim Ücret</th>
                                    <th>Toplam</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records->where('is_approved', true) as $record)
                                    <tr id="data_{{ $record->id }}">
                                        <td class="fw-semibold">
                                            @switch($record->created_type)
                                                @case('superadmin') {{ App\Models\SuperAdmin::find($record->created_by_user_id)->name ?? '—' }} @break
                                                @case('admin')      {{ App\Models\Admin::find($record->created_by_user_id)->name ?? '—' }} @break
                                                @case('dealer')     {{ App\Models\User::find($record->created_by_user_id)->name ?? '—' }} @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($record->created_type)
                                                @case('superadmin') <span class="badge" style="background:var(--indigo)">Üst Yönetici</span> @break
                                                @case('admin')      <span class="badge bg-secondary">Yönetici</span> @break
                                                @case('dealer')     <span class="badge bg-info text-dark">Partner</span> @break
                                            @endswitch
                                        </td>
                                        <td>{{ $record->top_up }}</td>
                                        <td>{{ $record->top_up_price }}₺</td>
                                        <td class="fw-semibold">{{ $record->total_amount }}₺</td>
                                        <td class="text-muted">{{ date('d.m.Y H:i', strtotime($record->created_at)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="no-records-eklenen" style="display:none;text-align:center;padding:20px;color:#9ca3af;">Veri bulunmuyor</div>
                    </div>
                </div>

                {{-- Ödeme Bekleyenler --}}
                <div class="tab-pane fade" id="odemeBekleyen" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table detail-table" id="tablo-odenmeyen">
                            <thead>
                                <tr>
                                    <th>Ekleyen</th>
                                    <th>Rol</th>
                                    <th>Kontör Adet</th>
                                    <th>Birim Ücret</th>
                                    <th>Toplam</th>
                                    <th>Ödendi Yap</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records->where('is_approved', true)->where('is_paid', false) as $record)
                                    <tr id="data_{{ $record->id }}">
                                        <td class="fw-semibold">
                                            @switch($record->created_type)
                                                @case('superadmin') {{ App\Models\SuperAdmin::find($record->created_by_user_id)->name ?? '—' }} @break
                                                @case('admin')      {{ App\Models\Admin::find($record->created_by_user_id)->name ?? '—' }} @break
                                                @case('dealer')     {{ App\Models\User::find($record->created_by_user_id)->name ?? '—' }} @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($record->created_type)
                                                @case('superadmin') <span class="badge" style="background:var(--indigo)">Üst Yönetici</span> @break
                                                @case('admin')      <span class="badge bg-secondary">Yönetici</span> @break
                                                @case('dealer')     <span class="badge bg-info text-dark">Partner</span> @break
                                            @endswitch
                                        </td>
                                        <td>{{ $record->top_up }}</td>
                                        <td>{{ $record->top_up_price }}₺</td>
                                        <td class="fw-semibold text-danger">{{ $record->total_amount }}₺</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="s-{{ $record->id }}"
                                                       role="switch" style="height:26px;width:52px;"
                                                       {{ $record->is_paid ? 'checked' : '' }}
                                                       onchange="paidFunction(this, '{{ $record->id }}')">
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ date('d.m.Y H:i', strtotime($record->created_at)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="no-records-odemeyen" style="display:none;text-align:center;padding:20px;color:#9ca3af;">Veri bulunmuyor</div>
                    </div>
                </div>

                {{-- Ödenenler --}}
                <div class="tab-pane fade" id="odenen" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table detail-table" id="tablo-odenen">
                            <thead>
                                <tr>
                                    <th>Ekleyen</th>
                                    <th>Rol</th>
                                    <th>Kontör Adet</th>
                                    <th>Birim Ücret</th>
                                    <th>Toplam</th>
                                    <th>Ödenmedi Yap</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records->where('is_approved', true)->where('is_paid', true) as $record)
                                    <tr id="data_{{ $record->id }}">
                                        <td class="fw-semibold">
                                            @switch($record->created_type)
                                                @case('superadmin') {{ App\Models\SuperAdmin::find($record->created_by_user_id)->name ?? '—' }} @break
                                                @case('admin')      {{ App\Models\Admin::find($record->created_by_user_id)->name ?? '—' }} @break
                                                @case('dealer')     {{ App\Models\User::find($record->created_by_user_id)->name ?? '—' }} @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($record->created_type)
                                                @case('superadmin') <span class="badge" style="background:var(--indigo)">Üst Yönetici</span> @break
                                                @case('admin')      <span class="badge bg-secondary">Yönetici</span> @break
                                                @case('dealer')     <span class="badge bg-info text-dark">Partner</span> @break
                                            @endswitch
                                        </td>
                                        <td>{{ $record->top_up }}</td>
                                        <td>{{ $record->top_up_price }}₺</td>
                                        <td class="fw-semibold text-success">{{ $record->total_amount }}₺</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="s-{{ $record->id }}"
                                                       role="switch" style="height:26px;width:52px;"
                                                       {{ $record->is_paid ? 'checked' : '' }}
                                                       onchange="unpaidFunction(this, '{{ $record->id }}')">
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ date('d.m.Y H:i', strtotime($record->created_at)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="no-records-odenen" style="display:none;text-align:center;padding:20px;color:#9ca3af;">Veri bulunmuyor</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Kurye Detay Offcanvas --}}
<div class="offcanvas offcanvas-end offcanvas-detail" tabindex="-1" id="courierDetailPanel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title mb-0 fw-bold" id="courierDetailTitle">Kurye Detayı</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3" id="courierDetailBody">
        <div id="courierDetailSpinner">
            <div class="spinner-border" style="color:var(--indigo);" role="status"></div>
            <div class="mt-2 text-muted" style="font-size:13px;">Yükleniyor...</div>
        </div>
        <div id="courierDetailContent" style="display:none;">
            {{-- Kurye Bilgileri --}}
            <div class="mb-3">
                <div class="section-header mb-2" style="border-radius:8px;">
                    <h5 style="font-size:13px;">Kurye Bilgileri</h5>
                    <span id="cd-status-badge"></span>
                </div>
                <div id="cd-info-rows"></div>
            </div>

            {{-- Mesafe Listesi --}}
            <div>
                <div class="section-header mb-2" style="border-radius:8px;">
                    <h5 style="font-size:13px;">Restorana Mesafeler</h5>
                    <span style="font-size:11px; color:var(--indigo-text);">yakından uzağa</span>
                </div>
                <div id="cd-distances">
                    <p class="text-muted text-center" style="font-size:13px;padding:20px 0;">Konum bilgisi yok</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Boş tablo kontrolü
    function checkAndShowNoData(tableId, noDataId) {
        const tbody = document.getElementById(tableId)?.querySelector('tbody');
        document.getElementById(noDataId).style.display = (!tbody || tbody.rows.length === 0) ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        checkAndShowNoData('tablo-talepler',  'no-records-talep');
        checkAndShowNoData('tablo-eklenen',   'no-records-eklenen');
        checkAndShowNoData('tablo-odenmeyen', 'no-records-odemeyen');
        checkAndShowNoData('tablo-odenen',    'no-records-odenen');
    });

    // Toplam tutar hesapla
    const topUpInput  = document.getElementById('top_up');
    const totalInput  = document.getElementById('total_amount');

    function updateTotal() {
        const priceEl = document.getElementById('top_up_price');
        const price = parseFloat(priceEl?.value) || 0;
        const qty   = parseInt(topUpInput?.value) || 0;
        if (totalInput) totalInput.value = (price * qty).toFixed(2) + '₺';
    }

    document.getElementById('top_up_price')?.addEventListener('change', updateTotal);
    topUpInput?.addEventListener('input', updateTotal);

    // Onay
    function approveFunction(checkbox, id) {
        const prev = checkbox.checked;
        Swal.fire({
            title: 'Onayla', text: 'Onaylamak istediğinizden emin misiniz?', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#4f46e5', cancelButtonColor: '#6b7280',
            cancelButtonText: 'Hayır', confirmButtonText: 'Evet, Onayla'
        }).then(r => {
            if (r.isConfirmed) {
                $.get('/superadmin/admin/topup/approve/' + id, data => {
                    if (data === 'OK') {
                        $('#data_' + id).fadeOut(300);
                        Swal.fire('Onaylandı!', 'Kontör başarıyla eklendi.', 'success');
                        checkAndShowNoData('tablo-talepler', 'no-records-talep');
                    } else { Swal.fire('Uyarı!', 'İşlem başarısız.', 'warning'); checkbox.checked = !prev; }
                }).fail(() => { Swal.fire('Hata!', 'Bir hata oluştu.', 'error'); checkbox.checked = !prev; });
            } else { checkbox.checked = !prev; }
        });
    }

    // Ödendi yap
    function paidFunction(checkbox, id) {
        const prev = checkbox.checked;
        Swal.fire({
            title: 'Ödendi Yap', text: 'Ödendi olarak işaretlenecek, emin misiniz?', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#16a34a', cancelButtonColor: '#6b7280',
            cancelButtonText: 'Hayır', confirmButtonText: 'Evet, Ödendi'
        }).then(r => {
            if (r.isConfirmed) {
                $.get('/superadmin/admin/topup/paid/' + id, data => {
                    if (data === 'OK') {
                        $('#data_' + id).fadeOut(300);
                        Swal.fire('Başarılı!', 'Ödeme işaretlendi.', 'success');
                        checkAndShowNoData('tablo-odenmeyen', 'no-records-odemeyen');
                    } else { Swal.fire('Uyarı!', 'İşlem başarısız.', 'warning'); checkbox.checked = !prev; }
                }).fail(() => { Swal.fire('Hata!', 'Bir hata oluştu.', 'error'); checkbox.checked = !prev; });
            } else { checkbox.checked = !prev; }
        });
    }

    // Kurye detay paneli
    function openCourierDetail(courierId) {
        const panelEl = document.getElementById('courierDetailPanel');
        const panel   = bootstrap.Offcanvas.getOrCreateInstance(panelEl);
        const spinner = document.getElementById('courierDetailSpinner');
        const content = document.getElementById('courierDetailContent');
        const infoEl  = document.getElementById('cd-info-rows');
        const distEl  = document.getElementById('cd-distances');
        const titleEl = document.getElementById('courierDetailTitle');
        const badgeEl = document.getElementById('cd-status-badge');

        spinner.style.display = 'block';
        content.style.display = 'none';
        panel.show();

        $.getJSON('/superadmin/admin/courier/' + courierId + '/detail', function (data) {
            const c = data.courier;
            titleEl.textContent = c.name;

            // Durum badge
            badgeEl.innerHTML = c.is_active
                ? '<span class="badge bg-success">Aktif</span>'
                : '<span class="badge bg-danger">Pasif</span>';

            // Kurye bilgi satırları
            const priceLabel = c.price_type === 'km' ? 'km Ücret' : 'Paket Ücret';
            const rows = [
                ['Telefon',    c.phone      || '—'],
                ['Araç',       c.vehicle_type || '—'],
                [priceLabel,   c.price ? c.price + '₺' : '—'],
                ['Enlem',      c.latitude   || 'Bilinmiyor'],
                ['Boylam',     c.longitude  || 'Bilinmiyor'],
            ];
            infoEl.innerHTML = rows.map(([label, val]) =>
                `<div class="courier-info-row">
                    <span class="courier-info-label">${label}</span>
                    <span class="courier-info-val">${val}</span>
                </div>`
            ).join('');

            // Mesafe listesi
            if (!c.latitude || !c.longitude) {
                distEl.innerHTML = '<p class="text-muted text-center" style="font-size:13px;padding:20px 0;">Kurye konum bilgisi yok</p>';
            } else if (!data.restaurants || data.restaurants.length === 0) {
                distEl.innerHTML = '<p class="text-muted text-center" style="font-size:13px;padding:20px 0;">Restoran bulunamadı</p>';
            } else {
                distEl.innerHTML = data.restaurants.map(r => {
                    let badgeCls, distText;
                    if (r.distance_km === null) {
                        badgeCls = 'dist-unknown'; distText = 'Bilinmiyor';
                    } else if (r.distance_km <= 3) {
                        badgeCls = 'dist-near';  distText = r.distance_km + ' km';
                    } else if (r.distance_km <= 8) {
                        badgeCls = 'dist-mid';   distText = r.distance_km + ' km';
                    } else {
                        badgeCls = 'dist-far';   distText = r.distance_km + ' km';
                    }
                    return `<div class="dist-row">
                        <span style="font-weight:600;color:#111827;">${r.name}</span>
                        <span class="dist-badge ${badgeCls}">${distText}</span>
                    </div>`;
                }).join('');
            }

            spinner.style.display = 'none';
            content.style.display  = 'block';
        }).fail(function () {
            spinner.style.display  = 'none';
            content.style.display  = 'block';
            infoEl.innerHTML = '<p class="text-danger text-center py-3">Veri alınamadı.</p>';
            distEl.innerHTML = '';
        });
    }

    // Ödenmedi yap
    function unpaidFunction(checkbox, id) {
        const prev = checkbox.checked;
        Swal.fire({
            title: 'Ödenmedi Yap', text: 'Ödenmedi olarak işaretlenecek, emin misiniz?', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
            cancelButtonText: 'Hayır', confirmButtonText: 'Evet, Ödenmedi'
        }).then(r => {
            if (r.isConfirmed) {
                $.get('/superadmin/admin/topup/unpaid/' + id, data => {
                    if (data === 'OK') {
                        $('#data_' + id).fadeOut(300);
                        Swal.fire('Başarılı!', 'Ödenmedi olarak işaretlendi.', 'success');
                        checkAndShowNoData('tablo-odenen', 'no-records-odenen');
                    } else { Swal.fire('Uyarı!', 'İşlem başarısız.', 'warning'); checkbox.checked = !prev; }
                }).fail(() => { Swal.fire('Hata!', 'Bir hata oluştu.', 'error'); checkbox.checked = !prev; });
            } else { checkbox.checked = !prev; }
        });
    }
</script>
@endsection
