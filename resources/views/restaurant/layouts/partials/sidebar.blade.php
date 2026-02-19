<aside class="w-72 bg-white border-r border-slate-100 min-h-screen hidden lg:flex flex-col sticky top-0 z-50">

    <div class="p-8">
        <div class="flex items-center gap-3 px-2 mb-10">
            <div class="w-10 h-10 bg-brand rounded-xl flex items-center justify-center shadow-lg shadow-brand/20">
                <i class="fa-solid fa-rocket text-white"></i>
            </div>
            <div>
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-tighter leading-none">GPS KURYE</h2>
                <span class="text-[9px] font-bold text-brand uppercase tracking-widest opacity-80">Restoran Paneli</span>
            </div>
        </div>

        <nav class="space-y-1">

            <a href="{{ url('/restaurant') }}"
               class="flex items-center gap-4 px-4 py-3 rounded-2xl transition-all duration-300 {{ request()->is('restaurant') ? 'bg-brand text-white shadow-xl shadow-brand/30' : 'text-slate-500 hover:bg-brand/5 hover:text-brand' }}">
                <i class="fa-solid fa-house-chimney w-5 text-center text-sm"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Anasayfa</span>
            </a>

            <div class="py-2">
                <button onclick="toggleSubmenu('cust-menu')"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-users w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Müşteriler</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50" id="cust-menu-icon"></i>
                </button>
                <div id="cust-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('restaurant.customers') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors tracking-wide">Müşteri Listesi</a>
                </div>
            </div>

            <div class="py-2">
                <button onclick="toggleSubmenu('prod-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-box-archive w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Ürünler</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50" id="prod-menu-icon"></i>
                </button>
                <div id="prod-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('restaurant.categories') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Kategoriler</a>
                    <a href="{{ route('restaurant.products') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Ürün Listesi</a>
                    <a href="{{ route('restaurant.menus.qr') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">QR Menü</a>
                </div>
            </div>

            <div class="py-2">
                <button onclick="toggleSubmenu('order-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-utensils w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Siparişler</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50" id="order-menu-icon"></i>
                </button>
                <div id="order-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('restaurant.deliveredOrders') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Teslim Edilenler</a>
                    <a href="{{ route('restaurant.deletedOrders') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">İptal Edilenler</a>
                </div>
            </div>

            <div class="py-2">
                <button onclick="toggleSubmenu('report-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-chart-line w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Raporlar</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50" id="report-menu-icon"></i>
                </button>
                <div id="report-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('restaurant.reports.orders') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Sipariş Raporu</a>
                    <a href="{{ route('restaurant.reports.couriers') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Kurye Raporu</a>
                </div>
            </div>

            <div class="py-2">
                <button onclick="toggleSubmenu('coupon-menu')" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-ticket w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-wide">Kuponlar</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-50" id="coupon-menu-icon"></i>
                </button>
                <div id="coupon-menu" class="hidden pl-12 space-y-1 mt-1 border-l-2 border-brand/10 ml-6">
                    <a href="{{ route('restaurant.coupons') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Kupon Listesi</a>
                    <a href="{{ route('restaurant.coupons.new') }}" class="block py-2 text-[11px] font-semibold text-slate-400 hover:text-brand transition-colors uppercase tracking-tight">Yeni Kupon</a>
                </div>
            </div>

            <a href="{{ route('restaurant.entegrations') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group">
                <i class="fa-solid fa-plug w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Entegrasyonlar</span>
            </a>

            <a href="{{ url('/restaurant/apps') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-slate-500 hover:bg-brand/5 hover:text-brand transition-all group font-bold">
                <i class="fa-solid fa-layer-group w-5 text-center text-sm group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Uygulamalar</span>
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
                    <p class="text-[10px] font-black text-slate-700 uppercase leading-none">DESTEK AL</p>
                    <p class="text-[9px] font-bold text-brand uppercase tracking-widest mt-1">Teknik Ekip</p>
                </div>
            </div>
        </a>
    </div>
</aside>

<script>
    function toggleSubmenu(id) {
        const menu = document.getElementById(id);
        const icon = document.getElementById(id + '-icon');
        const allMenus = ['cust-menu', 'prod-menu', 'order-menu', 'report-menu', 'coupon-menu']; // Liste genişletilebilir

        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>
