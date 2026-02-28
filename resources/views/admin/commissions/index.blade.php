@extends('admin.layouts.app')
@section('content')
<div class="container-fluid py-4">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                PLATFORM <span class="text-slate-400">KOMİSYON TAKİBİ</span>
            </h1>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                Platform bazlı brüt ciro, komisyon kesintisi ve net kazanç
            </p>
        </div>
        <ol class="breadcrumb !bg-transparent p-0 m-0">
            <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter"><a href="/admin" class="text-slate-400">Admin</a></li>
            <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter active text-slate-800">Komisyonlar</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-emerald-500 shadow-2xl rounded-2xl p-4">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-50 p-2 rounded-xl text-emerald-600"><i class="fas fa-check-circle"></i></div>
                <p class="text-sm font-bold text-slate-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Tarih Filtresi --}}
    <form method="GET" action="{{ route('admin.commissions.index') }}" class="bg-white !rounded-[32px] p-6 shadow-sm border border-slate-100 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Başlangıç</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control !rounded-2xl border-slate-100 bg-slate-50 p-3 font-bold text-slate-700 text-sm">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Bitiş</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control !rounded-2xl border-slate-100 bg-slate-50 p-3 font-bold text-slate-700 text-sm">
            </div>
            <button type="submit" class="bg-slate-900 text-white px-8 py-3 !rounded-2xl font-black text-[11px] uppercase tracking-widest">
                <i class="fa-solid fa-filter mr-2"></i> Filtrele
            </button>
        </div>
    </form>

    {{-- Özet Kartlar --}}
    <div class="row g-4 mb-6">
        @php
        $summaryCards = [
            ['label' => 'Toplam Sipariş', 'value' => number_format($totals['count']),            'icon' => 'fa-box',          'color' => 'indigo', 'suffix' => 'adet'],
            ['label' => 'Brüt Ciro',      'value' => number_format($totals['gross'], 2),      'icon' => 'fa-money-bill',   'color' => 'blue',   'suffix' => '₺'],
            ['label' => 'Platform Kesi.', 'value' => number_format($totals['commission'], 2), 'icon' => 'fa-scissors',     'color' => 'rose',   'suffix' => '₺'],
            ['label' => 'Net Kazanç',     'value' => number_format($totals['net'], 2),        'icon' => 'fa-circle-check', 'color' => 'emerald','suffix' => '₺'],
        ];
        @endphp
        @foreach($summaryCards as $c)
        <div class="col-xl-3 col-sm-6">
            <div class="bg-white !rounded-[28px] p-5 shadow-sm border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-{{ $c['color'] }}-50 text-{{ $c['color'] }}-600 rounded-2xl flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fas {{ $c['icon'] }}"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $c['label'] }}</p>
                        <p class="text-xl font-black text-slate-800 leading-none tracking-tighter">{{ $c['value'] }} <span class="text-sm font-bold text-slate-400">{{ $c['suffix'] }}</span></p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Platform Tablosu --}}
        <div class="col-xl-8">
            <div class="bg-white !rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50">
                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Platform Detayları</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-50">
                                <th class="text-left p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Platform</th>
                                <th class="text-right p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sipariş</th>
                                <th class="text-right p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Brüt Ciro</th>
                                <th class="text-right p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Komisyon %</th>
                                <th class="text-right p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kesinti</th>
                                <th class="text-right p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $platformColors = [
                                'telefon'     => ['bg' => '#6366f1', 'light' => '#6366f112'],
                                'getir'       => ['bg' => '#ff6900', 'light' => '#ff690012'],
                                'trendyol'    => ['bg' => '#f27115', 'light' => '#f2711512'],
                                'yemeksepeti' => ['bg' => '#fa0000', 'light' => '#fa000012'],
                                'migros'      => ['bg' => '#ef4444', 'light' => '#ef444412'],
                            ];
                            $platformIcons = [
                                'telefon'     => null,
                                'getir'       => 'getir.png',
                                'trendyol'    => 'trendyol.png',
                                'yemeksepeti' => 'yemeksepeti.png',
                                'migros'      => 'migros.png',
                            ];
                            @endphp
                            @foreach($results as $r)
                            @php $c = $platformColors[$r['key']] ?? ['bg' => '#64748b', 'light' => '#64748b12']; @endphp
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $c['light'] }};">
                                            @if($platformIcons[$r['key']])
                                                <img src="{{ asset('theme/images/platforms/'.$platformIcons[$r['key']]) }}" class="w-5 h-5 object-contain">
                                            @else
                                                <i class="fa-solid fa-phone" style="color:{{ $c['bg'] }}; font-size:12px;"></i>
                                            @endif
                                        </div>
                                        <span class="text-sm font-black text-slate-700">{{ $r['label'] }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-right text-sm font-black text-slate-600">{{ number_format($r['count']) }}</td>
                                <td class="p-4 text-right text-sm font-black text-slate-700">{{ number_format($r['gross'], 2) }} ₺</td>
                                <td class="p-4 text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-black" style="background:{{ $c['light'] }}; color:{{ $c['bg'] }};">
                                        %{{ $r['rate'] }}
                                    </span>
                                </td>
                                <td class="p-4 text-right text-sm font-black text-rose-500">-{{ number_format($r['commission'], 2) }} ₺</td>
                                <td class="p-4 text-right text-sm font-black text-emerald-600">{{ number_format($r['net'], 2) }} ₺</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50">
                                <td class="p-4 text-xs font-black text-slate-700 uppercase">Toplam</td>
                                <td class="p-4 text-right text-sm font-black text-slate-700">{{ number_format($totals['count']) }}</td>
                                <td class="p-4 text-right text-sm font-black text-slate-700">{{ number_format($totals['gross'], 2) }} ₺</td>
                                <td class="p-4"></td>
                                <td class="p-4 text-right text-sm font-black text-rose-600">-{{ number_format($totals['commission'], 2) }} ₺</td>
                                <td class="p-4 text-right text-sm font-black text-emerald-700">{{ number_format($totals['net'], 2) }} ₺</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Komisyon Oranı Ayarları --}}
        <div class="col-xl-4">
            <div class="bg-slate-900 !rounded-[32px] p-6 shadow-2xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-brand">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <h5 class="text-sm font-black text-white uppercase tracking-tighter m-0">Komisyon Oranları</h5>
                        <p class="text-[10px] font-bold text-slate-500 uppercase m-0">% olarak girin</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.commissions.update') }}">
                    @csrf
                    <div class="space-y-3">
                        @foreach($results as $r)
                        <div class="flex items-center justify-between gap-3">
                            <label class="text-[11px] font-black text-slate-300 uppercase tracking-wide flex-1">{{ $r['label'] }}</label>
                            <div class="flex items-center gap-1">
                                <input type="number" name="rates[{{ $r['key'] }}]"
                                       value="{{ $r['rate'] }}" min="0" max="100" step="0.5"
                                       class="w-20 bg-white/10 border border-white/10 text-white font-black text-sm text-center rounded-xl px-2 py-2 focus:outline-none focus:border-brand transition-colors">
                                <span class="text-slate-400 font-black text-sm">%</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="submit" class="w-full mt-6 bg-brand text-white py-3 !rounded-2xl font-black text-[11px] uppercase tracking-widest hover:opacity-90 transition-opacity">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
