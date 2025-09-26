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

        :root {
            --primary: #ECC631;
            --secondary: #333127;
            --text: #222222;
            --bg: #f0f0f0;
        }

        body {
            width: 100%;
            height: 100vh;
            background: url('{{ asset('assets/img/design-bg-2.png') }}') no-repeat center center;
            background-size: cover;
            padding: 20px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .design-container {
            width: 100%;
            border: 7px solid #ECC631;
            border-radius: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 140px;
        }

        .products {
            width: 100%;
            min-height: calc(100vh - 350px);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 3rem;
        }

        .product {
            width: 45%;
            display: flex;
            justify-content: center;
            align-items: end;
            /* gap: 1rem; */
        }

        .image-shape {
            position: relative;
            width: 60%;
            @if(count($products) > 4)
                height: 500px;
            @else
                height: 600px;
            @endif
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 1rem;
            border: 5px solid var(--primary);
            border-radius: 20px 20px 0px 20px;
        }

        .image-shape .product-image {
            width: 100%;
            height: 100%;
            /* @if ($products->count() > 4)
            max-height: 400px;
        @endif
        */ object-fit: contain;
        }

        .weight {
            position: absolute;
            top: 0%;
            left: 0%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 15%;
            padding: 1rem;
            color: white;
            background-color: red;
            border-radius: 15px 0px 15px;
            font-size: 3rem;
            font-weight: 800;
            line-height: 2rem;
        }

        .price-shape {
            width: 40%;
            background: #f8f8f8;
            border-top: 5px solid var(--primary);
            border-right: 5px solid var(--primary);
            box-shadow: 20px 10px 10px #22222280;
        }

        .name {
            width: 100%;
            padding: 1rem;
            color: var(--secondary);
            font-size: 3rem;
            font-weight: 800;
            line-height: 4rem;
            text-align: justify;
        }

        .price {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 25%;
            padding: 1rem;
            color: var(--secondary);
            background-color: var(--primary);
            font-size: 4rem;
            font-weight: 800;
        }
    </style>
</head>

<body>
    <div class="design-container">
        <div class="header flex justify-between items-center p-4"
            style="border-bottom: 7px solid #ECC631;background:#fdedd390;">
            <div class="logo-side flex justify-center items-center flex-col gap-2">
                <img src="{{ asset('assets/img/logo-light.png') }}" alt="Logo" />
                <h2 class="font-bold text-nowrap" style="color:var(--secondary);font-size: 70px;">
                    Coin <span style="color: #ecc631;">Market</span>
                </h2>
            </div>
            <div class="contact flex justify-center items-center gap-4">
                <div class="whatsapp flex justify-center items-center gap-4">
                    <i class="fa-brands fa-whatsapp" style="font-size:75px;color: #ECC631;"></i>
                    <span class="font-bold" style="color:var(--secondary);font-size: 70px;">71 34 97 93</span>
                </div>
                <div class="phone flex justify-center items-center gap-4">
                    <i class="fa-solid fa-phone" style="font-size:75px;color: #ECC631;"></i>
                    <span class="font-bold" style="color:var(--secondary);font-size: 70px;">09 21 26 72</span>
                </div>
            </div>
            <div class="shape-side">
                <img src="{{ asset('assets/img/design-shape-1.png') }}" alt="">
            </div>
        </div>
        <div class="products">
            @foreach ($products as $product)
                <div class="product">
                    <div class="image-shape">
                        <img class="product-image" src="{{ asset('storage/' . $product->image_path) }}"
                            alt="product image" />
                        <span class="weight">
                            {{ $product->weight }}
                        </span>
                    </div>
                    <div class="price-shape">
                        <h5 class="name">
                            {{ $product->name }}
                        </h5>
                        <span class="price">
                            {{ $product->price }}$
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>\
</body>

</html>
