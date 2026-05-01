<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المبيعات اليومي</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        th { background-color: #f5f5f5; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <h1>تقرير المبيعات التفصيلي</h1>
    <p>الفترة من {{ $from }} إلى {{ $to }}</p>
    <table>
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>العميل</th>
                <th>رقم الهاتف</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->customer->phone }}</td>
                <td>{{ number_format($order->total, 2) }}</td>
                <td>
                    @if($order->status == 'accepted') مقبول
                    @elseif($order->status == 'rejected') مرفوض
                    @else قيد الانتظار @endif
                </td>
                <td>{{ $order->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        تم التوليد بواسطة النظام - {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
