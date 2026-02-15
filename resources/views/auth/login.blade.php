<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Giriş</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pattern-bg {
            background-color: #4f46e5;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M92.66 7.38c0 .11-.03.22-.09.32l-1.34 2.32c-.06.1-.16.16-.27.16s-.21-.06-.27-.16l-1.34-2.32c-.06-.1-.09-.21-.09-.32 0-.37.3-.67.67-.67h2.68c.37 0 .67.3.67.67zM7.34 92.62c0-.11.03-.22.09-.32l1.34-2.32c.06-.1.16-.16.27-.16s.21.06.27.16l1.34 2.32c.06.1.09.21.09.32 0 .37-.3.67-.67.67H7.34c-.37 0-.67-.3-.67-.67zm9.27-56.32c0 .11-.03.22-.09.32l-1.34 2.32c-.06.1-.16.16-.27.16s-.21-.06-.27-.16l-1.34-2.32c-.06-.1-.09-.21-.09-.32 0-.37.3-.67.67-.67h2.68c.37 0 .67.3.67.67zm76.72 13.7c0-.11.03-.22.09-.32l1.34-2.32c.06-.1.16-.16.27-.16s.21.06.27.16l1.34 2.32c.06.1.09.21.09.32 0 .37-.3.67-.67.67h-2.68c-.37 0-.67-.3-.67-.67z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="h-full bg-white">

<div class="flex h-full min-h-screen">

    <div class="relative hidden w-0 flex-1 lg:block pattern-bg">
        <div class="absolute inset-0 bg-indigo-900/40 backdrop-blur-[2px]"></div>
        <div class="relative flex h-full items-center p-16">
            <div class="max-w-xl">
                <img src="{{config('site.logo')}}" alt="Logo" class="h-16 mb-12 brightness-0 invert">
                <h2 class="text-5xl font-extrabold tracking-tight text-white mb-6">
                    Lojistiğin Geleceğine <br> <span class="text-indigo-300">Hoş Geldiniz.</span>
                </h2>
                <p class="text-xl text-indigo-100 font-medium leading-relaxed">
                    {{config('site.name')}} ile operasyonlarınızı tek bir merkezden yönetin, teslimat hızınızı %40 artırın.
                </p>
                <div class="mt-12 grid grid-cols-2 gap-8">
                    <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/20">
                        <span class="block text-3xl font-bold text-white mb-1">24/7</span>
                        <span class="text-indigo-200 text-sm font-bold uppercase tracking-wider">Canlı Takip</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/20">
                        <span class="block text-3xl font-bold text-white mb-1">%100</span>
                        <span class="text-indigo-200 text-sm font-bold uppercase tracking-wider">Entegrasyon</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-1 flex-col justify-center px-8 py-12 sm:px-12 lg:flex-none lg:px-24 xl:px-32 bg-slate-50">
        <div class="mx-auto w-full max-w-sm lg:w-96">

            <div class="mb-10 lg:hidden">
                <img src="{{config('site.logo')}}" alt="Logo" class="h-10">
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Giriş Yap</h1>
                <p class="mt-3 text-sm font-bold text-slate-500">Hesabınıza erişmek için bilgilerinizi girin.</p>
            </div>

            <div class="mb-8 p-1 bg-slate-200/60 rounded-2xl flex">
                <button type="button" data-type="restaurant" class="type-btn flex-1 py-3 text-xs font-black uppercase tracking-widest rounded-xl transition-all bg-white text-indigo-600 shadow-sm">
                    Restoran
                </button>
                <button type="button" data-type="admin" class="type-btn flex-1 py-3 text-xs font-black uppercase tracking-widest rounded-xl transition-all text-slate-500 hover:text-slate-800">
                    Yönetici
                </button>
            </div>

            @if(session()->has('message') || session()->has('test'))
                <div class="mb-6 p-4 rounded-2xl text-xs font-black flex items-center gap-3 {{ session()->has('message') ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ session()->get('message') ?? session()->get('test') }}
                </div>
            @endif

            <form id="loginForm" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="user_type" id="userTypeInput" value="restaurant">

                <div class="space-y-1.5">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">E-Posta Adresi</label>
                    <input type="email" name="email" id="emailInput" required
                           class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl outline-none transition-all font-bold text-slate-700 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 placeholder:text-slate-300"
                           placeholder="isim@sirket.com">
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Şifre</label>
                        <a href="#" class="text-[10px] font-black text-indigo-600 hover:underline uppercase tracking-widest">Unuttum?</a>
                    </div>
                    <input type="password" name="password" id="passwordInput" required
                           class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl outline-none transition-all font-bold text-slate-700 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 placeholder:text-slate-300"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-200 transition-all active:scale-[0.98]">
                    Sisteme Giriş Yap
                </button>
            </form>

            <div class="mt-12 pt-8 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Masaüstü App</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Destek v2.0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const typeButtons = document.querySelectorAll('.type-btn');
    const userTypeInput = document.getElementById('userTypeInput');
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');

    const routeMap = {
        admin: "{{ route('admin.auth') }}",
        restaurant: "{{ route('restaurant.auth') }}"
    };

    const testMode = {{config('site.test_mode') ? 'true' : 'false' }};

    function setCredentials(type) {
        if (!testMode) return;
        emailInput.value = type === 'admin' ? 'test@admin.com' : 'test@restaurant.com';
        passwordInput.value = 'test';
    }

    typeButtons.forEach(button => {
        button.addEventListener('click', () => {
            typeButtons.forEach(btn => {
                btn.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm');
                btn.classList.add('text-slate-500');
            });
            button.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
            button.classList.remove('text-slate-500');
            const type = button.getAttribute('data-type');
            userTypeInput.value = type;
            setCredentials(type);
        });
    });

    document.getElementById('loginForm').addEventListener('submit', function(e) {
        this.action = routeMap[userTypeInput.value];
    });

    document.addEventListener('DOMContentLoaded', () => setCredentials('restaurant'));
</script>
</body>
</html>
