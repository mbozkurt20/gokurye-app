@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    KATEGORİ <span class="text-slate-400">DÜZENLE</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    #{{$categorie->id}} numaralı kategoriyi güncelliyorsunuz.
                </p>
            </div>
            <div>
                <ol class="breadcrumb !bg-transparent p-0 m-0">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter"><a href="/restaurant/categories" class="text-slate-400">Kategoriler</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter active text-slate-800">Düzenle</li>
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
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0 text-center md:text-left">Kategori Detaylarını Güncelle</h4>
                    </div>

                    <div class="p-8">
                        <form method="post" action="{{route('restaurant.categories.update')}}">
                            @csrf
                            <input type="hidden" name="id" value="{{$categorie->id}}">

                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Kategori Adı</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-pen-to-square text-slate-300 group-focus-within:text-slate-400 transition-colors"></i>
                                        </div>
                                        <input type="text" name="name" value="{{$categorie->name}}" required
                                               class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 pl-12 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border"
                                               placeholder="Kategori Adı">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">POS Görünüm Sırası</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-list-ol text-slate-300 group-focus-within:text-slate-400 transition-colors"></i>
                                        </div>
                                        <input type="number" name="desk" value="{{$categorie->desk}}"
                                               class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 pl-12 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border"
                                               placeholder="Örn: 5">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-10 pt-6 border-t border-slate-50 flex items-center justify-end gap-3">
                                <a href="/restaurant/categories" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Vazgeç</a>
                                <button type="submit"
                                        class="bg-[#0f172a] text-white px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-slate-200 hover:scale-105 active:scale-95 transition-all flex items-center gap-3">
                                    <i class="fa-solid fa-arrows-rotate text-brand"></i>
                                    KAYDI GÜNCELLE
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4">
                <div class="bg-slate-900 !rounded-[40px] p-8 shadow-2xl shadow-slate-200">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-brand mb-6">
                        <i class="fa-solid fa-eye text-lg"></i>
                    </div>
                    <h5 class="text-sm font-black text-white uppercase tracking-tighter mb-2">Şu Anki Durum</h5>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-6 italic">Sistem Kayıt Detayı</p>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Eski Adı:</span>
                            <span class="text-xs font-bold text-white uppercase">{{$categorie->name}}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Eski Sıra:</span>
                            <span class="text-xs font-bold text-white">{{$categorie->desk ?? 'Tanımsız'}}</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Eklenme:</span>
                            <span class="text-xs font-bold text-white">{{$categorie->created_at->format('d.m.Y H:i')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
