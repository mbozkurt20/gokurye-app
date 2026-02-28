<header class="h-20 bg-white border-b border-slate-100 sticky top-0 z-40 px-6 flex items-center justify-between shadow-sm shadow-slate-200/50">

    <div class="flex items-center gap-4">
        {{-- Mobile hamburger --}}
        <button type="button" onclick="openMobileSidebar()" class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-violet-600 hover:bg-violet-50 transition-all border-0 bg-transparent cursor-pointer">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <a href="{{ url('/dealer/dashboard') }}" class="flex items-center gap-3 group transition-transform hover:scale-105" style="text-decoration:none;">
            <div class="w-11 h-11 bg-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-600/20">
                <img src="{{ config('site.logo') }}" class="h-7 w-auto object-contain brightness-0 invert" alt="Logo">
            </div>
            <div class="hidden md:block">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-tighter leading-none" style="margin:0;">{{ config('site.name') }}</h2>
                <span class="text-[10px] font-bold text-violet-600 uppercase tracking-widest">Partner Paneli</span>
            </div>
        </a>
    </div>

    <div class="flex items-center gap-4">

        {{-- Profile Dropdown --}}
        <div class="relative">
            <button type="button" onclick="toggleProfileMenu(event)"
                    class="flex items-center gap-3 p-1.5 pr-4 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 bg-transparent outline-none cursor-pointer">
                <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden border-2 border-white shadow-sm ring-1 ring-slate-100">
                    <img src="/theme/images/avatar.jpg" class="w-full h-full object-cover" alt="Profile">
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-[11px] font-black text-slate-800 uppercase leading-none" style="margin:0;">{{ Auth::guard('dealer')->user()?->name ?? config('site.name') }}</p>
                    <p class="text-[9px] font-bold text-violet-600 uppercase tracking-tighter italic" style="margin:0;">Partner Hesabı</p>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>

            <div id="premiumProfileMenu" class="absolute right-0 mt-3 w-64 opacity-0 invisible transition-all duration-300 transform translate-y-2 z-[99999]">
                <div class="bg-white rounded-3xl shadow-2xl shadow-violet-600/20 border border-slate-100 overflow-hidden p-2">
                    <div class="p-4 bg-violet-50 rounded-2xl mb-2">
                        <p class="text-[10px] font-black text-violet-600/60 uppercase tracking-[0.2em]" style="margin-bottom:4px;">Partner Hesabı</p>
                        <p class="text-xs font-bold text-slate-800 truncate" style="margin:0;">{{ Auth::guard('dealer')->user()?->email ?? '' }}</p>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('dealer.profile') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-violet-50 text-slate-600 hover:text-violet-600 transition-all text-xs font-bold uppercase tracking-tight"
                           style="text-decoration:none;">
                            <i class="fa-solid fa-circle-user text-slate-300 w-5"></i> Profilim
                        </a>
                        <hr style="margin: 4px 16px; border-color: #f1f5f9;">
                        <a href="{{ route('dealer.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('dealer-logout-form').submit();"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-rose-50 text-rose-500 transition-all text-xs font-black uppercase tracking-tight"
                           style="text-decoration:none;">
                            <i class="fa-solid fa-power-off w-5"></i> Güvenli Çıkış
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form id="dealer-logout-form" action="{{ route('dealer.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</header>

<script>
    function toggleProfileMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('premiumProfileMenu');
        menu.classList.toggle('opacity-0');
        menu.classList.toggle('invisible');
        menu.classList.toggle('translate-y-2');
        menu.classList.toggle('translate-y-0');
    }
    window.onclick = function(event) {
        const menu = document.getElementById('premiumProfileMenu');
        if (menu && !menu.classList.contains('invisible') && !menu.contains(event.target)) {
            menu.classList.add('opacity-0', 'invisible', 'translate-y-2');
            menu.classList.remove('translate-y-0');
        }
    }
</script>
