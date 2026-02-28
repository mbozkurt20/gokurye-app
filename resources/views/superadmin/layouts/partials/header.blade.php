@if(config('site.test_mode') === true || (auth()->guard('admin')->check() && auth()->guard('admin')->user()->is_test))
    <div class="bg-amber-50 border-b border-amber-200 py-2.5">
        <div class="container mx-auto px-6 flex justify-center items-center gap-3">
            <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
            <p class="text-[11px] font-black uppercase tracking-widest text-amber-700">
                <span class="font-extrabold">TEST HESABI</span> — Her kategoriden en fazla 2 kayıt ekleyebilirsiniz. &nbsp;
                <a href="{{ route('superadmin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="underline text-amber-800 hover:text-amber-900">Çıkış Yap</a>
            </p>
        </div>
    </div>
@endif

<header class="h-20 bg-white border-b border-slate-100 sticky top-0 z-40 px-6 flex items-center justify-between shadow-sm shadow-slate-200/50">

    <div class="flex items-center gap-6">
        <a href="{{ url('/superadmin/dashboard') }}" class="flex items-center gap-3 group transition-transform hover:scale-105">
            <div class="w-11 h-11 bg-brand rounded-xl flex items-center justify-center shadow-lg shadow-brand/20">
                <img src="{{ config('site.logo') }}" class="h-7 w-auto object-contain brightness-0 invert" alt="Logo">
            </div>
            <div class="hidden md:block">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-tighter leading-none">{{ config('site.name') }}</h2>
                <span class="text-[10px] font-bold text-brand uppercase tracking-widest">Süper Yönetici</span>
            </div>
        </a>
    </div>

    <div class="flex items-center gap-4 lg:gap-8">

        <div class="relative">
            <button type="button" onclick="toggleNotificationMenu(event)" id="notificationDropdown" class="relative p-2.5 rounded-xl bg-slate-50 text-slate-400 hover:text-brand hover:bg-brand/5 transition-all border-0 outline-none cursor-pointer">
                <i class="fa-solid fa-bell text-lg"></i>
                @if(($notifications ?? collect())->count() > 0)
                    <span id="notificationCount" class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white border-2 border-white">
                        {{ ($notifications ?? collect())->count() }}
                    </span>
                @endif
            </button>

            <div id="notificationMenu" class="absolute right-0 mt-3 w-80 md:w-96 opacity-0 invisible transition-all duration-300 transform translate-y-2 z-[99999]">
                <div class="bg-white rounded-3xl shadow-2xl shadow-brand/20 border border-slate-100 overflow-hidden">
                    <div class="p-4 bg-brand flex justify-between items-center">
                        <span class="text-xs font-black text-white uppercase tracking-widest">Bildirimler</span>
                        <div class="clear-all-container">
                            @if(($notifications ?? collect())->count() > 0)
                                <button onclick="clearAllNotifications()" class="text-[10px] font-bold text-white/80 hover:text-white uppercase tracking-tighter bg-transparent border-0 cursor-pointer clear-all-link">Tümünü Temizle</button>
                            @endif
                        </div>
                    </div>

                    <ul id="notificationList" class="max-h-[400px] overflow-y-auto list-none m-0 p-0">
                        @forelse($notifications ?? [] as $notification)
                            <li class="border-b border-slate-50 flex items-center justify-between p-4 hover:bg-slate-50 transition-colors" data-id="{{ $notification->id }}">
                                <a href="{{ $notification->url }}" class="flex-grow no-underline">
                                    <p class="text-xs font-bold text-slate-800 m-0 leading-tight">{{ $notification->title }}</p>
                                    @if(!empty($notification->description))
                                        <p class="text-[10px] text-slate-400 m-0 mt-1">{{ $notification->description }}</p>
                                    @endif
                                </a>
                                <button onclick="deleteNotification({{ $notification->id }})" class="ml-2 text-slate-300 hover:text-rose-500 bg-transparent border-0 cursor-pointer">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </li>
                        @empty
                            <li class="p-8 text-center no-notification">
                                <i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2 block"></i>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Bildirim bulunmuyor</span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="relative">
            <button type="button" onclick="toggleProfileMenu(event)" class="flex items-center gap-3 p-1.5 pr-4 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 bg-transparent border-0 outline-none cursor-pointer">
                <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden border-2 border-white shadow-sm ring-1 ring-slate-100">
                    <img src="/theme/images/avatar.jpg" class="w-full h-full object-cover" alt="Profile">
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-[11px] font-black text-slate-800 uppercase leading-none m-0">{{ config('site.name') }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1 tracking-tighter italic m-0">Süper Yönetici</p>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
            </button>

            <div id="premiumProfileMenu" class="absolute right-0 mt-3 w-64 opacity-0 invisible transition-all duration-300 transform translate-y-2 z-[99999]">
                <div class="bg-white rounded-3xl shadow-2xl shadow-brand/20 border border-slate-100 overflow-hidden p-2">
                    <div class="p-4 bg-brand/5 rounded-2xl mb-2">
                        <p class="text-[10px] font-black text-brand/60 uppercase tracking-[0.2em] mb-1">Oturum Açıldı</p>
                        <p class="text-xs font-bold text-slate-800 truncate m-0">{{ auth()->user()->email ?? '' }}</p>
                    </div>

                    <div class="space-y-1">
                        <a href="{{ route('superadmin.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand/5 text-slate-600 hover:text-brand transition-all text-xs font-bold uppercase tracking-tight no-underline">
                            <i class="fa-solid fa-circle-user text-slate-300 w-5"></i> Profilim
                        </a>
                        <hr class="mx-4 border-slate-50 my-1">
                        <a href="{{ route('superadmin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-rose-50 text-rose-500 transition-all text-xs font-black uppercase tracking-tight no-underline">
                            <i class="fa-solid fa-power-off w-5"></i> Güvenli Çıkış
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form id="logout-form" action="{{ route('superadmin.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</header>

