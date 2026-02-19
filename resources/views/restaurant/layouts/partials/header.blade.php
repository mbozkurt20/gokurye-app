@php
    $restaurantParentAdmin = auth()->check() && auth()->user()->admin_id
        ? \App\Models\Admin::find(auth()->user()->admin_id)
        : null;
    $isTestRestaurant = config('site.test_mode') === true || ($restaurantParentAdmin && $restaurantParentAdmin->is_test);
@endphp
@if($isTestRestaurant)
    <div class="bg-amber-50 border-b border-amber-200 py-2.5">
        <div class="container mx-auto px-6 flex justify-center items-center gap-3">
            <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
            <p class="text-[11px] font-black uppercase tracking-widest text-amber-700">
                <span class="font-extrabold">TEST HESABI</span> — Her kategoriden en fazla 2 kayıt ekleyebilirsiniz.
            </p>
        </div>
    </div>
@endif

<header class="h-20 bg-white border-b border-slate-100 sticky top-0 z-40 px-6 flex items-center justify-between shadow-sm shadow-slate-200/50">

    <div class="flex items-center gap-6">
        <a href="{{ url('/restaurant') }}" class="flex items-center gap-3 group transition-transform hover:scale-105">
            <div class="w-11 h-11 bg-brand rounded-xl flex items-center justify-center shadow-lg shadow-brand/20">
                <img src="{{ config('site.logo') }}" class="h-7 w-auto object-contain brightness-0 invert" alt="Logo">
            </div>
            <div class="hidden md:block">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-tighter leading-none">{{ config('site.name') }}</h2>
                <span class="text-[10px] font-bold text-brand uppercase tracking-widest">Kontrol Merkezi</span>
            </div>
        </a>
    </div>

    <div class="flex items-center gap-4 lg:gap-8">

        <button id="openModalBtn2"
                class="hidden md:flex items-center gap-2 px-6 py-3 rounded-xl
                   bg-brand text-white font-bold uppercase tracking-wide text-[11px]
                   hover:bg-brand-dark hover:shadow-xl hover:shadow-brand/30 transition-all active:scale-95">
            <i class="fa-solid fa-bolt-lightning text-xs"></i>
            Hızlı Sipariş
        </button>

        @include('restaurant.layouts.partials.quick_order_modal')

        <div class="relative"> <button type="button"
                                       onclick="toggleProfileMenu(event)"
                                       class="flex items-center gap-3 p-1.5 pr-4 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 bg-transparent border-0 outline-none cursor-pointer">
                <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden border-2 border-white shadow-sm ring-1 ring-slate-100">
                    <img src="{{ asset('/theme/images/resLogo.svg') }}" class="w-full h-full object-cover" alt="Profile">
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-[11px] font-black text-slate-800 uppercase leading-none m-0">Restoran Yetkilisi</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1 tracking-tighter italic m-0">Hesabım</p>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>

            <div id="premiumProfileMenu"
                 class="absolute right-0 mt-3 w-64 opacity-0 invisible transition-all duration-300 transform translate-y-2 z-[99999]">

                <div class="bg-white rounded-3xl shadow-2xl shadow-brand/20 border border-slate-100 overflow-hidden p-2">

                    <div class="p-4 bg-brand/5 rounded-2xl mb-2">
                        <p class="text-[10px] font-black text-brand/60 uppercase tracking-[0.2em] mb-1">Oturum Açıldı</p>
                        <p class="text-xs font-bold text-slate-800 truncate m-0">{{ auth()->user()->email ?? 'user@restaurant.com' }}</p>
                    </div>

                    <div class="space-y-1">
                        <a href="{{ route('restaurant.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand/5 text-slate-600 hover:text-brand transition-all text-xs font-bold uppercase tracking-tight no-underline">
                            <i class="fa-solid fa-circle-user text-slate-300 w-5"></i> Profilim
                        </a>

                        <a href="https://download.anydesk.com/AnyDesk.exe" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand/5 text-slate-600 hover:text-brand transition-all text-xs font-bold uppercase tracking-tight no-underline">
                            <i class="fa-solid fa-headset text-slate-300 w-5"></i> Teknik Destek
                        </a>

                        <hr class="mx-4 border-slate-50 my-1">

                        <a href="{{ route('restaurant.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-rose-50 text-rose-500 transition-all text-xs font-black uppercase tracking-tight no-underline">
                            <i class="fa-solid fa-power-off w-5"></i> Güvenli Çıkış
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleProfileMenu(event) {
                event.stopPropagation();
                const menu = document.getElementById('premiumProfileMenu');

                if (menu.classList.contains('invisible')) {
                    // Menüyü aç
                    menu.classList.remove('opacity-0', 'invisible', 'translate-y-2');
                    menu.classList.add('opacity-100', 'visible', 'translate-y-0');
                } else {
                    // Menüyü kapat
                    menu.classList.add('opacity-0', 'invisible', 'translate-y-2');
                    menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                }
            }

            // Ekranın başka bir yerine tıklandığında menüyü kapat
            window.onclick = function(event) {
                const menu = document.getElementById('premiumProfileMenu');
                if (!menu.classList.contains('invisible')) {
                    menu.classList.add('opacity-0', 'invisible', 'translate-y-2');
                    menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                }
            }
        </script>

        <form id="logout-form" action="{{ route('restaurant.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</header>
