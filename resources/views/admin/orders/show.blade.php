@extends('layout.app')
@section('title', 'تفاصيل الطلب #' . $order->order_number)
@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-4">تفاصيل الطلب: {{ $order->order_number }}</h1>
            <p><strong>العميل:</strong> {{ $order->customer->name }}</p>
            <p><strong>الهاتف:</strong> {{ $order->customer->phone }}</p>
            <p><strong>العنوان:</strong> {{ $order->customer->address }}</p>
            <p><strong>رابط الخريطة:</strong> <a href="{{ $order->customer->map_link }}"
                    target="_blank">{{ $order->customer->map_link }}</a></p>
            <p><strong>الحالة الحالية:</strong>
                <span class="px-2 py-1 rounded text-xs
                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status == 'accepted') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ $order->status == 'accepted' ? 'مقبول' : ($order->status == 'rejected' ? 'مرفوض' : 'قيد الانتظار') }}
                </span>
            </p>
            @if($order->rejection_reason)
            <p><strong>سبب الرفض:</strong> {{ $order->rejection_reason }}</p> @endif
            <hr class="my-4">
            <h2 class="text-xl font-semibold mb-2">المنتجات المطلوبة</h2>
            <table class="min-w-full bg-gray-50 rounded">
                <thead>
                    <tr>
                        <th class="p-2">المنتج</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td class="p-2">{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                            <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="mt-4 text-xl font-bold">المجموع النهائي: {{ number_format($order->total, 2) }}</p>

            <hr class="my-4">
            <h2 class="text-xl font-semibold mb-2">تغيير حالة الطلب</h2>
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label>الحالة الجديدة</label>
                    <select name="status" class="border p-2 rounded">
                        <option value="accepted" @if($order->status == 'accepted') selected @endif>قبول</option>
                        <option value="rejected" @if($order->status == 'rejected') selected @endif>رفض</option>
                    </select>
                </div>
                <div>
                    <label>سبب الرفض (اختياري)</label>
                    <textarea name="rejection_reason" rows="2"
                        class="w-full border p-2 rounded">{{ $order->rejection_reason }}</textarea>
                </div>
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">تحديث الحالة</button>
            </form>
        </div>
    </div>
@endsection
