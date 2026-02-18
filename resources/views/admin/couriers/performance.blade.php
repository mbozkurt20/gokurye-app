@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    KURYE <span class="text-slate-400">PERFORMANSI</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    {{request('courier_id') ? App\Models\Courier::find(request('courier_id'))->name : 'Tüm Kuryeler'}} verimlilik analizi.
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-4 py-2 !rounded-2xl m-0 shadow-sm border border-slate-50">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest"><a href="/admin/couriers" class="text-slate-400">Kuryeler</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest text-indigo-600 active">Performans</li>
                </ol>
            </nav>
        </div>

        <div class="bg-white !rounded-[32px] shadow-sm border border-slate-50 p-6 mb-8">
            <form method="GET" action="{{ route('admin.courier.performance') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Kurye Seçimi</label>
                        <select name="courier_id" class="form-control !rounded-xl border-slate-100 font-bold text-slate-700 text-xs">
                            <option value="">Tümü</option>
                            @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}" {{ request('courier_id') == $courier->id ? 'selected' : '' }}>
                                    {{ $courier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Tarih</label>
                        <input type="date" name="date" class="form-control !rounded-xl border-slate-100 font-bold text-slate-700 text-xs" value="{{ request('date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Zaman Aralığı</label>
                        <select name="period" class="form-control !rounded-xl border-slate-100 font-bold text-slate-700 text-xs">
                            <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Günlük</option>
                            <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Haftalık</option>
                            <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Aylık</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="w-full bg-indigo-600 text-white py-3 !rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-900/10 hover:scale-[1.02] transition-all">
                            <i class="fa fa-filter me-2"></i> VERİLERİ GETİR
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
            <div class="px-8 pt-8 flex items-center justify-between border-b border-slate-50">
                <ul class="nav nav-tabs border-0 gap-8" id="performanceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active !border-0 !bg-transparent text-[11px] font-black uppercase tracking-widest pb-4 relative after:absolute after:bottom-0 after:left-0 after:w-full after:h-1 after:bg-indigo-600 after:rounded-full" data-bs-toggle="tab" data-bs-target="#summary">
                            📌 GENEL ÖZET
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link !border-0 !bg-transparent text-slate-400 text-[11px] font-black uppercase tracking-widest pb-4 hover:text-indigo-600 transition-all" data-bs-toggle="tab" data-bs-target="#topCouriers">
                            🏆 LİDERLİK TABLOSU
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content p-8">
                <div class="tab-pane fade show active" id="summary">
                    <div class="row g-8">
                        <div class="col-lg-7">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-xs"></i>
                                </div>
                                <h5 class="text-xs font-black text-slate-800 uppercase tracking-widest m-0">
                                    Aralık: <span class="text-indigo-600">{{ $startDate->format('d.m.Y') }} - {{ $endDate->format('d.m.Y') }}</span>
                                </h5>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-borderless align-middle">
                                    <thead>
                                    <tr class="border-b border-slate-50">
                                        <th class="text-[10px] font-black text-slate-400 uppercase py-4">Kurye</th>
                                        <th class="text-[10px] font-black text-slate-400 uppercase py-4">Durum</th>
                                        <th class="text-[10px] font-black text-slate-400 uppercase py-4 text-end">Süre (Dk)</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                    @forelse ($statusSummary as $row)
                                        <tr>
                                            <td class="py-4">
                                                <button data-bs-toggle="modal" data-bs-target="#courierModal{{ $row->courier_id }}" class="text-sm font-bold text-indigo-600 hover:underline">
                                                    {{ $couriers->firstWhere('id', $row->courier_id)?->name ?? 'Bilinmiyor' }}
                                                </button>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = ['break' => 'bg-amber-100 text-amber-600', 'service' => 'bg-blue-100 text-blue-600', 'active' => 'bg-emerald-100 text-emerald-600', 'passive' => 'bg-slate-100 text-slate-600'];
                                                    $statuses = ['break' => 'Molada', 'service' => 'Serviste', 'active' => 'Müsait', 'passive' => 'Pasif'];
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $statusColors[$row->status] ?? 'bg-slate-100' }}">
                                                        {{ $statuses[$row->status] ?? $row->status }}
                                                    </span>
                                            </td>
                                            <td class="text-end font-mono font-bold text-slate-700">{{ round($row->total_duration / 60, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-10 font-bold text-slate-300 uppercase text-xs">Veri bulunamadı</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="bg-slate-50 !rounded-[32px] p-8 flex flex-col items-center justify-center h-full">
                                <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8">Zaman Dağılım Grafiği</h6>
                                <div class="w-full" style="max-width: 300px;">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="topCouriers">
                    <div class="bg-indigo-600 !rounded-[32px] p-8 text-white mb-8 relative overflow-hidden shadow-xl shadow-indigo-900/20">
                        <i class="fas fa-trophy absolute -right-4 -bottom-4 text-white/10 text-9xl transform rotate-12"></i>
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-4 opacity-70 text-indigo-200">Günün Yıldızı</h4>
                        @if ($topActiveCourier)
                            <div class="flex items-end gap-2">
                                <h2 class="text-4xl font-black tracking-tighter">{{ $couriers->firstWhere('id', $topActiveCourier->courier_id)?->name }}</h2>
                                <span class="text-sm font-bold bg-white/20 px-3 py-1 rounded-lg mb-2">{{ round($topActiveCourier->active_duration / 60, 2) }} Dakika Aktif</span>
                            </div>
                        @else
                            <h2 class="text-xl font-bold italic opacity-50">Henüz bir veri girişi olmadı.</h2>
                        @endif
                    </div>

                    <div class="row g-6">
                        <div class="col-md-12">
                            <h6 class="text-[11px] font-black text-slate-800 uppercase tracking-widest mb-6 px-1">🧩 Durum Bazlı Liderler</h6>
                            <div class="bg-white border border-slate-100 !rounded-[24px] overflow-hidden shadow-sm">
                                <canvas id="topStatusChart" height="80" class="p-6"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔍 Kurye Modalları --}}
        @foreach ($couriers as $courier)
            <div class="modal fade" data-bs-backdrop="false" id="courierModal{{ $courier->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content !rounded-[32px] border-0 shadow-2xl overflow-hidden">
                        <div class="bg-[#0f172a] p-8 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-xl font-black uppercase tracking-tighter m-0">{{ $courier->name }}</h5>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Personel Künyesi</p>
                                </div>
                                <button type="button" class="text-white/50 hover:text-white" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="modal-body p-8">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Telefon</label>
                                    <p class="font-bold text-slate-700 text-sm">{{ $courier->phone }}</p>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Ödeme Türü</label>
                                    <p class="font-bold text-slate-700 text-sm uppercase">{{ $courier->price_type }}</p>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Online Durum</label>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase {{ $courier->online ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $courier->online ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Son Atama</label>
                                    <p class="font-bold text-slate-700 text-sm italic">{{ $courier->last_assigned_at ?? 'Yok' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js Default Ayarları
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. Durum Dağılım Grafiği (Doughnut)
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statusSummary->pluck('status')->map(fn($s) => $statuses[$s] ?? ucfirst($s))) !!},
                datasets: [{
                    data: {!! json_encode($statusSummary->pluck('total_duration')->map(fn($s) => round($s / 60, 2))) !!},
                    backgroundColor: ['#6366f1', '#3b82f6', '#f59e0b', '#a855f7'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20, font: { weight: '900', size: 10 } } }
                }
            }
        });

        // 2. Bar Grafiği
        new Chart(document.getElementById('topStatusChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($topStatusList->map(fn($row) => $couriers->firstWhere('id', $row->courier_id)?->name . ' (' . ($statuses[$row->status] ?? ucfirst($row->status)) . ')')) !!},
                datasets: [{
                    label: 'Süre (Dakika)',
                    data: {!! json_encode($topStatusList->pluck('total_duration')->map(fn($s) => round($s / 60, 2))) !!},
                    backgroundColor: '#6366f1',
                    borderRadius: 12,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { display: false }, ticks: { font: { weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 9 } } }
                }
            }
        });
    </script>
@endsection
