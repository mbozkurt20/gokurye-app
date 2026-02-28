@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    YENİ <span class="text-slate-400">ÜRÜN TANIMLA</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Menünüze yeni bir lezzet ekliyorsunuz.
                </p>
            </div>
            <div>
                <ol class="breadcrumb !bg-transparent p-0 m-0 text-[10px] font-black uppercase tracking-tighter">
                    <li class="breadcrumb-item"><a href="/restaurant/products" class="text-slate-400">Ürünler</a></li>
                    <li class="breadcrumb-item active text-slate-800">Yeni Ekle</li>
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
            <div class="col-xl-9 col-lg-12">
                <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter m-0">Yeni Ürün Kayıt Formu</h4>
                    </div>

                    <div class="p-8">
                        <form method="post" action="{{route('restaurant.products.create')}}" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-5">
                                <div class="col-12 mb-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 block">Ürün Görseli</label>
                                    <div class="image-upload-wrapper !w-full !max-w-md !border-slate-100 bg-slate-50/50 hover:!border-slate-300 transition-all rounded-[30px]" id="imageUploadWrapper">
                                        <label for="imageInput" class="image-upload-label !py-12" id="uploadLabel">
                                            <i class="fa-solid fa-image text-4xl text-slate-200 mb-3"></i>
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Resim Seç veya Sürükle</span>
                                        </label>
                                        <input type="file" id="imageInput" name="image" accept="image/*" required>
                                        <img id="imagePreview" alt="Seçilen Resim" class="!rounded-2xl shadow-xl border-4 border-white">
                                        <button type="button" id="changeImageBtn" style="display:none;"
                                                class="mt-4 bg-slate-900 text-white text-[9px] font-black uppercase px-6 py-2 rounded-xl tracking-widest">Görseli Değiştir</button>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Kategori Seçiniz</label>
                                    <select class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" name="category_id" required>
                                        @foreach($categories as $categorie)
                                            <option value="{{$categorie->id}}"> {{$categorie->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Adı</label>
                                    <input type="text" id="productName" name="name" class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" placeholder="Ürün Adı" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Kodu (Otomatik)</label>
                                    <input type="text" id="productCode" name="code" class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" placeholder="Ürün Kodu" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Fiyatı (₺)</label>
                                    <input type="text" name="price" class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" placeholder="0.00" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Hazırlanma Süresi (DK)</label>
                                    <input type="number" name="preparation_time" value="0" class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Türü Seçiniz</label>
                                    <select class="w-full !rounded-2xl border-slate-100 bg-slate-50/50 p-4 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border" name="begenilen" required>
                                        <option selected value="deactive">Standart Ürün</option>
                                        <option value="active">Beğenilen Ürün</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Ürün Detayları</label>
                                    <textarea name="details" rows="8" class="w-full !rounded-[30px] border-slate-100 bg-slate-50/50 p-6 font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all border"></textarea>
                                </div>
                            </div>

                            <div class="mt-10 pt-8 border-t border-slate-50 flex items-center justify-end gap-4">
                                <a href="/restaurant/products" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Vazgeç</a>
                                <button type="submit" class="bg-[#0f172a] text-white px-12 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:scale-105 active:scale-95 transition-all flex items-center gap-3 border-0">
                                    <i class="fa-solid fa-check text-brand"></i> Ürünü Kaydet
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
        .image-upload-wrapper input[type="file"] { opacity: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0; z-index: 2; }
        .image-upload-label { display: flex; flex-direction: column; align-items: center; }
        #imagePreview { margin-top: 20px; max-width: 100%; max-height: 250px; display: none; margin-left: auto; margin-right: auto; }
    </style>

    <script>
        // Orijinal Image Preview Logic
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const uploadLabel = document.getElementById('uploadLabel');
        const changeImageBtn = document.getElementById('changeImageBtn');

        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    uploadLabel.style.display = 'none';
                    changeImageBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });

        changeImageBtn.addEventListener('click', () => { imageInput.click(); });

        // Orijinal Product Code Generation Logic
        document.addEventListener("DOMContentLoaded", function () {
            const nameInput = document.getElementById('productName');
            const codeInput = document.getElementById('productCode');

            function generateProductCode(name) {
                if (!name) return '';
                const namePart = name.trim().substring(0, 3).toUpperCase().replace(/\s/g, '');
                const chars = 'ABCDEFGHIKLMNOPQRSTUVWXYZ0123456789';
                let randomPart = '';
                for (let i = 0; i < 5; i++) {
                    randomPart += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return namePart + randomPart;
            }

            nameInput.addEventListener('input', () => {
                codeInput.value = generateProductCode(nameInput.value);
            });
        });
    </script>
@endsection
