@extends('restaurant.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Gün Sonu Raporu</h2>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Günlük Satış Özeti</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}"
                       class="border border-slate-200 rounded-2xl px-4 py-2 text-sm font-semibold text-slate-700 focus:outline-none focus:border-brand">
                <button class="bg-brand text-white text-xs font-black uppercase tracking-wide px-5 py-2.5 rounded-2xl">Getir</button>
            </form>
            <button onclick="window.print()" class="bg-slate-800 text-white text-xs font-black uppercase tracking-wide px-5 py-2.5 rounded-2xl flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Yazdır
            </button>
        </div>
    </div>

    {{-- Özet Kartlar --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Toplam Sipariş</p>
            <p class="text-3xl font-black text-brand">{{ $totalCount }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Toplam Ciro</p>
            <p class="text-3xl font-black text-emerald-500">₺{{ number_format($totalAmount, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ortalama Sipariş</p>
            <p class="text-3xl font-black text-slate-800">₺{{ $totalCount > 0 ? number_format($totalAmount / $totalCount, 2, ',', '.') : '0,00' }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Platform Sayısı</p>
            <p class="text-3xl font-black text-indigo-500">{{ $byPlatform->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- Platform Dağılımı --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Platform Dağılımı</p>
            <div class="space-y-3">
                @forelse($byPlatform as $p)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-slate-700 capitalize">{{ $p['platform'] }}</span>
                        <span class="text-xs font-black text-brand">{{ $p['count'] }} sipariş · ₺{{ number_format($p['amount'], 2, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-brand h-1.5 rounded-full" style="width:{{ $totalCount > 0 ? round($p['count']/$totalCount*100) : 0 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Bu tarihte sipariş bulunamadı.</p>
                @endforelse
            </div>
        </div>

        {{-- Ödeme Yöntemi --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Ödeme Yöntemleri</p>
            <div class="space-y-3">
                @forelse($byPayment as $p)
                <div class="flex items-center justify-between bg-slate-50 rounded-2xl px-4 py-3">
                    <span class="text-xs font-bold text-slate-700 capitalize">{{ $p['method'] }}</span>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-800">{{ $p['count'] }} sipariş</p>
                        <p class="text-[11px] font-semibold text-emerald-500">₺{{ number_format($p['amount'], 2, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Veri yok.</p>
                @endforelse
            </div>
        </div>

        {{-- En Çok Satan Ürünler --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">En Çok Satan Ürünler</p>
            <div class="space-y-2">
                @forelse($topProducts as $i => $p)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-brand/10 text-brand text-[10px] font-black flex items-center justify-center">{{ $i+1 }}</span>
                        <span class="text-xs font-semibold text-slate-700 truncate max-w-[140px]">{{ $p['name'] }}</span>
                    </div>
                    <span class="text-xs font-black text-slate-800">× {{ $p['qty'] }}</span>
                </div>
                @empty
                <p class="text-xs text-slate-400">Ürün verisi yok.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Saatlik Dağılım --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Saatlik Sipariş Dağılımı</p>
        @php $maxHourly = max($hourly) ?: 1; @endphp
        <div class="flex items-end gap-1 h-28">
            @foreach($hourly as $h => $count)
            <div class="flex-1 flex flex-col items-center gap-1" title="Saat {{ $h }}:00 — {{ $count }} sipariş">
                <div class="w-full rounded-t-lg transition-all"
                     style="height:{{ round($count / $maxHourly * 100) }}%; background: {{ $count > 0 ? '#6366f1' : '#f1f5f9' }}; min-height: 2px"></div>
                <span class="text-[8px] font-bold text-slate-400">{{ $h }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

<style>
    @media print {
        aside, header, button { display: none !important; }
        .container-fluid { padding: 0 !important; }
        .rounded-3xl { border-radius: 8px !important; }
    }
</style>
@endsection
