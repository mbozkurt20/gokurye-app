@extends('restaurant.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                QR <span class="text-slate-400">MENÜ</span>
            </h1>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                Müşterileriniz bu kodu okutarak dijital menünüze ulaşabilir.
            </p>
        </div>
    </div>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-50 p-10 text-center">

            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl mb-6">
                <i class="fas fa-qrcode text-2xl"></i>
            </div>

            <h2 class="text-lg font-black text-slate-800 tracking-tighter uppercase mb-2">Dijital Menü QR Kodu</h2>
            <p class="text-xs font-bold text-slate-400 mb-8">Aşağıdaki QR kodu indirip masalarınıza yerleştirebilirsiniz.</p>

            {{-- QR Code --}}
            <div class="flex justify-center mb-6">
                <div class="bg-white border-4 border-slate-100 rounded-3xl p-4 inline-block shadow-inner" id="qr-container">
                    {!! $qrSvg !!}
                </div>
            </div>

            {{-- Menu URL --}}
            <div class="bg-slate-50 rounded-2xl px-5 py-3 mb-8 flex items-center gap-3">
                <i class="fas fa-link text-slate-400 text-xs"></i>
                <span class="text-xs font-bold text-slate-500 break-all">{{ $menuUrl }}</span>
                <button onclick="copyUrl()" class="ml-auto flex-shrink-0 text-indigo-500 hover:text-indigo-700 transition-colors" title="Kopyala">
                    <i class="fas fa-copy text-xs"></i>
                </button>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button onclick="downloadQr()"
                    class="flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-indigo-200 transition-all active:scale-[0.97]">
                    <i class="fas fa-download"></i> SVG İndir
                </button>
                <a href="{{ $menuUrl }}" target="_blank"
                   class="flex items-center justify-center gap-2 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-[11px] uppercase tracking-widest transition-all active:scale-[0.97]">
                    <i class="fas fa-external-link-alt"></i> Menüyü Önizle
                </a>
            </div>
        </div>

        <div class="mt-6 bg-amber-50 border border-amber-100 rounded-3xl p-6">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-lightbulb text-xs"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-amber-800 mb-1">Nasıl Kullanılır?</p>
                    <ul class="text-xs font-bold text-amber-700 space-y-1 list-disc list-inside">
                        <li>SVG dosyasını indirip profesyonel baskıda kullanabilirsiniz.</li>
                        <li>Müşterileriniz telefon kameraları ile kodu okutarak menünüze erişebilir.</li>
                        <li>Menü içeriğinizi güncelledikçe QR kod değişmez, içerik otomatik güncellenir.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadQr() {
    const svgEl = document.querySelector('#qr-container svg');
    const svgData = new XMLSerializer().serializeToString(svgEl);
    const blob = new Blob([svgData], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'qr-menu.svg';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function copyUrl() {
    navigator.clipboard.writeText('{{ $menuUrl }}').then(() => {
        alert('Link kopyalandı!');
    });
}
</script>
@endsection
