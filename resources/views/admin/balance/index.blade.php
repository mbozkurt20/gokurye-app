@extends('admin.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/reports/index.css')}}">

    <div class="container-fluid">
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Kontör Yönetimi</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Bakiye ve İşlem Hareketleri</p>
            </div>
            <div class="bg-indigo-600 px-4 py-3 rounded-2xl shadow-lg shadow-indigo-100 flex items-center gap-3">
                <i class="fas fa-wallet text-white text-lg"></i>
                <div>
                    <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest leading-none mb-1">Güncel Bakiyeniz</p>
                    <p class="text-white font-black text-lg leading-none m-0">{{\Illuminate\Support\Facades\Auth::guard('admin')->user()->top_up_balance}} Kontör</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden">
                    <div class="card-body p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 uppercase m-0">Ödeme Bilgileri</h4>
                        </div>

                        <p class="text-xs font-bold text-slate-500 leading-relaxed mb-4">
                            Kontör bakiyeniz yetersiz olması durumunda siparişleriniz sisteme düşmeyecektir. Lütfen bakiyenizi düzenli kontrol ediniz.
                        </p>

                        @if(auth()->check() && !auth()->user()->phone)
                            <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl mb-4 text-center">
                                <p class="text-xs font-black text-amber-700 uppercase mb-3">Telefon Doğrulaması Gerekli</p>
                                <a class="w-full inline-block py-3 bg-amber-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest no-underline" href="/admin/profile">Profilime Git</a>
                            </div>
                        @else
                            <div class="bg-slate-50 p-4 rounded-[24px] border border-slate-100">
                                <form method="POST" action="{{ route('admin.payment.paytr.form') }}">
                                    @csrf
                                    <input type="hidden" id="top_up_price" value="{{ auth()->user()->top_up_price }}">

                                    <div class="mb-4">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Birim Fiyat</label>
                                        <div class="bg-white border border-slate-200 p-3 rounded-xl font-black text-slate-800 flex justify-between">
                                            <span>1 Kontör</span>
                                            <span class="text-indigo-600">{{ auth()->user()->top_up_price }} ₺</span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Yüklenecek Adet</label>
                                        <input required class="form-control !rounded-xl border-slate-200 font-bold py-3 px-4 focus:ring-indigo-500 focus:border-indigo-500"
                                               type="number" min="1" placeholder="Adet giriniz" id="top_up" name="top_up">
                                    </div>

                                    <div class="bg-indigo-50 p-4 rounded-2xl mb-4" id="hesaplamaBox" style="display: none;">
                                        <p class="text-[10px] font-black text-indigo-400 uppercase mb-1">Ödenecek Tutar</p>
                                        <span id="toplamTutar" class="text-xl font-black text-indigo-900"></span>
                                        <span class="text-indigo-900 font-black">₺</span>
                                    </div>

                                    <button class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-indigo-100 border-0 transition-all hover:scale-[1.02] active:scale-95" type="submit">
                                        ÖDEMEYE GEÇ
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

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
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0">Sahibi</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0">Fiyat / Adet</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0">Toplam</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0 text-center">Durum</th>
                                    <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase border-0 text-end">Detay</th>
                                </tr>
                                </thead>
                                <tbody class="border-0">
                                @forelse($movements as $movement)
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 px-4">
                                            <p class="text-xs font-black text-slate-800 m-0">{{ $movement->created_type == 'admin' ? 'Siz' : 'Yönetici' }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 m-0">{{ $movement->created_at->format('d.m.Y H:i') }}</p>
                                        </td>
                                        <td class="py-4 px-4">
                                            <p class="text-xs font-bold text-slate-500 m-0">{{ $movement->top_up_price }} ₺ x {{ $movement->top_up }}</p>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-xs font-black text-slate-900">{{ $movement->total_amount }} ₺</span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($movement->is_paid)
                                                <span class="px-2 py-1 bg-green-50 text-green-600 text-[9px] font-black rounded-lg uppercase tracking-widest border border-green-100">ÖDENDİ</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-50 text-red-600 text-[9px] font-black rounded-lg uppercase tracking-widest border border-red-100">BEKLİYOR</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-end">
                                            @if($movement->payment_details)
                                                <button class="w-8 h-8 bg-white border border-slate-100 rounded-lg text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm"
                                                        data-bs-toggle="modal" data-bs-target="#paymentDetailsModal{{ $movement->id }}">
                                                    <i class="fas fa-search text-[10px]"></i>
                                                </button>

                                                <div class="modal fade" id="paymentDetailsModal{{ $movement->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); z-index: 9999;">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content !rounded-[32px] border-0 shadow-2xl">
                                                            <div class="modal-header border-0 p-6 pb-0">
                                                                <h5 class="text-sm font-black text-slate-800 uppercase tracking-widest">Ödeme Özeti</h5>
                                                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-6 text-start">
                                                                @php $details = json_decode($movement->payment_details, true); @endphp
                                                                @if(is_array($details))
                                                                    <div class="space-y-3">
                                                                        <div class="flex justify-between border-b border-slate-50 pb-2">
                                                                            <span class="text-[10px] font-black text-slate-400 uppercase">Kart</span>
                                                                            <span class="text-xs font-black text-slate-800 uppercase">{{ $details['card']['cardBrand'] ?? '-' }} ({{ $details['card']['cardType'] ?? '-' }})</span>
                                                                        </div>
                                                                        <div class="flex justify-between border-b border-slate-50 pb-2">
                                                                            <span class="text-[10px] font-black text-slate-400 uppercase">3D Secure</span>
                                                                            <span class="text-xs font-black text-slate-800">{{ ($details['is3D'] ?? false) ? 'EVET' : 'HAYIR' }}</span>
                                                                        </div>
                                                                        <div class="flex justify-between border-b border-slate-50 pb-2">
                                                                            <span class="text-[10px] font-black text-slate-400 uppercase">Tutar</span>
                                                                            <span class="text-xs font-black text-indigo-600">{{ $details['amount'] ?? $movement->total_amount }} ₺</span>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <p class="text-xs font-bold text-slate-400 italic">Detay verisi bulunamadı.</p>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer border-0 p-6 pt-0">
                                                                <button type="button" class="w-full py-3 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase border-0" data-bs-dismiss="modal">KAPAT</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-[10px] font-black text-slate-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center">
                                            <p class="text-xs font-black text-slate-300 uppercase tracking-widest">Hareket Bulunmuyor</p>
                                        </td>
                                    </tr>
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
        const topUpInput = document.getElementById("top_up");
        const fiyatValue = document.getElementById("top_up_price").value;
        const fiyat = parseFloat(fiyatValue);
        const hesaplamaBox = document.getElementById("hesaplamaBox");
        const toplamTutar = document.getElementById("toplamTutar");

        function hesapla() {
            let adet = parseInt(topUpInput.value);
            let toplam = adet * fiyat;

            if (!isNaN(toplam) && adet > 0) {
                toplamTutar.innerText = toplam.toLocaleString('tr-TR', { minimumFractionDigits: 2 });
                hesaplamaBox.style.display = "block";
            } else {
                hesaplamaBox.style.display = "none";
            }
        }

        topUpInput.addEventListener("input", hesapla);
    </script>
@endsection
