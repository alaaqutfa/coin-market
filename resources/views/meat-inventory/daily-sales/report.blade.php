@extends('layout.customer.app')

@section('title', 'تقرير المبيعات')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- رأس الصفحة -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تقرير المبيعات</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">عرض وتحليل عمليات البيع والمرتجعات</p>
                </div>
                @if (!session('mobile'))
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <a href="{{ route('meat-inventory.daily-sales.daily-summary') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            <i class="fas fa-chart-pie ml-2"></i>
                            الملخص اليومي
                        </a>
                        <a href="{{ route('meat-inventory.index') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            <i class="fas fa-home ml-2"></i>
                            الصفحة الرئيسية
                        </a>
                        <a href="{{ route('meat-inventory.daily-sales.create') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            <i class="fas fa-plus ml-2"></i>
                            إضافة عملية
                        </a>
                    </div>
                @endif
            </div>

            <!-- فلترة التقرير -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700 mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-filter ml-2"></i>
                        فلترة التقرير
                    </h2>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('meat-inventory.daily-sales.report') }}"
                        class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4 md:space-x-reverse">
                        <div class="flex-1">
                            <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                من تاريخ
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-calendar-alt text-gray-500"></i>
                                </div>
                                <input type="date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                        </div>

                        <div class="flex-1">
                            <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                إلى تاريخ
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-calendar-alt text-gray-500"></i>
                                </div>
                                <input type="date"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                <i class="fas fa-search ml-2"></i>
                                عرض التقرير
                            </button>

                            <a href="{{ route('meat-inventory.daily-sales.report') }}"
                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                <i class="fas fa-redo ml-2"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- بطاقات الإحصائيات -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- بطاقة إجمالي المبيعات -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-100">إجمالي المبيعات</p>
                            <p class="text-3xl font-bold text-white mt-2">
                                {{ number_format($stats['total_sales'], 2) }}
                                <span class="text-lg">$</span>
                            </p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-lg">
                            <i class="fas fa-cash-register text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <i class="fas fa-arrow-up text-green-200 ml-2"></i>
                        <span class="text-sm text-green-100">مدخلات نقدية</span>
                    </div>
                </div>
            </div>

            <!-- بطاقة إجمالي المرتجعات -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-100">إجمالي المرتجعات</p>
                            <p class="text-3xl font-bold text-white mt-2">
                                {{ number_format($stats['total_returns'], 2) }}
                                <span class="text-lg">$</span>
                            </p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-lg">
                            <i class="fas fa-undo-alt text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <i class="fas fa-arrow-down text-red-200 ml-2"></i>
                        <span class="text-sm text-red-100">مخرجات نقدية</span>
                    </div>
                </div>
            </div>

            <!-- بطاقة صافي المبلغ -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-100">صافي المبلغ</p>
                            <p class="text-3xl font-bold text-white mt-2">
                                {{ number_format($stats['net_amount'], 2) }}
                                <span class="text-lg">$</span>
                            </p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-lg">
                            <i class="fas fa-hand-holding-usd text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/30 text-white">
                            <i class="fas fa-balance-scale ml-1"></i>
                            صافي الربح
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول التفاصيل -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-table ml-2"></i>
                        تفاصيل العمليات
                    </h2>
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            عرض {{ $sales->firstItem() ?? 0 }} - {{ $sales->lastItem() ?? 0 }} من {{ $sales->total() }}
                            عملية
                        </span>
                        <button onclick="exportToCSV()"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            <i class="fas fa-file-export ml-2"></i>
                            تصدير
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">التاريخ</th>
                            <th scope="col" class="px-6 py-3">الوقت</th>
                            <th scope="col" class="px-6 py-3">المنتج</th>
                            <th scope="col" class="px-6 py-3">النوع</th>
                            <th scope="col" class="px-6 py-3">الكمية</th>
                            <th scope="col" class="px-6 py-3">السعر</th>
                            <th scope="col" class="px-6 py-3">الإجمالي</th>
                            <th scope="col" class="px-6 py-3">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-day text-blue-500 ml-2 text-sm"></i>
                                        {{ $sale->sale_date->format('Y-m-d') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $sale->sale_date->translatedFormat('l') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                        {{ $sale->transaction_time->format('H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $sale->meatProduct->name }}
                                    </div>
                                    @if ($sale->meatProduct->barcode ?? false)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            باركود: {{ $sale->meatProduct->barcode }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($sale->transaction_type == 'sale')
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
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end">
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($sale->quantity, 3) }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">كجم</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end">
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($sale->sale_price, 2) }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">$</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end">
                                        <span class="font-bold text-gray-900 dark:text-white">
                                            {{ number_format($sale->total_amount, 2) }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">$</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    @if ($sale->notes)
                                        <div class="relative group">
                                            <span class="truncate block max-w-[200px]">
                                                {{ Str::limit($sale->notes, 30) }}
                                            </span>
                                            <div
                                                class="absolute z-10 invisible group-hover:visible bg-gray-900 text-white text-sm rounded-lg px-3 py-2 bottom-full mb-2 w-64">
                                                {{ $sale->notes }}
                                                <div
                                                    class="absolute top-full right-4 -mt-2 border-4 border-transparent border-t-gray-900">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                                            <i class="fas fa-inbox text-3xl"></i>
                                        </div>
                                        <p class="text-lg font-medium mb-2">لا توجد بيانات</p>
                                        <p class="text-sm">لم يتم العثور على عمليات للفترة المحددة</p>
                                        <a href="{{ route('meat-inventory.daily-sales.create') }}"
                                            class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                            <i class="fas fa-plus ml-2"></i>
                                            أضف عملية جديدة
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sales->count() > 0)
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            عرض {{ $sales->count() }} سجل من أصل {{ $sales->total() }}
                        </div>
                        <div>
                            {{ $sales->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- مخطط بياني بسيط -->
        @if ($sales->count() > 0)
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-line ml-2"></i>
                        إحصائيات سريعة
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">متوسط المبيعات اليومية</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                                @php
                                    $days =
                                        \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) +
                                        1;
                                    $avgSales = $days > 0 ? $stats['total_sales'] / $days : 0;
                                @endphp
                                {{ number_format($avgSales, 2) }}
                                <span class="text-sm">$/يوم</span>
                            </p>
                        </div>

                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">متوسط الكمية اليومية</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">
                                @php
                                    $totalQuantity = $sales->sum('quantity');
                                    $avgQuantity = $days > 0 ? $totalQuantity / $days : 0;
                                @endphp
                                {{ number_format($avgQuantity, 3) }}
                                <span class="text-sm">كجم/يوم</span>
                            </p>
                        </div>

                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">متوسط سعر الكيلو</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-2">
                                @php
                                    $totalSalesAmount = $sales->where('transaction_type', 'sale')->sum('total_amount');
                                    $totalSalesQuantity = $sales->where('transaction_type', 'sale')->sum('quantity');
                                    $avgPrice = $totalSalesQuantity > 0 ? $totalSalesAmount / $totalSalesQuantity : 0;
                                @endphp
                                {{ number_format($avgPrice, 2) }}
                                <span class="text-sm">$/كجم</span>
                            </p>
                        </div>

                        <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">نسبة المرتجعات</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">
                                @php
                                    $returnsPercentage =
                                        $stats['total_sales'] > 0
                                            ? ($stats['total_returns'] / $stats['total_sales']) * 100
                                            : 0;
                                @endphp
                                {{ number_format($returnsPercentage, 2) }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('script')
        <script>
            // تصدير البيانات إلى CSV
            function exportToCSV() {
                const rows = [];
                const headers = ['التاريخ', 'الوقت', 'المنتج', 'النوع', 'الكمية (كجم)', 'السعر', 'الإجمالي ($)', 'ملاحظات'];
                rows.push(headers.join(','));

                @foreach ($sales as $sale)
                    rows.push([
                        '{{ $sale->sale_date->format('Y-m-d') }}',
                        '{{ $sale->transaction_time->format('H:i') }}',
                        '{{ $sale->meatProduct->name }}',
                        '{{ $sale->transaction_type == 'sale' ? 'بيع' : 'مرتجع' }}',
                        {{ $sale->quantity }},
                        {{ $sale->sale_price }},
                        {{ $sale->total_amount }},
                        '{{ addslashes($sale->notes ?? '') }}'
                    ].join(','));
                @endforeach

                const csvContent = "data:text/csv;charset=utf-8,\uFEFF" + rows.join('\n');
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement('a');
                link.setAttribute('href', encodedUri);
                link.setAttribute('download', 'تقرير_المبيعات_{{ $startDate }}_الى_{{ $endDate }}.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                showToast('تم تصدير البيانات بنجاح', 'success');
            }

            // فلترة سريعة بالنوع
            function filterByType(type) {
                const url = new URL(window.location.href);
                if (type === 'all') {
                    url.searchParams.delete('type');
                } else {
                    url.searchParams.set('type', type);
                }
                window.location.href = url.toString();
            }

            // فلترة بمنتج معين
            function filterByProduct(productId) {
                const url = new URL(window.location.href);
                url.searchParams.set('product_id', productId);
                window.location.href = url.toString();
            }

            // عرض تنبيه
            function showToast(message, type = 'info') {
                const toastId = 'toast-' + Date.now();
                const bgColor = type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' :
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';

                const toastHTML = `
            <div id="${toastId}" class="fixed bottom-4 left-4 z-50 animate-slide-in">
                <div class="flex items-center w-full max-w-xs p-4 mb-4 text-white ${bgColor} rounded-lg shadow dark:bg-gray-800 dark:text-white" role="alert">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="mr-3 text-sm font-normal">${message}</div>
                    <button type="button" onclick="document.getElementById('${toastId}').remove()"
                            class="mr-auto -mx-1.5 -my-1.5 text-white hover:text-gray-300 rounded-lg p-1.5 hover:bg-white/10 inline-flex items-center justify-center h-8 w-8">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

                document.body.insertAdjacentHTML('beforeend', toastHTML);

                setTimeout(() => {
                    const toast = document.getElementById(toastId);
                    if (toast) toast.remove();
                }, 5000);
            }

            // طباعة التقرير
            function printReport() {
                const printContent = document.querySelector('.bg-white.rounded-xl').outerHTML;
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
            <!DOCTYPE html>
            <html dir="rtl">
            <head>
                <title>تقرير المبيعات</title>
                <style>
                    body { font-family: 'Arial', sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                    th { background-color: #f2f2f2; }
                    .badge { padding: 2px 6px; border-radius: 4px; font-size: 12px; }
                    .bg-success { background-color: #d1fae5; color: #065f46; }
                    .bg-danger { background-color: #fee2e2; color: #991b1b; }
                </style>
            </head>
            <body>
                <h1>تقرير المبيعات</h1>
                <p>الفترة: {{ $startDate }} إلى {{ $endDate }}</p>
                ${printContent}
            </body>
            </html>
        `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            }
        </script>
    @endpush

    @push('css')
        <style>
            .animate-slide-in {
                animation: slideIn 0.3s ease-out;
            }

            @keyframes slideIn {
                from {
                    transform: translateX(-100%);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            /* تخصيص ألوان الجدول في الوضع الداكن */
            .dark table tbody tr:hover {
                background-color: #374151;
            }

            /* تخصيص الترقيم */
            .pagination {
                display: flex;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .pagination li {
                margin: 0 2px;
            }

            .pagination a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 32px;
                height: 32px;
                padding: 0 8px;
                border-radius: 6px;
                border: 1px solid #d1d5db;
                background-color: white;
                color: #374151;
                font-size: 14px;
                transition: all 0.2s;
            }

            .pagination a:hover {
                background-color: #f3f4f6;
                border-color: #9ca3af;
            }

            .pagination .active a {
                background-color: #3b82f6;
                border-color: #3b82f6;
                color: white;
            }

            .dark .pagination a {
                background-color: #374151;
                border-color: #4b5563;
                color: #d1d5db;
            }

            .dark .pagination a:hover {
                background-color: #4b5563;
            }

            .dark .pagination .active a {
                background-color: #2563eb;
                border-color: #2563eb;
                color: white;
            }
        </style>
    @endpush

@endsection
