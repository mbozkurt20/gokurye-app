
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content>
    <meta name="author" content>
    <meta name="robots" content>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{config('site.name')}} :  Süper Admin Ekranı">
    <meta property="og:title" content="{{config('site.name')}} :  Süper Admin Ekranı">
    <meta property="og:description" content="{{config('site.name')}} :  Süper Admin Ekranı">
    <meta property="og:image" content="images/social-image.png">
    <meta name="format-detection" content="telephone=no">

    <title>Partner - {{config('site.name')}}</title>

    @vite('resources/js/app.js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="shortcut icon" type="image/png" href="{{config('site.logo')}}">
    <link rel="stylesheet" href="{{ asset('theme/css/chartist.min.css') }}">
    <link href="{{ asset('theme/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/css/nice-select.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme/css/select2.min.css') }}">
    <link href="{{ asset('theme/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .text-head {
            background: linear-gradient(135deg, rgba(13, 38, 70, 0.9), rgba(241, 44, 109, 0.85));
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 14px 24px;
            border-radius: 14px;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            animation: fadeIn 0.6s ease-in-out;
        }

        .text-head h2 {
            color: white;
            margin: 0;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item {
            font-size: 0.95rem;
            color: white;
            position: relative;
            padding-right: 14px;
            transition: color 0.3s ease;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #4f46e5;
            font-weight: bold;
            padding-right: 10px;
            font-size: 1rem;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #ffe6f0;
            font-size: 16px;
            font-weight: bold;
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: #ffffff;
            text-shadow: 0 0 8px rgba(241, 44, 109, 0.9);
        }

        .breadcrumb-item.active {
            color: #ffffff;
            font-weight: 600;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }


        .dataTables_wrapper .dataTables_paginate {
            text-align: left !important;
            float: left !important;
            margin-top: 5px;
        }

        /* Önceki / Sonraki butonlarının genişliği ve boşluğu */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 14px; /* buton boyutu */
            margin-right: 6px; /* butonlar arası boşluk */
            min-width: 70px; /* yazılar sığsın diye minimum genişlik */
            box-sizing: border-box;
        }

        .special-button {
            background-color: #259a38; /* Indigo-600 */
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 2.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .special-button:hover {
            background-color: #112b4c; /* Indigo-700 */
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .special-ok-button {
            background-color: #4f46e5; /* Indigo-600 */
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 2.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .special-ok-button:hover {
            background-color: #4f46e5; /* Indigo-700 */
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        /* Genel alert stili */
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            max-width: 350px;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.5s ease forwards;
            cursor: default;
            z-index: 1000;
        }

        /* Başarılı alert */
        .custom-alert.success {
            background: linear-gradient(135deg, #11e65a, #3fd66b);
            border: 1px solid #28c76f;
        }

        /* Hata alert */
        .custom-alert.error {
            background: linear-gradient(135deg, #ff5858, #f09819);
            border: 1px solid #e74c3c;
        }

        .close-btn {
            margin-left: auto; /* Butonu sağa iter */
            color: #fff;
            font-weight: bold;
            font-size: 20px;
            cursor: pointer;
            user-select: none;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #000000bb;
        }

        /* Alert mesajı */
        .alert-message {
            flex-grow: 1;
            font-size: 16px;
            padding-right: 10px;
        }

        /* Slide in animasyon */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

    </style>
</head>
