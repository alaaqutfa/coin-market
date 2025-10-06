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
            background: url('{{ asset('assets/img/design-bg-3.png') }}') no-repeat center center;
            background-size: cover;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .design-container {
            width: 100%;
            height: 100%;
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
            min-height: calc(100vh - 30rem);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem;
        }

        .product {
            width: 45%;
            display: flex;
            justify-content: center;
            align-items: end;
            flex-direction: column;
            /* gap: 1rem; */
        }

        .image-shape {
            position: relative;
            width: 100%;
            @if (count($products) > 4)
                height: 350px;
            @else
                height: 450px;
            @endif
            padding: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 1rem;
            /* border: 5px solid var(--primary); */
            border-radius: 20px 20px 0px 0px;
        }

        .image-shape .product-image {
            width: 100%;
            height: 100%;
            /* @if ($products->count() > 4)
            max-height: 400px;
        @endif
        */ object-fit: contain;
        }

        .price-weight-shape {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: absolute;
            bottom: 0%;
            left: 0%;
            width: 100%;
            height: 15%;
            padding: 0.5rem;
            background-color: var(--primary);
        }

        .weight {
            color: var(--secondary);
            font-size: 3rem;
            font-weight: 800;
            line-height: 2rem;
        }

        .price-shape {
            width: 100%;
            height: 150px;
            box-shadow: 20px 10px 10px #22222280;
        }

        .name {
            width: 100%;
            padding: 1rem;
            color: var(--primary);
            font-size: 2.25rem;
            font-weight: 800;
            text-align: center;
        }

        .price {
            color: var(--secondary);
            font-size: 3rem;
            font-weight: 800;
        }
    </style>
</head>

<body>
    <div class="design-container">
        <div class="top-products" style="height: 18.5rem;"></div>
        <div class="products">
            @foreach ($products as $product)
                <div class="product">
                    <div class="image-shape">
                        <img class="product-image" src="{{ asset('storage/' . $product->image_path) }}"
                            alt="product image" />
                            <div class="price-weight-shape">
                                <span class="price">
                                    {{ $product->price }}$
                                </span>
                                @if ($product->weight != 0)
                                    <span class="weight">
                                        {{ $product->weight }}
                                    </span>
                                @endif
                            </div>
                    </div>
                    <div class="price-shape">
                        <h5 class="name">
                            {{ $product->name }}
                        </h5>

                    </div>
                </div>
            @endforeach
        </div>
        <div class="contct-products" style="height: 10rem;"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>\
</body>

</html>
