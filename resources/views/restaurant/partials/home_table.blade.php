<div class="fade-in-up">
    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2 px-3 pt-2">
        <ul class="nav nav-pills nav-pill-group p-1 neo-surface border-0 no-scrollbar flex-nowrap" id="orderStatusTabs" role="tablist" style="overflow-x: auto; white-space: nowrap;">
            @foreach(\App\Helpers\OrderStatus::statuses() as $value => $key)
                <li class="nav-item" role="presentation">
                    <button class="nav-link nav-link-custom border-0 {{ $loop->first ? 'active' : '' }}"
                            id="{{$key}}-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#{{$key}}"
                            type="button"
                            role="tab"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <span class="small uppercase tracking-widest px-2">{{__('statuses.'.$key)}}</span>
                    </button>
                </li>
            @endforeach
        </ul>

        <button onclick="fetchOrders()" class="btn neo-surface border-0 px-4 py-2 text-indigo fw-black small uppercase tracking-tighter hover-rotate">
            <i class="fa fa-refresh me-2"></i> Yenile
        </button>
    </div>

    <div id="newOrder" class="alert-nebula mb-4 text-center py-3 shadow-lg" style="display:none;">
        <div class="d-flex align-items-center justify-content-center gap-3">
            <span class="ping-animation"></span>
            <span class="fw-black tracking-tighter uppercase">Yeni Sipariş Geldi</span>
        </div>
    </div>

    @php
        $statuses = \App\Helpers\OrderStatus::statuses();
    @endphp

    <div class="tab-content" id="orderStatusTabsContent">
        @foreach ($statuses as $statusKey => $statusId)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                 id="{{ $statusId }}"
                 role="tabpanel"
                 aria-labelledby="{{ $statusId }}-tab">

                <div class="table-responsive">
                        <table class="table custom-modern-table m-0 w-100">
                            <thead>
                            <tr>
                                <th>Restoran</th>
                                <th>Sipariş No</th>
                                <th>Saat</th>
                                <th>Müşteri</th>
                                <th>Kurye</th>
                                <th>Ara Tutar</th>
                                <th>İndirim</th>
                                <th>Tutar</th>
                                <th>Ödeme</th>
                                <th>Mesafe</th>
                                <th>Durum</th>
                                <th class="text-end">İşlem</th>
                            </tr>
                            </thead>
                            <tbody id="order-tbody-{{ $statusId }}"></tbody>
                        </table>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .neo-surface {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .nav-pill-group .nav-link-custom {
        background: transparent;
        color: #64748b;
        padding: 6px 14px;
        border-radius: 12px !important;
        font-weight: 700;
        font-size: 11px;
        transition: 0.3s;
    }

    .nav-pill-group .nav-link-custom.active {
        background: #4f46e5 !important;
        color: white !important;
    }

    .custom-modern-table thead th {
        background: #f8fafc;
        padding: 10px 12px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        color: #64748b;
        border: none;
        white-space: nowrap;
    }

    .custom-modern-table tbody td {
        padding: 10px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .custom-modern-table tbody tr {
        transition: background 0.15s;
    }

    .custom-modern-table tbody tr:hover td {
        background: #f8fafc;
    }

    /* Güzel checkbox */
    .bulk-order-checkbox,
    .select-all-checkbox {
        width: 16px;
        height: 16px;
        border: 2px solid #c7d2fe;
        border-radius: 5px;
        cursor: pointer;
        accent-color: #4f46e5;
        transition: border-color 0.2s;
    }
    .bulk-order-checkbox:hover,
    .select-all-checkbox:hover {
        border-color: #4f46e5;
    }

    .tab-content { padding-bottom: 1rem; }

    .alert-nebula {
        background: #0f172a;
        color: #10b981;
        border-radius: 16px;
    }

    .ping-animation {
        width: 10px; height: 10px; background: #10b981; border-radius: 50%;
        position: relative; display: inline-block;
    }
    .ping-animation::after {
        content: ''; position: absolute; width: 100%; height: 100%;
        background: inherit; border-radius: 50%; animation: ping 1.5s infinite;
    }
    @keyframes ping { 75%, 100% { transform: scale(3); opacity: 0; } }

    .no-scrollbar::-webkit-scrollbar { display: none; }
</style>

@include('partials.home_scripts',['key' => 'restaurant'])
