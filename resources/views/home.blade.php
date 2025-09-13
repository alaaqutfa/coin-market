<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Coin Market Social Stock') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f9fafb;
        }

        .filter-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
        }

        .table-container {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
        }

        .quick-filter-btn {
            transition: all 0.3s ease;
        }

        .quick-filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination li {
            margin: 0 5px;
            display: inline-block;
        }

        .pagination a {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #667eea;
            text-decoration: none;
        }

        .pagination a:hover {
            background-color: #667eea;
            color: white;
        }

        .pagination .active a {
            background-color: #667eea;
            color: white;
            border: 1px solid #667eea;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">نظام إدارة المنتجات</h1>
        <p class="text-center text-gray-600 mb-8">قم بتصفية المنتجات حسب المعايير المختلفة</p>

        <!-- بطاقة الفلترة -->
        <div class="filter-section p-6 mb-8 text-white">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="fas fa-filter ml-2"></i>
                <span>تصفية المنتجات</span>
            </h2>

            <form id="filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- حقل الباركود -->
                <div>
                    <label class="block mb-2 text-sm font-medium">البحث بالباركود</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-barcode text-gray-400"></i>
                        </div>
                        <input type="text" name="barcode" placeholder="أدخل الباركود"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5">
                    </div>
                </div>

                <!-- حقل الاسم -->
                <div>
                    <label class="block mb-2 text-sm font-medium">البحث بالاسم</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-tag text-gray-400"></i>
                        </div>
                        <input type="text" name="name" placeholder="أدخل اسم المنتج"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5">
                    </div>
                </div>

                <!-- حقل السعر -->
                <div>
                    <label class="block mb-2 text-sm font-medium">السعر</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-dollar-sign text-gray-400"></i>
                        </div>
                        <input type="number" name="price" placeholder="السعر"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5">
                    </div>
                </div>

                <!-- حقل الوزن -->
                <div>
                    <label class="block mb-2 text-sm font-medium">الوزن</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-weight text-gray-400"></i>
                        </div>
                        <input type="number" name="weight" placeholder="الوزن"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5">
                    </div>
                </div>

                <!-- نطاق التواريخ -->
                <div>
                    <label class="block mb-2 text-sm font-medium">من تاريخ</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-calendar-day text-gray-400"></i>
                        </div>
                        <input type="date" name="date_from"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">إلى تاريخ</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-calendar-day text-gray-400"></i>
                        </div>
                        <input type="date" name="date_to"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5">
                    </div>
                </div>

                <!-- خيارات تاريخ سريعة -->
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium">خيارات سريعة</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setDateFilter('today')"
                            class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-calendar-day ml-2"></i> اليوم
                        </button>
                        <button type="button" onclick="setDateFilter('yesterday')"
                            class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-calendar-minus ml-2"></i> البارحة
                        </button>
                        <button type="button" onclick="setDateFilter('week')"
                            class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-calendar-week ml-2"></i> آخر أسبوع
                        </button>
                        <button type="button" onclick="setDateFilter('month')"
                            class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-calendar-alt ml-2"></i> آخر شهر
                        </button>
                        <button type="button" onclick="clearDateFilter()"
                            class="quick-filter-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-times ml-2"></i> مسح التواريخ
                        </button>
                    </div>
                </div>

                <!-- زر التصفية -->
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2.5 px-4 rounded-lg flex items-center justify-center">
                        <i class="fas fa-filter ml-2"></i>
                        تطبيق الفلترة
                    </button>
                </div>
            </form>
        </div>

        <!-- جدول المنتجات -->
        <div class="table-container bg-white rounded-lg">
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-list ml-2"></i>
                    قائمة المنتجات
                </h2>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
                    <i class="fas fa-boxes ml-2"></i>
                    <span id="products-count">{{ $products->total() }}</span> منتج
                </span>
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الباركود</span>
                                    <input type="text" name="barcode"
                                        class="filter-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-white text-xs focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="البحث..." />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">اسم المنتج</span>
                                    <input type="text" name="name"
                                        class="filter-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-white text-xs focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="البحث..." />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">السعر</span>
                                    <input type="text" name="price"
                                        class="filter-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-white text-xs focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="البحث..." />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الوزن</span>
                                    <input type="text" name="weight"
                                        class="filter-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-white text-xs focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="البحث..." />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">تاريخ الإضافة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الإجراءات</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body">
                        @if (count($products) > 0)
                            @include('partials.products-table', ['products' => $products])
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-500 text-lg">لا توجد منتجات</p>
                                        <p class="text-gray-400 text-sm">لم يتم العثور على أي منتجات تطابق معايير البحث
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        // تعريف الدالة في النطاق العام
        window.applyFilters = function() {
            // إظهار مؤشر التحميل
            $('#loadingOverlay').show();

            let data = {
                barcode: $("input[name='barcode']").val(),
                name: $("input[name='name']").val(),
                price: $("input[name='price']").val(),
                weight: $("input[name='weight']").val(),
                date_from: $("input[name='date_from']").val(),
                date_to: $("input[name='date_to']").val(),
            };

            $.ajax({
                url: "{{ route('products.filter') }}",
                type: "GET",
                data: data,
                success: function(response) {
                    $("#products-table-body").html(response);
                    // تحديث عدد المنتجات
                    let count = $(response).find('tr').length;
                    $("#products-count").text(count);

                    // إخفاء مؤشر التحميل
                    $('#loadingOverlay').hide();
                },
                error: function() {
                    // إخفاء مؤشر التحميل في حالة الخطأ
                    $('#loadingOverlay').hide();
                    alert('حدث خطأ أثناء جلب البيانات');
                }
            });
        };

        $(document).ready(function() {
            // فلترة أثناء الكتابة
            $(".filter-input").on("keyup change", function() {
                applyFilters();
            });

            // منع إعادة تحميل الصفحة عند submit
            $("#filter-form").on("submit", function(e) {
                e.preventDefault();
                applyFilters();
            });
        });

        // تعيين الفلتر حسب التاريخ - الإصدار المصحح
        function setDateFilter(type) {
            const today = new Date();
            let fromDate = new Date();
            let toDate = new Date();

            switch (type) {
                case 'today':
                    // من بداية اليوم إلى نهايته
                    fromDate.setHours(0, 0, 0, 0);
                    toDate.setHours(23, 59, 59, 999);
                    break;
                case 'yesterday':
                    // من بداية البارحة إلى نهايتها - التصحيح هنا
                    fromDate.setDate(today.getDate() - 1);
                    fromDate.setHours(0, 0, 0, 0);
                    toDate.setDate(today.getDate() - 1);
                    toDate.setHours(23, 59, 59, 999);
                    break;
                case 'week':
                    // من بداية الأسبوع إلى اليوم
                    fromDate.setDate(today.getDate() - 6); // 7 أيام بما فيها اليوم
                    fromDate.setHours(0, 0, 0, 0);
                    toDate.setHours(23, 59, 59, 999);
                    break;
                case 'month':
                    // من بداية الشهر إلى اليوم
                    fromDate.setDate(1);
                    fromDate.setHours(0, 0, 0, 0);
                    toDate.setHours(23, 59, 59, 999);
                    break;
            }

            // تنسيق التاريخ إلى yyyy-mm-dd
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            $("input[name='date_from']").val(formatDate(fromDate));
            $("input[name='date_to']").val(formatDate(toDate));

            // تطبيق الفلترة تلقائياً
            applyFilters();
        }

        // مسح فلترة التاريخ
        function clearDateFilter() {
            $("input[name='date_from']").val('');
            $("input[name='date_to']").val('');

            // تطبيق الفلترة تلقائياً
            applyFilters();
        }

        // تطبيق الفلاتر بعد تحميل الصفحة مباشرة
        setTimeout(() => {
            applyFilters();
        }, 100);
    </script>
</body>

</html>
