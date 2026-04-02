@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Sipariş Yönetimi</h1>
            <p class="text-sm text-slate-500 mt-1">Paket birleştirme ve transfer yönetimi</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-2xl text-xs font-black uppercase tracking-wider">
                <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                {{ $pendingOrders->count() }} Bekleyen Paket
            </span>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-4 p-5 bg-emerald-50 border border-emerald-200 rounded-3xl">
            <div class="w-10 h-10 bg-emerald-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-white text-sm"></i>
            </div>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-4 p-5 bg-red-50 border border-red-200 rounded-3xl">
            <div class="w-10 h-10 bg-red-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation text-white text-sm"></i>
            </div>
            <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-2 p-1.5 bg-slate-100 rounded-2xl w-fit">
        <button onclick="switchTab('merge')" id="tab-merge"
            class="tab-btn px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all bg-white text-indigo-600 shadow-sm">
            <i class="fas fa-layer-group mr-2"></i>Paket Birleştirme
        </button>
        <button onclick="switchTab('transfer')" id="tab-transfer"
            class="tab-btn px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-slate-500 hover:text-slate-800">
            <i class="fas fa-right-left mr-2"></i>Transfer Yönetimi
            @if($transfers->total() > 0)
                <span class="ml-1 px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-[10px]">{{ $transfers->total() }}</span>
            @endif
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TAB 1: PAKET BİRLEŞTİRME --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div id="panel-merge">
        <form method="POST" action="{{ route('admin.order.merge') }}" id="mergeForm">
            @csrf
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- Sol: Sipariş Listesi --}}
                <div class="xl:col-span-2">
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Bekleyen Siparişler</h2>
                                <p class="text-xs text-slate-400 mt-0.5">Birleştirmek istediğiniz siparişleri seçin</p>
                            </div>
                            <button type="button" onclick="selectAll()" class="text-xs font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-wider">
                                Tümünü Seç
                            </button>
                        </div>

                        @if($pendingOrders->isEmpty())
                            <div class="py-20 text-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-box-open text-slate-400 text-xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-500">Bugün atanmayı bekleyen sipariş yok</p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-50" id="orderList">
                                @foreach($pendingOrders as $order)
                                <label class="flex items-start gap-5 px-8 py-5 cursor-pointer hover:bg-slate-50 transition-colors order-row group" data-id="{{ $order->id }}">
                                    <div class="relative mt-0.5">
                                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}"
                                               class="order-checkbox w-5 h-5 rounded-lg border-2 border-slate-200 text-indigo-600 cursor-pointer focus:ring-indigo-500 focus:ring-offset-0">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-1">
                                            <span class="text-sm font-black text-slate-900">#{{ $order->tracking_id }}</span>
                                            <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase">{{ $order->platform ?? 'Manuel' }}</span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700 truncate">{{ $order->full_name }}</p>
                                        <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $order->address }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-xs font-black text-slate-900">{{ $order->restaurant->name ?? '-' }}</p>
                                        @if($order->distance)
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                <i class="fas fa-route text-[9px]"></i> {{ number_format($order->distance, 1) }} km
                                            </p>
                                        @endif
                                        <p class="text-[10px] text-slate-300 mt-1">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sağ: Kurye Seçimi + Özet --}}
                <div class="space-y-4">
                    {{-- Seçim Özeti --}}
                    <div class="bg-indigo-600 rounded-3xl p-6 text-white">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200 mb-3">Seçim Özeti</p>
                        <div class="text-5xl font-black mb-1" id="selectedCount">0</div>
                        <p class="text-indigo-200 text-sm font-bold">sipariş seçildi</p>
                    </div>

                    {{-- Kurye Seç --}}
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4">Kurye Seç</h3>

                        @if($couriers->isEmpty())
                            <div class="py-8 text-center">
                                <i class="fas fa-user-slash text-slate-300 text-2xl mb-3 block"></i>
                                <p class="text-xs text-slate-400 font-bold">Aktif kurye bulunamadı</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($couriers as $courier)
                                <label class="flex items-center gap-3 p-3 rounded-2xl border-2 border-transparent hover:border-indigo-100 hover:bg-indigo-50/50 cursor-pointer transition-all has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                    <input type="radio" name="courier_id" value="{{ $courier->id }}" class="text-indigo-600">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-black text-slate-800 truncate">{{ $courier->name }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $courier->phone }}</p>
                                    </div>
                                    <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-black
                                        {{ $courier->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $courier->status === 'active' ? 'Müsait' : 'Yolda' }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Ata Butonu --}}
                    <button type="submit" id="mergeBtn" disabled
                        class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-200 transition-all active:scale-[0.98]">
                        <i class="fas fa-layer-group mr-2"></i>Paketleri Birleştir & Ata
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TAB 2: TRANSFER YÖNETİMİ --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div id="panel-transfer" class="hidden">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Transfer Kayıtları</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kuryeler tarafından devredilen paketler</p>
            </div>

            @if($transfers->isEmpty())
                <div class="py-20 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-right-left text-slate-400 text-xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-500">Henüz transfer kaydı yok</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach($transfers as $transfer)
                    <div class="px-8 py-6 hover:bg-slate-50 transition-colors">
                        <div class="flex items-start gap-6">
                            {{-- Transfer Sebebi Badge --}}
                            <div class="flex-shrink-0">
                                @php
                                    $badgeMap = [
                                        'accident' => ['bg-red-100', 'text-red-700', 'Kaza'],
                                        'fault'    => ['bg-amber-100', 'text-amber-700', 'Arıza'],
                                        'other'    => ['bg-slate-100', 'text-slate-600', 'Diğer'],
                                    ];
                                    $badge = $badgeMap[$transfer->status] ?? ['bg-slate-100', 'text-slate-600', $transfer->status];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[11px] font-black {{ $badge[0] }} {{ $badge[1] }}">
                                    {{ $badge[2] }}
                                </span>
                            </div>

                            {{-- Sipariş Bilgisi --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="text-sm font-black text-slate-900">
                                        #{{ $transfer->order->tracking_id ?? '-' }}
                                    </span>
                                    <span class="text-xs text-slate-400">→</span>
                                    <span class="text-xs font-bold text-slate-600">
                                        {{ $transfer->order->restaurant->name ?? '-' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mb-1">
                                    <i class="fas fa-user-minus text-red-400 mr-1"></i>
                                    Transfer eden: <span class="font-bold text-slate-700">{{ $transfer->courier->name ?? '-' }}</span>
                                </p>
                                @if($transfer->reason)
                                    <p class="text-xs text-slate-400 italic">"{{ $transfer->reason }}"</p>
                                @endif
                                <p class="text-[10px] text-slate-300 mt-1">{{ $transfer->created_at->format('d.m.Y H:i') }}</p>
                            </div>

                            {{-- Sipariş Durumu + Yeniden Ata --}}
                            <div class="flex-shrink-0 text-right space-y-3">
                                @php
                                    $orderStatus = $transfer->order->status ?? null;
                                    $statusColors = [
                                        'PREPARED'  => 'bg-blue-100 text-blue-700',
                                        'DELIVERED' => 'bg-emerald-100 text-emerald-700',
                                        'ASSIGNED'  => 'bg-purple-100 text-purple-700',
                                    ];
                                    $statusClass = $statusColors[$orderStatus] ?? 'bg-slate-100 text-slate-600';
                                    $statusLabels = [
                                        'PREPARED'  => 'Atama Bekliyor',
                                        'DELIVERED' => 'Teslim Edildi',
                                        'ASSIGNED'  => 'Atandı',
                                        'HANDOVER'  => 'Yolda',
                                    ];
                                @endphp
                                <span class="inline-flex px-3 py-1.5 rounded-xl text-[10px] font-black {{ $statusClass }}">
                                    {{ $statusLabels[$orderStatus] ?? $orderStatus }}
                                </span>

                                @if($orderStatus === 'PREPARED')
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[11px] font-black transition-all">
                                        <i class="fas fa-user-plus text-[10px]"></i> Yeniden Ata
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-cloak
                                        class="absolute right-0 top-10 z-50 w-64 bg-white border border-slate-200 rounded-2xl shadow-2xl p-3">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Kurye Seç</p>
                                        @foreach($couriers as $c)
                                        <form method="POST" action="{{ route('admin.order.reassign', $transfer->order_id) }}">
                                            @csrf
                                            <input type="hidden" name="courier_id" value="{{ $c->id }}">
                                            <button type="submit"
                                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-indigo-50 transition-colors text-left group">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">{{ $c->name }}</p>
                                                    <p class="text-[10px] text-slate-400">{{ $c->phone }}</p>
                                                </div>
                                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full
                                                    {{ $c->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ $c->status === 'active' ? 'Müsait' : 'Yolda' }}
                                                </span>
                                            </button>
                                        </form>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($transfers->hasPages())
                <div class="px-8 py-5 border-t border-slate-100">
                    {{ $transfers->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    // Tab switching
    function switchTab(tab) {
        document.getElementById('panel-merge').classList.toggle('hidden', tab !== 'merge');
        document.getElementById('panel-transfer').classList.toggle('hidden', tab !== 'transfer');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm');
            btn.classList.add('text-slate-500');
        });
        const active = document.getElementById('tab-' + tab);
        active.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
        active.classList.remove('text-slate-500');
    }

    // Checkbox + button state
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const countEl   = document.getElementById('selectedCount');
    const mergeBtn  = document.getElementById('mergeBtn');

    function updateState() {
        const checked = document.querySelectorAll('.order-checkbox:checked').length;
        if (countEl) countEl.textContent = checked;
        const hasCourier = document.querySelector('input[name="courier_id"]:checked');
        if (mergeBtn) mergeBtn.disabled = checked === 0 || !hasCourier;
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateState));
    document.querySelectorAll('input[name="courier_id"]').forEach(r => r.addEventListener('change', updateState));

    function selectAll() {
        const allChecked = [...checkboxes].every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        updateState();
    }

    // Row click highlight
    document.querySelectorAll('.order-row').forEach(row => {
        row.addEventListener('change', () => {
            const cb = row.querySelector('.order-checkbox');
            row.classList.toggle('bg-indigo-50/50', cb.checked);
            row.classList.toggle('border-l-4', cb.checked);
            row.classList.toggle('border-indigo-500', cb.checked);
        });
    });
</script>
@endpush
@endsection
