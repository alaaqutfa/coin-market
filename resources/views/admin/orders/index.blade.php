@extends('layout.app')
@section('title', 'الطلبات')
@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">جميع الطلبات</h1>

        <!-- نموذج بحث وفلترة -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="text" name="search" placeholder="رقم الطلب" value="{{ request('search') }}"
                    class="border rounded p-2">
                <select name="status" class="border rounded p-2">
                    <option value="">كل الحالات</option>
                    <option value="pending" @selected(request('status') == 'pending')>قيد الانتظار</option>
                    <option value="accepted" @selected(request('status') == 'accepted')>مقبول</option>
                    <option value="rejected" @selected(request('status') == 'rejected')>مرفوض</option>
                </select>
                <input type="text" name="customer" placeholder="اسم العميل أو هاتفه" value="{{ request('customer') }}"
                    class="border rounded p-2">
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="border rounded p-2">
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="border rounded p-2">
                <div class="flex gap-2">
                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">فلترة</button>
                    <a href="{{ route('admin.orders.index') }}"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded">إلغاء</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right">رقم الطلب</th>
                        <th>العميل</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ number_format($order->total, 2) }}</td>
                            <td><span class="px-2 py-1 rounded text-xs
                                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status == 'accepted') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                    {{ $order->status == 'accepted' ? 'مقبول' : ($order->status == 'rejected' ? 'مرفوض' : 'قيد الانتظار') }}
                                </span></td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td class="flex gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline">عرض</a>
                                <a href="{{ route('admin.orders.contact', $order) }}" target="_blank"
                                    class="text-green-600 hover:underline"><i class="fab fa-whatsapp"></i> تواصل</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>
@endsection
