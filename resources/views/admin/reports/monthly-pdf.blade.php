<!DOCTYPE html>
<html dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تقرير المبيعات الشهري</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        th {
            background-color: #f5f5f5;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <h1>تقرير المبيعات الشهري (ملخص)</h1>
    <p>الفترة من {{ $from }} إلى {{ $to }}</p>
    <table>
        <thead>
            <tr>
                <th>الشهر</th>
                <th>عدد الطلبات</th>
                <th>إجمالي المبيعات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthly as $row)
                <tr>
                    <td>{{ $row->month }}</td>
                    <td>{{ $row->total_orders }}</td>
                    <td>{{ number_format($row->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        تم التوليد بواسطة النظام - {{ now()->format('Y-m-d H:i') }}
    </div>
</body>

</html>
