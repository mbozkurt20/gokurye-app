@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">Restoran <span class="text-slate-400">Ürünleri</span></h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">Ürün Listesi ve Yönetimi</p>
            </div>
            <div>
                <ol class="breadcrumb !bg-transparent p-0 m-0">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter"><a href="javascript:void(0)" class="text-slate-400">Ürünler</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-tighter active text-slate-800">Liste</li>
                </ol>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="relative w-full md:max-w-md group">
                <input type="text" id="custom-filter-product"
                       class="w-full !rounded-[24px] border-0 bg-white shadow-sm p-4 pl-12 font-bold text-slate-500 focus:ring-2 focus:ring-slate-100 transition-all placeholder:text-slate-300"
                       placeholder="Ürün ara..">
                <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
            </div>
            <a href="{{route('restaurant.products.new')}}"
               class="bg-[#0f172a] text-white px-8 py-3.5 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-slate-200 hover:scale-105 transition-all flex items-center gap-3">
                <i class="fas fa-shopping-bag text-brand"></i> Yeni Ekle
            </a>
        </div>

        <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="exampleProduct" class="table !mb-0 border-0" style="min-width: 845px">
                        <thead>
                        <tr class="border-0">
                            <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0" style="width: 15%">Resim</th>
                            <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0" style="width: 15%">Ürün Kodu</th>
                            <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0" style="width: 30%">Ürün Adı</th>
                            <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0" style="width: 15%">Satış Fiyatı</th>
                            <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0" style="width: 15%">Hazırlanma Süresi</th>
                            <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-right" style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                        @foreach($products as $product)
                            <tr id="data_{{$product->id}}-product" class="group hover:bg-slate-50/50 transition-all align-middle">
                                <td class="py-4 px-4 border-0">
                                    <div class="w-14 h-14 bg-slate-50 rounded-xl overflow-hidden border border-slate-100 flex-shrink-0">
                                        <img alt="" src="{{$product->image}}" class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="py-4 px-4 border-0 font-bold text-slate-400 text-xs">{{$product->code}}</td>
                                <td class="py-4 px-4 border-0 font-black text-slate-700 text-sm uppercase tracking-tight">{{$product->name}}</td>
                                <td class="py-4 px-4 border-0">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[11px] font-black">{{$product->price}} ₺</span>
                                </td>
                                <td class="py-4 px-4 border-0">
                                    <span class="text-xs font-bold text-slate-500"><i class="far fa-clock me-1 opacity-50"></i>{{$product->preparation_time}} dk.</span>
                                </td>
                                <td class="py-4 px-4 border-0 text-right">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{route('restaurant.products.edit', ['id' => $product->id])}}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white transition-all">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </a>
                                        <button onclick="DeleteFunction({{$product->id}})"
                                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all border-0">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="pagination-container" class="px-8 py-6 border-t border-slate-50 flex items-center justify-between">
                <div id="table-info" class="text-[10px] font-black text-slate-300 uppercase tracking-widest"></div>
                <div id="table-paginate"></div>
            </div>
        </div>
    </div>

    <style>
        /* DataTable Default Hide */
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { display: none; }

        /* Paginate Styling */
        .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 36px !important;
            height: 36px !important;
            border-radius: 10px !important;
            font-size: 11px !important;
            font-weight: 900 !important;
            margin: 0 2px !important;
            cursor: pointer !important;
            color: #94a3b8 !important;
            transition: all 0.2s;
        }
        .paginate_button.current {
            background: #0f172a !important;
            color: white !important;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function(){
            var table = $('#exampleProduct').DataTable({
                dom: 'rtip',
                language: {
                    search: "Ara:",
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json",
                    lengthMenu: "Sayfa başına _MENU_ kayıt",
                    info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
                    infoEmpty: "Gösterilecek kayıt yok",
                    paginate: { next: "Sonraki", previous: "Önceki" }
                },
                drawCallback: function() {
                    $('#table-paginate').html($('.dataTables_paginate').html());
                    const info = table.page.info();
                    $('#table-info').html(`GÖSTERİLEN: ${info.start + 1} - ${info.end} / TOPLAM: ${info.recordsTotal}`);
                }
            });

            $('#custom-filter-product').keyup(function() {
                table.search(this.value).draw();
            });

            $(document).on('click', '#table-paginate .paginate_button', function() {
                if($(this).hasClass('next')) table.page('next').draw('page');
                else if($(this).hasClass('previous')) table.page('previous').draw('page');
                else table.page($(this).data('dt-idx')).draw('page');
            });
        });

        function DeleteFunction(id) {
            Swal.fire({
                title: 'ÜRÜNÜ SİL',
                html: '<p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest">Bu ürünü silmek istediğinize emin misiniz?</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'EVET, SİL',
                cancelButtonText: 'VAZGEÇ',
                background: '#ffffff',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[40px] border-0 p-12 shadow-2xl',
                    title: 'font-black tracking-tighter text-slate-800 text-2xl uppercase',
                    confirmButton: 'bg-slate-900 text-white px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2 shadow-lg shadow-slate-200',
                    cancelButton: 'bg-slate-100 text-slate-400 px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/restaurant/products/delete/' + id,
                        success: function(data) {
                            if (data == "OK") {
                                Swal.fire({ title: 'SİLİNDİ', icon: 'success', customClass: { popup: 'rounded-[40px]' } });
                                $("#data_" + id + "-product").fadeOut(function() { $(this).remove(); });
                            } else if (data == "NO") {
                                Swal.fire({ title: 'UYARI', text: 'Bu Ürüne ait sipariş bulunmaktadır.', icon: 'warning', customClass: { popup: 'rounded-[40px]' } });
                            }
                        },
                        error: function() {
                            Swal.fire({ title: 'HATA', text: 'İşlem başarısız.', icon: 'error', customClass: { popup: 'rounded-[40px]' } });
                        }
                    });
                }
            });
        }
    </script>
@endsection
