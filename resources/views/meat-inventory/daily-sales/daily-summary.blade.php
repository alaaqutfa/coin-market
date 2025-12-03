@extends('layout.customer.app')

@section('title', 'ملخص المبيعات اليومية')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- رأس الصفحة مع الفلترة -->
        <div class="mb-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar text-white text-2xl ml-3"></i>
                            <h1 class="text-2xl font-bold text-white">ملخص المبيعات اليومية</h1>
                        </div>
                        <span class="bg-white text-blue-700 px-4 py-2 rounded-lg font-semibold text-lg">
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ route('meat-inventory.daily-sales.daily-summary') }}"
                        class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4 md:space-x-reverse">
                        <div class="flex-1">
                            <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                اختر التاريخ:
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-calendar-alt text-gray-500"></i>
                                </div>
                                <input type="date" id="date" name="date" value="{{ $date }}"
                                    max="{{ date('Y-m-d') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="submit"
                                class="flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-lg dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                <i class="fas fa-search ml-2"></i>
                                عرض التقرير
                            </button>

                            <button type="button" onclick="printReport()"
                                class="flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 rounded-lg dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                                <i class="fas fa-print ml-2"></i>
                                طباعة التقرير
                            </button>

                            <a href="{{ route('meat-inventory.daily-sales.report') }}"
                                class="flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-blue-300 rounded-lg dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                <i class="fas fa-list ml-2"></i>
                                التقرير التفصيلي
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- بطاقات الإحصائيات الرئيسية -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- بطاقة إجمالي المبيعات -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-l-4 border-blue-500 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <i class="fas fa-cash-register text-blue-600 dark:text-blue-300 text-xl"></i>
                            </div>
                        </div>
                        <div class="mr-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                إجمالي المبيعات
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ number_format($netAmount['total_sales'], 2) }}
                                <span class="text-sm text-gray-500 dark:text-gray-400">$</span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            <i class="fas fa-arrow-up ml-1 text-xs"></i>
                            مدخلات
                        </span>
                    </div>
                </div>
            </div>

            <!-- بطاقة إجمالي المرتجعات -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-l-4 border-red-500 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                                <i class="fas fa-undo-alt text-red-600 dark:text-red-300 text-xl"></i>
                            </div>
                        </div>
                        <div class="mr-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                إجمالي المرتجعات
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ number_format($netAmount['total_returns'], 2) }}
                                <span class="text-sm text-gray-500 dark:text-gray-400">$</span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            <i class="fas fa-arrow-down ml-1 text-xs"></i>
                            مخرجات
                        </span>
                    </div>
                </div>
            </div>

            <!-- بطاقة صافي المبلغ -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-l-4 border-green-500 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                                <i class="fas fa-hand-holding-usd text-green-600 dark:text-green-300 text-xl"></i>
                            </div>
                        </div>
                        <div class="mr-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                صافي المبلغ
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ number_format($netAmount['net_amount'], 2) }}
                                <span class="text-sm text-gray-500 dark:text-gray-400">$</span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            <i class="fas fa-balance-scale ml-1 text-xs"></i>
                            صافي الربح
                        </span>
                    </div>
                </div>
            </div>

            <!-- بطاقة إجمالي العمليات -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-l-4 border-purple-500 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                <i class="fas fa-exchange-alt text-purple-600 dark:text-purple-300 text-xl"></i>
                            </div>
                        </div>
                        <div class="mr-4 flex-1">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                إجمالي العمليات
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ $summary->count() }}
                                <span class="text-sm text-gray-500 dark:text-gray-400">عملية</span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                بيع: {{ $summary->where('transaction_type', 'sale')->count() }}
                            </span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                مرتجع: {{ $summary->where('transaction_type', 'return')->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول ملخص العمليات -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-table ml-2"></i>
                        تفاصيل العمليات حسب المنتج
                    </h2>
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            عدد المنتجات: {{ $summary->groupBy('meat_product_id')->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">المنتج</th>
                            <th scope="col" class="px-6 py-3">نوع العملية</th>
                            <th scope="col" class="px-6 py-3">الكمية</th>
                            <th scope="col" class="px-6 py-3">المبلغ الإجمالي</th>
                            <th scope="col" class="px-6 py-3">عدد العمليات</th>
                            <th scope="col" class="px-6 py-3">متوسط السعر</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $item)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-3">
                                            <p class="font-semibold">{{ $item->meatProduct->name ?? 'غير معروف' }}</p>
                                            @if ($item->meatProduct->barcode ?? false)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    باركود: {{ $item->meatProduct->barcode }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->transaction_type == 'sale')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            <i class="fas fa-shopping-cart ml-1 text-xs"></i>
                                            بيع
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            <i class="fas fa-undo-alt ml-1 text-xs"></i>
                                            مرتجع
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($item->total_quantity, 3) }}
                                    <span class="text-sm text-gray-500 dark:text-gray-400">كجم</span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($item->total_amount, 2) }}
                                    <span class="text-sm text-gray-500 dark:text-gray-400">$</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                        {{ $item->transaction_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->total_quantity > 0)
                                        {{ number_format($item->total_amount / $item->total_quantity, 2) }}
                                        <span class="text-sm text-gray-500 dark:text-gray-400">$/كجم</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-4"></i>
                                        <p class="text-lg">لا توجد عمليات لهذا التاريخ</p>
                                        <p class="text-sm mt-2">اختر تاريخاً آخر أو قم بإضافة عمليات جديدة</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($summary->count() > 0)
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            عرض {{ $summary->count() }} سجل
                        </div>
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <button onclick="exportToExcel()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                <i class="fas fa-file-excel ml-2"></i>
                                تصدير Excel
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- مخطط دائري للمقارنة -->
        @if ($netAmount['total_sales'] > 0 || $netAmount['total_returns'] > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-pie ml-2"></i>
                        تحليل المبيعات والمرتجعات
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- المخطط الدائري -->
                        <div>
                            <div class="h-64 flex items-center justify-center">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>

                        <!-- تحليل النسب -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">تحليل النسب</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">نسبة
                                            المبيعات</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            @php
                                                $total = $netAmount['total_sales'] + $netAmount['total_returns'];
                                                $salesPercentage =
                                                    $total > 0 ? ($netAmount['total_sales'] / $total) * 100 : 0;
                                            @endphp
                                            {{ number_format($salesPercentage, 1) }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="bg-green-600 h-2.5 rounded-full"
                                            style="width: {{ $salesPercentage }}%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">نسبة
                                            المرتجعات</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            @php
                                                $returnsPercentage =
                                                    $total > 0 ? ($netAmount['total_returns'] / $total) * 100 : 0;
                                            @endphp
                                            {{ number_format($returnsPercentage, 1) }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="bg-red-600 h-2.5 rounded-full"
                                            style="width: {{ $returnsPercentage }}%"></div>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">صافي الربح</p>
                                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                {{ number_format($netAmount['net_amount'], 2) }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-500">$</p>
                                        </div>
                                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">نسبة الربح</p>
                                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                @if ($netAmount['total_sales'] > 0)
                                                    {{ number_format(($netAmount['net_amount'] / $netAmount['total_sales']) * 100, 2) }}
                                                @else
                                                    0.00
                                                @endif
                                                %
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- طباعة التقرير -->
    <div id="printSection" class="hidden">
        <!-- محتوى الطباعة -->
    </div>

    @push('css')
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #printSection,
                #printSection * {
                    visibility: visible;
                }

                #printSection {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }
            }
        </style>
    @endpush

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // طباعة التقرير
            function printReport() {
                // إنشاء محتوى الطباعة
                const printContent = `
            <div class="p-8" dir="rtl">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2">ملخص المبيعات اليومية</h1>
                    <p class="text-xl">التاريخ: {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
                    <p class="text-lg text-gray-600">تاريخ الطباعة: ${new Date().toLocaleDateString('ar-EG')}</p>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="border p-4 text-center">
                        <h3 class="font-bold">إجمالي المبيعات</h3>
                        <p class="text-2xl">${Number({{ $netAmount['total_sales'] }}).toFixed(2)} $</p>
                    </div>
                    <div class="border p-4 text-center">
                        <h3 class="font-bold">إجمالي المرتجعات</h3>
                        <p class="text-2xl">${Number({{ $netAmount['total_returns'] }}).toFixed(2)} $</p>
                    </div>
                    <div class="border p-4 text-center">
                        <h3 class="font-bold">صافي المبلغ</h3>
                        <p class="text-2xl">${Number({{ $netAmount['net_amount'] }}).toFixed(2)} $</p>
                    </div>
                </div>

                <table class="w-full border-collapse border border-gray-400">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-400 p-2">المنتج</th>
                            <th class="border border-gray-400 p-2">النوع</th>
                            <th class="border border-gray-400 p-2">الكمية</th>
                            <th class="border border-gray-400 p-2">المبلغ</th>
                            <th class="border border-gray-400 p-2">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary as $item)
                        <tr>
                            <td class="border border-gray-400 p-2">{{ $item->meatProduct->name ?? 'غير معروف' }}</td>
                            <td class="border border-gray-400 p-2">
                                {{ $item->transaction_type == 'sale' ? 'بيع' : 'مرتجع' }}
                            </td>
                            <td class="border border-gray-400 p-2">{{ number_format($item->total_quantity, 3) }} كجم</td>
                            <td class="border border-gray-400 p-2">{{ number_format($item->total_amount, 2) }} $</td>
                            <td class="border border-gray-400 p-2">{{ $item->transaction_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        `;

                // تعيين محتوى الطباعة
                document.getElementById('printSection').innerHTML = printContent;

                // فتح نافذة الطباعة
                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.write(`
            <!DOCTYPE html>
            <html dir="rtl">
            <head>
                <title>طباعة التقرير</title>
                <style>
                    body { font-family: 'Arial', sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
            </html>
        `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }

            // تصدير إلى Excel
            function exportToExcel() {
                const table = document.querySelector('table');
                const html = table.outerHTML;
                const blob = new Blob([html], {
                    type: 'application/vnd.ms-excel'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'ملخص_المبيعات_{{ $date }}.xls';
                a.click();
                URL.revokeObjectURL(url);
            }

            // مخطط دائري للمبيعات والمرتجعات
            @if ($netAmount['total_sales'] > 0 || $netAmount['total_returns'] > 0)
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    const salesChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['المبيعات', 'المرتجعات'],
                            datasets: [{
                                data: [
                                    {{ $netAmount['total_sales'] }},
                                    {{ $netAmount['total_returns'] }}
                                ],
                                backgroundColor: [
                                    '#10B981', // أخضر
                                    '#EF4444' // أحمر
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    rtl: true,
                                    labels: {
                                        font: {
                                            family: 'Arial, sans-serif'
                                        },
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    rtl: true,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += Number(context.raw).toFixed(2) + ' $';
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            @endif
        </script>
    @endpush
@endsection
