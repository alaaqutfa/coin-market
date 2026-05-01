@php
    $message =
        "مرحباً، أريد الاستفسار عن المنتج: {$product->name}\n" .
        "السعر: {$product->price} {$product->symbol}\n" .
        'رابط المنتج: ' .
        url()->current();
    $whatsappNumber = '96171349793'; // بدون +
    $encodedMessage = urlencode($message);
    $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$encodedMessage}";
@endphp
<div class="mb-3">
    <label class="block text-gray-600 text-sm mb-1">الكمية:</label>
    <input type="number" class="product-quantity-input w-full border rounded-lg p-2 text-center" value="1" min="1"
        max="99">
</div>
<div class="flex gap-2">
    {{-- <a href="{{ $whatsappUrl }}" target="_blank" --}} <a href="#"
        class="whatsapp-order-btn flex-1 bg-green-500 hover:bg-green-600 text-white text-center py-2.5 px-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2"
        data-product-id="{{ $product->id }}" data-quantity="1">
        <i class="fa-brands fa-whatsapp text-lg"></i>
        <span class="text-sm">واتساب</span>
    </a>
    <a href="#"
        class="add-to-cart-btn flex-1 bg-yellow-500 hover:bg-yellow-600 text-white text-center py-2.5 px-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2"
        data-product-id="{{ $product->id }}" data-quantity="1">
        <i class="fa-solid fa-cart-plus"></i>
        <span class="text-sm">السلة</span>
    </a>
</div>
