@extends('layout.customer.app')

@section('title', $product->name)

@section('content')
    <div dir="rtl" class="container mx-auto py-8">
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="md:flex">
                <!-- الصورة -->
                <div class="md:w-1/2 p-6 flex justify-center items-center">
                    <img src="{{ asset('public/storage/' . $product->image_path) }}"
                        onerror="this.src='{{ asset('public/assets/img/place-holder.png') }}'" alt="{{ $product->name }}"
                        class="w-full h-80 object-contain rounded">
                </div>

                <!-- تفاصيل المنتج -->
                <div class="md:w-1/2 p-6 flex flex-col justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                        <p class="text-gray-700 mb-2">
                            <strong>السعر:</strong>
                            @if ($product->symbol == 'LBP')
                                {{ number_format($product->price, 0, '.', ',') }} {{ $product->symbol ?? 'LBP' }}
                            @else
                                {{ number_format($product->price, 2, '.', ',') }} {{ $product->symbol ?? '$' }}
                            @endif
                        </p>
                        @if ($product->weight)
                            <p class="text-gray-700 mb-2"><strong>الوزن:</strong> {{ $product->weight }}</p>
                        @endif
                        <p class="text-gray-700 mb-2"><strong>الفئة:</strong> {{ $product->category?->name ?? '-' }}</p>
                        <p class="text-gray-700 mb-4"><strong>البراند:</strong> {{ $product->brand?->name ?? '-' }}</p>
                        @if ($product->description)
                            <p class="text-gray-600">{{ $product->description }}</p>
                        @endif
                    </div>

                    <!-- الأزرار -->
                    <div class="flex items-center gap-4">
                        <label class="text-gray-700 font-semibold">الكمية:</label>
                        <input type="number" class="product-quantity-input w-24 border rounded-lg p-2 text-center" value="1"
                            min="1" max="99">
                    </div>
                    <div class="flex gap-3 space-x-3 rtl:space-x-reverse mt-6">
                        <a href="#"
                            class="add-to-cart-btn flex justify-center items-center text-center bg-yellow-400 hover:bg-yellow-500 text-white px-5 py-3 rounded-lg font-semibold"
                            data-product-id="{{ $product->id }}" data-quantity="1">
                            <i class="fa-solid fa-cart-plus mr-2"></i> أضف إلى السلة
                        </a>

                        <a href="{{ $whatsappUrl }}" target="_blank"
                            class="whatsapp-order-btn flex justify-center items-center text-center bg-green-400 hover:bg-green-500 text-white px-5 py-3 rounded-lg font-semibold"
                            data-product-id="{{ $product->id }}" data-quantity="1">
                            <i class="fa-brands fa-whatsapp mr-2"></i> أطلب الآن على واتساب
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- مودال بيانات العميل -->
    @include('customer.partials.customer-modal')
@endsection
