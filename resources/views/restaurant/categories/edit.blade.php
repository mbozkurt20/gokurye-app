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
            <div class="col-xl-6 col-lg-8">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0 text-center md:text-left">Kategori Detaylarını Güncelle</h4>
                    </div>

                    <div class="p-8">
                        <form method="post" action="{{route('restaurant.categories.update')}}" enctype="multipart/form-data">
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
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Kategori Görseli</label>

                                    @if($categorie->image)
                                        <div id="currentImageWrapper" class="mb-4 flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                                            <img src="{{ asset($categorie->image) }}" alt="{{ $categorie->name }}"
                                                 class="w-16 h-16 rounded-xl object-cover border border-slate-200">
                                            <div class="flex-1">
                                                <p class="text-xs font-black text-slate-700 m-0">Mevcut Görsel</p>
                                                <p class="text-[10px] font-bold text-slate-400 m-0 mt-0.5">Yeni görsel seçerseniz değiştirilir</p>
                                            </div>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="remove_image" value="1"
                                                       class="rounded" onchange="this.checked ? (document.getElementById('currentImageWrapper').style.opacity='0.4') : (document.getElementById('currentImageWrapper').style.opacity='1')">
                                                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Kaldır</span>
                                            </label>
                                        </div>
                                    @endif

                                    <div id="imagePreviewWrapper" class="hidden mb-4">
                                        <div class="relative inline-block">
                                            <img id="imagePreview" src="#" alt="Önizleme"
                                                 class="w-24 h-24 rounded-2xl object-cover border-2 border-slate-100 shadow-sm">
                                            <button type="button" onclick="clearImage()"
                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center border-0 shadow-md">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <label for="imageInput"
                                           class="flex items-center gap-3 cursor-pointer w-full border-2 border-dashed border-slate-200 !rounded-2xl p-5 hover:border-brand/40 hover:bg-brand/5 transition-all group">
                                        <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center group-hover:bg-brand/10 transition-colors flex-shrink-0">
                                            <i class="fa-solid fa-image text-slate-300 group-hover:text-brand transition-colors"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-slate-600 m-0">{{ $categorie->image ? 'Yeni Görsel Seç' : 'Görsel Seç veya Sürükle' }}</p>
                                            <p class="text-[10px] font-bold text-slate-300 m-0 mt-0.5">JPG, PNG, WEBP — Maks. 2MB</p>
                                        </div>
                                    </label>
                                    <input type="file" id="imageInput" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
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
                        @if($categorie->image)
                            <div class="flex justify-between items-center py-3 border-b border-white/5">
                                <span class="text-[10px] font-black text-slate-400 uppercase">Görsel:</span>
                                <img src="{{ asset($categorie->image) }}" class="w-10 h-10 rounded-xl object-cover" alt="">
                            </div>
                        @endif
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Adı:</span>
                            <span class="text-xs font-bold text-white uppercase">{{$categorie->name}}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Sıra:</span>
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

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreviewWrapper').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearImage() {
            document.getElementById('imageInput').value = '';
            document.getElementById('imagePreviewWrapper').classList.add('hidden');
        }
    </script>
@endsection
