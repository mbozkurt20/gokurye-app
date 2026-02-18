<aside class="w-72 bg-white border-r border-slate-100 min-h-screen hidden lg:flex flex-col sticky top-0 z-50">

    <div class="p-8">
        <div class="flex items-center gap-3 px-2 mb-10">
            <div class="w-10 h-10 bg-brand rounded-xl flex items-center justify-center shadow-lg shadow-brand/20">
                <i class="fa-solid fa-rocket text-white"></i>
            </div>
            <div>
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-tighter leading-none">Go Kurye</h2>
                <span class="text-[9px] font-bold text-brand uppercase tracking-widest opacity-80">Yönetim Paneli</span>
            </div>
        </div>

        <nav class="space-y-1">

            <div class="py-1">
                <button onclick="toggleSubmenu('dash-menu')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl {{ request()->is('admin') || request()->is('admin/statistics') ? 'bg-brand/5 text-brand' : 'text-slate-500 hover:bg-brand/5 hover:text-brand' }} transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Panel</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="dash-menu-icon"></i>
                </button>
                <div id="dash-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ url('/admin') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors tracking-wide uppercase">Anasayfa</a>
                    <a href="{{ url('/admin/statistics') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors tracking-wide uppercase">Genel Raporlar</a>
                </div>
            </div>

            <div class="py-1">
                <button onclick="toggleSubmenu('res-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-shop w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Restaurantlar</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="res-menu-icon"></i>
                </button>
                <div id="res-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('admin.restaurants') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Restaurant Listesi</a>
                    <a href="{{ route('admin.restaurants.new') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Yeni Ekle</a>
                </div>
            </div>

            <div class="py-1">
                <button onclick="toggleSubmenu('cour-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-motorcycle w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Kuryeler</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="cour-menu-icon"></i>
                </button>
                <div id="cour-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('admin.couriers') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Tüm Kuryeler</a>
                    <a href="{{ route('admin.couriers.new') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Kurye Ekle</a>
                    <a href="{{ route('admin.couriers.maps') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Kurye Takip</a>
                    <a href="{{ route('admin.courier.performance') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Performans</a>
                </div>
            </div>

            <div class="py-1">
                <button onclick="toggleSubmenu('order-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-utensils w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Siparişler</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="order-menu-icon"></i>
                </button>
                <div id="order-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('admin.deliveredOrders') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Teslim Edilenler</a>
                    <a href="{{ route('admin.deletedOrders') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">İptal Edilenler</a>
                </div>
            </div>

            <div class="py-1">
                <button onclick="toggleSubmenu('acc-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Muhasebe</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="acc-menu-icon"></i>
                </button>
                <div id="acc-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('admin.expenses.index') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Genel Giderler</a>
                    <a href="{{ route('admin.progress_payment.restaurant') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Restoran Paket Raporu</a>
                    <a href="{{ route('admin.progress_payment.courier') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Kurye Hakedişler</a>
                </div>
            </div>

            <a href="{{ route('admin.reports') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                <i class="fa-solid fa-chart-line w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Raporlar</span>
            </a>

            <div class="py-1">
                <button onclick="toggleSubmenu('ent-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-plug w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Entegrasyonlar</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform" id="ent-menu-icon"></i>
                </button>
                <div id="ent-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('admin.sms.entegrations') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Sms Entegrasyonu</a>
                </div>
            </div>

            <a href="{{ route('admin.balance') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                <i class="fa-solid fa-money-bill-wave w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Bakiye</span>
            </a>

            <a href="{{ route('admin.features') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                <i class="fa-solid fa-gears w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Ayarlar</span>
            </a>

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
        // Tüm menülerin listesi (birini açınca diğerini kapatmak isterseniz burayı kullanabilirsiniz)
        const allMenus = ['dash-menu', 'res-menu', 'cour-menu', 'order-menu', 'acc-menu', 'ent-menu'];

        if (menu.classList.contains('hidden')) {
            // İsteğe bağlı: Diğerlerini kapat
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
