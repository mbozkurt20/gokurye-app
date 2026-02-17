@extends('restaurant.layouts.app')

@section('content')
    <div class="card card-body container py-5">
        <h2 class="mb-4 text-center">Menü Tasarımı Seç</h2>

        <form method="POST" action="{{ route('restaurant.menu.template.select') }}">
            @csrf
            <div class="row">
                @foreach([
                    ['key' => 'first', 'title' => 'Tasarım 1', 'img' => '/theme/images/img.png'],
                    ['key' => 'second', 'title' => 'Tasarım 2', 'img' => '/theme/images/img_1.png'],

                ] as $template)
                    <div class="col-md-4 mb-4">
                        <label class="template-card w-100 h-100">
                            <input style="display: none"  @if($template['key'] == auth()->user()->menu_template) checked @endif type="radio" name="template" value="{{ $template['key'] }}">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ $template['img'] }}" class="card-img-top img-fluid" alt="{{ $template['title'] }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $template['title'] }}</h5>
                                </div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="special-button px-4">Seçimi Kaydet</button>
            </div>
        </form>
    </div>

    <style>
        /* Kart genel stil */
        .template-card {
            cursor: pointer;
            display: block;
            text-decoration: none;
        }

        /* Hover efekti */
        .template-card:hover .card {
            transform: translateY(-4px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        /* Seçili olan kart */
        .template-card input:checked + .card {
            border: 3px solid #4f46e5;
            box-shadow: 0 0 12px rgba(13, 110, 253, 0.4);
        }

        /* Görselleri orantılı kesme */
        .card-img-top {
            object-fit: cover;
            height: 250px;
            border-bottom: 1px solid #e9ecef;
        }

        /* Kart başlığı */
        .card-title {
            font-weight: 600;
            margin-top: 10px;
        }

    </style>
@endsection
