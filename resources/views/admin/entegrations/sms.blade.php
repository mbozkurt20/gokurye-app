@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">SMS Entegrasyonu</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Vatan SMS API Ayarları</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-slate-100 px-4 py-2 !rounded-2xl m-0">
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest"><a href="javascript:void(0)" class="text-slate-500">Sistem</a></li>
                    <li class="breadcrumb-item text-[10px] font-black uppercase tracking-widest text-indigo-600 active">Entegrasyon</li>
                </ol>
            </nav>
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

        <div class="row g-4">
            <div class="col-xl-5 col-lg-12">
                <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden">
                    <div class="bg-indigo-600 p-5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white">
                            <i class="fas fa-key"></i>
                        </div>
                        <h4 class="text-sm font-black text-white uppercase m-0 tracking-widest">Vatan SMS Bilgileri</h4>
                    </div>
                    <div class="card-body p-5">
                        <form method="post" action="{{ route('admin.sms.entegrations.update') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Customer ID (45***)</label>
                                    <input required type="text" class="form-control !rounded-xl border-slate-200 py-3 font-bold text-slate-700" name="vatan_sms_customer" placeholder="Müşteri No" value="{{ $admin->vatan_sms_customer }}">
                                </div>
                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Username</label>
                                    <input required type="text" class="form-control !rounded-xl border-slate-200 py-3 font-bold text-slate-700" name="vatan_sms_username" placeholder="905XXXXXXXXX" value="{{ $admin->vatan_sms_username }}">
                                </div>
                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Password</label>
                                    <input required type="text" class="form-control !rounded-xl border-slate-200 py-3 font-bold text-slate-700" name="vatan_sms_password" placeholder="API Şifreniz" value="{{ $admin->vatan_sms_password }}">
                                </div>
                                <div class="col-12">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Orginator (Başlık)</label>
                                    <input required type="text" class="form-control !rounded-xl border-slate-200 py-3 font-bold text-slate-700" name="vatan_sms_orginator" placeholder="SMS Başlığı" value="{{ $admin->vatan_sms_orginator }}">
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-indigo-100 border-0 transition-all hover:scale-[1.01]">GÜNCELLE</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-5 col-lg-12">
                <div class="card border-0 shadow-sm !rounded-[32px] overflow-hidden">
                    <div class="bg-slate-800 p-5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h4 class="text-sm font-black text-white uppercase m-0 tracking-widest">Hızlı Test Paneli</h4>
                    </div>
                    <div class="card-body p-5">
                        <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl mb-5">
                            <p class="text-amber-700 text-xs font-bold leading-relaxed m-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Önemli:</strong> Test mesajı telefonunuza ulaşmadan sistemi aktif etmeyiniz. Hatalı bilgiler kontör kaybına yol açabilir.
                            </p>
                        </div>

                        <form method="post" action="{{route('admin.sms.entegrations.test')}}" class="mb-5">
                            @csrf
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Test Telefon Numarası</label>
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                                </div>
                                <button class="px-4 bg-slate-800 text-white rounded-xl font-black text-[10px] uppercase border-0 transition-all hover:bg-slate-900" type="submit">GÖNDER</button>
                            </div>
                        </form>

                        <hr class="border-slate-100 my-5">

                        <div class="text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">SİSTEM DURUMU</p>

                            @php $isSms = \Illuminate\Support\Facades\Auth::guard('admin')->user()->is_sms; @endphp

                            <div class="bg-slate-50 p-6 rounded-[24px] border border-slate-100">
                                <p class="text-xs font-bold text-slate-600 mb-4">SMS Doğrulama şu an <strong>{{ $isSms ? 'AKTİF' : 'PASİF' }}</strong></p>

                                <button id="toggle-button"
                                        onclick="DeleteFunction()"
                                        class="w-full py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] border-0 transition-all {{ $isSms ? 'bg-red-50 text-red-600 shadow-lg shadow-red-100' : 'bg-green-50 text-green-600 shadow-lg shadow-green-100' }}"
                                        data-status="{{ $isSms ? 'active' : 'passive' }}">
                                    {{ $isSms ? 'SİSTEMİ DURDUR' : 'SİSTEMİ AKTİF ET' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function DeleteFunction() {
            const button = document.getElementById('toggle-button');
            const isActivating = button.getAttribute('data-status') === 'passive';

            Swal.fire({
                title: `<span class="text-lg font-black uppercase text-slate-800">${isActivating ? 'AKTİF ET' : 'PASİF ET'}</span>`,
                html: `<p class="text-xs font-bold text-slate-500 uppercase">SMS Entegrasyon durumunu değiştirmek üzeresiniz.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isActivating ? '#10b981' : '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Evet, Onaylıyorum',
                cancelButtonText: 'Vazgeç',
                customClass: {
                    popup: '!rounded-[32px] border-0 shadow-2xl',
                    confirmButton: 'py-3 px-6 !rounded-xl font-black text-xs uppercase',
                    cancelButton: 'py-3 px-6 !rounded-xl font-black text-xs uppercase'
                }
            }).then(function (result) {
                if (result.value) {
                    $.get('/admin/update/entegrations/status', function (data) {
                        if (data === "OK") {
                            location.reload(); // Tasarımı anlık güncellemek için reload en temizi
                        } else {
                            Swal.fire("Hata", "İşlem başarısız.", "error");
                        }
                    });
                }
            });
        }
    </script>
@endsection
