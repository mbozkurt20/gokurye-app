@extends('admin.layouts.app')
@section('content')
<div class="container-fluid py-4">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                KURYELERİN <span class="text-slate-400">VARDİYA TAKİBİ</span>
            </h1>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }} tarihli çalışma süreleri
            </p>
        </div>
        <ol class="breadcrumb !bg-transparent p-0 m-0">
            <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter"><a href="/admin/couriers" class="text-slate-400">Kuryeler</a></li>
            <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter active text-slate-800">Vardiya</li>
        </ol>
    </div>

    {{-- Tarih Seçici --}}
    <form method="GET" action="{{ route('admin.couriers.shifts') }}" class="bg-white !rounded-[28px] p-5 shadow-sm border border-slate-100 mb-6">
        <div class="flex items-end gap-4">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Tarih</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control !rounded-2xl border-slate-100 bg-slate-50 p-3 font-bold text-slate-700 text-sm">
            </div>
            <button type="submit" class="bg-slate-900 text-white px-8 py-3 !rounded-2xl font-black text-[11px] uppercase tracking-widest">
                <i class="fa-solid fa-search mr-2"></i> Göster
            </button>
        </div>
    </form>

    @if(count($courierShifts) === 0)
        <div class="bg-white !rounded-[32px] border border-dashed border-slate-200 p-16 text-center">
            <i class="fa-solid fa-clock text-slate-200 text-5xl mb-4 block"></i>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Bu tarihte kayıt bulunamadı</p>
        </div>
    @else
    <div class="space-y-4">
        @foreach($courierShifts as $shift)
        @php
            $courier   = $shift['courier'];
            $totalWork = $shift['total_work'];
            $totalBreak= $shift['total_break'];
            $wH = floor($totalWork / 3600);
            $wM = floor(($totalWork % 3600) / 60);
            $bH = floor($totalBreak / 3600);
            $bM = floor(($totalBreak % 3600) / 60);
            $firstIn = $shift['first_in'] ? \Carbon\Carbon::parse($shift['first_in'])->format('H:i') : '—';
            $lastOut = $shift['last_out'] ? \Carbon\Carbon::parse($shift['last_out'])->format('H:i') : 'Devam';

            // Timeline: day starts at 07:00, ends at 24:00 = 17 hours = 1020 min span
            $dayStart = \Carbon\Carbon::parse($date . ' 07:00:00');
            $dayEnd   = \Carbon\Carbon::parse($date . ' 24:00:00');
            $span     = $dayEnd->diffInMinutes($dayStart);
        @endphp
        <div class="bg-white !rounded-[28px] shadow-sm border border-slate-100 overflow-hidden">
            {{-- Başlık --}}
            <div class="flex flex-wrap items-center gap-4 p-5 border-b border-slate-50">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center font-black text-indigo-600 text-sm flex-shrink-0">
                    {{ mb_substr($courier->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-slate-800 uppercase tracking-tight truncate">{{ $courier->name }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $courier->phone }}</p>
                </div>

                {{-- Özet --}}
                <div class="flex items-center gap-6 flex-wrap">
                    <div class="text-center">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Giriş</p>
                        <p class="text-sm font-black text-slate-700">{{ $firstIn }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Çıkış</p>
                        <p class="text-sm font-black text-slate-700">{{ $lastOut }}</p>
                    </div>
                    <div class="text-center px-4 py-2 rounded-2xl bg-emerald-50">
                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-0.5">Aktif</p>
                        <p class="text-sm font-black text-emerald-600">{{ $wH }}s {{ $wM }}dk</p>
                    </div>
                    <div class="text-center px-4 py-2 rounded-2xl bg-amber-50">
                        <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-0.5">Mola</p>
                        <p class="text-sm font-black text-amber-600">{{ $bH }}s {{ $bM }}dk</p>
                    </div>
                    <div class="text-center px-4 py-2 rounded-2xl bg-indigo-50">
                        <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-0.5">Teslimat</p>
                        <p class="text-sm font-black text-indigo-600">{{ $shift['movements']->where('status','service')->count() }} paket</p>
                    </div>
                </div>
            </div>

            {{-- Zaman Çizelgesi --}}
            <div class="px-5 py-4">
                <div class="relative h-7 bg-slate-100 rounded-full overflow-hidden">
                    @foreach($shift['movements'] as $mv)
                    @php
                        $mvStart = \Carbon\Carbon::parse($mv->started_at);
                        $mvEnd   = $mv->ended_at ? \Carbon\Carbon::parse($mv->ended_at) : now();
                        $startOffset = max(0, $dayStart->diffInMinutes($mvStart, false));
                        $duration    = max(0, $mvStart->diffInMinutes($mvEnd));
                        $leftPct     = min(100, ($startOffset / $span) * 100);
                        $widthPct    = min(100 - $leftPct, ($duration / $span) * 100);

                        $statusColor = match($mv->status) {
                            'active'  => '#10b981',
                            'service' => '#6366f1',
                            'break'   => '#f59e0b',
                            'passive' => '#94a3b8',
                            default   => '#e2e8f0',
                        };
                    @endphp
                    @if($widthPct > 0)
                    <div class="absolute top-0 h-full transition-all"
                         style="left:{{ $leftPct }}%; width:{{ $widthPct }}%; background:{{ $statusColor }}; opacity:0.85;"
                         title="{{ $mv->status }} — {{ $mvStart->format('H:i') }} → {{ $mv->ended_at ? $mvEnd->format('H:i') : 'devam' }}">
                    </div>
                    @endif
                    @endforeach
                </div>
                {{-- Saat etiketleri --}}
                <div class="flex justify-between mt-1">
                    @for($h = 7; $h <= 24; $h += 2)
                    <span class="text-[8px] font-bold text-slate-300">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</span>
                    @endfor
                </div>
                {{-- Legend --}}
                <div class="flex items-center gap-4 mt-2">
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span><span class="text-[9px] font-bold text-slate-400 uppercase">Müsait</span></div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-indigo-500 inline-block"></span><span class="text-[9px] font-bold text-slate-400 uppercase">Serviste</span></div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span><span class="text-[9px] font-bold text-slate-400 uppercase">Mola</span></div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-400 inline-block"></span><span class="text-[9px] font-bold text-slate-400 uppercase">Kapalı</span></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
