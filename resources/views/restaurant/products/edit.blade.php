@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    Ürün <span class="text-slate-400">Düzenle</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    #{{$product->id}} - {{$product->name}} kaydını güncelliyorsunuz.
                </p>
            </div>
            <div>
                <ol class="breadcrumb !bg-transparent p-0 m-0 text-[10px] font-black uppercase tracking-tighter">
                    <li class="breadcrumb-item"><a href="/restaurant/products" class="text-slate-400">Ürünler</a></li>
                    <li class="breadcrumb-item active text-slate-800">Düzenle</li>
                </ol>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 flex items-center justify-center text-white text-xs"><i class="fa-solid fa-check"></i></div>
                <span class="text-xs font-black text-emerald-700 uppercase tracking-tight">{{ session()->get('message') }}</span>
            </div>
        @endif

        @if(session()->has('test'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3 text-rose-700">
                <div class="w-8 h-8 rounded-xl bg-rose-500 flex items-center justify-center text-white text-xs"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <span class="text-xs font-black uppercase tracking-tight">{{ session()->get('test') }}</span>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-9 col-lg-12">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0 text-center md:text-left">Ürün Detay Formu</h4>
                    </div>

                    <div class="p-8">
                        <form method="post" action="{{route('restaurant.products.update')}}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$product->id}}">

                            <div class="row g-5">
                                <div class="col-12 mb-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 block">Ürün Görseli</label>
                                    <div class="image-upload-wrapper !w-full !max-w-md !border-slate-100 bg-slate-50/50 hover:!border-slate-300 transition-all rounded-[30px]" onclick="document.getElementById('imageInput').click();">
                                        <label for="imageInput" class="image-upload-label !py-10">
                                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-300 mb-3"></i>
                                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Resim Seç veya Sürükle</span>
                                        </label>
                                        <input type="file" id="imageInput" name="image" accept="image/*">
                                        <img id="imagePreview" alt="Seçilen Resim" class="!rounded-2xl shadow-xl border-4 border-white" style="display: {{ $product->image ? 'block' : 'none' }}; src: {{ $product->image }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Kategori Seçiniz</label>
                                    <select class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" name="category_id" required>
                                        @foreach($categories as $category)
                                            <option @if($category->id == $product->category_id) selected @endif value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Adı</label>
                                    <input value="{{$product->name}}" type="text" name="name" class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" placeholder="Ürün Adı" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Kodu</label>
                                    <input value="{{$product->code}}" type="text" name="code" class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" placeholder="Ürün Kodu" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Fiyatı (₺)</label>
                                    <input value="{{$product->price}}" type="text" name="price" class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" placeholder="0.00" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Hazırlanma Süresi (DK)</label>
                                    <input type="number" name="preparation_time" value="{{$product->preparation_time}}" class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" placeholder="30" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Türü</label>
                                    <select class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" name="begenilen" required>
                                        <option {{$product->begenilen == 'deactive' ? 'selected' : null}} value="deactive">Standart Ürün</option>
                                        <option {{$product->begenilen == 'active' ? 'selected' : null}} value="active">Beğenilen Ürün</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Detayları</label>
                                    <textarea name="details" rows="6" class="w-full !rounded-[30px] border-slate-100 bg-slate-50/50 p-6 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border">{{$product->details}}</textarea>
                                </div>
                            </div>

                            <div class="mt-10 pt-8 border-t border-slate-50 flex items-center justify-end gap-4">
                                <a href="/restaurant/products" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Vazgeç</a>
                                <button type="submit" class="bg-[#0f172a] text-white px-12 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:scale-105 active:scale-95 transition-all flex items-center gap-3">
                                    <i class="fa-solid fa-floppy-disk text-brand"></i> Kaydet ve Güncelle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .image-upload-wrapper { position: relative; cursor: pointer; border: 2px dashed; text-align: center; }
        .image-upload-wrapper input[type="file"] { display: none; }
        .image-upload-label { display: flex; flex-direction: column; align-items: center; }
        #imagePreview { margin-top: 20px; max-width: 100%; max-height: 250px; display: block; margin-left: auto; margin-right: auto; }
    </style>

    <script>
        // Orijinal Resim Preview Scripti
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Orijinal Otomatik Kod Oluşturma Scripti
        const nameInput = document.querySelector('input[name="name"]');
        const codeInput = document.querySelector('input[name="code"]');
        function generateCode(name) {
            if (!name) return '';
            const shortName = name.trim().substring(0, 3).toUpperCase().replace(/\s/g, '');
            const randomStr = Math.random().toString(36).substring(2, 5).toUpperCase();
            return shortName + randomStr;
        }
        nameInput.addEventListener('input', () => {
            if(codeInput.value == "" || codeInput.value == "{{$product->code}}") {
                codeInput.value = generateCode(nameInput.value);
            }
        });
    </script>
@endsection
