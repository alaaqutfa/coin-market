@foreach ($categories as $category)
    @if ($category->products->count() > 0)
        <div class="category-section mb-12">
            <!-- عنوان الفئة -->
            <div class="flex items-center mb-6">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-folder ml-2 text-yellow-500"></i>
                        {{ $category->name }}
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">
                        <i class="fas fa-box ml-1"></i>
                        {{ $category->products->count() }} منتج
                    </p>
                </div>

                <a href="{{ route('customer.category.products', $category->id) }}"
                    class="show-more-btn bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-external-link-alt ml-2"></i>
                    عرض الكل
                </a>

            </div>

            <!-- منتجات الفئة -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach ($category->products as $product)
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

                    <div
                        class="group bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        <!-- صورة المنتج -->
                        <div class="relative overflow-hidden">
                            <a href="{{ $productLink }}" class="block">
                                <img class="w-full h-56 object-contain p-4 bg-gray-50 group-hover:scale-105 transition-transform duration-300"
                                    src="{{ asset('public/storage/' . $product->image_path) }}"
                                    onerror="this.src='{{ asset('public/assets/img/place-holder.png') }}'"
                                    alt="{{ $product->name }}">
                            </a>
                            <!-- شارة الوزن -->
                            @if ($product->weight > 0)
                                <div
                                    class="absolute top-3 left-3 bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    {{ $product->weight }}
                                </div>
                            @endif
                        </div>

                        <!-- معلومات المنتج -->
                        <div class="p-4">
                            <a href="{{ $productLink }}" class="block">
                                <h3 class="font-semibold text-gray-800 text-lg mb-2 group-hover:text-yellow-600 transition-colors duration-200 line-clamp-2"
                                    title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h3>
                            </a>

                            <!-- السعر -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="text-2xl font-bold text-gray-900">
                                        {{ $product->price }}
                                    </span>
                                    <span class="text-sm text-gray-500 mr-1">
                                        {{ $product->symbol ?? '$' }}
                                    </span>
                                </div>
                                @if ($product->brand)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                        {{ $product->brand->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- الأزرار -->
                            <div class="flex gap-2">
                                <a href="{{ $whatsappUrl }}" target="_blank"
                                    class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center py-2.5 px-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fa-brands fa-whatsapp text-lg"></i>
                                    <span class="text-sm">واتساب</span>
                                </a>
                                <a href="#"
                                    class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white text-center py-2.5 px-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    <span class="text-sm">السلة</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- أزرار التحكم -->
            <div class="flex justify-center gap-4 mt-6">
                <a href="{{ route('customer.category.products', $category->id) }}"
                    class="show-more-btn bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-external-link-alt ml-2"></i>
                    عرض الكل
                </a>
            </div>

            @if (!$loop->last)
                <div class="my-8 border-t border-gray-200"></div>
            @endif
        </div>
    @endif
@endforeach

@if ($categories->count() === 0)
    <div class="text-center py-12">
        <div class="max-w-md mx-auto">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-inbox text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">لا توجد منتجات</h3>
            <p class="text-gray-500">لم يتم العثور على منتجات تطابق معايير البحث</p>
        </div>
    </div>
@endif
