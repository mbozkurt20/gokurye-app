<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $data['name'] }} - Menü</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        h2, h3, h4 {
            font-family: 'Playfair Display', serif;
        }
        .menu-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            border: 2px solid #f3f4f6;
        }
        .menu-card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #facc15;
        }
        .price-tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: linear-gradient(135deg, #facc15, #f59e0b);
            color: #1f2937;
            font-weight: bold;
            border-radius: 9999px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-white text-gray-800">

<!-- Menü Bölümü -->
<div id="menu" class="bg-fixed bg-center bg-cover" style="background-image: url('img/antique-cafe-bg-02.jpg');">
    <div class="min-h-screen flex items-center justify-center py-16 px-4 bg-white bg-opacity-90">
        <div class="max-w-7xl w-full text-center">

            <!-- Başlık -->
            <h2 class="text-4xl md:text-5xl font-bold mb-12 py-4 px-8 rounded-lg border-b-4 border-yellow-400 inline-block bg-yellow-50 text-gray-900">
                {{ $data['name'] }} - Menü
            </h2>

            <!-- Kategoriler -->
            @foreach($data['categories'] as $category)
                <div class="mb-16">
                    <h3 class="text-3xl font-semibold mb-8 text-yellow-600 border-b-4 border-yellow-400 inline-block pb-2">
                        {{ $category->name }}
                    </h3>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 px-4">
                        @foreach($category->products as $product)
                            <div class="bg-white rounded-lg shadow-sm menu-card p-4 flex flex-col sm:flex-row items-start sm:items-center">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full sm:w-48 h-48 object-cover rounded-lg mb-4 sm:mb-0 sm:mr-4 border-2 border-gray-200" />
                                @else
                                    <div class="w-full sm:w-48 h-48 bg-gray-100 rounded-lg mb-4 sm:mb-0 sm:mr-4 flex items-center justify-center text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1 text-left">
                                    <h4 class="text-xl font-semibold mb-2 text-yellow-700">{{ $product->name }}</h4>
                                    @if($product->details)
                                        <p class="mb-3 text-sm text-gray-600">{{ $product->details }}</p>
                                    @endif
                                    <span class="price-tag">{{ number_format($product->price, 2, ',', '.') }} ₺</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>

</body>
</html>
