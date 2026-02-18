@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    GENEL <span class="text-slate-400">RAPORLAR & ANALİZ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    İşletme performansını verilerle takip edin.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="location.reload()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                    <i class="fa-solid fa-rotate text-sm"></i>
                </button>
            </div>
        </div>

        <div class="bg-white !rounded-[32px] shadow-sm border border-slate-50 p-6 mb-8">
            <form method="GET" class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none"
                           value="{{ \Carbon\Carbon::parse($startDate)->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Bitiş Tarihi</label>
                    <input type="date" name="end_date" class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none"
                           value="{{ \Carbon\Carbon::parse($endDate)->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Gruplama</label>
                    <select name="group_by" class="form-control !rounded-xl border-slate-100 font-bold text-xs p-3 shadow-none">
                        <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>GÜNLÜK RAPOR</option>
                        <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>HAFTALIK RAPOR</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="w-full bg-[#0f172a] text-white py-3.5 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-slate-200 hover:scale-[1.02] transition-all border-0" type="submit">
                        <i class="fa fa-filter me-2 text-indigo-400"></i> VERİLERİ ANALİZ ET
                    </button>
                </div>
            </form>
        </div>

        <div class="row g-4">
            @foreach ($metrics as $index => $metric)
                <div class="col-md-4">
                    <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden group hover:border-indigo-100 transition-all">
                        <div class="p-8 pb-0">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">{{ $metric['title'] }}</p>
                            <h3 class="text-3xl font-black text-slate-800 tracking-tighter m-0 group-hover:text-indigo-600 transition-colors">
                                {{ $metric['value'] }}
                            </h3>
                        </div>

                        <div class="px-4 pb-6 pt-4" style="height:220px;">
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

                // Gradient Oluşturma
                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
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
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#4f46e5',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3
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
                                bodyFont: { size: 12, weight: '900' },
                                padding: 12,
                                displayColors: false,
                                borderRadius: 12
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    autoSkip: true,
                                    maxTicksLimit: 5,
                                    font: { size: 9, weight: '700' },
                                    color: '#cbd5e1'
                                }
                            },
                            y: {
                                grid: { color: '#f8fafc' },
                                ticks: {
                                    font: { size: 9, weight: '700' },
                                    color: '#cbd5e1',
                                    maxTicksLimit: 5
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
