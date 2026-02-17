@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Yönetici Özellikler</h2>
        </div>
         @if(session()->has('message'))
        <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-green-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out animate-bounce-short">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 p-2 rounded-xl">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">İşlem Başarılı</p>
                    <p class="text-sm font-bold text-slate-700 leading-tight">
                        {{ session()->get('message') }}
                    </p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session()->has('test'))
        <div class="fixed top-5 right-5 z-[10000] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-2xl p-4 transform transition-all duration-500 ease-in-out">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 p-2 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Hata Oluştu</p>
                    <p class="text-sm font-bold text-slate-700 leading-tight">
                        {{ session()->get('test') }}
                    </p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
    @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Aktif/Pasif Özellikler</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            @foreach($features as $feature)
                                <div class="form-check form-switch me-3">
                                    <label class="form-check-label text-dark fw-bold px-3 mt-3" for="s-{{$feature->id}}">{{$feature->name}}</label>
                                    <input class="form-check-input" type="checkbox" id="s-{{$feature->id}}" onclick="changeFeature('{{$feature->id}}')"
                                           role="switch" style="height: 40px; width: 80px;"
                                           @if(\App\Models\AdminSystemFeature::where('admin_id',$admin->id)->where('system_feature_id', $feature->id)->exists()) checked @endif
                                    >
                                </div>
                                <hr>
                            @endforeach
                        </div>
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
                    let message = '';

                    message = 'Durum Güncellendi';

                    Swal.fire({
                        title: message,
                        icon: 'success',
                        text: 'Özellik Durumu Güncellendi!',
                        confirmButtonText: 'Tamam',
                        background: '#ffffff',
                        color: '#fff',
                        iconColor: '#4f46e5',
                        confirmButtonColor: '#4f46e5',
                        customClass: {
                            popup: 'rounded-xl shadow-2xl',
                            confirmButton: 'px-6 py-3 text-lg font-semibold',
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown',
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp',
                        }
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

@endsection


