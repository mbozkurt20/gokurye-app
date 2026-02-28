@extends('restaurant.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Uygulamalar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Uygulamalar</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yükle</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card text-center shadow-sm border-0">

                    <div class="card-body">
                        <img src="/theme/images/print.jpg" class="img-fluid card-img-top p-4" alt="{{config('site.name')}} Yazıcı">
                        <h5 class="card-title">{{config('site.name')}} Yazıcı</h5>
                        <p class="card-text">
                            {{config('site.name')}} yazıcınızı indirip kurun. Kurulum sırasında sizden <strong>Restaurant ID</strong> istenecektir.
                            Lütfen aşağıdaki ID’yi giriniz:
                        </p>
                        <p class="bg-black text-white rounded-circle d-inline-block p-3 fw-bold" style="font-size: 2rem; min-width: 50px; text-align: center;">
                            {{ auth()->id() }}
                        </p>
                        <br><br>
                        <a href="/apps/go-kurye.exe" download class="btn btn-primary btn-lg">
                            İndir
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 15px;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-title {
            font-weight: bold;
            font-size: 1.25rem;
        }
        .card-text {
            color: #555;
        }
        .btn-primary {
            background-color: #259a38;
            border: none;
        }
        .btn-primary:hover {
            background-color: #112d50;
        }
    </style>
@endsection
