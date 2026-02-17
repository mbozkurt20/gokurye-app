<!DOCTYPE html>
<html lang="tr" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Partner Girişi</title>

    <link rel="shortcut icon" type="image/png" href="{{config('site.logo')}}">
    <link href="{{asset('theme/login/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('theme/login/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/pages/restaurants/login/index.css')}}">
    <style>
        .custom-alert {
            padding: 10px 14px;
            border-radius: 12px;
            margin: 10px 0;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        .custom-alert.success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
        }

        .custom-alert.error {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            color: #fff;
        }

        .close-btn {
            position: absolute;
            top: 6px;
            right: 8px;
            font-size: 16px;
            color: rgba(255,255,255,0.8);
            cursor: pointer;
            transition: color 0.2s;
        }
        .close-btn:hover {
            color: #fff;
        }

        /* küçük animasyon */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
<div class="login-container">
    <div class="login-box">
        <div class="logo">
            <a href="{{route('restaurant.login')}}">
                <img src="{{config('site.logo')}}" alt="Logo">
            </a>
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

        <h2 class="form-title" id="formTitle">Partner  Girişi</h2>

        <form method="POST" action="{{route('dealer.auth')}}">
            @csrf
            <input type="hidden" name="user_type" id="userTypeInput" value="restaurant">

            <div class="mb-3">
                <input type="text" name="email" id="emailInput" class="form-control" placeholder="E-posta Adresiniz" required>
            </div>

            <div class="mb-4">
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Şifreniz" required>
            </div>

            <button type="submit" class="btn btn-login">Giriş Yap</button>
        </form>
    </div>
</div>

<script src="{{asset('theme/login/js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>
