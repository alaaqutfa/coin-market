@if($products->count() > 0)
    <div class="filtered-results mb-12">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-search ml-2 text-yellow-500"></i>
                نتائج البحث ({{ $products->total() }} منتج)
            </h3>
            <button id="clearFiltersBtn" class="text-yellow-600 hover:text-yellow-700 text-sm">
                <i class="fas fa-times ml-1"></i> إلغاء الفلترة
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @foreach($products as $product)
                @include('customer.partials.product-card', ['product' => $product])
            @endforeach
        </div>

        @if($products->hasPages())
            <div dir="ltr" class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@else
    <div class="text-center py-12">
        <div class="max-w-md mx-auto">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-search text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">لا توجد منتجات</h3>
            <p class="text-gray-500">جرب استخدام كلمات بحث أخرى أو تغيير الفلاتر</p>
        </div>
    </div>
@endif
