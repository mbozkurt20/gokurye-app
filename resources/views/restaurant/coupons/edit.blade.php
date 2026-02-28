@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    KUPON <span class="text-slate-400">DÜZENLE</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    <span class="text-indigo-600">"{{$coupon->name}}"</span> tanımlamasını güncelliyorsunuz.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('restaurants/coupons') }}" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
            </div>
        </div>

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

        @if(session()->has('error'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 p-2 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Hata Oluştu</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">
                            {{ session()->get('error') }}
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-9">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] m-0">GÜNCELLEME FORMU</h4>
                        <span class="text-[10px] font-black bg-indigo-100 text-indigo-600 px-3 py-1 rounded-lg uppercase">ID: #{{$coupon->id}}</span>
                    </div>
                    <div class="p-8">
                        <form method="post" action="{{route('restaurant.coupons.update')}}">
                            @csrf
                            <input type="hidden" name="id" value="{{$coupon->id}}">
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">KUPON ADI</label>
                                        <div class="relative">
                                            <input required type="text" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all"
                                                   name="name" value="{{$coupon->name}}" placeholder="Kupon Adı">
                                            <i class="fa-solid fa-pen-to-square absolute right-5 top-1/2 -translate-y-1/2 text-slate-200"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">KUPON TUTARI (₺)</label>
                                        <div class="relative">
                                            <input required type="number" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all"
                                                   name="total_seller_amount" value="{{$coupon->total_seller_amount}}" placeholder="0.00">
                                            <i class="fa-solid fa-lira-sign absolute right-5 top-1/2 -translate-y-1/2 text-slate-200"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">AÇIKLAMA</label>
                                        <textarea class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all"
                                                  name="description" rows="3" placeholder="Opsiyonel açıklama">{{$coupon->description}}</textarea>
                                    </div>
                                </div>

                                <div class="col-12 flex justify-end gap-3 mt-4">
                                    <button type="submit" class="bg-indigo-600 text-white px-12 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all border-0">
                                        KAYDI GÜNCELLE
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="bg-slate-900 !rounded-[40px] p-8 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h5 class="text-xs font-black uppercase tracking-[0.2em] mb-4 text-indigo-400">BİLGİ</h5>
                        <p class="text-[11px] font-bold leading-relaxed text-slate-400">
                            Mevcut bir kuponun tutarını değiştirmek, o kuponu henüz kullanmamış olan tüm müşterileri etkileyecektir.
                        </p>
                        <div class="mt-8 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center">
                                <i class="fa-solid fa-clock-rotate-left text-xs text-indigo-400"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-500 uppercase m-0">Son Güncelleme</p>
                                <p class="text-[10px] font-bold text-slate-200 m-0">{{ $coupon->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
