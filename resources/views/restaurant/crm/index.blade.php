@extends('restaurant.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Müşteri CRM</h2>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Müşteri Segmentasyonu & Analizi</p>
        </div>
        <div class="flex items-center gap-2">
            @foreach([7=>'7 Gün', 30=>'30 Gün', 90=>'90 Gün'] as $d => $label)
            <a href="?days={{ $d }}"
               class="text-xs font-black uppercase tracking-wide px-4 py-2 rounded-2xl border transition-all
                      {{ $days == $d ? 'bg-brand text-white border-brand' : 'border-slate-200 text-slate-500 hover:border-brand hover:text-brand' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Segment Kartlar --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Toplam Müşteri</p>
            <p class="text-3xl font-black text-brand">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-emerald-50 rounded-3xl border border-emerald-100 shadow-sm p-5">
            <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">Yeni Müşteri</p>
            <p class="text-3xl font-black text-emerald-600">{{ $stats['new'] }}</p>
            <p class="text-[10px] text-emerald-400 font-semibold mt-1">İlk kez sipariş verenler</p>
        </div>
        <div class="bg-indigo-50 rounded-3xl border border-indigo-100 shadow-sm p-5">
            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Tekrar Eden</p>
            <p class="text-3xl font-black text-indigo-600">{{ $stats['returning'] }}</p>
            <p class="text-[10px] text-indigo-400 font-semibold mt-1">Dönemde siparişi olanlar</p>
        </div>
        <div class="bg-rose-50 rounded-3xl border border-rose-100 shadow-sm p-5">
            <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">Kaybedilen</p>
            <p class="text-3xl font-black text-rose-600">{{ $stats['lost'] }}</p>
            <p class="text-[10px] text-rose-400 font-semibold mt-1">Son dönemde sipariş yok</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Top 10 Müşteri --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">
                <i class="fa-solid fa-crown text-amber-400 mr-1"></i> En Sadık 10 Müşteri
            </p>
            <div class="space-y-2">
                @forelse($top10 as $i => $c)
                <div class="flex items-center gap-3 bg-slate-50 rounded-2xl px-4 py-3">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-black
                        {{ $i < 3 ? 'bg-amber-100 text-amber-600' : 'bg-slate-200 text-slate-500' }}">{{ $i+1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ $c['name'] }}</p>
                        <p class="text-[10px] text-slate-400 font-semibold">{{ $c['phone'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-brand">{{ $c['count'] }} sipariş</p>
                        <p class="text-[10px] font-semibold text-emerald-500">₺{{ number_format($c['total'], 2, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Veri yok.</p>
                @endforelse
            </div>
        </div>

        {{-- Yeni Müşteri Trendi --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">
                <i class="fa-solid fa-user-plus text-emerald-400 mr-1"></i> Günlük Yeni Müşteri Kaydı
            </p>
            @php $maxNew = $dailyNew->max('count') ?: 1; @endphp
            <div class="flex items-end gap-1 h-28">
                @forelse($dailyNew as $d)
                <div class="flex-1 flex flex-col items-center gap-1" title="{{ $d->date }}: {{ $d->count }} müşteri">
                    <div class="w-full rounded-t-md bg-emerald-400"
                         style="height:{{ round($d->count / $maxNew * 100) }}%; min-height:2px"></div>
                    <span class="text-[8px] font-bold text-slate-300" style="writing-mode:vertical-rl">{{ \Carbon\Carbon::parse($d->date)->format('d/m') }}</span>
                </div>
                @empty
                <p class="text-xs text-slate-400 w-full text-center mt-4">Bu dönemde yeni müşteri kaydı yok.</p>
                @endforelse
            </div>
            @if($dailyNew->count() > 0)
            <p class="mt-3 text-[11px] font-bold text-slate-400 text-center">Toplam {{ $dailyNew->sum('count') }} yeni müşteri</p>
            @endif
        </div>
    </div>

</div>
@endsection
