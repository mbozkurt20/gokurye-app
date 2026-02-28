<aside class="w-72 bg-white border-r border-slate-100 min-h-screen hidden lg:flex flex-col sticky top-0 z-50">

    <div class="p-8">
        <div class="flex items-center gap-3 px-2 mb-10">
            <div class="w-10 h-10 bg-brand rounded-xl flex items-center justify-center shadow-lg shadow-brand/20">
                <i class="fa-solid fa-rocket text-white"></i>
            </div>
            <div>
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-tighter leading-none">Go Kurye</h2>
                <span class="text-[9px] font-bold text-brand uppercase tracking-widest opacity-80">Süper Admin</span>
            </div>
        </div>

        <nav class="space-y-1">

            <div class="py-1">
                <a href="{{ route('superadmin.dashboards') }}"
                   class="flex items-center gap-4 px-4 py-3 rounded-2xl {{ request()->routeIs('superadmin.dashboards') ? 'bg-brand/5 text-brand' : 'text-slate-500 hover:bg-brand/5 hover:text-brand' }} transition-all group">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Dashboard</span>
                </a>
            </div>

            <div class="py-1">
                <a href="{{ route('superadmin.orders') }}"
                   class="flex items-center gap-4 px-4 py-3 rounded-2xl {{ request()->routeIs('superadmin.orders') ? 'bg-brand/5 text-brand' : 'text-slate-500 hover:bg-brand/5 hover:text-brand' }} transition-all group">
                    <i class="fa-solid fa- cart-shopping w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Siparişler</span>
                </a>
            </div>

            <div class="py-1">
                <a href="{{ route('superadmin.reports') }}"
                   class="flex items-center gap-4 px-4 py-3 rounded-2xl {{ request()->routeIs('superadmin.reports') ? 'bg-brand/5 text-brand' : 'text-slate-500 hover:bg-brand/5 hover:text-brand' }} transition-all group">
                    <i class="fa-solid fa-chart-line w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Raporlar</span>
                </a>
            </div>

            <div class="py-1">
                <button onclick="toggleSubmenu('ent-menu')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-plug w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Entegrasyonlar</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="ent-menu-icon"></i>
                </button>
                <div id="ent-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('superadmin.payment.entegrations') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Sanal Pos</a>
                </div>
            </div>

            <div class="py-1">
                <button onclick="toggleSubmenu('admin-menu')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-user-shield w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Yöneticiler</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="admin-menu-icon"></i>
                </button>
                <div id="admin-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('superadmin.admin') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Yönetici Listesi</a>
                    <a href="{{ route('superadmin.admin_create') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Yönetici Ekle</a>
                </div>
            </div>

            <div class="py-1">
                <button onclick="toggleSubmenu('partner-menu')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-handshake w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Partnerler</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="partner-menu-icon"></i>
                </button>
                <div id="partner-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('superadmin.dealer') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Partner Listesi</a>
                    <a href="{{ route('superadmin.dealer_create') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Partner Ekle</a>
                </div>
            </div>

        </nav>
    </div>

    <div class="mt-auto p-6">
        <a href="https://download.anydesk.com/AnyDesk.exe" class="block bg-brand-light/50 border border-brand/10 rounded-3xl p-4 transition-all hover:bg-brand/5 group">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-brand/10 flex items-center justify-center border border-brand/20 group-hover:bg-brand group-hover:text-white transition-all">
                    <i class="fa-solid fa-headset text-xs"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-700 uppercase leading-none">TEKNİK DESTEK</p>
                    <p class="text-[9px] font-bold text-brand uppercase tracking-widest mt-1">Her zaman hazır</p>
                </div>
            </div>
        </a>
    </div>
</aside>

<script>
    function toggleSubmenu(id) {
        const menu = document.getElementById(id);
        const icon = document.getElementById(id + '-icon');
        const allMenus = ['ent-menu', 'admin-menu', 'partner-menu'];

        if (menu.classList.contains('hidden')) {
            allMenus.forEach(mId => {
                const m = document.getElementById(mId);
                const i = document.getElementById(mId + '-icon');
                if (m && mId !== id) {
                    m.classList.add('hidden');
                    if(i) i.classList.remove('rotate-180');
                }
            });

            menu.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>
