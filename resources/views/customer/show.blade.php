@extends('layout.customer.app')

@section('title', $product->name)

@section('content')
    <div dir="rtl" class="container mx-auto py-8">
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="md:flex">
                <!-- الصورة -->
                <div class="md:w-1/2 p-6 flex justify-center items-center">
                    <img src="{{ asset('storage/' . $product->image_path) }}"
                        onerror="this.src='{{ asset('assets/img/place-holder.png') }}'" alt="{{ $product->name }}"
                        class="w-full h-80 object-contain rounded">
                </div>

                <!-- تفاصيل المنتج -->
                <div class="md:w-1/2 p-6 flex flex-col justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                        <p class="text-gray-700 mb-2"><strong>السعر:</strong> {{ $product->price }}
                            {{ $product->symbol ?? '$' }}</p>
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
                    <div class="flex gap-3 space-x-3 rtl:space-x-reverse mt-6">
                        <a href="#"
                            class="flex justify-center items-center text-center bg-yellow-400 hover:bg-yellow-500 text-white px-5 py-3 rounded-lg font-semibold">
                            <i class="fa-solid fa-cart-plus mr-2"></i> أضف إلى السلة
                        </a>
                        @php
                            $message =
                                "مرحباً، أريد الاستفسار عن المنتج: {$product->name}\n" .
                                "السعر: {$product->price} {$product->symbol}\n" .
                                'رابط المنتج: ' .
                                url()->current();
                            $whatsappNumber = '96171349793'; // بدون +
                            $encodedMessage = urlencode($message);
                            $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$encodedMessage}";
                        @endphp
                        <a href="{{ $whatsappUrl }}" target="_blank"
                            class="flex justify-center items-center text-center bg-green-400 hover:bg-green-500 text-white px-5 py-3 rounded-lg font-semibold">
                            <i class="fa-brands fa-whatsapp mr-2"></i> أطلب الآن على واتساب
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
