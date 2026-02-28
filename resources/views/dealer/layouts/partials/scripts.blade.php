<script src="{{ asset('theme/js/global.min.js') }}"></script>
<script src="{{ asset('theme/js/Chart.bundle.min.js') }}"></script>
<script src="{{ asset('theme/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('theme/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/js/datatables.init.js') }}"></script>
<script src="{{ asset('theme/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('theme/js/select2.full.min.js') }}"></script>
<script src="{{ asset('theme/js/select2-init.js') }}"></script>

<script>
    window.addEventListener('load', function () {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.style.opacity = '0';
            loader.style.pointerEvents = 'none';
            setTimeout(() => loader.remove(), 500);
        }
        const content = document.getElementById('app-content');
        if (content) {
            setTimeout(() => {
                content.classList.remove('opacity-0', 'translate-x-8');
            }, 100);
        }
    });
</script>
