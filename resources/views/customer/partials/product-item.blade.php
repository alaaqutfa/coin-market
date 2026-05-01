<div dir="ltr" class="links w-full pb-8">
    {{ $products->links() }}
</div>
@foreach ($products as $product)
    <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm">
        <a href="{{ $productLink }}" class="flex justify-center items-center overflow-hidden">
            <img class="h-64 p-8 rounded-t-lg object-contain" src="{{ asset('public/storage/' . $product->image_path) }}"
                onerror="this.src='{{ asset('public/assets/img/place-holder.png') }}'" alt="product image" />
        </a>
        <div class="px-5 pb-5">
            <a href="{{ $productLink }}">
                <h5 class="text-xl font-semibold tracking-tight text-gray-900">
                    {{ $product->name }}
                </h5>
            </a>

            @if ($product->weight > 0)
                <div class="flex items-center mt-2.5 mb-5">
                    <span class="bg-yellow-100 text-yellow-500 text-base font-semibold px-2.5 py-0.5 rounded-sm">
                        {{ $product->weight }}
                    </span>
                </div>
            @endif

            <div class="flex items-center justify-between">
                <span class="text-3xl font-bold text-gray-900">
                    @if ($product->symbol == 'LBP')
                        {{ number_format($product->price, 0, '.', ',') }}
                    @else
                        {{ number_format($product->price, 2, '.', ',') }}
                    @endif {{ $product->symbol ?? '$' }}
                </span>
                @include('customer.partials.action-buttons', ['product' => $product])
            </div>
        </div>
    </div>
@endforeach
