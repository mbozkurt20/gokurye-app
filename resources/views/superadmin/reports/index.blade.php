@extends('superadmin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    PERFORMANS <span class="text-slate-400">ANALİZLERİ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sistem genelindeki tüm hareketlerin periyodik dökümü.
                </p>
            </div>
        </div>

        <div class="bg-[#0f172a] !rounded-[30px] shadow-xl p-6 mb-8 border border-slate-800">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">BAŞLANGIÇ TARİHİ</label>
                    <input type="date" name="start_date" class="form-control !rounded-xl border-slate-700 bg-slate-800 text-white font-bold text-xs p-3 focus:ring-2 focus:ring-indigo-500 transition-all"
                           value="{{ \Carbon\Carbon::parse($startDate)->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">BİTİŞ TARİHİ</label>
                    <input type="date" name="end_date" class="form-control !rounded-xl border-slate-700 bg-slate-800 text-white font-bold text-xs p-3 focus:ring-2 focus:ring-indigo-500 transition-all"
                           value="{{ \Carbon\Carbon::parse($endDate)->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">GRUPLAMA PERİYODU</label>
                    <select name="group_by" class="form-control !rounded-xl border-slate-700 bg-slate-800 text-white font-bold text-xs p-3 focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Günlük Analiz</option>
                        <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Haftalık Analiz</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-black text-[10px] uppercase tracking-[0.2em] py-3.5 !rounded-xl transition-all shadow-lg shadow-indigo-900/20 border-0" type="submit">
                        VERİLERİ FİLTRELE
                    </button>
                </div>
            </form>
        </div>

        <div class="row g-4">
            @foreach ($metrics as $index => $metric)
                <div class="col-xl-4 col-md-6">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden hover:shadow-md transition-all group">
                        <div class="p-8 pb-0 flex justify-between items-start">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">{{ $metric['title'] }}</p>
                                <h3 class="text-3xl font-black text-slate-800 tracking-tighter">{{ $metric['value'] }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                <i class="fa-solid fa-chart-line text-sm"></i>
                            </div>
                        </div>
                        <div class="px-4 pb-4" style="height:180px;">
                            <canvas id="chart-{{ $index }}"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const metrics = @json($metrics);

            metrics.forEach((metric, index) => {
                const canvas = document.getElementById(`chart-${index}`);
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                // Indigo gradyan oluşturma
                const gradient = ctx.createLinearGradient(0, 0, 0, 150);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.1)');
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

                const labels = Object.keys(metric.data || {});
                const values = Object.values(metric.data || {});

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: metric.title,
                            data: values,
                            borderColor: '#4f46e5',
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            fill: true,
                            backgroundColor: gradient,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 10, weight: 'bold' },
                                bodyFont: { size: 12, weight: 'bold' },
                                padding: 12,
                                cornerRadius: 10,
                                displayColors: false
                            }
                        },
                        scales: {
                            x: {
                                display: false
                            },
                            y: {
                                display: false,
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
