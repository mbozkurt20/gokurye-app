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
            background-color: #4338ca;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M92.66 7.38c0 .11-.03.22-.09.32l-1.34 2.32c-.06.1-.16.16-.27.16s-.21-.06-.27-.16l-1.34-2.32c-.06-.1-.09-.21-.09-.32 0-.37.3-.67.67-.67h2.68c.37 0 .67.3.67.67zM7.34 92.62c0-.11.03-.22.09-.32l1.34-2.32c.06-.1.16-.16.27-.16s.21.06.27.16l1.34 2.32c.06.1.09.21.09.32 0 .37-.3.67-.67.67H7.34c-.37 0-.67-.3-.67-.67zm9.27-56.32c0 .11-.03.22-.09.32l-1.34 2.32c-.06.1-.16.16-.27.16s-.21-.06-.27-.16l-1.34-2.32c-.06-.1-.09-.21-.09-.32 0-.37.3-.67.67-.67h2.68c.37 0 .67.3.67.67zm76.72 13.7c0-.11.03-.22.09-.32l1.34-2.32c.06-.1.16-.16.27-.16s.21.06.27.16l1.34 2.32c.06.1.09.21.09.32 0 .37-.3.67-.67.67h-2.68c-.37 0-.67-.3-.67-.67z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="h-full bg-white">

<div class="flex h-full min-h-screen">

    <div class="relative hidden w-0 flex-1 lg:block pattern-bg overflow-y-auto">
        <div class="absolute inset-0 bg-indigo-950/50 backdrop-blur-[2px]"></div>
        <div class="relative flex h-full flex-col justify-center p-16">
            <div class="max-w-xl">
                <img src="{{config('site.logo')}}" alt="Logo" class="h-40 brightness-0 invert mb-10">

                <h2 class="text-6xl font-extrabold tracking-tight text-white mb-8 leading-[1.1]">
                    Pazaryeri Teslimatında <br> <span class="text-emerald-400">Tam Kontrol.</span>
                </h2>

                <p class="text-xl text-indigo-100/80 font-medium leading-relaxed mb-12">
                    Tüm pazaryeri siparişlerinizi tek panelden yönetin. {{config('site.name')}} ile kuryelerinizi anlık takip edin, operasyonel hızınızı ikiye katlayın.
                </p>

                <div class="grid grid-cols-2 gap-6 mb-12">
                    <div class="bg-white/5 backdrop-blur-xl p-6 rounded-3xl border border-white/10 hover:border-white/20 transition-all">
                        <span class="block text-4xl font-black text-white mb-2">0.1s</span>
                        <span class="text-emerald-300 text-xs font-black uppercase tracking-widest">Anlık Entegrasyon</span>
                    </div>
                    <div class="bg-white/5 backdrop-blur-xl p-6 rounded-3xl border border-white/10 hover:border-white/20 transition-all">
                        <span class="block text-4xl font-black text-white mb-2">∞</span>
                        <span class="text-emerald-300 text-xs font-black uppercase tracking-widest">Sınırsız Kurye Ağı</span>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <a href="{{ route('demo.index') }}" class="group flex items-center justify-between p-6 bg-indigo-500 hover:bg-indigo-400 text-white rounded-3xl transition-all duration-300 shadow-2xl shadow-indigo-900/20">
                        <div>
                            <h4 class="text-lg font-black uppercase tracking-tight">Sistemi İnceleyin</h4>
                            <p class="text-indigo-100 text-sm font-medium">Pazaryeri panelini demo ile keşfedin</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-2xl group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </a>

                    <a href="{{ route('courier.apply') }}" class="group flex items-center justify-between p-6 bg-white/10 hover:bg-white/20 text-white rounded-3xl border border-white/10 transition-all duration-300">
                        <div>
                            <h4 class="text-lg font-black uppercase tracking-tight">Kurye Başvurusu</h4>
                            <p class="text-emerald-200 text-sm font-medium">Pazaryeri kurye ekibine dahil olun</p>
                        </div>
                        <div class="bg-white/10 p-3 rounded-2xl group-hover:translate-x-1 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-1 flex-col justify-center px-8 py-12 sm:px-12 lg:flex-none lg:px-24 xl:px-40 bg-slate-50">
        <div class="mx-auto w-full max-w-sm lg:w-96">

            <div class="mb-10 lg:hidden text-center">
                <img src="{{config('site.logo')}}" alt="Logo" class="h-10 mx-auto">
            </div>

            <div class="mb-10">
                <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Giriş Yap</h1>
                <p class="text-sm font-bold text-slate-500">Yönetim paneline erişmek için bilgilerinizi kullanın.</p>
            </div>

            <div class="mb-8 p-1.5 bg-slate-200/60 rounded-2xl flex gap-1">
                <button type="button" data-type="restaurant" class="type-btn flex-1 py-3.5 text-xs font-black uppercase tracking-widest rounded-xl transition-all bg-white text-indigo-600 shadow-sm">
                    Restoran
                </button>
                <button type="button" data-type="admin" class="type-btn flex-1 py-3.5 text-xs font-black uppercase tracking-widest rounded-xl transition-all text-slate-500 hover:text-slate-800">
                    Yönetici
                </button>
            </div>

            @if(session()->has('error') || session()->has('test'))
                <div class="mb-6 p-4 rounded-2xl text-[11px] font-black flex items-center gap-3 {{ session()->has('message') ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    <span class="flex-1 uppercase tracking-wider">{{ session()->get('success') ?? session()->get('test') }}</span>
                </div>
            @endif

            <form id="loginForm" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="user_type" id="userTypeInput" value="restaurant">

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">E-Posta</label>
                    <input type="email" name="email" id="emailInput" required
                           class="w-full px-6 py-5 bg-white border-2 border-slate-100 rounded-2xl outline-none transition-all font-bold text-slate-700 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 placeholder:text-slate-300"
                           placeholder="örnek@firma.com">
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Şifre</label>
                        <a href="#" class="text-[10px] font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">Şifremi Unuttum</a>
                    </div>
                    <input type="password" name="password" id="passwordInput" required
                           class="w-full px-6 py-5 bg-white border-2 border-slate-100 rounded-2xl outline-none transition-all font-bold text-slate-700 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 placeholder:text-slate-300"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-indigo-200 transition-all active:scale-[0.98]">
                    Oturum Aç
                </button>
            </form>

            <div class="mt-12 pt-8 border-t border-slate-200 flex items-center justify-between opacity-50">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">v2.4.0 Stable</span>
                <div class="flex gap-4">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
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
