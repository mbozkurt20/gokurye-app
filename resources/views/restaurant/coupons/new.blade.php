@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    YENİ <span class="text-slate-400">KUPON TANIMLA</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sisteme yeni bir kampanya veya indirim kodu girişi yapın.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{  url('restaurants/coupons')  }}" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-indigo-500 shadow-2xl rounded-2xl p-5 transform transition-all duration-500 animate-bounce-short">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-50 p-2 rounded-xl">
                        <i class="fas fa-check-circle text-indigo-600 text-lg"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">SİSTEM MESAJI</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('message') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-300 hover:text-slate-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
        @endif

        @if(session()->has('test'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-rose-500 shadow-2xl rounded-2xl p-5 transform transition-all">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-rose-50 p-2 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-rose-600 text-lg"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">UYARI</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('test') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-300 hover:text-slate-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-9">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">KUPON BİLGİLERİ</h4>
                    </div>
                    <div class="p-8">
                        <form method="post" action="{{route('restaurant.coupons.create')}}">
                            @csrf
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">KUPON ADI / KODU</label>
                                        <div class="relative">
                                            <input required type="text" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all placeholder:text-slate-300"
                                                   name="name" placeholder="Örn: BAHAR2026">
                                            <i class="fa-solid fa-tag absolute right-5 top-1/2 -translate-y-1/2 text-slate-200"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">İNDİRİM TUTARI (₺)</label>
                                        <div class="relative">
                                            <input required type="number" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all placeholder:text-slate-300"
                                                   name="total_seller_amount" placeholder="0.00">
                                            <i class="fa-solid fa-lira-sign absolute right-5 top-1/2 -translate-y-1/2 text-slate-200"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">KAMPANYA AÇIKLAMASI (OPSİYONEL)</label>
                                        <textarea class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all placeholder:text-slate-300"
                                                  name="description" rows="3" placeholder="Bu kupon ne için tanımlanıyor?"></textarea>
                                    </div>
                                </div>

                                <div class="col-12 flex justify-end gap-3 mt-4">
                                    <a href="{{  url('/restaurants/coupons') }}" class="px-8 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-400 bg-slate-100 hover:bg-slate-200 transition-all decoration-none">
                                        VAZGEÇ
                                    </a>
                                    <button type="submit" class="bg-[#0f172a] text-white px-12 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-slate-200 hover:scale-[1.02] active:scale-95 transition-all border-0">
                                        KUPONU KAYDET VE YAYINLA
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="bg-indigo-600 !rounded-[40px] p-8 text-white shadow-xl shadow-indigo-900/20 relative overflow-hidden">
                    <i class="fa-solid fa-circle-info absolute -right-4 -top-4 text-8xl text-indigo-500 opacity-30"></i>
                    <h5 class="text-xs text-white uppercase tracking-[0.2em] mb-4 relative z-10">İPUCU</h5>
                    <p class="text-[11px] font-bold leading-relaxed text-indigo-100 relative z-10">
                        Kupon adı müşterilerin sepet ekranında gireceği kod olacağı için kısa ve hatırlanabilir olmasına özen gösterin.
                    </p>
                    <div class="mt-6 pt-6 border-t border-indigo-500 relative z-10">
                        <p class="text-[9px] font-black text-indigo-200 uppercase mb-2">Önerilen Formatlar</p>
                        <ul class="list-none p-0 m-0 space-y-2">
                            <li class="text-[10px] font-bold bg-indigo-700/50 p-2 rounded-lg">HOŞGELDİN20</li>
                            <li class="text-[10px] font-bold bg-indigo-700/50 p-2 rounded-lg">İFTAR30</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
