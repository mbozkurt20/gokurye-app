@extends('superadmin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    SİSTEM <span class="text-slate-400">YÖNETİCİLERİ</span>
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                    Partner ve bölge yöneticilerinin tam listesi.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="location.reload();" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                    <i class="fas fa-sync text-sm"></i>
                </button>
                <a href="{{ route('superadmin.admin_create') }}" class="inline-flex items-center gap-3 bg-[#0f172a] text-white px-8 py-3.5 !rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-slate-200 hover:scale-105 transition-all border-0 decoration-none">
                    <i class="fas fa-user-plus text-indigo-400"></i>
                    YENİ YÖNETİCİ EKLE
                </a>
            </div>
        </div>

        <div class="mb-8">
            <div class="relative group">
                <input type="text" id="custom-filter"
                       class="w-full !rounded-[24px] border-0 bg-white shadow-sm p-5 pl-14 font-bold text-slate-500 focus:ring-2 focus:ring-indigo-100 transition-all placeholder:text-slate-300"
                       placeholder="Partner adı, email veya bölgeye göre filtrele...">
                <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-400 transition-colors"></i>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
            <div class="table-responsive p-4">
                <table id="courierTable" class="table !mb-0 border-0">
                    <thead>
                    <tr class="border-0">
                        <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0">YÖNETİCİ BİLGİLERİ</th>
                        <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">BÖLGE</th>
                        <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">KONTÖR ÜCRETİ</th>
                        <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-center">DURUM</th>
                        <th class="py-6 px-4 text-[11px] font-black text-slate-800 uppercase tracking-widest border-0 text-right">İŞLEMLER</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    @foreach($admins as $admin)
                        <tr id="data_{{ $admin->id }}" class="group transition-all hover:bg-slate-50/50">
                            <td class="py-6 px-4 border-0">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex flex-col items-center justify-center shadow-sm group-hover:bg-indigo-600 transition-all">
                                        <span class="text-[10px] font-black leading-none">{{ $admin->code }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-700 m-0 uppercase tracking-tight">{{ $admin->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-300 m-0 lowercase mt-1">{{ $admin->email }} <span class="mx-1">•</span> {{ $admin->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-4 border-0 text-center">
                                <span class="block text-xs font-black text-slate-600 uppercase">
                                    {{ $admin->city_id ? \App\Models\City::find($admin->city_id)->name : '-' }}
                                </span>
                                <span class="block text-[9px] font-bold text-slate-300 uppercase mt-1">
                                    {{ $admin->district_id ? \App\Models\District::find($admin->district_id)->name : '-'}}
                                </span>
                            </td>
                            <td class="py-6 px-4 border-0 text-center">
                                <span class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-xs font-black tracking-tight border border-indigo-100/50">
                                    {{ number_format($admin->top_up_price,2) }} ₺
                                </span>
                            </td>
                            <td class="py-6 px-4 border-0 text-center">
                                <div class="form-check form-switch flex justify-center p-0">
                                    <input class="form-check-input !w-12 !h-6 cursor-pointer !bg-slate-200 checked:!bg-emerald-500 !border-0"
                                           type="checkbox" role="switch" id="s-{{$admin->id}}"
                                           {{ $admin->is_active ? 'checked' : '' }}
                                           onchange="StatusFunction(this, '{{$admin->id}}')">
                                </div>
                            </td>
                            <td class="py-6 px-4 border-0">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('superadmin.admin_topup', $admin->id) }}"
                                       class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all border-0 decoration-none" title="Kontör İşlemleri">
                                        <i class="fas fa-receipt text-xs"></i>
                                    </a>
                                    <a href="{{ route('superadmin.admin_edit', $admin->id) }}"
                                       class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all border-0 decoration-none" title="Düzenle">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </a>
                                    <button onclick="DeleteFunction({{ $admin->id }})"
                                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all border-0" title="Sil">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div id="custom-pagination-container" class="px-10 py-8 flex flex-col md:flex-row items-center justify-between gap-4 bg-slate-50/30">
                <div id="table-info-box" class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]"></div>
                <div id="table-pagination-box" class="flex items-center gap-2"></div>
            </div>
        </div>
    </div>

    <style>
        /* DataTable UI Cleanup */
        #courierTable_wrapper .dataTables_filter,
        #courierTable_wrapper .dataTables_info,
        #courierTable_wrapper .dataTables_paginate { display: none; }

        .form-check-input:focus { box-shadow: none; border-color: transparent; }

        /* Paginate Styling */
        .paginate_button {
            display: inline-flex !important; align-items: center !important; justify-content: center !important;
            min-width: 36px !important; height: 36px !important; border-radius: 10px !important;
            font-size: 11px !important; font-weight: 900 !important; cursor: pointer !important;
            border: 0 !important; margin: 0 2px !important; color: #94a3b8 !important; transition: all 0.2s;
        }
        .paginate_button.current { background: #0f172a !important; color: white !important; }
        .paginate_button:hover:not(.current) { background: #e2e8f0 !important; color: #0f172a !important; }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#courierTable').DataTable({
                order: [[0, "desc"]],
                dom: 'rtip',
                pageLength: 10,
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json" },
                drawCallback: function() {
                    $('#table-pagination-box').html($('.dataTables_paginate').html());
                    const info = table.page.info();
                    $('#table-info-box').html(`YÖNETİCİ: ${info.start + 1} - ${info.end} / TOPLAM: ${info.recordsTotal}`);
                }
            });

            $('#custom-filter').on('keyup', function () {
                table.search(this.value).draw();
            });

            $(document).on('click', '#table-pagination-box .paginate_button', function() {
                if($(this).hasClass('next')) table.page('next').draw('page');
                else if($(this).hasClass('previous')) table.page('previous').draw('page');
                else table.page($(this).data('dt-idx')).draw('page');
            });
        });

        function StatusFunction(checkbox, id) {
            const currentState = checkbox.checked;
            Swal.fire({
                title: 'DURUM GÜNCELLE',
                html: '<p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Bu yöneticinin sisteme erişim durumunu değiştirmek üzeresiniz.</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'EVET, GÜNCELLE',
                cancelButtonText: 'İPTAL',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[40px] p-12 shadow-2xl',
                    confirmButton: 'bg-emerald-500 text-white px-8 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2 shadow-lg shadow-emerald-100',
                    cancelButton: 'bg-slate-100 text-slate-400 px-8 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/superadmin/admin/status/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                Swal.fire({ icon: 'success', title: 'GÜNCELLENDİ', showConfirmButton: false, timer: 1000 });
                            } else {
                                checkbox.checked = !currentState;
                                Swal.fire("HATA", "İşlem başarısız.", "warning");
                            }
                        }
                    });
                } else {
                    checkbox.checked = !currentState;
                }
            });
        }

        function DeleteFunction(id) {
            Swal.fire({
                title: 'KAYDI SİL',
                html: '<p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Yönetici kaydı kalıcı olarak silinecektir. Bu işlem geri alınamaz.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'SİL',
                cancelButtonText: 'VAZGEÇ',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[40px] p-12 shadow-2xl',
                    confirmButton: 'bg-rose-500 text-white px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2',
                    cancelButton: 'bg-slate-100 text-slate-400 px-10 py-4 !rounded-2xl font-black text-[11px] uppercase tracking-widest mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/superadmin/admin/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                $('#data_' + id).fadeOut(300, function () { $(this).remove(); });
                                Swal.fire({ icon: 'success', title: 'SİLİNDİ', showConfirmButton: false, timer: 1000 });
                            } else {
                                Swal.fire("UYARI", "Bu kayıt silinemez.", "warning");
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
