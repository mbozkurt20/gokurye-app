<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Üst Yönetici Girişi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pattern-bg {
            background-color: #1e1b4b; /* Daha koyu indigo/navy */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M92.66 7.38c0 .11-.03.22-.09.32l-1.34 2.32c-.06.1-.16.16-.27.16s-.21-.06-.27-.16l-1.34-2.32c-.06-.1-.09-.21-.09-.32 0-.37.3-.67.67-.67h2.68c.37 0 .67.3.67.67zM7.34 92.62c0-.11.03-.22.09-.32l1.34-2.32c.06-.1.16-.16.27-.16s.21.06.27.16l1.34 2.32c.06.1.09.21.09.32 0 .37-.3.67-.67.67H7.34c-.37 0-.67-.3-.67-.67zm9.27-56.32c0 .11-.03.22-.09.32l-1.34 2.32c-.06.1-.16.16-.27.16s-.21-.06-.27-.16l-1.34-2.32c-.06-.1-.09-.21-.09-.32 0-.37.3-.67.67-.67h2.68c.37 0 .67.3.67.67zm76.72 13.7c0-.11.03-.22.09-.32l1.34-2.32c.06-.1.16-.16.27-.16s.21.06.27.16l1.34 2.32c.06.1.09.21.09.32 0 .37-.3.67-.67.67h-2.68c-.37 0-.67-.3-.67-.67z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="h-full bg-white">

<div class="flex h-full min-h-screen">

    <div class="relative hidden w-0 flex-1 lg:block pattern-bg">
        <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-[1px]"></div>
        <div class="relative flex h-full items-center p-20">
            <div class="max-w-xl">
                <img src="{{config('site.logo')}}" alt="Logo" class="h-12 brightness-0 invert mb-12">
                <h2 class="text-6xl font-extrabold tracking-tighter text-white mb-8 leading-[1.1]">
                    Merkezi <br> <span class="text-indigo-400">Kontrol Paneli.</span>
                </h2>
                <p class="text-xl text-slate-300 font-medium leading-relaxed mb-12">
                    Üst yönetici yetkileriyle tüm sistemi, restoranları ve kurye ağını tek bir noktadan denetleyin.
                </p>
                <div class="flex items-center gap-6">
                    <div class="flex -space-x-4">
                        <div class="w-12 h-12 rounded-full border-4 border-slate-900 bg-slate-800 flex items-center justify-center text-xs font-black text-white">SA</div>
                        <div class="w-12 h-12 rounded-full border-4 border-slate-900 bg-indigo-600 flex items-center justify-center text-xs font-black text-white">AD</div>
                        <div class="w-12 h-12 rounded-full border-4 border-slate-900 bg-emerald-500 flex items-center justify-center text-xs font-black text-white text-[10px]">LIVE</div>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em]">Güvenli Üst Yönetim Erişimi</p>
                </div>
            </div>
        </div>
        <div class="absolute bottom-10 left-20">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">&copy; 2026 {{config('site.name')}} - CORE v4.0</p>
        </div>
    </div>

    <div class="flex flex-1 flex-col justify-center px-8 py-12 sm:px-12 lg:flex-none lg:px-24 xl:px-40 bg-white">
        <div class="mx-auto w-full max-w-sm lg:w-96">

            <div class="mb-12 lg:hidden">
                <img src="{{config('site.logo')}}" alt="Logo" class="h-10">
            </div>

            <div class="mb-10">
                <div class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest mb-4">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-2 rounded-full bg-indigo-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                    </span>
                    Güvenli Bölge
                </div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter">Giriş Yap</h1>
                <p class="mt-3 text-sm font-bold text-slate-400 uppercase tracking-tight">Üst Yönetici Kimlik Doğrulaması</p>
            </div>

            @if(session()->has('error') || session()->has('test'))
                <div class="mb-8 p-5 rounded-3xl text-xs font-black flex items-center gap-4 {{ session()->has('message') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                    <i class="fas {{ session()->has('message') ? 'fa-check-circle' : 'fa-exclamation-triangle' }} text-lg"></i>
                    {{ session()->get('success') ?? session()->get('test') }}
                </div>
            @endif

            <form action="{{route('superadmin.auth')}}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="restaurant">

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">E-Posta</label>
                    <div class="relative">
                        <input type="email" name="email" required
                               class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent rounded-[24px] outline-none transition-all font-bold text-slate-700 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 placeholder:text-slate-300"
                               placeholder="admin@sirket.com">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Şifre</label>
                    </div>
                    <input type="password" name="password" required
                           class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent rounded-[24px] outline-none transition-all font-bold text-slate-700 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 placeholder:text-slate-300"
                           placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-5 bg-slate-900 hover:bg-black text-white rounded-[24px] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                        Sistem Kontrolünü Başlat
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>

            <div class="mt-12 space-y-4">
                <p class="text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">Diğer Giriş Kanalları</p>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('restaurant.login') }}" class="py-4 border-2 border-slate-50 rounded-2xl flex items-center justify-center gap-2 hover:bg-slate-50 transition-all">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Restoran Girişi</span>
                    </a>
                    <a href="{{ route('admin.login') }}" class="py-4 border-2 border-slate-50 rounded-2xl flex items-center justify-center gap-2 hover:bg-slate-50 transition-all">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Yönetici Girişi</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>
