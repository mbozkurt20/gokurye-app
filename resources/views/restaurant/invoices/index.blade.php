@extends('restaurant.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Faturalar</h2>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Günlük & Sipariş Bazlı Fatura</p>
        </div>
        <form method="GET" class="flex items-center gap-3">
            <input type="month" name="month" value="{{ $month }}"
                   class="border border-slate-200 rounded-2xl px-4 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none focus:border-brand">
            <button class="bg-brand text-white text-xs font-black uppercase tracking-wide px-5 py-2.5 rounded-2xl">Filtrele</button>
        </form>
    </div>

    {{-- Ay özeti --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ay Toplamı</p>
            <p class="text-3xl font-black text-emerald-500">₺{{ number_format($monthTotal, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Toplam Sipariş</p>
            <p class="text-3xl font-black text-brand">{{ $monthCount }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Çalışılan Gün</p>
            <p class="text-3xl font-black text-slate-800">{{ $dailySummary->count() }}</p>
        </div>
    </div>

    {{-- Günlük liste --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <p class="text-xs font-black text-slate-500 uppercase tracking-widest">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('tr')->isoFormat('MMMM YYYY') }} — Gün Bazlı Faturalar
            </p>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <th class="px-6 py-3 text-left">Tarih</th>
                    <th class="px-4 py-3 text-center">Sipariş Sayısı</th>
                    <th class="px-4 py-3 text-right">Günlük Ciro</th>
                    <th class="px-4 py-3 text-right">Ort. Sipariş</th>
                    <th class="px-4 py-3 text-center">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($dailySummary as $day)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-slate-800">
                            {{ \Carbon\Carbon::parse($day->date)->locale('tr')->isoFormat('DD MMMM YYYY, dddd') }}
                        </p>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="text-sm font-black text-brand">{{ $day->count }}</span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <span class="text-sm font-black text-emerald-600">₺{{ number_format($day->total, 2, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <span class="text-sm font-semibold text-slate-500">₺{{ number_format($day->total / $day->count, 2, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('restaurant.invoices.daily', ['date' => $day->date]) }}"
                               target="_blank"
                               class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wide text-indigo-500 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-xl transition-colors">
                                <i class="fa-solid fa-file-invoice text-xs"></i> Günlük Fatura
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">
                        Bu ayda teslim edilmiş sipariş bulunmuyor.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
