<!DOCTYPE html>
<html lang="tr" class="h-full bg-[#f8fafc]">

@include('admin.layouts.partials.head')

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

    @include('admin.layouts.partials.sidebar')

    <div class="flex flex-col flex-1 min-w-0">

        @include('admin.layouts.partials.header')

        <main id="app2-content" class="flex-1 p-4 lg:p-10 opacity-0 translate-x-8 transition-all duration-700 ease-out">
            <div class=" mx-auto">
                @yield('content')
            </div>
        </main>

        @include('admin.layouts.partials.footer')
    </div>
</div>

<audio id="notif-sound" src="{{ url('pos/audio/new_beep.mp3') }}" preload="auto" muted></audio>

@include('admin.layouts.partials.scripts')

</body>
</html>
