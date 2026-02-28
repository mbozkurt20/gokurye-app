
<!DOCTYPE html>
<html lang="tr" class="h-full bg-[#f8fafc]">

@include('dealer.layouts.partials.head')

<body class="h-full antialiased text-slate-900 overflow-x-hidden">

<div id="page-loader" class="fixed inset-0 z-[999] flex items-center justify-center bg-white transition-opacity duration-500">
    <div class="flex flex-col items-center gap-4">
        <div class="relative w-16 h-16">
            <div class="absolute inset-0 border-4 border-violet-100 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-t-violet-600 rounded-full animate-spin"></div>
        </div>
        <span class="text-[10px] font-black text-violet-600 uppercase tracking-[0.3em] animate-pulse">Yükleniyor</span>
    </div>
</div>

<div id="main-wrapper" class="flex min-h-screen">

    @include('dealer.layouts.partials.sidebar')

    <div class="flex flex-col flex-1 min-w-0">

        @include('dealer.layouts.partials.header')

        <main id="app-content" class="flex-1 p-4 lg:p-10 opacity-0 translate-x-8 transition-all duration-700 ease-out">
            <div class="mx-auto">
                @yield('content')
            </div>
        </main>

        @include('dealer.layouts.partials.footer')
    </div>
</div>

@yield('scripts')
@include('dealer.layouts.partials.scripts')
</body>
</html>
