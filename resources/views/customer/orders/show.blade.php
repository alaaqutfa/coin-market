@extends('layout.customer.app')
@section('title', 'تفاصيل الطلب #' . $order->order_number)
@section('content')
    <div dir="rtl" class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">تفاصيل الطلب #{{ $order->order_number }}</h1>
                <a href="{{ route('customer.orders') }}" class="text-yellow-600 hover:underline">← العودة إلى طلباتي</a>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>الحالة:</strong>
                        <span class="px-2 py-1 rounded text-xs
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'accepted') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $order->status == 'accepted' ? 'مقبول' : ($order->status == 'rejected' ? 'مرفوض' : 'قيد الانتظار') }}
                        </span>
                    </p>
                    @if($order->rejection_reason)
                        <p><strong>سبب الرفض:</strong> {{ $order->rejection_reason }}</p>
                    @endif
                </div>
                <div>
                    <p><strong>الإجمالي:</strong> {{ number_format($order->total, 2) }}</p>
                    <p><strong>ملاحظات:</strong> {{ $order->notes ?? 'لا توجد' }}</p>
                </div>
            </div>
            <h2 class="text-xl font-semibold mb-3">المنتجات المطلوبة</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-50 rounded">
                    <thead>
                        <tr class="border-b">
                            <th class="p-2 text-right">المنتج</th>
                            <th class="p-2 text-right">الكمية</th>
                            <th class="p-2 text-right">السعر</th>
                            <th class="p-2 text-right">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-t">
                                <td class="p-2">{{ $item->product->name }}</td>
                                <td class="p-2">{{ $item->quantity }}</td>
                                <td class="p-2">{{ number_format($item->price, 2) }}</td>
                                <td class="p-2">{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 text-center">
                <a href="{{ url('/') }}" class="inline-block bg-yellow-500 text-white px-6 py-2 rounded-lg">متابعة
                    التسوق</a>
            </div>
        </div>
    </div>
@endsection
