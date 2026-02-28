@extends('restaurant.layouts.app')

@section('content')

    @if(session()->has('success'))
        <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-indigo-500 shadow-2xl rounded-2xl p-4">
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
        <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 p-2 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Hata Oluştu</p>
                    <p class="text-sm font-bold text-slate-700 leading-tight">{{ session()->get('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    KATEGORİ <span class="text-slate-400">YÖNETİMİ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    {{ count($categories) }} kategori · Sıralamayı sürükle-bırak ile değiştirebilirsiniz
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div id="saveOrderBtn" style="display:none;">
                    <button onclick="saveOrder()"
                            class="inline-flex items-center gap-2 bg-emerald-500 text-white px-6 py-3 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg hover:bg-emerald-600 transition-all">
                        <i class="fa-solid fa-floppy-disk"></i>
                        SIRALAMAYI KAYDET
                    </button>
                </div>
                <a href="{{ route('restaurant.categories.new') }}"
                   class="inline-flex items-center gap-3 bg-[#0f172a] text-white px-8 py-3.5 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-slate-200 hover:scale-105 transition-all">
                    <i class="fas fa-plus text-brand"></i>
                    YENİ KATEGORİ
                </a>
            </div>
        </div>

        <div id="sortable-categories" class="space-y-2">
            @foreach($categories as $categorie)
                <div class="sortable-item bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 px-5 py-4 hover:shadow-md transition-all cursor-grab active:cursor-grabbing"
                     data-id="{{ $categorie->id }}" id="data_{{ $categorie->id }}">

                    {{-- Drag Handle --}}
                    <div class="drag-handle text-slate-200 hover:text-slate-400 transition-colors flex-shrink-0">
                        <i class="fa-solid fa-grip-vertical text-sm"></i>
                    </div>

                    {{-- Sıra Numarası --}}
                    <div class="order-badge w-7 h-7 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0">
                        <span class="text-[10px] font-black text-slate-400 order-num">{{ $loop->iteration }}</span>
                    </div>

                    {{-- Resim / Avatar --}}
                    @if($categorie->image)
                        <img src="{{ asset($categorie->image) }}"
                             class="w-12 h-12 rounded-xl object-cover border border-slate-100 flex-shrink-0" alt="{{ $categorie->name }}">
                    @else
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-400 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0">
                            {{ mb_substr($categorie->name, 0, 1) }}
                        </div>
                    @endif

                    {{-- İsim --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-slate-700 uppercase tracking-tight truncate">{{ $categorie->name }}</p>
                        <p class="text-[10px] font-bold text-slate-300 uppercase mt-0.5">ID: #{{ $categorie->id }} · {{ $categorie->created_at->format('d.m.Y') }}</p>
                    </div>

                    {{-- Aksiyonlar --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('restaurant.categories.edit', ['id' => $categorie->id]) }}"
                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all">
                            <i class="fa-solid fa-pencil text-xs"></i>
                        </a>
                        <button onclick="DeleteFunction({{ $categorie->id }})"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all border-0">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            @endforeach

            @if(count($categories) === 0)
                <div class="bg-white rounded-3xl border border-dashed border-slate-200 p-16 text-center">
                    <i class="fa-solid fa-layer-group text-slate-200 text-4xl mb-4 block"></i>
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Henüz kategori eklenmemiş</p>
                    <a href="{{ route('restaurant.categories.new') }}" class="inline-flex items-center gap-2 mt-4 bg-brand text-white px-6 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest">
                        <i class="fa-solid fa-plus"></i> İlk Kategoriyi Ekle
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        var orderChanged = false;

        var sortable = Sortable.create(document.getElementById('sortable-categories'), {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'opacity-40',
            onEnd: function () {
                orderChanged = true;
                document.getElementById('saveOrderBtn').style.display = 'block';
                // Sıra numaralarını güncelle
                document.querySelectorAll('#sortable-categories .order-num').forEach(function(el, i) {
                    el.textContent = i + 1;
                });
            }
        });

        function saveOrder() {
            var ids = [];
            document.querySelectorAll('#sortable-categories .sortable-item').forEach(function(el) {
                ids.push(el.getAttribute('data-id'));
            });

            fetch('{{ route('restaurant.categories.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: ids })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.success) {
                    orderChanged = false;
                    document.getElementById('saveOrderBtn').style.display = 'none';
                    Swal.fire({
                        title: 'Kaydedildi',
                        text: 'Kategori sıralaması güncellendi.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-[32px]' }
                    });
                }
            });
        }

        function DeleteFunction(id) {
            Swal.fire({
                title: 'SİLME İŞLEMİ',
                html: '<p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest">Bu kategoriyi silmek istediğinize emin misiniz?</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'EVET, SİL',
                cancelButtonText: 'VAZGEÇ',
                background: '#ffffff',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[40px] border-0 p-12 shadow-2xl',
                    title: 'font-black tracking-tighter text-slate-800 text-2xl uppercase',
                    confirmButton: 'bg-slate-900 text-white px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2 hover:bg-rose-500 transition-all',
                    cancelButton: 'bg-slate-100 text-slate-400 px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/restaurant/categories/delete/' + id,
                        success: function(data) {
                            if (data === "OK") {
                                Swal.fire({ title: 'SİLİNDİ', icon: 'success', customClass: { popup: 'rounded-[40px]' } });
                                $("#data_" + id).fadeOut();
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
