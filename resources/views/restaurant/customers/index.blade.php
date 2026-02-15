@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid py-6 px-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-black tracking-tighter text-slate-900 uppercase">Müşteri Portföyü</h2>
                <p class="text-slate-500 font-medium">Toplam {{ count($customers) }} kayıtlı müşteri bulunmaktadır.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="javascript:void(0);" onclick="location.reload();" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-brand transition-all shadow-sm">
                    <i class="fas fa-sync text-lg"></i>
                </a>
                <a href="{{ route('restaurant.customers.new') }}" class="flex items-center gap-2 px-6 py-3.5 bg-slate-950 text-white rounded-2xl font-black tracking-tighter shadow-xl shadow-slate-200 hover:bg-brand transition-all active:scale-95">
                    <i class="fas fa-user-plus"></i> YENİ MÜŞTERİ EKLE
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 mb-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400 group-focus-within:text-brand transition-colors"></i>
                </div>
                <input type="text" id="custom-filter"
                       class="block w-full pl-12 pr-4 py-5 bg-white border-0 rounded-[24px] shadow-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand transition-all font-bold text-slate-700 placeholder:text-slate-400"
                       placeholder="Müşteri adı, telefon veya adres ile hızlı arama yapın...">
            </div>
        </div>

        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table id="customerTable" class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">Müşteri Bilgisi</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">İletişim</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Kayıt Tarihi</th>
                        <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Yönetim</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    @foreach($customers as $customer)
                        <tr id="data_{{ $customer->id }}" class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-brand/10 text-brand flex items-center justify-center font-black text-lg">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-900 text-base tracking-tight leading-none mb-1">{{ $customer->name }}</div>
                                        <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded-md font-bold text-slate-500 uppercase">ID: #{{ $customer->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <a href="tel:{{ $customer->phone }}" class="text-slate-700 font-bold hover:text-brand transition-colors">
                                        <i class="fas fa-phone-alt mr-2 text-slate-300"></i>{{ $customer->phone }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center text-slate-500 font-medium">
                                {{ $customer->created_at->translatedFormat('d F Y') }}
                                <span class="block text-[10px] text-slate-400">{{ $customer->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2 items-center">
                                    <a href="{{ route('restaurant.customers.edit', $customer->id) }}"
                                       class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-slate-400 hover:bg-brand hover:text-white hover:border-brand hover:-translate-y-1 transition-all duration-200 shadow-sm group/btn">
                                        <i class="fas fa-pencil-alt text-xs transition-transform group-hover/btn:scale-110"></i>
                                    </a>

                                    <button onclick="DeleteFunction({{ $customer->id }})"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-slate-400 hover:bg-red-500 hover:text-white hover:border-red-500 hover:-translate-y-1 transition-all duration-200 shadow-sm group/btn">
                                        <i class="fa fa-trash text-xs transition-transform group-hover/btn:scale-110"></i>
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
        /* DataTables Temizliği */
        .dataTables_wrapper .dataTables_filter { display: none; }
        .dataTables_wrapper .dataTables_length { display: none; }
        .dataTables_info { padding: 20px !important; font-size: 12px !important; font-weight: 700 !important; color: #94a3b8 !important; }
        .dataTables_paginate { padding: 20px !important; }
        .paginate_button.current { background: #0f172a !important; color: white !important; border: 0 !important; border-radius: 12px !important; }
        table.dataTable thead th { border-bottom: none !important; }
        table.dataTable.no-footer { border-bottom: none !important; }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#customerTable').DataTable({
                order: [[2, "desc"]],
                pageLength: 15,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json"
                },
                dom: 'rtip' // Sadece tablo, info ve pagination göster
            });

            $('#custom-filter').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function DeleteFunction(id) {
            Swal.fire({
                title: 'Müşteriyi Sil?',
                text: "Bu kayıt ile ilgili tüm adres geçmişi silinecektir!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                cancelButtonColor: '#f43f5e',
                cancelButtonText: 'Vazgeç',
                confirmButtonText: 'Evet, Sil!',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-[32px]',
                    confirmButton: 'rounded-xl px-4 py-2 font-bold',
                    cancelButton: 'rounded-xl px-4 py-2 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/restaurant/customers/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                $('#data_' + id).addClass('scale-95 opacity-0').fadeOut(300, function () {
                                    $(this).remove();
                                });
                                Swal.fire("Başarılı!", "Müşteri kaydı silindi.", "success");
                            } else {
                                Swal.fire("Hata!", "Müşteri silinemedi.", "error");
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
