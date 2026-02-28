@extends('superadmin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    ÖDEME <span class="text-slate-400">ENTEGRASYONU</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    PayTR API anahtarlarını ve ödeme akışı ayarlarını yönetin.
                </p>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-2xl border border-emerald-100">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">SSL Bağlantısı Aktif</span>
            </div>
        </div>

        <div class="row ">
            <div class="col-xl-6 col-lg-8">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center">
                                <i class="fa-solid fa-credit-card text-indigo-600"></i>
                            </div>
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">PAYTR API AYARLARI</h4>
                        </div>
                        @if(config('payment.paytr.sandbox'))
                            <span class="px-3 py-1 bg-amber-100 text-amber-600 text-[9px] font-black rounded-lg uppercase tracking-widest border border-amber-200">
                                Sandbox Aktif
                            </span>
                        @endif
                    </div>

                    <div class="p-8">
                        <form method="POST" action="{{ route('superadmin.payment.paytr.update') }}">
                            @csrf

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block pl-1">MERCHANT ID</label>
                                    <input type="text" name="merchant_id"
                                           class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all"
                                           value="{{ old('merchant_id', config('payment.paytr.merchant_id')) }}"
                                           placeholder="Örn: 123456">
                                </div>

                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block pl-1">MERCHANT KEY</label>
                                    <input type="text" name="merchant_key"
                                           class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all font-mono"
                                           value="{{ old('merchant_key', config('payment.paytr.merchant_key')) }}"
                                           placeholder="API Anahtarınız">
                                </div>

                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block pl-1">MERCHANT SALT</label>
                                    <input type="password" name="merchant_salt"
                                           class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all font-mono"
                                           value="{{ old('merchant_salt', config('payment.paytr.merchant_salt')) }}"
                                           placeholder="Güvenlik Salt Değeri">
                                </div>

                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block pl-1">ÇALIŞMA MODU (SANDBOX)</label>
                                    <select name="sandbox" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                                        <option value="0" {{ !config('payment.paytr.sandbox') ? 'selected' : '' }}>
                                            ⚡ CANLI MOD (Üretim Ortamı)
                                        </option>
                                        <option value="1" {{ config('payment.paytr.sandbox') ? 'selected' : '' }}>
                                            🛠 TEST MODU (Sandbox)
                                        </option>
                                    </select>
                                    <p class="text-[9px] font-bold text-slate-400 mt-2 px-1 uppercase tracking-tighter">
                                        * Test modu açıkken gerçek tahsilat yapılmaz.
                                    </p>
                                </div>

                                <div class="col-12 mt-6">
                                    <button class="w-full bg-[#0f172a] text-white py-5 !rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:scale-[1.01] active:scale-95 transition-all border-0">
                                        ENTEGRASYON BİLGİLERİNİ GÜNCELLE
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-6 p-6 bg-indigo-50/50 rounded-[30px] border border-indigo-100/50">
                    <div class="flex gap-4">
                        <i class="fa-solid fa-circle-info text-indigo-500 mt-1"></i>
                        <p class="text-[11px] font-medium text-indigo-900 leading-relaxed">
                            Bu alandaki bilgiler doğrudan ödeme akışını etkiler. Merchant Key ve Salt bilgilerini üçüncü şahıslarla paylaşmayınız. Değişiklik yaptıktan sonra test ödemesi yapmanız önerilir.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
