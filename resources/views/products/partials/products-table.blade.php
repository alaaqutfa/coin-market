@foreach ($products as $product)
    <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200" data-id="{{ $product->id }}">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
            <input type="checkbox" name="" id="" class="border border-gray-400 rounded" />
        </th>
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
            <button class="mx-2" title="{{ $product->name }} - {{ $product->price }}$" onclick="copyTitle(this)">
                <i
                    class="fas fa-copy text-base text-gray-400 hover:text-gray-800 transition-all duration-150 ease-linear"></i>
            </button>
            {{ $product->barcode }}
        </th>
        <td class="px-6 py-4">
            <div data-field="image">
                <img src="{{ asset('storage/' . $product->image_path) }}"
                    onerror="this.src='{{ asset('assets/img/place-holder.png') }}'"
                    class="w-20 h-20 object-contain rounded" />
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="name">
                {{ $product->name }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="price">
                {{ $product->price }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="weight">
                {{ $product->weight }}
            </div>
        </td>
        <td class="px-6 py-4">
            {{ $product->created_at->format('Y-m-d') }}
        </td>
        <td class="px-6 py-4 flex space-x-2 space-x-reverse gap-2">
            <button onclick="deleteProduct({{ $product->id }})" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
            <a href="https://www.google.com/search?q={{ $product->barcode . ' ' . $product->name . ' high quality png image' }}"
                title="{{ $product->name }} - {{ $product->price }}$" class="text-blue-600 hover:text-blue-800"
                target="_blank" rel="noopener noreferrer" onclick="copyTitle(this)">
                <i class="fas fa-search"></i>
            </a>
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="7">
        <!-- Pagination -->
        <div class="p-4 border-t">
            {{ $products->links() }}
        </div>
    </td>
</tr>
