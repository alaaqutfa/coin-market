@php
    $whatsappNumber = '96171349793';
    $productName = $product->name;
    $productLink = route('customer.product.show', $product->id);
    $message =
        "مرحباً، أريد الاستفسار عن المنتج: {$productName}\n" .
        "السعر: {$product->price} {$product->symbol}\n" .
        "رابط المنتج: {$productLink}";
    $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . rawurlencode($message);
@endphp

<div
    class="group bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
    <!-- صورة المنتج -->
    <div class="relative overflow-hidden">
        <a href="{{ $productLink }}" class="block">
            <img class="w-full h-56 object-contain p-4 bg-gray-50 group-hover:scale-105 transition-transform duration-300"
                src="{{ asset('public/storage/' . $product->image_path) }}"
                onerror="this.src='{{ asset('public/assets/img/place-holder.png') }}'" alt="{{ $product->name }}">
        </a>
        <!-- شارة الوزن -->
        @if ($product->weight > 0)
            <div class="absolute top-3 left-3 bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                {{ $product->weight }}
            </div>
        @endif
    </div>

    <!-- معلومات المنتج -->
    <div class="p-4">
        <a href="{{ $productLink }}" class="block">
            <h3 class="font-semibold text-gray-800 text-lg mb-2 group-hover:text-yellow-600 transition-colors duration-200 line-clamp-2"
                title="{{ $product->name }}">
                {{ $product->name }}
            </h3>
        </a>

        <!-- السعر -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <span class="text-2xl font-bold text-gray-900">
                    {{ $product->price }}
                </span>
                <span class="text-sm text-gray-500 mr-1">
                    {{ $product->symbol ?? '$' }}
                </span>
            </div>
            @if ($product->brand)
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                    {{ $product->brand->name }}
                </span>
            @endif
        </div>

        <!-- الأزرار -->
        <div class="flex gap-2">
            <a href="{{ $whatsappUrl }}" target="_blank"
                class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center py-2.5 px-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-lg"></i>
                <span class="text-sm">واتساب</span>
            </a>
            <a href="#"
                class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white text-center py-2.5 px-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                <i class="fa-solid fa-cart-plus"></i>
                <span class="text-sm">السلة</span>
            </a>
        </div>
    </div>
</div>
