@extends('layout.customer.app')
@section('title', 'طلباتي')
@section('content')
    <div dir="rtl" class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">طلباتي</h1>
        @if($orders->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right">رقم الطلب</th>
                            <th class="px-6 py-3 text-right">التاريخ</th>
                            <th class="px-6 py-3 text-right">الإجمالي</th>
                            <th class="px-6 py-3 text-right">الحالة</th>
                            <th class="px-6 py-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-t">
                                <td class="px-6 py-4">{{ $order->order_number }}</td>
                                <td class="px-6 py-4">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4">{{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs
                                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status == 'accepted') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                        {{ $order->status == 'accepted' ? 'مقبول' : ($order->status == 'rejected' ? 'مرفوض' : 'قيد الانتظار') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('customer.order.show', $order->order_number) }}"
                                        class="text-blue-600 hover:underline">عرض التفاصيل</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-600">لا توجد طلبات حتى الآن</p>
                <a href="{{ route('customer.home') }}" class="inline-block mt-4 bg-yellow-500 text-white px-6 py-2 rounded-lg">تسوق الآن</a>
            </div>
        @endif
    </div>
@endsection
