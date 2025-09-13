@foreach ($products as $product)
    <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200" data-id="{{ $product->id }}">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
            {{ $product->barcode }}
        </th>
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
                class="text-blue-600 hover:text-blue-800" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-search"></i>
            </a>
        </td>
    </tr>
@endforeach
