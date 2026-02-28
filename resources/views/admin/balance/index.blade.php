@extends('admin.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/reports/index.css')}}">

    <div class="container-fluid">
        {{-- Başlık ve Bakiye Kartı --}}
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Kontör Yönetimi</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Bakiye ve İşlem Hareketleri</p>
            </div>
            <div class="bg-indigo-600 px-4 py-3 rounded-2xl shadow-lg shadow-indigo-100 flex items-center gap-3">
                <i class="fas fa-wallet text-white text-lg"></i>
                <div>
                    <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest leading-none mb-1">Güncel Bakiyeniz</p>
                    <p class="text-white font-black text-lg leading-none m-0">
                        {{ Auth::guard('admin')->user()->top_up_balance }} Kontör
                    </p>
                </div>
            </div>
        </div>

        {{-- Bildirimler --}}
        @if(session()->has('success'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-indigo-500 shadow-2xl rounded-2xl p-4 animate-bounce-short">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-50 p-2 rounded-xl text-indigo-600"><i class="fas fa-check-circle"></i></div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase">BAŞARILI</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            {{-- Ödeme Formu --}}
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden">
                    <div class="card-body p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 uppercase m-0">Bakiye Yükle</h4>
                        </div>

                        @if(Auth::guard('admin')->check() && !Auth::guard('admin')->user()->phone)
                            <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl text-center">
                                <p class="text-xs font-bold text-amber-700 mb-3">Ödeme yapabilmek için telefon numaranızı doğrulamanız gerekmektedir.</p>
                                <a class="btn btn-warning w-full rounded-xl font-black text-[10px] uppercase" href="/admin/profile">Profilime Git</a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.payment.paytr.form') }}">
                                @csrf
                                <input type="hidden" id="top_up_price" value="{{ Auth::guard('admin')->user()->top_up_price }}">

                                <div class="mb-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Birim Fiyat</label>
                                    <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl font-black text-slate-700">
                                        1 Kontör = {{ Auth::guard('admin')->user()->top_up_price }} ₺
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Yüklenecek Adet</label>
                                    <input required class="form-control !rounded-xl border-slate-200 font-bold py-3"
                                           type="number" min="1" placeholder="Örn: 100" id="top_up" name="top_up">
                                </div>

                                <div class="bg-indigo-50 p-4 rounded-2xl mb-4" id="hesaplamaBox" style="display: none;">
                                    <p class="text-[10px] font-black text-indigo-400 uppercase mb-1">Ödenecek Toplam</p>
                                    <span id="toplamTutar" class="text-xl font-black text-indigo-900"></span>
                                    <span class="text-indigo-900 font-black">₺</span>
                                </div>

                                <button class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg border-0 transition-all hover:bg-indigo-700" type="submit">
                                    ÖDEMEYE GEÇ
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Hareket Tablosu --}}
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden">
                    <div class="card-header bg-white border-b border-slate-50 p-5">
                        <h4 class="text-sm font-black text-slate-800 uppercase m-0">Kontör Hareketleri</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle m-0">
                                <thead class="bg-slate-50">
                                <tr>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase">Sahibi/Tarih</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase">Birim Fiyat</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase">Adet</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase">Toplam</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-center">Durum</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase text-center">Ödeme Tarihi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($movements as $movement)
                                    @php $details = is_string($movement->payment_details) ? json_decode($movement->payment_details, true) : null; @endphp
                                    <tr class="border-b border-slate-50">
                                        <td class="py-4 px-4">
                                            <p class="text-xs font-black text-slate-800 m-0">{{ $movement->created_type == 'admin' ? 'Siz' : 'Yönetici' }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 m-0">{{ $movement->created_at->format('d.m.Y H:i') }}</p>
                                        </td>
                                        <td class="py-4 px-4 text-xs font-bold text-slate-500">
                                            {{ $movement->top_up_price }} ₺
                                        </td>
                                        <td class="py-4 px-4 text-xs font-bold text-slate-500">
                                            {{ $movement->top_up }} adet
                                        </td>
                                        <td class="py-4 px-4 text-xs font-black text-slate-900">
                                            {{ $movement->total_amount }} ₺
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($movement->is_paid)
                                                <span class="px-2 py-1 bg-green-50 text-green-600 text-[9px] font-black rounded-lg uppercase">ÖDENDİ</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-50 text-red-600 text-[9px] font-black rounded-lg uppercase">BEKLİYOR</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center text-xs font-bold text-slate-500">
                                            @if(!empty($details['paid_at']))
                                                {{ \Carbon\Carbon::parse($details['paid_at'])->format('d.m.Y H:i') }}
                                            @else
                                                <span class="text-slate-300">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-10 text-center text-slate-300 font-black uppercase text-[10px]">Henüz bir hareket yok.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript kısmında çalışan mantığı koruyoruz
        const topUpInput = document.getElementById("top_up");
        const fiyatElement = document.getElementById("top_up_price");
        const hesaplamaBox = document.getElementById("hesaplamaBox");
        const toplamTutar = document.getElementById("toplamTutar");

        if(topUpInput && fiyatElement) {
            const fiyat = parseFloat(fiyatElement.value.replace(',', '.'));

            topUpInput.addEventListener("input", function() {
                let adet = parseInt(this.value);
                let toplam = adet * fiyat;

                if (!isNaN(toplam) && adet > 0) {
                    toplamTutar.innerText = toplam.toLocaleString('tr-TR', { minimumFractionDigits: 2 });
                    hesaplamaBox.style.display = "block";
                } else {
                    hesaplamaBox.style.display = "none";
                }
            });
        }
    </script>
@endsection
