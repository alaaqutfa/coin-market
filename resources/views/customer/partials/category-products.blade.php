@if($products->count() > 0)
<div class="category-products">
    @foreach($products as $product)
        @include('customer.partials.product-card', ['product' => $product])
    @endforeach

    {{-- Pagination Links --}}
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
            <i class="fas fa-inbox text-6xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">لا توجد منتجات</h3>
    </div>
</div>
@endif
