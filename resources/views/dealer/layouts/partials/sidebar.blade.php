<aside class="w-72 bg-white border-r border-slate-100 min-h-screen hidden lg:flex flex-col sticky top-0 z-50">

    <div class="p-8">
        <div class="flex items-center gap-3 px-2 mb-10">
            <div class="w-10 h-10 bg-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-600/20">
                <i class="fa-solid fa-handshake text-white text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-tighter leading-none" style="margin:0;">{{ config('site.name') }}</h2>
                <span class="text-[9px] font-bold text-violet-600 uppercase tracking-widest opacity-80">Partner Paneli</span>
            </div>
        </div>

        <nav class="space-y-1">

            <div class="py-1">
                <a href="{{ route('dealer.dashboards') }}"
                   class="flex items-center gap-4 px-4 py-3 rounded-2xl {{ request()->routeIs('dealer.dashboards') ? 'bg-violet-50 text-violet-600' : 'text-slate-500 hover:bg-violet-50 hover:text-violet-600' }} transition-all group"
                   style="text-decoration:none;">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Dashboard</span>
                </a>
            </div>

            <div class="py-1">
                <a href="{{ route('dealer.reports') }}"
                   class="flex items-center gap-4 px-4 py-3 rounded-2xl {{ request()->routeIs('dealer.reports*') ? 'bg-violet-50 text-violet-600' : 'text-slate-500 hover:bg-violet-50 hover:text-violet-600' }} transition-all group"
                   style="text-decoration:none;">
                    <i class="fa-solid fa-chart-line w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Raporlar</span>
                </a>
            </div>

            <div class="py-1">
                <button onclick="toggleSubmenu('admin-menu')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-violet-50 hover:text-violet-600 transition-all group bg-transparent border-0 cursor-pointer">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-user-shield w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Yöneticiler</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="admin-menu-icon"></i>
                </button>
                <div id="admin-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-violet-100 ml-6">
                    <a href="{{ route('dealer.admin') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-violet-600 transition-colors uppercase tracking-tight" style="text-decoration:none;">Yönetici Listesi</a>
                    <a href="{{ route('dealer.admin_create') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-violet-600 transition-colors uppercase tracking-tight" style="text-decoration:none;">Yönetici Ekle</a>
                </div>
            </div>

        </nav>
    </div>

    <div class="mt-auto p-6">
        <a href="{{ route('dealer.profile') }}" class="block bg-violet-50 border border-violet-100 rounded-3xl p-4 transition-all hover:bg-violet-100/50 group" style="text-decoration:none;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center border border-violet-200 group-hover:bg-violet-600 transition-all">
                    <i class="fa-solid fa-user text-xs text-violet-600 group-hover:text-white transition-colors"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-700 uppercase leading-none" style="margin:0;">{{ Auth::guard('dealer')->user()?->name ?? 'Profil' }}</p>
                    <p class="text-[9px] font-bold text-violet-600 uppercase tracking-widest mt-1" style="margin:0;">Partner Hesabı</p>
                </div>
            </div>
        </a>
    </div>
</aside>

{{-- Mobile Sidebar --}}
<div id="mobile-sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden" onclick="closeMobileSidebar()"></div>
<aside id="mobile-sidebar" class="hidden fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-2xl flex-col lg:hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-violet-600 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-handshake text-white text-sm"></i>
            </div>
            <span class="text-sm font-black text-slate-800 uppercase tracking-tighter">Partner</span>
        </div>
        <button onclick="closeMobileSidebar()" class="p-2 rounded-xl text-slate-400 hover:bg-slate-50 bg-transparent border-0">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="p-4 space-y-1 overflow-y-auto">
        <a href="{{ route('dealer.dashboards') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-violet-50 hover:text-violet-600 transition-all" style="text-decoration:none;">
            <i class="fa-solid fa-chart-pie text-sm w-5 text-center"></i>
            <span class="text-xs font-bold uppercase">Dashboard</span>
        </a>
        <a href="{{ route('dealer.reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-violet-50 hover:text-violet-600 transition-all" style="text-decoration:none;">
            <i class="fa-solid fa-chart-line text-sm w-5 text-center"></i>
            <span class="text-xs font-bold uppercase">Raporlar</span>
        </a>
        <a href="{{ route('dealer.admin') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-violet-50 hover:text-violet-600 transition-all" style="text-decoration:none;">
            <i class="fa-solid fa-user-shield text-sm w-5 text-center"></i>
            <span class="text-xs font-bold uppercase">Yöneticiler</span>
        </a>
        <a href="{{ route('dealer.admin_create') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-violet-50 hover:text-violet-600 transition-all" style="text-decoration:none;">
            <i class="fa-solid fa-user-plus text-sm w-5 text-center"></i>
            <span class="text-xs font-bold uppercase">Yönetici Ekle</span>
        </a>
    </div>
</aside>

<script>
    function toggleSubmenu(id) {
        const menu = document.getElementById(id);
        const icon = document.getElementById(id + '-icon');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    }
    function openMobileSidebar() {
        document.getElementById('mobile-sidebar').classList.remove('hidden');
        document.getElementById('mobile-sidebar').classList.add('flex');
        document.getElementById('mobile-sidebar-overlay').classList.remove('hidden');
    }
    function closeMobileSidebar() {
        document.getElementById('mobile-sidebar').classList.add('hidden');
        document.getElementById('mobile-sidebar').classList.remove('flex');
        document.getElementById('mobile-sidebar-overlay').classList.add('hidden');
    }
</script>
