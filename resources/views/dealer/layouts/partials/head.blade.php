
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('site.name') }} Partner Paneli">
    <meta name="format-detection" content="telephone=no">

    <title>{{ config('site.name') }} | Partner Paneli</title>

    <link rel="shortcut icon" type="image/png" href="{{ config('site.logo') }}">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#7c3aed',
                            dark:    '#6d28d9',
                            light:   '#f5f3ff'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Plus Jakarta Sans', 'sans-serif']
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body { font-family: 'Inter', 'Plus Jakarta Sans', sans-serif; }

        /* Override old theme: #main-wrapper was opacity:0 waiting for custom.min.js */
        #main-wrapper { opacity: 1 !important; }

        /* Modal z-index */
        .modal-backdrop { z-index: 1040 !important; }
        .modal          { z-index: 1050 !important; }
        .modal-content  { z-index: 1060 !important; pointer-events: auto !important; }
        #main-wrapper aside, #main-wrapper header { z-index: 10 !important; }
        body.modal-open { overflow: hidden !important; padding-right: 0 !important; }

        /* DataTables */
        .dataTables_wrapper .dataTables_paginate { text-align: left !important; float: left !important; margin-top: 5px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 6px 14px; margin-right: 6px; min-width: 70px; box-sizing: border-box; }

        /* Buttons */
        .special-button {
            background-color: #7c3aed;
            color: white !important;
            padding: 0.6rem 1.25rem;
            font-size: 0.82rem;
            font-weight: 700;
            border: none;
            border-radius: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .special-button:hover { background-color: #6d28d9; transform: translateY(-1px); }
        .special-ok-button {
            background-color: #7c3aed;
            color: white !important;
            padding: 0.6rem 1.25rem;
            font-size: 0.82rem;
            font-weight: 700;
            border: none;
            border-radius: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        .special-ok-button:hover { background-color: #6d28d9; }

        /* Breadcrumb / text-head */
        .text-head {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.9), rgba(109, 40, 217, 0.85));
            backdrop-filter: blur(8px);
            padding: 14px 24px;
            border-radius: 14px;
            color: white;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.2);
        }
        .text-head h2 { color: white; margin: 0; font-weight: 700; }
        .breadcrumb { margin: 0; background: transparent; padding: 0; }
        .breadcrumb-item a { text-decoration: none; color: rgba(255,255,255,.85); font-weight: 600; }
        .breadcrumb-item.active { color: white; }
        .breadcrumb-item + .breadcrumb-item::before { content: "›"; color: #c4b5fd; font-weight: bold; padding-right: 8px; }

        /* Toast alerts */
        .custom-alert {
            position: fixed; top: 20px; right: 20px;
            min-width: 300px; max-width: 350px;
            padding: 15px 20px; border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            color: #fff; display: flex; align-items: center;
            justify-content: space-between; z-index: 9999;
            animation: toastSlideIn 0.4s ease;
        }
        .custom-alert.success { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .custom-alert.error   { background: linear-gradient(135deg, #f43f5e, #dc2626); }
        @keyframes toastSlideIn {
            from { opacity: 0; transform: translateX(100%); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .close-btn { margin-left: auto; color: #fff; font-weight: bold; font-size: 18px; cursor: pointer; }
        .alert-message { flex-grow: 1; font-size: 14px; padding-right: 10px; }
    </style>
</head>
