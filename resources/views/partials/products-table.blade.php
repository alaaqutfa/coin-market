{{-- coin-market-soical-stock\backend\resources\views\partials\products-table.blade.php --}}
@foreach ($products as $product)
    <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
            {{ $product->barcode }}
        </th>
        <td class="px-6 py-4">
            {{ $product->name }}
        </td>
        <td class="px-6 py-4">
            {{ $product->price }}
        </td>
        <td class="px-6 py-4">
            {{ $product->weight }}
        </td>
        <td class="px-6 py-4">
            {{ $product->created_at->format('Y-m-d') }}
        </td>
        <td class="px-6 py-4">
            {{-- <a href="#" class="font-medium text-blue-600 hover:underline">Edit</a> --}}
            <a href="https://www.google.com/search?q={{ $product->barcode . ' ' . $product->name . ' high quality png image' }}"
                class="font-medium text-blue-600 hover:underline" target="_blank" rel="noopener noreferrer">Discover</a>
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="5" class="px-6 py-4">
        {{ $products->links() }}
    </td>
</tr>
