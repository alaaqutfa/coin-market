<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        :root {
            --primary: #ECC631;
            --secondary: #1E1F1C;
            --text: #222222;
            --bg: #f0f0f0;
            --bg-green-screen: #00FF00;
            --card-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255,255,240,0.1);
            --transition-smooth: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        body {
            width: 100%;
            height: 100vh;
            background: var(--bg-green-screen);
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

        /* ==========  قسم المنتجات المعاد تصميمه بشكل عصري مع الحفاظ على الشفافية ========== */
        .products {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 28rem);
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.75rem;
            justify-items: center;
            align-items: start;
            padding: 1rem 2rem;
        }

        /* حالة منتج واحد فقط: يظهر في المنتصف بشكل أنيق وواضح */
        .products:has(.product:only-child) {
            grid-template-columns: 1fr;
            justify-items: center;
            align-items: center;
        }

        .products:has(.product:only-child) .product {
            max-width: 550px;
            width: 80%;
            transform: scale(1.5);
            transition: transform 0.2s ease;
        }

        /* بطاقة المنتج - خلفية شفافة بالكامل باستثناء شريط السعر الذي يحافظ على الهوية */
        .product {
            width: 100%;
            max-width: 520px;
            background: transparent;
            border-radius: 2rem;
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(0px);
            box-shadow: none;
            position: relative;
        }

        /* تأثير ناعم عند التمرير مع إضاءة محيطية خفيفة لا تضر بالخلفية الخضراء */
        .product:hover {
            transform: translateY(-6px);
        }

        .product:hover .image-shape {
            filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.2));
        }

        /* حاوية الصورة: بدون خلفية، حواف دائرية متطورة فقط أسفلها شريط السعر */
        .image-shape {
            position: relative;
            width: 100%;
            height: auto;
            min-height: 500px;
            background: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 1.25rem 1.25rem 0rem 1.25rem;
            border-radius: 2rem 2rem 0 0;
            transition: var(--transition-smooth);
        }

        /* الصورة نفسها شفافة بالكامل (المنتج مقصوص) */
        .product-image {
            width: 100%;
            height: 100%;
            max-height: 500px;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.1));
            transition: transform 0.4s ease;
        }

        .product:hover .product-image {
            transform: scale(1.02);
        }

        /* شريط السعر والوزن - تصميم انسيابي حديث بألوان العلامة */
        .price-weight-shape {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            position: absolute;
            bottom: -20px;
            left: 8%;
            width: 84%;
            background: var(--primary);
            backdrop-filter: blur(2px);
            border-radius: 100px;
            padding: 0.7rem 1.8rem;
            box-shadow: 0 10px 18px -6px rgba(0, 0, 0, 0.2);
            transition: var(--transition-smooth);
            z-index: 12;
            border: 1px solid rgba(255, 245, 190, 0.6);
        }

        .product:hover .price-weight-shape {
            background: #f5d742;
            box-shadow: 0 14px 22px -8px rgba(0, 0, 0, 0.25);
            transform: scale(1.01);
        }

        /* نص السعر - متناسق وبارز */
        .price {
            color: var(--secondary);
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.2;
            text-shadow: none;
            display: inline-flex;
            align-items: baseline;
            gap: 0.2rem;
        }

        /* نص الوزن - بنفس الهوية لكن بلمسة عصرية */
        .weight {
            color: var(--secondary);
            font-size: 3rem;
            font-weight: 700;
            background: rgba(30, 31, 28, 0.1);
            padding: 0.2rem 0.9rem;
            border-radius: 60px;
            line-height: 1.3;
            letter-spacing: 0.5px;
            backdrop-filter: blur(2px);
        }

        /* حاوية الاسم - خلفية شفافة تامة لتبقى على الخلفية الخضراء */
        .price-shape {
            width: 100%;
            background: transparent;
            margin-top: 2rem;
            padding: 0.8rem 0.5rem 1rem;
            text-align: center;
        }

        /* اسم المنتج: تباين عالي على الخلفية الخضراء مع الحفاظ على الأناقة */
        .name {
            color: #101010;
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            text-align: center;
            margin: 0;
            padding: 0.4rem 0.2rem;
            background: transparent;
            line-height: 1.3;
            text-shadow: 0 1px 1px rgba(255, 255, 200, 0.3);
            word-break: break-word;
            display: inline-block;
            border-bottom: 2px solid transparent;
            transition: border 0.2s;
        }

        .product:hover .name {
            border-bottom-color: var(--primary);
            color: #000000;
        }

        /* عند وجود 3 أو 4 منتجات الحفاظ على التناسق التام */
        @media (max-width: 880px) {
            .products {
                gap: 1.25rem;
                padding: 0.75rem 1.2rem;
            }

            .price {
                font-size: 1.6rem;
            }

            .weight {
                font-size: 1.4rem;
                padding: 0.1rem 0.7rem;
            }

            .name {
                font-size: 1.6rem;
            }

            .image-shape {
                min-height: 320px;
            }

            .product-image {
                max-height: 320px;
            }
        }

        @media (max-width: 640px) {
            .products {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 1rem;
            }

            .products:has(.product:only-child) .product {
                width: 90%;
                transform: scale(1);
            }

            .image-shape {
                min-height: 280px;
            }

            .price-weight-shape {
                padding: 0.5rem 1.2rem;
                width: 86%;
                left: 7%;
                bottom: -16px;
            }

            .price {
                font-size: 1.5rem;
            }

            .weight {
                font-size: 1.3rem;
            }

            .name {
                font-size: 1.5rem;
            }
        }

        /* تحسينات إضافية لمعالجة المنتج الذي لا يحتوي على وزن */
        .price-weight-shape:has(.weight:empty) {
            justify-content: flex-end;
        }

        .weight:empty {
            display: none;
        }

        /* ضمان عدم ظهور أي خلفية غير مرغوبة للأقسام الأخرى */
        .top-products,
        .contct-products {
            background: transparent;
        }

        /* تعديل طفيف للحفاظ على المسافات */
        .top-products {
            height: 16rem;
        }

        .contct-products {
            height: 7rem;
        }

        /* حد أدنى من الجمالية للبطاقات مع الحفاظ على الشفافية المطلقة */
        .product {
            background: transparent;
        }

        /* إزالة أي خلفية افتراضية يمكن أن تظهر */
        .image-shape,
        .price-shape,
        .design-container,
        .products {
            background: transparent;
        }

        /* تعزيز وضوح النصوص على الخلفية الخضراء */
        .name {
            font-weight: 800;
            background: rgba(0, 0, 0, 0.05);
            display: inline-block;
            padding: 0.2rem 0.8rem;
            border-radius: 60px;
            backdrop-filter: blur(1px);
        }

        /* اضف لمسة أنيقة بشريط جانبي للاسم عند hover */
        .price-shape {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="design-container">
        <div class="top-products"></div>
        <div class="products">
            @foreach ($products as $product)
                <div class="product">
                    <div class="image-shape">
                        <img class="product-image" src="{{ asset('public/storage/' . $product->image_path) }}"
                            alt="{{ $product->name }}"
                            loading="lazy"
                            style="background: transparent;" />
                        <div class="price-weight-shape">
                            <span class="price">
                                {{ $product->price }}{{ $product->symbol ?? '$' }}
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
        <div class="contct-products"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>
