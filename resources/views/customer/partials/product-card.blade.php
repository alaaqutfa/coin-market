@php
    $productLink = route('customer.product.show', $product->id);
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
                    @if ($product->symbol == 'LBP')
                        {{ number_format($product->price, 0, '.', ',') }}
                    @else
                        {{ number_format($product->price, 2, '.', ',') }}
                    @endif
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
        @include('customer.partials.action-buttons', ['product' => $product])
    </div>
</div>
