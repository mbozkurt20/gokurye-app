@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none">
                    RESTAURANT <span class="text-slate-400">YÖNETİMİ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Sistemde toplam {{ count($restaurants) }} kayıtlı işletme bulunmaktadır.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="location.reload()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                    <i class="fa-solid fa-rotate text-sm"></i>
                </button>
                <a href="{{ route('admin.restaurants.new') }}"
                   class="inline-flex items-center gap-3 bg-[#0f172a] text-white px-8 py-3.5 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-slate-200 hover:scale-105 transition-all">
                    <i class="fas fa-store text-indigo-400"></i>
                    YENİ İŞLETME EKLE
                </a>
            </div>
        </div>

        <div class="mb-8">
            <div class="relative group">
                <input type="text" id="custom-filter-restaurants"
                       class="w-full !rounded-[24px] border-0 bg-white shadow-sm p-5 pl-14 font-bold text-slate-500 focus:ring-2 focus:ring-slate-100 transition-all placeholder:text-slate-300"
                       placeholder="Restaurant adı, işyeri kodu veya yetkili ara...">
                <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-400 transition-colors"></i>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
            <div class="table-responsive p-4">
                <table id="restaurants-table" class="table !mb-0 border-0">
                    <thead>
                    <tr class="border-0">
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0">İşletme Bilgisi</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">Yetkili</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">İletişim</th>
                        <th class="py-6 px-8 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-right">Yönetim</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    @foreach($restaurants as $restaurant)
                        <tr id="data_{{ $restaurant->id }}" class="group transition-all hover:bg-slate-50/50">
                            <td class="py-6 px-8 border-0">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center font-black text-sm group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-colors">
                                        {{ substr($restaurant->restaurant_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-700 m-0 uppercase tracking-tight">{{ $restaurant->restaurant_name }}</p>
                                        <p class="text-[10px] font-bold text-slate-300 m-0 uppercase mt-1">KOD: #{{ $restaurant->restaurant_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <p class="text-xs font-black text-slate-600 m-0 uppercase">{{ $restaurant->name }}</p>
                                <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">Mağaza Yetkilisi</span>
                            </td>
                            <td class="py-6 px-8 border-0 text-center">
                                <p class="text-xs font-black text-slate-600 m-0">{{ $restaurant->phone }}</p>
                                <p class="text-[9px] font-bold text-slate-300 m-0 lowercase">{{ $restaurant->email }}</p>
                            </td>
                            <td class="py-6 px-8 border-0">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.restaurants.edit', ['id' => $restaurant->id]) }}"
                                       class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-indigo-600 transition-all">
                                        <i class="fa-solid fa-pencil text-xs"></i>
                                    </a>
                                    <button onclick="DeleteFunction({{ $restaurant->id }})"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all border-0">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @if(count($restaurants) == 0)
                    <div class="py-20 text-center">
                        <i class="fas fa-store-slash text-slate-100 fa-4x mb-4"></i>
                        <p class="text-slate-300 font-black uppercase tracking-[0.2em] text-xs">Kayıtlı Restaurant Bulunmamaktadır</p>
                    </div>
                @endif
            </div>

            <div id="custom-pagination-container" class="px-10 py-8 flex flex-col md:flex-row items-center justify-between gap-4 border-t border-slate-50">
                <div id="table-info-box" class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]"></div>
                <div id="table-pagination-box" class="flex items-center gap-2"></div>
            </div>
        </div>
    </div>

    <style>
        #restaurants-table_wrapper .dataTables_filter,
        #restaurants-table_wrapper .dataTables_info,
        #restaurants-table_wrapper .dataTables_paginate { display: none; }

        .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 38px !important;
            height: 38px !important;
            border-radius: 12px !important;
            font-size: 11px !important;
            font-weight: 900 !important;
            background: transparent !important;
            border: 0 !important;
            margin: 0 2px !important;
            cursor: pointer !important;
            color: #cbd5e1 !important;
            transition: all 0.2s;
        }
        .paginate_button.current {
            background: #0f172a !important;
            color: white !important;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2) !important;
        }
        .paginate_button:hover:not(.current) {
            background: #f8fafc !important;
            color: #4f46e5 !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            var table = $('#restaurants-table').DataTable({
                order: [[0, 'asc']],
                dom: 'rtip',
                pageLength: 10,
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json" },
                drawCallback: function() {
                    $('#table-pagination-box').html($('.dataTables_paginate').html());
                    const info = table.page.info();
                    $('#table-info-box').html(`Showing ${info.start + 1} to ${info.end} of ${info.recordsTotal} entries`);
                }
            });

            $('#custom-filter-restaurants').on('keyup', function() {
                table.search(this.value).draw();
            });

            $(document).on('click', '#table-pagination-box .paginate_button', function() {
                if($(this).hasClass('next')) table.page('next').draw('page');
                else if($(this).hasClass('previous')) table.page('previous').draw('page');
                else table.page($(this).data('dt-idx')).draw('page');
            });
        });

        function DeleteFunction(id) {
            Swal.fire({
                title: 'İŞLETME SİLİNSİN Mİ?',
                html: '<p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest">Bu restaurantı sildiğinizde bağlı veriler etkilenebilir.</p>',
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
                        url: '/admin/restaurants/delete/' + id,
                        success: function(data) {
                            if (data === "OK") {
                                Swal.fire({ title: 'SİLİNDİ', icon: 'success', customClass: { popup: 'rounded-[40px]' } });
                                $("#data_" + id).fadeOut();
                            } else {
                                Swal.fire("Hata", "Bu işletme silinemez.", "error");
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
