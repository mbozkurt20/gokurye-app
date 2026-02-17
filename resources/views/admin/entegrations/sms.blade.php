@extends('admin.layouts.app')
@section('content')
    <style>
        #phone-input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #30d760;
            border-radius: 5px;
            width: 200px;
        }

        #phone-input:invalid {
            border-color: #242323;
        }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Sms Entegrasyon</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Sms Entegrasyon</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Güncelle</a></li>
                </ol>
            </div>
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
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-header" style="background: #4f46e5;color:#fff">
                        <h4 class="card-title text-white">Vatan Sms Bilgileri</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="repeater" action="{{ route('admin.sms.entegrations.update') }}">
                            @csrf

                            <div class="basic-form">
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Customer (45***)</label>
                                        <input required type="text" class="form-control" name="vatan_sms_customer"
                                               placeholder="Müşteri Giriniz"
                                               value="{{ $admin->vatan_sms_customer }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Username (905***5**8**)</label>
                                        <input required type="text" class="form-control" name="vatan_sms_username"
                                               placeholder="Kullanıcı Adı Giriniz"
                                               value="{{ $admin->vatan_sms_username }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Password</label>
                                        <input required type="text" class="form-control" name="vatan_sms_password"
                                               placeholder="Şifre Giriniz"
                                               value="{{ $admin->vatan_sms_password }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Orginator (8*0**3*3**)</label>
                                        <input required type="text" class="form-control" name="vatan_sms_orginator"
                                               placeholder="Başlatıcı Giriniz"
                                               value="{{ $admin->vatan_sms_orginator }}">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="special-button">Bilgileri Güncelle</button>
                        </form>
                    </div>
                </div>

            </div>
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-header" style="background: #4f46e5;color:#fff">
                        <h4 class="card-title text-white">Vatan Sms Test</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <div class="row">
                                <p>Sol tarafta <strong>VatanSms</strong> bilgilerinizi giriniz.</p>
                                <p>Doğru olduğunu düşünüyorsanız Telefon No girip test messajı gönderiniz.</p>

                                <p class="bg-danger-light text-danger">Uyarı:: Test mesajı size ulaşmadan sms aktif etmeyiniz!!!</p>
                                <form method="post" action="{{route('admin.sms.entegrations.test')}}">
                                    @csrf
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                                    <button class="special-button" type="submit">Gönder</button>
                                </form>


                                <div class="absolute bottom-0 mt-5">
                                    <p class="size-3 py-2  text-dark fw-bold rounded-xl">
                                        Test mesajımız size iletildi ise <strong class="text-success">Aktif Et</strong> diyerek siparişlerinizin kurye teslimini sms doğrulama ile yapabilirsiniz...</p>

                                    <a id="toggle-button"
                                       onclick="DeleteFunction()"
                                       class="{{ \Illuminate\Support\Facades\Auth::guard('admin')->user()->is_sms ? 'special-ok-button' : 'special-button' }}"
                                       data-status="{{ \Illuminate\Support\Facades\Auth::guard('admin')->user()->is_sms ? 'active' : 'passive' }}">
                                        {{ \Illuminate\Support\Facades\Auth::guard('admin')->user()->is_sms ? 'Pasif Et' : 'Aktif Et' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function DeleteFunction() {
            const button = document.getElementById('toggle-button');
            const currentStatus = button.getAttribute('data-status'); // "active" or "passive"
            const willActivate = currentStatus === 'passive';

            const newText = willActivate ? 'Pasif Et' : 'Aktif Et';
            const newClass = willActivate ? 'special-ok-button' : 'special-button';
            const oldClass = willActivate ? 'special-button' : 'special-ok-button';

            Swal.fire({
                title: `${willActivate ? 'Aktif' : 'Pasif'} etmek istediğinizden emin misiniz?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#259a38',
                cancelButtonColor: '#3057d7',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet İstiyorum!',
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'GET',
                        url: '/admin/update/entegrations/status',
                        success: function (data) {
                            if (data === "OK") {
                                Swal.fire("Güncellendi!", "Güncelleme İşlemi Başarılı.", "success");

                                // 🔄 Butonun text, class ve data-status'unu değiştir
                                button.textContent = newText;
                                button.classList.remove(oldClass);
                                button.classList.add(newClass);
                                button.setAttribute('data-status', willActivate ? 'active' : 'passive');

                            } else {
                                Swal.fire("Uyarı !!", "Üzgünüz, Güncelleme Yapılamadı.", "warning");
                            }
                        },
                        error: function () {
                            Swal.fire("Hata!", "Sunucuya bağlanılamadı.", "error");
                        }
                    });
                }
            });
        }

        const phoneInput = document.getElementById('phone-input');

        phoneInput.addEventListener('input', function (e) {
            // Sadece rakam girilmesine izin ver
            this.value = this.value.replace(/\D/g, '');

            // Max 10 hane olacak şekilde sınırla
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });

        // Form gönderilmeden önce validasyon
        document.querySelector('form').addEventListener('submit', function (e) {
            const phone = phoneInput.value;

            if (!/^5\d{9}$/.test(phone)) {
                alert("Lütfen geçerli bir telefon numarası girin (5453455125 formatında).");
                e.preventDefault();
            }
        });
    </script>
@endsection
