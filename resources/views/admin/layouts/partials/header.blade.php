<div class="nav-header" style="background:  #ffffff">
    <a href="{{ url('/admin') }}" class="d-flex justify-content-center align-items-center mt-3">
        <div class="brand-title" style="width: 185px; height:40px">
            <img src="{{ config('site.logo') }}" alt="Logo" style="height: 50px;width: auto">
        </div>
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>

<div class="header">
    @if(config('site.test_mode') === true)
        <div class="row" style="background-color: #f3eded; color: #4f46e5;">
            <div class="text-center fw-bold py-2">
                <strong>{{config('site.name')}}</strong> Test Modu Hesabı Kullanmaktasınız.
            </div>
        </div>
    @endif

    <div class="container-fluid py-2 px-3">
        <div class="d-flex align-items-center justify-content-end w-100" style="color: #4f46e5;">

            <!-- Kalan Kontör -->

            <p class="size-4 mb-0 me-4 fw-bold " style="color: #259a38">
                Davet Kodunuz:
                <strong>[ {{\Illuminate\Support\Facades\Auth::guard('admin')->user()->code}} ]</strong>
            </p>
            <p class="size-4 mb-0 me-4 fw-bold " style="color: #259a38">
                Kontör Bakiyei:
                <strong>{{\Illuminate\Support\Facades\Auth::guard('admin')->user()->top_up_balance}}</strong>
            </p>

            <!-- Bildirimler -->
            <div class="dropdown me-3">
                <a class="nav-link position-relative" href="#" id="notificationDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-bell-fill" style="font-size: 1.5rem;"></i>
                    @if(($notifications ?? collect())->count() > 0)
                        <span id="notificationCount"
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ ($notifications ?? collect())->count() }}
                        </span>
                    @endif
                </a>

                <div class="dropdown-menu dropdown-menu-end shadow-lg p-0"
                     aria-labelledby="notificationDropdown"
                     style="width: 400px; border-radius: 10px; overflow: hidden;">

                    <div class="d-flex justify-content-between align-items-center text-white px-3 py-2"
                         style="background: #4f46e5">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bell me-2"></i>
                            <strong>Bildirimler</strong>
                        </div>
                        <div class="clear-all-container">
                            @if(($notifications ?? collect())->count() > 0)
                                <a href="javascript:void(0)" class="text-white small clear-all-link"
                                   onclick="clearAllNotifications()">Tümünü Temizle</a>
                            @endif
                        </div>
                    </div>

                    <ul class="list-unstyled mb-0 p-1 py-2" id="notificationList"
                        style="max-height: 40vh;overflow-y: scroll">
                        @forelse($notifications ?? [] as $notification)
                            <li class="border-bottom d-flex justify-content-between align-items-center p-2"
                                data-id="{{ $notification->id }}">
                                <a href="{{ $notification->url }}"
                                   class="text-decoration-none text-dark flex-grow-1 me-2">
                                    <span class="d-block fw-bold">{{ $notification->title }}</span>
                                    @if(!empty($notification->description))
                                        <small class="text-muted">{{ $notification->description }}</small>
                                    @endif
                                </a>
                                <a class="text-danger" style="cursor: pointer;"
                                   onclick="deleteNotification({{ $notification->id }})">
                                    <strong class="size-3">x</strong>
                                </a>
                            </li>
                        @empty
                            <li class="p-3 text-center text-muted no-notification">📭 Bildirim yok</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Profil -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                    <img src="/theme/images/avatar.jpg" class="rounded-circle border border-2"
                         style="height: 45px; width: 45px; border-color: #4f46e5;" alt="Avatar">
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{route('admin.profile')}}">
                            <i class="bi bi-person-circle text-primary me-2"></i> Profil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap
                        </a>
                    </li>
                </ul>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <!-- Ses Uyarısı -->
    <audio id="audioPlayer" class="d-none" controls>
        <source src="{{ asset('upload/arrived.mp3') }}" type="audio/mp3">
        Tarayıcınız ses öğesini desteklemiyor.
    </audio>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: 'mt1',
        encrypted: true
    });

    var channel = pusher.subscribe('notifications-' + {{ auth()->id() }});

    channel.bind('new-notify-' + {{ auth()->id() }}, function(data) {
        const audio = new Audio('{{ asset('voices/notifications/Bell.mp3') }}');
        audio.play().catch(err => console.error("Ses çalma başarısız:", err));

        let notificationList = document.getElementById('notificationList');

        // 📭 Bildirim yok mesajını kaldır
        let emptyItem = notificationList.querySelector('.no-notification');
        if (emptyItem) {
            emptyItem.remove();
        }

        notificationList.insertAdjacentHTML('afterbegin', `
        <li class="border-bottom d-flex justify-content-between align-items-center p-2" data-id="${data.id}">
            <a href="${data.url}" class="text-decoration-none text-dark flex-grow-1 me-2">
                <span class="d-block fw-bold">${data.title}</span>
                ${data.description ? `<small class="text-muted">${data.description}</small>` : ''}
            </a>
            <a class="text-danger" style="cursor: pointer" onclick="deleteNotification(${data.id})">
                <strong class="size-3">x</strong>
            </a>
        </li>
    `);

        updateCount(1);
        showClearAllLink();
    });

    function updateCount(change) {
        let countElem = $('#notificationCount');
        let count = parseInt(countElem.text() || '0') + change;
        if (count > 0) {
            if (countElem.length === 0) {
                $('#notificationDropdown').append(`
                    <span id="notificationCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">${count}</span>
                `);
            } else {
                countElem.text(count);
            }
        } else {
            countElem.remove();
            hideClearAllLink();
        }
    }

    function showClearAllLink() {
        if ($('.clear-all-link').length === 0) {
            $('.clear-all-container').append(`
                <a href="javascript:void(0)" class="text-white small clear-all-link" onclick="clearAllNotifications()">Tümünü Temizle</a>
            `);
        }
    }

    function hideClearAllLink() {
        $('.clear-all-link').remove();
    }

    function clearAllNotifications() {
        $.ajax({
            type: 'GET',
            url: '/admin/notifications/clear-all',
            data: {_token: '{{ csrf_token() }}'},
            success: function () {
                $('#notificationList').html('<li class="p-3 text-center text-muted">📭 Bildirim yok</li>');
                updateCount(-parseInt($('#notificationCount').text() || '0'));
            },
            error: function (err) {
                console.error(err);
            }
        });
    }

    function deleteNotification(id) {
        $.ajax({
            type: 'GET',
            url: '/admin/notifications/' + id,
            data: {_token: '{{ csrf_token() }}'},
            success: function () {
                $('#notificationList').find('li[data-id="' + id + '"]').remove();
                updateCount(-1);
                if ($('#notificationList li').length === 0) {
                    $('#notificationList').html('<li class="p-3 text-center text-muted">📭 Bildirim yok</li>');
                }
            },
            error: function (err) {
                console.error(err);
            }
        });
    }
</script>

<script>
    function autoOrders(e) {
        const durum = document.getElementById('auto_order').checked;
        const status = durum ? 1 : 0;

        console.log(status);

        $.ajax({
            type: 'GET',
            url: '/admin/order/auto_order/' + status,
            success: function (data) {
                let message = '';
                if (data == "Active") {
                    message = 'Otomatik Sipariş Aktif!';
                } else if (data == "Passive") {
                    message = 'Otomatik Sipariş Kapalı!';
                } else {
                    message = 'Bilinmeyen Durum!';
                }

                Swal.fire({
                    title: message,
                    icon: 'success',
                    text: 'Atama durumu başarıyla güncellendi.',
                    confirmButtonText: 'Tamam',
                    background: '#ffffff',
                    color: '#fff',
                    iconColor: '#4f46e5',
                    confirmButtonColor: '#4f46e5',
                    customClass: {
                        popup: 'rounded-xl shadow-2xl',
                        confirmButton: 'px-6 py-3 text-lg font-semibold',
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown',
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp',
                    }
                });
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    }
</script>
