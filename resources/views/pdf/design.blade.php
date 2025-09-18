<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            width: 1080px;
            height: 1080px;

            background: url('{{ asset('assets/img/design-bg.png') }}') no-repeat center center;
            background-size: cover;
            padding: 40px;
        }

        :root {
            --primary: #ECC631;
            --secondary: #333127;
            --text: #222222;
            --bg: #f0f0f0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 120px;
        }

        .products {
            @if(count($products) > 4)
                display: grid;
            @else
                display: flex;
            @endif
            flex-wrap: wrap;
            justify-content: center;
            gap: 5rem;
        }

        .product {
            width: 200px;
            text-align: center;
        }

        .product img {
            width: 180px;
            height: auto;
        }

        .price {
            background: #FFD700;
            padding: 5px 10px;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 5px;
            display: inline-block;
        }

        .price-shape {
            width: 204.23px;
            height: 108.12px;
            background: url('{{ asset('assets/img/price-shape.png') }}') no-repeat center center;
            background-size: contain;
            position: absolute;
            left: -20%;
            bottom: -20%;
            display: flex;
            justify-content: space-between;
            align-items: start;
            flex-direction: column;
            gap: 0.25rem;
            box-shadow: -20px 10px 5px #22222280;
        }

        .price-text-xl {
            height: 60px;
            color: var(--primary);
            font-size: 1.25rem;
            line-height: 1.25rem;
            font-weight: 800;
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
        }

        .product-image {
            width: 100%;
            height: 300px;
            object-fit: contain;

        }
    </style>
</head>

<body>
    <div class="header flex justify-around items-center" style="border-bottom: 5px solid #ECC631;">
        <div class="logo-side flex justify-center items-center flex-col gap-2">
            <img src="{{ asset('assets/img/logo-dark.png') }}" alt="Logo" />
            <h2 class="text-white font-bold" style="font-size: 22pt;">
                Coin <span style="color: #ecc631;">Market</span>
            </h2>
        </div>
        <div class="contact flex justify-center items-center gap-4">
            <div class="whatsapp flex justify-center items-center gap-4">
                <i class="fa-brands fa-whatsapp" style="font-size:40px;color: #ECC631;"></i>
                <span class="text-white font-bold" style="font-size: 30pt;">72 34 97 93</span>
            </div>
            <div class="phone flex justify-center items-center gap-4">
                <i class="fa-solid fa-phone" style="font-size:40px;color: #ECC631;"></i>
                <span class="text-white font-bold" style="font-size: 30pt;">09 21 26 72</span>
            </div>
        </div>
        <div class="shape-side">
            <img src="{{ asset('assets/img/design-shape.png') }}" alt="">
        </div>
    </div>
    <div class="w-full flex justify-center items-center">
        <div class="products grid grid-cols-3 grid-rows-2">
            @foreach ($products as $product)
                <div class="relative w-full max-w-sm rounded-lg shadow-sm">
                    <a href="#" class="flex justify-center items-center">
                        <img class="product-image" src="{{ asset('storage/' . $product->image_path) }}"
                            alt="product image" />
                    </a>
                    <div class="price-shape pt-2 px-5 pb-5">
                        <a href="#">
                            <h5 class="price-text-xl line-clamp-2 font-semibold tracking-tight text-gray-900">
                                {{ $product->name }}
                            </h5>
                        </a>
                        <div class="flex items-end justify-between gap-2">
                            <span style="width: 90px;height: 25px;background: var(--primary);color:var(--secondary);"
                                class="font-black text-xl text-center rounded-lg">
                                {{ $product->price }}$
                            </span>
                            <a href="#" style="color: var(--secondary);" class="font-black text-xl text-center">
                                {{ $product->weight }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>\
</body>

</html>
