@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid pb-5">
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="font-black text-slate-800 uppercase tracking-tighter mb-0">Güvenli Ödeme</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Ödemeniz PayTR Altyapısıyla Korunmaktadır</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white rounded-xl border border-slate-100 shadow-sm flex items-center gap-2">
                    <i class="fas fa-lock text-green-500 text-[10px]"></i>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">SSL SECURE</span>
                </div>

            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="card border-0 shadow-2xl !rounded-[40px] overflow-hidden bg-white">
                    <div class="bg-indigo-600 p-5 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white backdrop-blur-sm">
                                <i class="fas fa-credit-card text-lg"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.2em] block leading-none mb-1">Ödeme Paneli</span>
                                <span class="text-white font-black text-sm uppercase m-0">Kart Bilgilerinizi Giriniz</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <img src="https://www.paytr.com/img/paytr-logo.svg" style="height: 22px; filter: brightness(0) invert(1);" alt="PayTR">
                        </div>
                    </div>

                    <div class="card-body p-0 bg-slate-50 min-vh-100">
                        <iframe src="https://www.paytr.com/odeme/guvenli/{{ $token }}"
                                frameborder="0"
                                scrolling="no"
                                id="paytr_iframe"
                                style="width: 100%; min-height: 700px; border: none; display: block;">
                        </iframe>
                    </div>

                    <div class="p-6 bg-white border-t border-slate-50 text-center">
                        <div class="flex items-center justify-center gap-6 opacity-40 grayscale">
                            <i class="fab fa-cc-visa fa-2x"></i>
                            <i class="fab fa-cc-mastercard fa-2x"></i>
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-4 m-0">
                            Ödeme tamamlanana kadar lütfen bu sayfadan ayrılmayınız.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('message', function(event) {
            if (typeof event.data == 'string' && event.data.indexOf('paytr:') === 0) {
                var height = parseInt(event.data.split(':')[1]);
                var iframe = document.getElementById('paytr_iframe');
                if (iframe) {
                    iframe.style.height = (height + 30) + 'px';
                }
            }
        });
    </script>
@endsection
