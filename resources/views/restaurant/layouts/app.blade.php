<!DOCTYPE html>
<html lang="tr" class="h-full bg-[#f8fafc]">

@include('restaurant.layouts.partials.head')

<body class="h-full font-sans antialiased text-slate-900 overflow-x-hidden">

<div id="page-loader" class="fixed inset-0 z-[999] flex items-center justify-center bg-white transition-opacity duration-500">
    <div class="flex flex-col items-center gap-4">
        <div class="relative w-16 h-16">
            <div class="absolute inset-0 border-4 border-brand/10 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-t-brand rounded-full animate-spin"></div>
        </div>
        <span class="text-[10px] font-black text-brand uppercase tracking-[0.3em] animate-pulse">Yükleniyor</span>
    </div>
</div>

<div id="main-wrapper" class="flex min-h-screen">

    @include('restaurant.layouts.partials.sidebar')

    <div class="flex flex-col flex-1 min-w-0">

        @include('restaurant.layouts.partials.header')

        <main id="app-content" class="flex-1 p-4 lg:p-10 opacity-0 translate-x-8 transition-all duration-700 ease-out">
            <div class=" mx-auto">
                @yield('content')
            </div>
        </main>

        @include('restaurant.layouts.partials.footer')
    </div>
</div>

<button onclick="toggleDrawer()"
        class="fixed bottom-10 right-10 z-50 group flex items-center gap-3 px-8 py-4 rounded-full
                   bg-brand text-white shadow-[0_20px_50px_rgba(88,80,236,0.4)]
                   hover:bg-brand-dark hover:-translate-y-2 transition-all duration-300 active:scale-95">
    <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center group-hover:rotate-90 transition-transform">
        <i class="fa-solid fa-plus text-sm"></i>
    </div>
    <span class="font-bold tracking-wide text-sm">Sipariş Ekle</span>
</button>

<div id="drawerOverlay" onclick="toggleDrawer()"
     class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-[60] hidden opacity-0 transition-opacity duration-300"></div>

<div id="drawerContainer"
     class="fixed top-0 right-0 h-full w-full max-w-[90vw] lg:max-w-[1200px] bg-white z-[70] shadow-2xl transform translate-x-full transition-transform duration-500 ease-in-out border-l border-slate-100">
    @include('restaurant.orders.new')
</div>

<audio id="notif-sound" src="{{ url('pos/audio/new_beep.mp3') }}" preload="auto" muted></audio>

@include('restaurant.layouts.partials.scripts')



</body>
</html>
