@extends('restaurant.layouts.app')
@section('content')
<div class="container-fluid pb-5">

    <div class="mb-6">
        <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Çalışma Saatleri</h2>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Restoran Açık / Kapalı Saatleri</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-3 mb-5 text-sm font-semibold text-emerald-700">
        <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('restaurant.working-hours.update') }}">
        @csrf
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-5">
            <div class="space-y-4">
                @foreach($days as $key => $label)
                @php
                    $h = $workingHours[$key] ?? ['open' => true, 'open_time' => '09:00', 'close_time' => '22:00'];
                    $isOpen = $h['open'] ?? true;
                @endphp
                <div class="flex items-center gap-4 py-4 border-b border-slate-50 last:border-0">
                    {{-- Gün adı --}}
                    <div class="w-28 flex-shrink-0">
                        <p class="text-sm font-black text-slate-700">{{ $label }}</p>
                    </div>

                    {{-- Açık / Kapalı toggle --}}
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                        <input type="checkbox" name="hours[{{ $key }}][open]" value="1" class="sr-only peer day-toggle"
                               data-day="{{ $key }}" {{ $isOpen ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:bg-brand
                                    peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                    after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all relative"></div>
                        <span class="ml-2 text-xs font-bold text-slate-500 day-label" data-day="{{ $key }}">
                            {{ $isOpen ? 'Açık' : 'Kapalı' }}
                        </span>
                    </label>

                    {{-- Saat aralığı --}}
                    <div class="flex items-center gap-3 day-hours-{{ $key }} {{ !$isOpen ? 'opacity-30 pointer-events-none' : '' }}">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Açılış</span>
                            <input type="time" name="hours[{{ $key }}][open_time]"
                                   value="{{ $h['open_time'] ?? '09:00' }}"
                                   class="border border-slate-200 rounded-xl px-3 py-1.5 text-sm font-semibold text-slate-700 focus:outline-none focus:border-brand">
                        </div>
                        <span class="text-slate-300 font-bold">—</span>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Kapanış</span>
                            <input type="time" name="hours[{{ $key }}][close_time]"
                                   value="{{ $h['close_time'] ?? '22:00' }}"
                                   class="border border-slate-200 rounded-xl px-3 py-1.5 text-sm font-semibold text-slate-700 focus:outline-none focus:border-brand">
                        </div>
                    </div>

                    {{-- Kapalı badge --}}
                    <div class="day-closed-{{ $key }} {{ $isOpen ? 'hidden' : '' }}">
                        <span class="text-[10px] font-black px-3 py-1 rounded-full bg-slate-100 text-slate-400 uppercase tracking-wide">Kapalı</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" onclick="setAllDays(true)"
                    class="text-xs font-black uppercase tracking-wide px-5 py-2.5 rounded-2xl border border-slate-200 text-slate-600 hover:border-brand hover:text-brand transition-all">
                Hepsini Aç
            </button>
            <button type="button" onclick="setAllDays(false)"
                    class="text-xs font-black uppercase tracking-wide px-5 py-2.5 rounded-2xl border border-slate-200 text-slate-600 hover:border-rose-400 hover:text-rose-400 transition-all">
                Hepsini Kapat
            </button>
            <button type="submit"
                    class="bg-brand text-white text-sm font-black uppercase tracking-wide px-8 py-2.5 rounded-2xl shadow-lg shadow-brand/20">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Kaydet
            </button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.day-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        var day = this.dataset.day;
        var hours = document.querySelector('.day-hours-' + day);
        var closed = document.querySelector('.day-closed-' + day);
        var label = document.querySelector('.day-label[data-day="' + day + '"]');

        if (this.checked) {
            hours.classList.remove('opacity-30', 'pointer-events-none');
            closed.classList.add('hidden');
            label.textContent = 'Açık';
        } else {
            hours.classList.add('opacity-30', 'pointer-events-none');
            closed.classList.remove('hidden');
            label.textContent = 'Kapalı';
        }
    });
});

function setAllDays(open) {
    document.querySelectorAll('.day-toggle').forEach(function(toggle) {
        toggle.checked = open;
        toggle.dispatchEvent(new Event('change'));
    });
}
</script>
@endsection
