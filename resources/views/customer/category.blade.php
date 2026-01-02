@extends('layout.customer.app')

@section('title', $category->name)

@section('content')
    <div dir="rtl" class="container mx-auto px-4 py-8">
        <!-- عنوان الفئة -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-folder ml-2 text-yellow-500"></i>
                        {{ $category->name }}
                    </h1>
                    <p class="text-gray-600 mt-2">
                        {{ $products->total() }} منتج
                    </p>
                </div>
                <a href="{{ url('/') }}" class="text-yellow-500 hover:text-yellow-600 font-medium">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للرئيسية
                </a>
            </div>
        </div>

        <!-- منتجات الفئة -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @foreach ($products as $product)
                @include('customer.partials.product-card', ['product' => $product])
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
