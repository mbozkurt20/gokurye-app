@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    YENİ <span class="text-slate-400">KATEGORİ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sisteme yeni bir ürün grubu tanımlıyorsunuz.
                </p>
            </div>
            <div>
                <ol class="breadcrumb !bg-transparent p-0 m-0">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter"><a href="/restaurant/categories" class="text-slate-400">Kategoriler</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter active text-slate-800">Yeni Ekle</li>
                </ol>
            </div>
        </div>
        @if(session()->has('message'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-green-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out animate-bounce-short">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 p-2 rounded-xl">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">İşlem Başarılı</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">
                            {{ session()->get('message') }}
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session()->has('test'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 p-2 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Hata Oluştu</p>
                        <p class="text-sm font-bold text-slate-700 leading-tight">
                            {{ session()->get('test') }}
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-6 col-lg-8">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Kategori Bilgileri</h4>
                    </div>

                    <div class="p-8">
                        <form method="post" action="{{route('restaurant.categories.create')}}">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Kategori Adı</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-tag text-slate-300 group-focus-within:text-slate-400 transition-colors"></i>
                                        </div>
                                        <input required type="text" name="name"
                                               class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 pl-12 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all placeholder:text-slate-300 border"
                                               placeholder="Örn: Pizzalar, İçecekler...">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">POS Ekran Sıralaması</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-arrow-down-1-9 text-slate-300 group-focus-within:text-slate-400 transition-colors"></i>
                                        </div>
                                        <input type="number" name="desk"
                                               class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 pl-12 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all placeholder:text-slate-300 border"
                                               placeholder="Örn: 1">
                                    </div>
                                    <p class="text-[9px] font-bold text-slate-300 uppercase mt-3 italic leading-relaxed">
                                        * Kategorinin satış ekranındaki yerini belirler. Boş bırakılırsa sona eklenir.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-10 pt-6 border-t border-slate-50 flex items-center justify-end gap-3">
                                <a href="/restaurant/categories" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Vazgeç</a>
                                <button type="submit"
                                        class="bg-[#0f172a] text-white px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-slate-200 hover:scale-105 active:scale-95 transition-all flex items-center gap-3">
                                    <i class="fa-solid fa-floppy-disk text-brand"></i>
                                    KATEGORİYİ KAYDET
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4">
                <div class="bg-indigo-50/50 !rounded-[40px] p-8 border border-indigo-100/50">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-400 shadow-sm mb-6">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <h5 class="text-sm font-black text-indigo-900 uppercase tracking-tighter mb-4">İpucu</h5>
                    <p class="text-xs font-bold text-indigo-700/70 leading-relaxed m-0 uppercase tracking-tight">
                        Kategorileri net ve kısa isimlerle tanımlamak, POS ekranında garsonların ürünü daha hızlı bulmasını sağlar.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
