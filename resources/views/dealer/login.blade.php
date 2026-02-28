<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Partner Girişi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pattern-bg {
            background-color: #0f172a;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M0 0h40v40H0V0zm40 40h40v40H40V40zm0-40h2l-2 2V0zm0 4l4-4h2l-6 6V4zm0 4l8-8h2L40 10V8zm0 4L52 0h2L40 14v-2zm0 4L56 0h2L40 18v-2zm0 4L60 0h2L40 22v-2zm0 4L64 0h2L40 26v-2zm0 4L68 0h2L40 30v-2zm0 4L72 0h2L40 34v-2zm0 4L76 0h2L40 38v-2zm0 4L80 0v2L42 40h-2zm4 0L80 4v2L46 40h-2zm4 0L80 8v2L50 40h-2zm4 0l28-28v2L54 40h-2zm4 0l24-24v2L58 40h-2zm4 0l20-20v2L62 40h-2zm4 0l16-16v2L66 40h-2zm4 0l12-12v2L70 40h-2zm4 0l8-8v2l-6 6h-2zm4 0l4-4v2l-2 2h-2z'/%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="h-full bg-white">

<div class="flex h-full min-h-screen">

    {{-- Sol Panel --}}
    <div class="relative hidden w-0 flex-1 lg:block pattern-bg">
        <div class="absolute inset-0 bg-slate-950/30"></div>
        <div class="relative flex h-full items-center p-20">
            <div class="max-w-xl">
                <img src="{{config('site.logo')}}" alt="Logo" class="h-12 brightness-0 invert mb-12">
                <h2 class="text-6xl font-extrabold tracking-tighter text-white mb-8 leading-[1.1]">
                    Bayi <br> <span class="text-violet-400">Kontrol Paneli.</span>
                </h2>
                <p class="text-xl text-slate-300 font-medium leading-relaxed mb-12">
                    Bayi hesabınızla giriş yapın; adminlerinizi, komisyon kazançlarınızı ve performansınızı tek noktadan yönetin.
                </p>
                <div class="flex items-center gap-6">
                    <div class="flex -space-x-4">
                        <div class="w-12 h-12 rounded-full border-4 border-slate-900 bg-violet-700 flex items-center justify-center text-xs font-black text-white">BY</div>
                        <div class="w-12 h-12 rounded-full border-4 border-slate-900 bg-slate-700 flex items-center justify-center text-xs font-black text-white">AD</div>
                        <div class="w-12 h-12 rounded-full border-4 border-slate-900 bg-emerald-500 flex items-center justify-center text-[10px] font-black text-white">%20</div>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em]">Bayi Komisyon Sistemi</p>
                </div>
            </div>
        </div>
        <div class="absolute bottom-10 left-20">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">&copy; {{ date('Y') }} {{config('site.name')}} - Bayi Portalı</p>
        </div>
    </div>

    {{-- Sağ Form --}}
    <div class="flex flex-1 flex-col justify-center px-8 py-12 sm:px-12 lg:flex-none lg:px-24 xl:px-40 bg-white">
        <div class="mx-auto w-full max-w-sm lg:w-96">

            {{-- Mobile logo --}}
            <div class="mb-12 lg:hidden">
                <img src="{{config('site.logo')}}" alt="Logo" class="h-10">
            </div>

            <div class="mb-10">
                <div class="inline-flex items-center gap-2 bg-violet-50 text-violet-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest mb-4">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-2 rounded-full bg-violet-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-violet-600"></span>
                    </span>
                    Güvenli Bayi Girişi
                </div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter">Giriş Yap</h1>
                <p class="mt-3 text-sm font-bold text-slate-400 uppercase tracking-tight">Partner Kimlik Doğrulaması</p>
            </div>

            @if(session()->has('error'))
                <div class="mb-8 p-5 rounded-3xl text-xs font-black flex items-center gap-4 bg-rose-50 text-rose-700 border border-rose-100">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                    {{ session()->get('error') }}
                </div>
            @endif

            @if(session()->has('success'))
                <div class="mb-8 p-5 rounded-3xl text-xs font-black flex items-center gap-4 bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <i class="fas fa-check-circle text-lg"></i>
                    {{ session()->get('success') }}
                </div>
            @endif

            <form action="{{route('dealer.auth')}}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">E-Posta</label>
                    <input type="email" name="email" required
                           class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent rounded-[24px] outline-none transition-all font-bold text-slate-700 focus:bg-white focus:border-violet-600 focus:ring-4 focus:ring-violet-600/5 placeholder:text-slate-300"
                           placeholder="bayi@sirket.com">
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Şifre</label>
                    <input type="password" name="password" required
                           class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent rounded-[24px] outline-none transition-all font-bold text-slate-700 focus:bg-white focus:border-violet-600 focus:ring-4 focus:ring-violet-600/5 placeholder:text-slate-300"
                           placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-5 bg-slate-900 hover:bg-black text-white rounded-[24px] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                        Panele Giriş Yap
                        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="mt-12 space-y-4">
                <p class="text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">Diğer Giriş Kanalları</p>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('restaurant.login') }}" class="py-4 border-2 border-slate-50 rounded-2xl flex items-center justify-center hover:bg-slate-50 transition-all">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Restoran Girişi</span>
                    </a>
                    <a href="{{ route('admin.login') }}" class="py-4 border-2 border-slate-50 rounded-2xl flex items-center justify-center hover:bg-slate-50 transition-all">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Yönetici Girişi</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