<audio id="audioPlayer" class="hidden" controls>
    <source src="{{ asset('upload/arrived.mp3') }}" type="audio/mp3">
</audio>

<script>
    // Menü Kontrolleri
    function toggleNotificationMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('notificationMenu');
        const profileMenu = document.getElementById('premiumProfileMenu');

        profileMenu.classList.add('opacity-0', 'invisible', 'translate-y-2');
        menu.classList.toggle('opacity-0');
        menu.classList.toggle('invisible');
        menu.classList.toggle('translate-y-2');
        menu.classList.toggle('translate-y-0');
    }

    function toggleProfileMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('premiumProfileMenu');
        const notifMenu = document.getElementById('notificationMenu');

        notifMenu.classList.add('opacity-0', 'invisible', 'translate-y-2');
        menu.classList.toggle('opacity-0');
        menu.classList.toggle('invisible');
        menu.classList.toggle('translate-y-2');
        menu.classList.toggle('translate-y-0');
    }

    window.onclick = function(event) {
        ['notificationMenu', 'premiumProfileMenu'].forEach(id => {
            const menu = document.getElementById(id);
            if (menu && !menu.classList.contains('invisible') && !menu.contains(event.target)) {
                menu.classList.add('opacity-0', 'invisible', 'translate-y-2');
                menu.classList.remove('translate-y-0');
            }
        });
    }

    // Pusher & Bildirim Dinamikleri
    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', { cluster: 'mt1', encrypted: true });
    var channel = pusher.subscribe('notifications-' + {{ auth()->id() }});

    channel.bind('new-notify-' + {{ auth()->id() }}, function(data) {
        new Audio('{{ asset('voices/notifications/Bell.mp3') }}').play().catch(e => {});

        let list = document.getElementById('notificationList');
        if (list.querySelector('.no-notification')) list.innerHTML = '';

        list.insertAdjacentHTML('afterbegin', `
            <li class="border-b border-slate-50 flex items-center justify-between p-4 hover:bg-slate-50 transition-colors" data-id="${data.id}">
                <a href="${data.url}" class="flex-grow no-underline">
                    <p class="text-xs font-bold text-slate-800 m-0 leading-tight">${data.title}</p>
                    ${data.description ? `<p class="text-[10px] text-slate-400 m-0 mt-1">${data.description}</p>` : ''}
                </a>
                <button onclick="deleteNotification(${data.id})" class="ml-2 text-slate-300 hover:text-rose-500 bg-transparent border-0 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </li>
        `);
        updateCount(1);
    });

    function updateCount(change) {
        let countElem = $('#notificationCount');
        let current = parseInt(countElem.text() || '0') + change;

        if (current > 0) {
            if (countElem.length === 0) {
                $('#notificationDropdown').append(`<span id="notificationCount" class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white border-2 border-white">${current}</span>`);
            } else {
                countElem.text(current);
            }
            if($('.clear-all-link').length === 0) {
                $('.clear-all-container').html('<button onclick="clearAllNotifications()" class="text-[10px] font-bold text-white/80 hover:text-white uppercase tracking-tighter bg-transparent border-0 cursor-pointer clear-all-link">Tümünü Temizle</button>');
            }
        } else {
            countElem.remove();
            $('.clear-all-container').empty();
            $('#notificationList').html('<li class="p-8 text-center no-notification"><i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2 block"></i><span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Bildirim bulunmuyor</span></li>');
        }
    }

    function clearAllNotifications() {
        $.get('/superadmin/notifications/clear-all', function () {
            updateCount(-999);
        });
    }

    function deleteNotification(id) {
        $.get('/superadmin/notifications/' + id, function () {
            $(`li[data-id="${id}"]`).remove();
            updateCount(-1);
        });
    }
</script>
