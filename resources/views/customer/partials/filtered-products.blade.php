@if($products->count() > 0)
<div class="filtered-products">
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800">
            نتائج البحث: {{ $products->total() }} منتج
        </h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @foreach($products as $product)
            @include('customer.partials.product-card', ['product' => $product])
        @endforeach
    </div>

    {{-- Pagination Links --}}
    @if($products->hasPages())
    <div class="mt-8">
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
        <h3 class="text-xl font-semibold text-gray-700 mb-2">لم يتم العثور على منتجات</h3>
        <p class="text-gray-500">جرب استخدام كلمات بحث أخرى أو تغيير الفلاتر</p>
    </div>
</div>
@endif
