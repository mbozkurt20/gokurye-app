<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('site.name') }} | Kontrol Paneli</title>

    <link rel="shortcut icon" type="image/png" href="{{ config('site.logo') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#5850ec', // Görseldeki Indigo Moru
                            dark: '#4338ca',
                            light: '#eef2ff'
                        },
                        slate: {
                            950: '#0f172a'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="{{ asset('theme/css/chartist.min.css') }}">
    <link href="{{ asset('theme/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/css/style.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" referrerpolicy="no-referrer" />

    <link href="{{ asset('pos/assets/css/ui.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('pos/assets/css/OverlayScrollbars.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>

    <style>
        /* Perde her zaman 1040 kalsın */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        /* Modal her zaman perdeden yüksek olsun */
        .modal {
            z-index: 1050 !important;
        }

        /* Modal içindeki içerik en üstte kalsın */
        .modal-content {
            z-index: 1060 !important;
            pointer-events: auto !important;
        }

        /* Sidebar ve Header'ı zemine sabitle */
        #main-wrapper aside,
        #main-wrapper header {
            z-index: 10 !important;
        }

        /* Bootstrap Karartma Perdesi (Backdrop) */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        /* Modal Kutusu */
        .modal {
            z-index: 1050 !important;
        }

        /* Tıklanabilirlik ve Kaydırma Kontrolü */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        #dateModal .modal-content {
            pointer-events: auto !important;
        }
    </style>
</head>
