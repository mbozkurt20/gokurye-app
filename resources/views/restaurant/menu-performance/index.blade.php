@extends('restaurant.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Menü Performansı</h2>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Ürün & Kategori Analizi</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            @foreach([7=>'7 Gün', 30=>'30 Gün', 90=>'90 Gün'] as $d => $label)
            <a href="?days={{ $d }}"
               class="text-xs font-black uppercase tracking-wide px-4 py-2 rounded-2xl border transition-all
                      {{ $days == $d ? 'bg-brand text-white border-brand' : 'border-slate-200 text-slate-500 hover:border-brand hover:text-brand' }}">
                {{ $label }}
            </a>
            @endforeach
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- En Çok Satanlar --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">
                <i class="fa-solid fa-fire text-orange-400 mr-1"></i> En Çok Satan Ürünler
            </p>
            <div class="space-y-3">
                @forelse($topProducts as $i => $p)
                @php $bar = $maxQty > 0 ? round($p['qty'] / $maxQty * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full text-[9px] font-black flex items-center justify-center
                                {{ $i < 3 ? 'bg-orange-100 text-orange-500' : 'bg-slate-100 text-slate-500' }}">{{ $i+1 }}</span>
                            <span class="text-xs font-semibold text-slate-700 truncate max-w-[160px]">{{ $p['name'] }}</span>
                        </div>
                        <span class="text-xs font-black text-slate-800">{{ $p['qty'] }} adet</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $i < 3 ? 'bg-orange-400' : 'bg-brand' }}" style="width:{{ $bar }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Bu dönemde teslim edilen sipariş bulunamadı.</p>
                @endforelse
            </div>
        </div>

        {{-- En Az Satanlar --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">
                <i class="fa-solid fa-arrow-trend-down text-rose-400 mr-1"></i> En Az Satan Ürünler
            </p>
            <div class="space-y-2">
                @forelse($bottomProducts as $p)
                <div class="flex items-center justify-between bg-rose-50 rounded-2xl px-4 py-2.5">
                    <span class="text-xs font-semibold text-slate-700 truncate max-w-[180px]">{{ $p['name'] }}</span>
                    <span class="text-xs font-black text-rose-500">{{ $p['qty'] }} adet</span>
                </div>
                @empty
                <p class="text-xs text-slate-400">Veri yok.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- Kategori Performansı --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">
                <i class="fa-solid fa-layer-group text-indigo-400 mr-1"></i> Kategori Dağılımı
            </p>
            <div class="space-y-3">
                @forelse($categoryStats as $cat)
                <div class="flex items-center justify-between bg-indigo-50/50 rounded-2xl px-4 py-3">
                    <span class="text-xs font-bold text-slate-700">{{ $cat->category_name }}</span>
                    <span class="text-xs font-black text-indigo-500">{{ $cat->product_count }} ürün</span>
                </div>
                @empty
                <p class="text-xs text-slate-400">Kategori verisi yok.</p>
                @endforelse
            </div>
        </div>

        {{-- Günlük Sipariş Trendi --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">
                <i class="fa-solid fa-chart-line text-emerald-400 mr-1"></i> Günlük Sipariş Trendi
            </p>
            @php $maxDaily = $dailyTrend->max('count') ?: 1; @endphp
            <div class="flex items-end gap-1 h-24">
                @forelse($dailyTrend as $d)
                <div class="flex-1 flex flex-col items-center gap-1" title="{{ $d->date }}: {{ $d->count }} sipariş">
                    <div class="w-full rounded-t-md bg-emerald-400"
                         style="height:{{ round($d->count / $maxDaily * 100) }}%; min-height:2px"></div>
                    <span class="text-[8px] font-bold text-slate-300" style="writing-mode:vertical-rl">{{ \Carbon\Carbon::parse($d->date)->format('d/m') }}</span>
                </div>
                @empty
                <p class="text-xs text-slate-400 w-full text-center">Veri yok.</p>
                @endforelse
            </div>
            <div class="mt-3 flex justify-between text-[10px] font-bold text-slate-400">
                <span>Toplam: {{ $dailyTrend->sum('count') }} sipariş</span>
                <span>Ciro: ₺{{ number_format($dailyTrend->sum('amount'), 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
