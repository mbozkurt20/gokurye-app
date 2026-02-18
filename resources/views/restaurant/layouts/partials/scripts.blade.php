<script src="{{ asset('theme/js/global.min.js') }}"></script>
<script src="{{ asset('theme/js/Chart.bundle.min.js') }}"></script>
<script src="{{ asset('theme/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('theme/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/js/datatables.init.js') }}"></script>
<script src="{{ asset('theme/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('theme/js/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('theme/js/form-repeater.int.js') }}"></script>
<!-- Chart piety plugin files -->
<script src="{{ asset('theme/js/select2.full.min.js') }}"></script>
<script src="{{ asset('theme/js/select2-init.js') }}"></script>
<!-- Dashboard 1 -->
<script src="{{ asset('theme/js/dashboard-1.js') }}"></script>
<script src="{{ asset('theme/js/custom.min.js') }}"></script>
<script src="{{ asset('theme/js/deznav-init.js') }}"></script>

{{-- <script src="{{ asset('pos/assets/js/jquery-2.0.0.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('pos/assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('pos/assets/js/OverlayScrollbars.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://panel.parskurye.net/theme/js/sweetalert2.all.min.js"></script>
--}}

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script>
    // Sayfa İlk Yüklendiğinde
    window.addEventListener('load', () => {
        const loader = document.getElementById('page-loader');
        const content = document.getElementById('app-content');

        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
            // İçeriği kaydırarak göster
            content.classList.remove('opacity-0', 'translate-x-8');
            content.classList.add('opacity-100', 'translate-x-0');
        }, 500);
    });

    // Link Tıklamalarında Akıcı Geçiş (Transition)
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href && href.includes(window.location.origin) && !link.target && !href.includes('#') && !href.includes('javascript')) {
                const loader = document.getElementById('page-loader');
                loader.style.display = 'flex';
                setTimeout(() => loader.style.opacity = '1', 10);
            }
        });
    });

    // DRAWER (Sipariş Paneli) YÖNETİMİ
    function toggleDrawer() {
        const container = document.getElementById('drawerContainer');
        const overlay = document.getElementById('drawerOverlay');

        if (container.classList.contains('translate-x-full')) {
            container.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
            document.body.classList.add('drawer-open'); // Sidebar'ı karartmak için şart
        } else {
            container.classList.add('translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => {
                overlay.classList.add('hidden');
                document.body.classList.remove('drawer-open');
            }, 300);
        }
    }
</script>
