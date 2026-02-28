@extends('superadmin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800 uppercase leading-none m-0">
                    PARTNER <span class="text-slate-400">YÖNETİM PANELİ</span>
                </h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Sistem Ortakları</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tam Liste</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="javascript:void(0);" onclick="location.reload();" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-indigo-600 transition-all">
                    <i class="fas fa-sync text-sm"></i>
                </a>
                <a href="{{ route('superadmin.dealer_create') }}" class="flex items-center gap-3 px-6 py-3.5 bg-indigo-600 text-white !rounded-2xl shadow-lg shadow-indigo-100 hover:scale-[1.02] transition-all border-0">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="text-xs font-black uppercase tracking-widest">YENİ PARTNER EKLE</span>
                </a>
            </div>
        </div>

        <div class="bg-white !rounded-[30px] shadow-sm border border-slate-50 p-6 mb-6">
            <div class="row items-center g-3">
                <div class="col-md-4">
                    <div class="relative">
                        <input type="text" id="custom-filter" class="form-control !rounded-2xl border-slate-100 bg-slate-50/50 p-4 pl-12 font-bold text-slate-600 focus:ring-2 focus:ring-indigo-100 transition-all" placeholder="Partner ismi, email veya telefon ara...">
                        <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Toplam Kayıt: <span class="text-indigo-600">{{ count($dealers) }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white !rounded-[40px] shadow-sm border border-slate-50 overflow-hidden">
            <div class="p-8">
                <table id="dealerTable" class="table !border-0 text-slate-700">
                    <thead>
                    <tr class="!border-b !border-slate-50">
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4">PARTNER BİLGİSİ</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4">İLETİŞİM</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-center">BÖLGE</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-center">YÖNETİCİ</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-center">KOMİSYON</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-center">KAZANÇ</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-center">DURUM</th>
                        <th class="text-[10px] font-black text-slate-400 uppercase tracking-widest pb-4 text-end">İŞLEM</th>
                    </tr>
                    </thead>
                    <tbody class="align-middle">
                    @foreach($dealers as $dealer)
                        <tr id="data_{{ $dealer->id }}" class="group hover:bg-slate-50/50 transition-all">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-xs">
                                        {{ strtoupper(substr($dealer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-700 text-sm leading-none mb-1">{{ $dealer->name }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">ID: #{{ $dealer->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-[11px] font-bold text-slate-600 mb-1"><i class="fa-regular fa-envelope me-2 text-indigo-400"></i>{{ $dealer->email }}</div>
                                <div class="text-[11px] font-bold text-slate-600"><i class="fa-solid fa-phone me-2 text-emerald-400"></i>{{ $dealer->phone }}</div>
                            </td>
                            <td class="text-center">
                                    <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest">
                                        {{ $dealer->city_id ? \App\Models\City::find($dealer->city_id)->name : '-' }}
                                    </span>
                                <div class="mt-1 text-[9px] font-bold text-slate-400 italic">
                                    {{ $dealer->district_id ? \App\Models\District::find($dealer->district_id)->name : '-'}}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                                    {{ $dealer->admins_count ?? 0 }}
                                </span>
                                <div class="text-[9px] font-bold text-slate-400 mt-1">yönetici</div>
                            </td>
                            <td class="text-center">
                                <span class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-black">
                                    %{{ $dealer->commission_rate ?? 20 }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="text-sm font-black text-emerald-600">{{ number_format($dealer->commission_balance ?? 0, 2, ',', '.') }} ₺</div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch flex justify-center p-0">
                                    <input class="form-check-input !w-12 !h-6 cursor-pointer" type="checkbox"
                                           role="switch" {{ $dealer->is_active ? 'checked' : '' }}
                                           onchange="StatusFunction(this, '{{$dealer->id}}')">
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('superadmin.dealer_edit', $dealer->id) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </a>
                                    <button onclick="DeleteFunction({{ $dealer->id }})" class="w-9 h-9 flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all border-0">
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
    </div>

    <style>
        /* DataTable Style Overrides */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: white !important;
            border-radius: 12px !important;
            font-weight: 800;
        }
        .dataTables_wrapper .dataTables_info { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8 !important; padding-top: 2rem; }
        .dataTables_wrapper .dataTables_length { display: none; }
        .dataTables_filter { display: none; }
        table.dataTable thead th { border-bottom: 1px solid #f8fafc !important; }
        .form-check-input:checked { background-color: #10b981; border-color: #10b981; }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#dealerTable').DataTable({
                order: [[3, "desc"]],
                dom: 'rtip', // Sayfalama ve tabloyu göster, varsayılan aramayı gizle
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json"
                }
            });

            $('#custom-filter').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function StatusFunction(checkbox, id) {
            const currentState = checkbox.checked;
            Swal.fire({
                title: 'Durum Güncellensin mi?',
                text: "Partnerin sisteme erişimi değişecektir.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#f43f5e',
                confirmButtonText: 'Evet, Güncelle!',
                cancelButtonText: 'Vazgeç',
                background: '#ffffff',
                customClass: { popup: '!rounded-[30px]', confirmButton: '!rounded-xl px-4 py-2', cancelButton: '!rounded-xl px-4 py-2' }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get('/superadmin/dealer/status/' + id, function (data) {
                        if (data === "OK") {
                            Swal.fire({ title: "Başarılı!", text: "Durum güncellendi.", icon: "success", customClass: { popup: '!rounded-[30px]' } });
                        } else {
                            checkbox.checked = !currentState;
                            Swal.fire("Uyarı!", "İşlem başarısız.", "warning");
                        }
                    }).fail(function() {
                        checkbox.checked = !currentState;
                        Swal.fire("Hata!", "Bir hata oluştu.", "error");
                    });
                } else {
                    checkbox.checked = !currentState;
                }
            });
        }

        function DeleteFunction(id) {
            Swal.fire({
                title: 'Partner Silinsin mi?',
                text: "Bu partner ve tüm verileri kalıcı olarak silinecektir!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Evet, Sil!',
                cancelButtonText: 'Hayır',
                customClass: { popup: '!rounded-[30px]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get('/superadmin/dealer/delete/' + id, function (data) {
                        if (data === "OK") {
                            $('#data_' + id).fadeOut(400, function() { $(this).remove(); });
                            Swal.fire({ title: "Silindi!", icon: "success", customClass: { popup: '!rounded-[30px]' } });
                        } else {
                            Swal.fire("Uyarı!", "Bu kayıt silinemez.", "warning");
                        }
                    });
                }
            });
        }
    </script>
@endsection
