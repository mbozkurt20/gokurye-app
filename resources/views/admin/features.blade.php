@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Sistem Özellikleri</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Yönetici Yetki ve Fonksiyonlarını Yönetin</p>
            </div>
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                <i class="fas fa-sliders-h"></i>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-indigo-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 animate-bounce-short">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 p-2 rounded-xl text-indigo-600">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">İşlem Başarılı</p>
                        <p class="text-xs font-bold text-slate-700 leading-tight">{{ session()->get('message') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-300 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-[10px]"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden">
                    <div class="card-header bg-white border-b border-slate-50 p-5">
                        <h4 class="text-sm font-black text-slate-800 uppercase m-0">Özellik Listesi</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($features as $feature)
                                <div class="list-group-item border-slate-50 p-4 transition-colors hover:bg-slate-50/50">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                                                <i class="fas fa-layer-group text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-slate-800 uppercase tracking-tighter m-0">{{$feature->name}}</p>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest m-0">Fonksiyonel Yetki</p>
                                            </div>
                                        </div>

                                        <div class="form-check form-switch p-0 m-0">
                                            <input class="form-check-input !w-14 !h-7 !cursor-pointer focus:shadow-none shadow-sm"
                                                   type="checkbox"
                                                   id="s-{{$feature->id}}"
                                                   onclick="changeFeature('{{$feature->id}}')"
                                                   role="switch"
                                                   @if(\App\Models\AdminSystemFeature::where('admin_id',$admin->id)->where('system_feature_id', $feature->id)->exists()) checked @endif
                                            >
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-slate-50 border-0 p-4">
                        <p class="text-[10px] font-bold text-slate-400 text-center m-0 uppercase tracking-widest">
                            <i class="fas fa-info-circle me-1"></i> Yapılan değişiklikler anlık olarak sisteme yansır
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function changeFeature(e) {
            $.ajax({
                type: 'GET',
                url: '/admin/features/update/' + e,
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#ffffff',
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: '<span class="text-xs font-black text-slate-800 uppercase tracking-tighter">Yetki Güncellendi</span>'
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

    <style>
        /* Custom Indigo Switch Styling */
        .form-switch .form-check-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba(0, 0, 0, 0.25)'/%3e%3c/svg%3e");
            transition: background-position .15s ease-in-out;
            border-color: #e2e8f0;
        }
        .form-switch .form-check-input:focus {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%234f46e5'/%3e%3c/svg%3e");
        }
        .form-switch .form-check-input:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        }
    </style>
@endsection
