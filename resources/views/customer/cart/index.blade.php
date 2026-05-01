@extends('layout.customer.app')
@section('title', 'سلة المشتريات')
@section('content')
    <div dir="rtl" class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">سلة المشتريات</h1>
        @if($cart->items->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right">المنتج</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>الإجمالي</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart->items as $item)
                            <tr>
                                <td class="px-6 py-4">{{ $item->product->name }}</td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('customer.cart.update', $item->id) }}" method="POST"
                                        class="flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                            class="w-20 border rounded p-1">
                                        <button type="submit" class="text-blue-600">تحديث</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">{{ number_format($item->price, 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($item->price * $item->quantity, 2) }}</td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('customer.cart.remove', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 bg-gray-50 flex justify-between">
                    <strong>المجموع: {{ number_format($cart->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</strong>
                    <form action="{{ route('customer.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded-lg">إتمام الطلب</button>
                    </form>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <h2>السلة فارغة</h2>
                <a href="{{ route('customer.home') }}" class="text-yellow-600">تسوق الآن</a>
            </div>
        @endif
    </div>
@endsection
