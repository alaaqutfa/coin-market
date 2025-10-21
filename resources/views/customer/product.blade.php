@extends('layout.customer.app')

@section('title', 'Home')

@section('content')
    <div class="container mx-auto py-8">
        <div class="links w-full pb-8">
            {{ $products->links() }}
        </div>
        <div class="product-container w-full flex justify-evenly items-start gap-4 flex-wrap">
            @foreach ($products as $product)
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

                <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm">
                    <a href="{{ $productLink }}" class="flex justify-center items-center overflow-hidden">
                        <img class="h-64 p-8 rounded-t-lg object-contain" src="{{ asset('storage/' . $product->image_path) }}"
                            onerror="this.src='{{ asset('assets/img/place-holder.png') }}'" alt="product image" />
                    </a>
                    <div class="px-5 pb-5">
                        <a href="{{ $productLink }}">
                            <h5 class="text-xl font-semibold tracking-tight text-gray-900">
                                {{ $product->name }}
                            </h5>
                        </a>

                        @if ($product->weight > 0)
                            <div class="flex items-center mt-2.5 mb-5">
                                <span
                                    class="bg-yellow-100 text-yellow-500 text-base font-semibold px-2.5 py-0.5 rounded-sm">
                                    {{ $product->weight }}
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <span class="text-3xl font-bold text-gray-900">
                                {{ $product->price }} {{ $product->symbol ?? '$' }}
                            </span>
                            <div class="flex justify-center items-center gap-2">
                                <a href="#" title="إضافة إلى السلة"
                                    class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </a>
                                <a href="{{ $whatsappUrl }}" target="_blank" title="أطلب الآن على واتساب"
                                    class="text-white bg-green-400 hover:bg-green-500 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
