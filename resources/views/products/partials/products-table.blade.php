@foreach ($products as $product)
    <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200" data-id="{{ $product->id }}">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
            <input type="checkbox" name="" id="" class="border border-gray-400 rounded" />
        </th>
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
            <button class="mx-2" title="{{ $product->name }} {{ $product->weight }} - {{ $product->price }}$"
                onclick="copyTitle(this)">
                <i
                    class="fas fa-copy text-base text-gray-400 hover:text-gray-800 transition-all duration-150 ease-linear"></i>
            </button>
            {{ $product->barcode }}
        </th>
        <td class="px-6 py-4">
            <div data-field="image">
                @if ($product->image_path)
                    @php
                        $extension = pathinfo($product->image_path, PATHINFO_EXTENSION);
                        $downloadName = $product->barcode . '.' . $extension;
                    @endphp
                    <a href="{{ asset('public/storage/' . $product->image_path) }}" download="{{ $downloadName }}"
                        class="bg-gray-500">
                        <img src="{{ asset('public/storage/' . $product->image_path) }}"
                            onerror="this.src='{{ asset('public/assets/img/place-holder.png') }}'"
                            class="w-20 h-20 object-contain rounded cursor-pointer bg-gray-500" title="تحميل الصورة" />
                    </a>
                @else
                    <img src="{{ asset('public/assets/img/place-holder.png') }}"
                        class="w-20 h-20 object-contain rounded" />
                @endif
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="name">
                {{ $product->name }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="weight">
                {{ $product->weight }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="price">
                {{ $product->price }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="symbol">
                {{ $product->symbol }}
            </div>
        </td>

        <td class="px-6 py-4">
            <div class="category-editable-container" data-product-id="{{ $product->id }}">
                <!-- وضع العرض (Display Mode) -->
                <div class="display-mode flex items-center gap-2 justify-between cursor-pointer group">
                    <span class="category-name text-gray-800">
                        {{ $product->category ? $product->category->name : 'بدون فئة' }}
                    </span>
                    <button type="button"
                        class="edit-category-btn opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-gray-400 hover:text-yellow-500 ml-2">
                        <i class="fas fa-edit text-sm"></i>
                    </button>
                </div>

                <!-- وضع التحرير (Edit Mode) -->
                <div class="edit-mode hidden">
                    <select name="category_id"
                        class="category-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200"
                        data-original-category="{{ $product->category_id }}">
                        <option value="">-- اختر فئة --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- أزرار الإجراءات في وضع التحرير -->
                    <div class="edit-actions flex items-center gap-2 mt-2">
                        <button type="button"
                            class="save-category-btn bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                            حفظ
                        </button>
                        <button type="button"
                            class="cancel-edit-btn bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm transition-colors duration-200">
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </td>

        <td class="px-6 py-4">
            <div class="editable-field brand-field" contenteditable="true" data-field="brand_name">
                {{ $product->brand ? $product->brand->name : '' }}
            </div>
        </td>
        <td class="px-6 py-4">
            {{ $product->created_at->format('Y-m-d') }}
        </td>
        <td class="px-6 py-4">
            <div class="flex space-x-2 space-x-reverse gap-2">
                <button onclick="deleteProduct({{ $product->id }})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>

                @php
                    $searchParts = [$product->barcode, $product->name];

                    if ($product->weight > 0) {
                        $searchParts[] = $product->weight;
                    }

                    $searchParts[] = 'high quality png image';

                    $searchQuery = rawurlencode(implode(' ', $searchParts));
                @endphp


                <a href="https://www.google.com/search?tbm=isch&q={{ $searchQuery }}" title="{{ $product->barcode }}"
                    class="text-blue-600 hover:text-blue-800" target="_blank" rel="noopener noreferrer"
                    onclick="copyTitle(this)">
                    <i class="fab fa-google"></i>
                </a>

                <a href="https://www.bing.com/images/search?q={{ $searchQuery }}" title="{{ $product->barcode }}"
                    class="text-green-600 hover:text-green-800" target="_blank" rel="noopener noreferrer"
                    onclick="copyTitle(this)">
                    <i class="fab fa-microsoft"></i>
                </a>

                <a href="https://duckduckgo.com/?q={{ $searchQuery }}&iax=images&ia=images"
                    title="{{ $product->barcode }}" class="text-purple-600 hover:text-purple-800" target="_blank"
                    rel="noopener noreferrer" onclick="copyTitle(this)">
                    <i class="fas fa-image"></i>
                </a>

                <a href="https://yandex.com/images/search?text={{ $searchQuery }}" title="{{ $product->barcode }}"
                    class="text-red-600 hover:text-red-800" target="_blank" rel="noopener noreferrer"
                    onclick="copyTitle(this)">
                    <i class="fas fa-camera"></i>
                </a>

                <!-- زر تحميل الصورة -->
                @if ($product->image_path)
                    @php
                        $extension = pathinfo($product->image_path, PATHINFO_EXTENSION);
                        $downloadName =
                            $product->name . ' ' . $product->weight . ' - ' . $product->price . '$.' . $extension;
                    @endphp
                    <a href="{{ asset('public/storage/' . $product->image_path) }}" download="{{ $downloadName }}"
                        class="text-green-600 hover:text-green-800" title="تحميل الصورة">
                        <i class="fas fa-download"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
@endforeach

<tr dir="ltr">
    <td colspan="7">
        <!-- Pagination -->
        <div class="p-4 border-t">
            {{ $products->links() }}
        </div>
    </td>
</tr>
