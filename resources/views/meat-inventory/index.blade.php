@extends('layout.customer.app')

@section('title', 'نظام إدارة الملحمه')

@push('css')
    <!-- مكتبة Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        /* تخصيص Select2 للغة العربية */
        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            height: 42px;
            padding: 0.5rem 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            text-align: right;
            padding-right: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            left: 8px;
            right: auto;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #ECC631;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            text-align: right;
        }

        /* تحسينات للغة العربية */
        .select2-container--default .select2-results__option {
            text-align: right;
            padding: 8px 12px;
        }

        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 768px) {
            .select2-container--default .select2-selection--single {
                height: 38px;
                font-size: 0.875rem;
            }
        }
    </style>
    <style>
        :root {
            --primary: #ECC631;
            --secondary: #333127;
            --text: #222222;
            --bg: #f0f0f0;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f9fafb;
        }

        .filter-section {
            background: var(--secondary);
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

        .stat-card {
            transition: all 0.3s ease;
            border-right: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .content-section {
            transition: all 0.3s ease;
        }

        .nav-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
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
            border-top: 5px solid var(--primary);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .movement-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .movement-in {
            background: #DCFCE7;
            color: #166534;
        }

        .movement-out {
            background: #FEE2E2;
            color: #991B1B;
        }

        .movement-return {
            background: #FEF3C7;
            color: #92400E;
        }

        .movement-waste {
            background: #F3F4F6;
            color: #374151;
        }

        .dynamic-table input,
        .dynamic-table select {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            width: 100%;
        }

        .dynamic-table input:focus,
        .dynamic-table select:focus {
            outline: none;
            ring: 2px;
            ring-color: #ECC631;
            border-color: #ECC631;
        }

        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
        }

        /* تحسينات RTSL والاستجابة */
        .table-responsive {
            max-height: 400px;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive table {
            min-width: 100%;
            width: auto;
        }

        .table-responsive th,
        .table-responsive td {
            white-space: nowrap;
            min-width: 120px;
            padding: 12px 8px;
        }

        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .stat-card {
                margin-bottom: 1rem;
            }

            .nav-btn {
                font-size: 0.8rem;
                padding: 0.75rem 0.5rem;
            }

            .table-responsive th,
            .table-responsive td {
                padding: 8px 6px;
                font-size: 0.875rem;
                min-width: 100px;
            }

            .grid-cols-1 {
                grid-template-columns: 1fr;
            }

            .grid-cols-2 {
                grid-template-columns: 1fr;
            }

            .md\:grid-cols-2,
            .md\:grid-cols-3,
            .md\:grid-cols-4 {
                grid-template-columns: 1fr;
            }

            .text-2xl {
                font-size: 1.25rem;
            }

            .text-3xl {
                font-size: 1.5rem;
            }
        }

        /* تحسينات للشاشات المتوسطة */
        @media (min-width: 769px) and (max-width: 1024px) {

            .table-responsive th,
            .table-responsive td {
                min-width: 110px;
                padding: 10px 8px;
            }
        }

        /* تحسينات للجداول */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #374151;
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
        }

        .data-table td {
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
        }

        .data-table tr:hover {
            background-color: #f9fafb;
        }

        /* تحسينات للأزرار في الجداول */
        .table-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .table-actions button {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* تحسينات للنماذج */
        .form-grid {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        /* تحسينات للعناوين */
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        /* تحسينات للبطاقات */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f8fafc;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* تحسينات للشريط التنقل */
        .nav-tabs {
            display: flex;
            flex-wrap: wrap;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        .nav-tab {
            padding: 0.75rem 1rem;
            border: none;
            background: none;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        /* تحسينات للمودال */
        .modal-content {
            max-height: 85vh;
            overflow-y: auto;
        }

        @media (max-width: 640px) {
            .modal-content {
                margin: 1rem;
                width: calc(100% - 2rem);
            }
        }
    </style>
@endpush

@section('content')
    <div dir="rtl" class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-2">نظام إدارة الملحمه</h1>
        <p class="text-center text-gray-600 mb-6 sm:mb-8 text-sm sm:text-base">إدارة المخزون والحسابات اليومية للحم</p>

        <!-- تبويبات التنقل -->
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
            <ul class="flex flex-wrap -mb-px overflow-x-auto">
                <li class="me-2 flex-shrink-0">
                    <button type="button"
                        class="nav-btn inline-block p-3 sm:p-4 text-yellow-400 border-b-2 border-yellow-400 rounded-t-lg active"
                        data-target="dashboard-section">
                        <i class="fas fa-chart-pie ml-2"></i>لوحة التحكم
                    </button>
                </li>
                <li class="me-2 flex-shrink-0">
                    <button type="button"
                        class="nav-btn inline-block p-3 sm:p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target="products-management-section">
                        <i class="fas fa-cube ml-2"></i>إدارة المنتجات
                    </button>
                </li>
                @if (request()->query('mobile') != 1)
                    <li class="me-2 flex-shrink-0">
                        <button type="button"
                            class="nav-btn inline-block p-3 sm:p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                            data-target="daily-sales-section">
                            <i class="fas fa-chart-bar ml-2"></i>المبيعات اليومية
                        </button>
                    </li>
                @endif
                <li class="me-2 flex-shrink-0">
                    <button type="button"
                        class="nav-btn inline-block p-3 sm:p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target="purchases-section">
                        <i class="fas fa-file-invoice ml-2"></i>فواتير الشراء
                    </button>
                </li>
                <li class="me-2 flex-shrink-0">
                    <button type="button"
                        class="nav-btn inline-block p-3 sm:p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target="inventory-section">
                        <i class="fas fa-warehouse ml-2"></i>إدارة المخزون
                    </button>
                </li>
                <li class="me-2 flex-shrink-0">
                    <button type="button"
                        class="nav-btn inline-block p-3 sm:p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target="reports-section">
                        <i class="fas fa-chart-bar ml-2"></i>التقارير
                    </button>
                </li>
            </ul>
        </div>

        <!-- قسم لوحة التحكم -->
        <div class="nav-item dashboard-section">
            <!-- الإحصائيات -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="stat-card bg-white rounded-lg shadow p-4 border-r-blue-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600 text-xs sm:text-sm">إجمالي المبيعات اليوم</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-800" id="totalSales">0 $</p>
                        </div>
                        <div class="text-blue-500">
                            <i class="fas fa-dollar-sign text-lg sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-lg shadow p-4 border-r-green-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600 text-xs sm:text-sm">اللحم المباع</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-800" id="soldWeight">0 كغ</p>
                        </div>
                        <div class="text-green-500">
                            <i class="fas fa-weight text-lg sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-lg shadow p-4 border-r-yellow-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600 text-xs sm:text-sm">الهدر</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-800" id="wasteWeight">0 كغ</p>
                        </div>
                        <div class="text-yellow-500">
                            <i class="fas fa-trash text-lg sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-white rounded-lg shadow p-4 border-r-purple-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600 text-xs sm:text-sm">الربح الصافي</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-800" id="netProfit">0 $</p>
                        </div>
                        <div class="text-purple-500">
                            <i class="fas fa-chart-line text-lg sm:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- الحركات الأخيرة -->
                <div class="table-container bg-white rounded-lg">
                    <div class="p-4 border-b">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-history ml-2"></i>
                            آخر الحركات
                        </h2>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th class="px-3 sm:px-6 py-3">التاريخ</th>
                                    <th class="px-3 sm:px-6 py-3">المنتج</th>
                                    <th class="px-3 sm:px-6 py-3">نوع الحركة</th>
                                    <th class="px-3 sm:px-6 py-3">الكمية</th>
                                    <th class="px-3 sm:px-6 py-3">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody id="recentMovementsTable">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">جاري التحميل...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- المخزون الحالي -->
                <div class="table-container bg-white rounded-lg">
                    <div class="p-4 border-b">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-boxes ml-2"></i>
                            المخزون الحالي
                        </h2>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th class="px-3 sm:px-6 py-3">الباركود</th>
                                    <th class="px-3 sm:px-6 py-3">المنتج</th>
                                    <th class="px-3 sm:px-6 py-3">الوزن المتوفر</th>
                                    <th class="px-3 sm:px-6 py-3">سعر البيع</th>
                                </tr>
                            </thead>
                            <tbody id="currentStockTable">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center">جاري التحميل...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم إدارة المنتجات -->
        <div class="nav-item products-management-section" style="display: none;">
            <!-- نموذج إضافة/تعديل المنتج -->
            <div id="productFormSection" class="table-container bg-white rounded-lg mt-6" style="display: none;">
                <div class="p-4 border-b bg-indigo-50">
                    <h2 class="text-lg sm:text-xl font-semibold text-indigo-800 flex items-center gap-2">
                        <i class="fas fa-edit ml-2"></i>
                        <span id="productFormTitle">إضافة منتج جديد</span>
                    </h2>
                </div>
                <div class="p-4 sm:p-6">
                    <form id="productForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="productId">
                        <div class="form-grid">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">اسم المنتج</label>
                                <input type="text" name="name" id="productName" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="أدخل اسم المنتج">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">باركود</label>
                                <input type="text" name="barcode" id="productBarcode"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                    placeholder="أدخل الباركود">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">الصورة</label>
                                <input type="file" name="image" id="productImage"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">الوزن الحالي (كغ)</label>
                                <input type="number" name="current_stock" id="productStock" step="0.1" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="الوزن بالكيلو">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">سعر التكلفة ($)</label>
                                <input type="number" name="cost_price" id="productCost" step="0.01" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="سعر التكلفة">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">سعر البيع ($)</label>
                                <input type="number" name="selling_price" id="productPrice" step="0.01" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="سعر البيع">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">نسبة الهدر %</label>
                                <input type="number" name="waste_percentage" id="productWaste" step="0.1" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="نسبة الهدر">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-700">الوصف</label>
                                <textarea name="description" id="productDescription"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="وصف المنتج"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-3 flex-wrap">
                            <button type="submit"
                                class="bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-save ml-2"></i>
                                <span id="productFormSubmitText">حفظ المنتج</span>
                            </button>
                            <button type="button" onclick="hideProductForm()"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-times ml-2"></i>
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-container bg-white rounded-lg">
                <div class="p-4 border-b bg-indigo-50">
                    <h2 class="text-lg sm:text-xl font-semibold text-indigo-800 flex items-center gap-2">
                        <i class="fas fa-cube ml-2"></i>
                        إدارة منتجات اللحم
                    </h2>
                </div>
                <div class="p-4 sm:p-6">
                    <!-- زر إضافة منتج جديد -->
                    <div class="mb-6">
                        <button onclick="showProductForm()"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                            <i class="fas fa-plus ml-2"></i>
                            إضافة منتج جديد
                        </button>
                    </div>

                    <!-- جدول المنتجات -->
                    <div class="table-responsive">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th class="px-3 sm:px-6 py-3">الباركود</th>
                                    <th class="px-3 sm:px-6 py-3">الاسم</th>
                                    <th class="px-3 sm:px-6 py-3">الوزن الحالي</th>
                                    <th class="px-3 sm:px-6 py-3">سعر التكلفة</th>
                                    <th class="px-3 sm:px-6 py-3">سعر البيع</th>
                                    <th class="px-3 sm:px-6 py-3">نسبة الهدر</th>
                                    <th class="px-3 sm:px-6 py-3">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="productsManagementTable">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center">جاري التحميل...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم إدارة المبيعات اليومية -->
        <div class="nav-item daily-sales-section" style="display: none;">
            <div class="table-container bg-white rounded-lg">
                <div class="p-4 border-b bg-purple-50">
                    <h2 class="text-lg sm:text-xl font-semibold text-purple-800 flex items-center gap-2">
                        <i class="fas fa-chart-bar ml-2"></i>
                        المبيعات اليومية
                    </h2>
                </div>
                <div class="p-4 my-8">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('meat-inventory.daily-sales.create') }}"
                            class="flex-1 flex items-center gap-4 bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-lg bg-blue-500 flex items-center justify-center">
                                    <i class="fas fa-plus text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">إضافة عملية</h3>
                                <p class="text-gray-600 text-sm">تسجيل بيع أو مرتجع جديد</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-left text-blue-500 text-lg"></i>
                            </div>
                        </a>

                        <a href="{{ route('meat-inventory.daily-sales.report') }}"
                            class="flex-1 flex items-center gap-4 bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-lg bg-green-500 flex items-center justify-center">
                                    <i class="fas fa-chart-line text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">تقرير المبيعات</h3>
                                <p class="text-gray-600 text-sm">عرض وتحليل جميع العمليات</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-left text-emerald-500 text-lg"></i>
                            </div>
                        </a>

                        <a href="{{ route('meat-inventory.daily-sales.daily-summary') }}"
                            class="flex-1 flex items-center gap-4 bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-lg bg-purple-500 flex items-center justify-center">
                                    <i class="fas fa-chart-pie text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 mb-1">الملخص اليومي</h3>
                                <p class="text-gray-600 text-sm">إحصائيات ومؤشرات الأداء</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-left text-purple-500 text-lg"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم فواتير الشراء -->
        <div class="nav-item purchases-section" style="display: none;">
            <!-- نموذج إنشاء فاتورة شراء -->
            <div id="purchaseInvoiceFormSection" class="table-container bg-white rounded-lg mt-6" style="display: none;">
                <div class="p-4 border-b bg-blue-50">
                    <h2 class="text-lg sm:text-xl font-semibold text-blue-800 flex items-center gap-2">
                        <i class="fas fa-receipt ml-2"></i>
                        إنشاء فاتورة شراء جديدة
                    </h2>
                </div>
                <div class="p-4 sm:p-6">
                    <form id="purchaseInvoiceForm">
                        @csrf
                        <!-- معلومات الفاتورة -->
                        <div class="form-grid mb-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">اسم المورد</label>
                                <input type="text" name="supplier_name"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="اسم المورد">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">تاريخ الشراء</label>
                                <input type="date" name="purchase_date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">ملاحظات</label>
                                <input type="text" name="notes"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="ملاحظات">
                            </div>
                        </div>

                        <!-- جدول العناصر الديناميكي -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                                <h3 class="text-lg font-semibold text-gray-800">عناصر الفاتورة</h3>
                                <button type="button" onclick="addPurchaseItemRow()"
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class="fas fa-plus"></i>
                                    إضافة سطر
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="data-table dynamic-table w-full">
                                    <thead>
                                        <tr>
                                            <th class="px-2 py-3 w-12 text-center">#</th>
                                            <th class="px-2 py-3 min-w-[200px]">المنتج</th>
                                            <th class="px-2 py-3 min-w-[120px]">الكمية (كغ)</th>
                                            <th class="px-2 py-3 min-w-[120px]">سعر الوحدة ($)</th>
                                            <th class="px-2 py-3 min-w-[120px]">الإجمالي ($)</th>
                                            <th class="px-2 py-3 min-w-[80px] text-center">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchaseItemsTable">
                                        <!-- سيتم إضافة الأسطر ديناميكياً -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="4" class="px-4 py-3 text-right font-bold">المجموع:</td>
                                            <td class="px-4 py-3 font-bold" id="purchaseTotalAmount">0.00 $</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="flex gap-3 flex-wrap">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-save ml-2"></i>
                                حفظ الفاتورة
                            </button>
                            <button type="button" onclick="hidePurchaseInvoiceForm()"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-times ml-2"></i>
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-container bg-white rounded-lg">
                <div class="p-4 border-b bg-blue-50">
                    <h2 class="text-lg sm:text-xl font-semibold text-blue-800 flex items-center gap-2">
                        <i class="fas fa-file-invoice ml-2"></i>
                        فواتير الشراء
                    </h2>
                </div>
                <div class="p-4 sm:p-6">
                    <!-- زر إنشاء فاتورة جديدة -->
                    <div class="mb-6">
                        <button onclick="showPurchaseInvoiceForm()"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                            <i class="fas fa-plus ml-2"></i>
                            إنشاء فاتورة شراء جديدة
                        </button>
                    </div>

                    <!-- قائمة الفواتير -->
                    <div class="table-responsive">
                        <table class="data-table w-full">
                            <thead>
                                <tr>
                                    <th class="px-3 sm:px-6 py-3">رقم الفاتورة</th>
                                    <th class="px-3 sm:px-6 py-3">المورد</th>
                                    <th class="px-3 sm:px-6 py-3">التاريخ</th>
                                    <th class="px-3 sm:px-6 py-3">المبلغ الإجمالي</th>
                                    <th class="px-3 sm:px-6 py-3">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseInvoicesTable">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">جاري التحميل...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم إدارة المخزون -->
        <div class="nav-item inventory-section" style="display: none;">
            <div class="grid grid-cols-1 gap-4 sm:gap-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                    <button onclick="showSection('sale')"
                        class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center gap-2 transition text-sm sm:text-base">
                        <i class="fas fa-sign-out-alt ml-2"></i>
                        تسجيل خروج للبيع
                    </button>
                    <button onclick="showSection('return')"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center gap-2 transition text-sm sm:text-base">
                        <i class="fas fa-undo ml-2"></i>
                        تسجيل إرجاع
                    </button>
                    <button onclick="showSection('waste')"
                        class="bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center gap-2 transition text-sm sm:text-base">
                        <i class="fas fa-trash ml-2"></i>
                        تسجيل هدر
                    </button>
                </div>

                <!-- باقي محتوى قسم إدارة المخزون بنفس النمط المحسن... -->
                <!-- تسجيل خروج للبيع -->
                <div id="saleSection" class="content-section table-container bg-white rounded-lg">
                    <div class="p-4 border-b bg-green-50">
                        <h2 class="text-lg sm:text-xl font-semibold text-green-800 flex items-center gap-2">
                            <i class="fas fa-sign-out-alt ml-2"></i>
                            تسجيل خروج بضاعة للبيع
                        </h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <form id="saleForm">
                            @csrf
                            <div class="form-grid">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">المنتج</label>
                                    <select name="meat_product_id" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                        id="saleProductsSelect">
                                        <option value="">اختر المنتج</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">الكمية (كيلو)</label>
                                    <input type="number" name="quantity" step="0.1" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                        placeholder="مثال: 2.5">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">سعر الكيلو ($)</label>
                                    <input type="number" name="unit_price" step="0.01" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                        placeholder="مثال: 15.5">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">التاريخ</label>
                                    <input type="date" name="movement_date" value="{{ date('Y-m-d') }}" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                                        <i class="fas fa-check ml-2"></i>
                                        تسجيل الخروج
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- تسجيل إرجاع -->
                <div id="returnSection" class="content-section table-container bg-white rounded-lg"
                    style="display: none;">
                    <div class="p-4 border-b bg-yellow-50">
                        <h2 class="text-lg sm:text-xl font-semibold text-yellow-800 flex items-center gap-2">
                            <i class="fas fa-undo ml-2"></i>
                            تسجيل إرجاع باقي للمستودع
                        </h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <form id="returnForm">
                            @csrf
                            <div class="form-grid">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">المنتج</label>
                                    <select name="meat_product_id" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                        id="returnProductsSelect">
                                        <option value="">اختر المنتج</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">الكمية المرتجعة
                                        (كيلو)</label>
                                    <input type="number" name="quantity" step="0.1" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                        placeholder="مثال: 5.2">
                                </div>
                                <div id="default_waste_persent_div" style="display: none;">
                                    <label for="default_waste_persent_field"
                                        class="block mb-2 text-sm font-medium text-gray-700">
                                        نسبة الهدر الافتراضية (%)
                                    </label>
                                    <input type="number" id="default_waste_persent_field"
                                        name="default_waste_persent_field" value="0"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2" disabled />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">التاريخ</label>
                                    <input type="date" name="movement_date" value="{{ date('Y-m-d') }}" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <div class="flex items-center mb-4">
                                        <input id="calc_default_waste_persent" type="checkbox" value="0"
                                            class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                        <label for="calc-default-waste-persent"
                                            class="select-none ms-2 text-sm font-medium text-heading">
                                            حساب نسبة الهدر الافتراضية للمنتج
                                        </label>
                                    </div>
                                    <button type="submit"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                                        <i class="fas fa-undo ml-2"></i>
                                        تسجيل الإرجاع
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- تسجيل هدر -->
                <div id="wasteSection" class="content-section table-container bg-white rounded-lg"
                    style="display: none;">
                    <div class="p-4 border-b bg-red-50">
                        <h2 class="text-lg sm:text-xl font-semibold text-red-800 flex items-center gap-2">
                            <i class="fas fa-trash ml-2"></i>
                            تسجيل هدر
                        </h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <form id="wasteForm">
                            @csrf
                            <div class="form-grid">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">المنتج</label>
                                    <select name="meat_product_id" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                        id="wasteProductsSelect">
                                        <option value="">اختر المنتج</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">كمية الهدر (كيلو)</label>
                                    <input type="number" id="wasteProductsQuantity" name="quantity" step="0.1"
                                        required class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                        placeholder="مثال: 1.5">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">التاريخ</label>
                                    <input type="date" id="wasteProductsMovementDate" name="movement_date"
                                        value="{{ date('Y-m-d') }}" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2">
                                        <i class="fas fa-trash ml-2"></i>
                                        تسجيل الهدر
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم التقارير -->
        <div class="nav-item reports-section" style="display: none;">
            <div class="table-container bg-white rounded-lg">
                <div class="p-4 border-b bg-purple-50">
                    <h2 class="text-lg sm:text-xl font-semibold text-purple-800 flex items-center gap-2">
                        <i class="fas fa-chart-bar ml-2"></i>
                        التقارير والإحصائيات
                    </h2>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="form-grid mb-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">من تاريخ</label>
                            <input type="date" id="startDate"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ date('Y-m-01') }}">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">إلى تاريخ</label>
                            <input type="date" id="endDate"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <button onclick="loadReports()"
                        class="bg-purple-500 hover:bg-purple-600 text-white font-medium py-2.5 px-6 rounded-lg flex items-center justify-center gap-2 mb-6">
                        <i class="fas fa-sync ml-2"></i>
                        عرض التقارير
                    </button>

                    <div id="reportsResults" class="bg-gray-50 rounded-lg p-4 sm:p-6">
                        <p class="text-center text-gray-500">اختر الفترة ثم انقر على "عرض التقارير"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مؤشر التحميل -->
    <div class="loading-overlay">
        <div class="spinner"></div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/i18n/ar.js"></script>
    <script>
        // المتغيرات العامة
        let purchaseItemCounter = 0;
        let currentProducts = [];

        // عند تحميل الصفحة
        $(document).ready(function() {
            loadDailyReport();
            loadProducts();
            loadRecentMovements();
            loadCurrentStock();
            loadPurchaseInvoices();

            // إعداد النماذج
            setupForms();
            setupNavigation();
            initializeSelect2();
        });


        function initializeSelect2() {
            // تهيئة Select2 للعناصر الموجودة حالياً
            $('.product-select, #saleProductsSelect, #returnProductsSelect, #wasteProductsSelect').select2({
                language: "ar",
                placeholder: "اختر المنتج",
                allowClear: true,
                width: '100%',
                dir: "rtl"
            });

            // إعادة تهيئة Select2 عند إضافة صفوف جديدة
            $(document).on('select2:open', () => {
                document.querySelector('.select2-container--open .select2-search__field').focus();
            });
        }

        // إعادة تهيئة Select2 للعناصر الجديدة
        function reinitializeSelect2() {
            $('.product-select:not(.select2-hidden-accessible)').select2({
                language: "ar",
                placeholder: "اختر المنتج",
                allowClear: true,
                width: '100%',
                dir: "rtl"
            });
        }


        // إعداد التنقل بين الأقسام
        function setupNavigation() {
            $('.nav-btn').on('click', function() {
                // إزالة التنسيقات من الأزرار
                $('.nav-btn').removeClass('text-yellow-400 border-yellow-400 active')
                    .addClass('border-transparent');

                // إضافة تنسيق للزر النشط
                $(this).addClass('text-yellow-400 border-yellow-400 active')
                    .removeClass('border-transparent');

                // إخفاء كل العناصر
                $('.nav-item').hide();

                // إظهار العنصر المطلوب
                let target = $(this).data('target');
                $('.' + target).fadeIn(300);

                // تحميل البيانات حسب القسم
                if (target === 'products-management-section') {
                    loadProductsManagement();
                } else if (target === 'purchases-section') {
                    loadPurchaseInvoices();
                } else if (target === 'dashboard-section') {
                    loadDailyReport();
                    loadRecentMovements();
                    loadCurrentStock();
                }
            });
        }

        // إظهار وإخفاء الأقسام الداخلية
        function showSection(sectionName) {
            $('.content-section').hide();
            $('#' + sectionName + 'Section').fadeIn(300);
        }

        // إعداد النماذج
        function setupForms() {
            // نموذج المنتج
            $('#productForm').on('submit', function(e) {
                e.preventDefault();
                saveProduct();
            });

            // نموذج فاتورة الشراء
            $('#purchaseInvoiceForm').on('submit', function(e) {
                e.preventDefault();
                savePurchaseInvoice();
            });

            // نموذج البيع
            $('#saleForm').on('submit', function(e) {
                e.preventDefault();
                recordSale();
            });

            // نموذج الإرجاع
            $('#returnForm').on('submit', function(e) {
                e.preventDefault();
                recordReturn();
            });

            // نموذج الهدر
            $('#wasteForm').on('submit', function(e) {
                e.preventDefault();
                recordWaste();
            });
        }

        // ========== إدارة المنتجات ==========
        function showProductForm() {
            $('#productFormSection').fadeIn(300);
            setTimeout(() => {
                reinitializeSelect2();
            }, 300);
        }

        function hideProductForm() {
            $('#productFormSection').fadeOut(300);
            $('#productForm')[0].reset();
            $('#productId').val('');
            $('#productFormTitle').text('إضافة منتج جديد');
            $('#productFormSubmitText').text('حفظ المنتج');
        }

        function loadProductsManagement() {
            showLoading();
            $.ajax({
                url: '{{ route('meat-inventory.products.index') }}',
                type: 'GET',
                success: function(response) {
                    let tableBody = '';
                    if (response.length === 0) {
                        tableBody =
                            '<tr><td colspan="6" class="px-6 py-4 text-center">لا توجد منتجات</td></tr>';
                    } else {
                        response.forEach(product => {
                            tableBody += `
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">${product.barcode}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">${product.name}</td>
                                    <td class="px-6 py-4">${product.current_stock} كغ</td>
                                    <td class="px-6 py-4">${product.cost_price} $</td>
                                    <td class="px-6 py-4">${product.selling_price} $</td>
                                    <td class="px-6 py-4">${product.waste_percentage}%</td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <button onclick="editProduct(${product.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm flex items-center gap-1">
                                                <i class="fas fa-edit"></i>
                                                تعديل
                                            </button>
                                            <button onclick="deleteProduct(${product.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm flex items-center gap-1">
                                                <i class="fas fa-trash"></i>
                                                حذف
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    $('#productsManagementTable').html(tableBody);
                    hideLoading();
                },
                error: function(xhr) {
                    showNotification('خطأ في تحميل المنتجات', 'error');
                    hideLoading();
                }
            });
        }

        function saveProduct() {
            showLoading();
            const formData = new FormData($('#productForm')[0]);
            const productId = $('#productId').val();
            const url = productId ?
                `/api/meat-inventory/products/${productId}` :
                '{{ route('meat-inventory.products.store') }}';
            const method = productId ? 'PUT' : 'POST';

            // إضافة _method للحقول إذا كان تحديث
            if (productId) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hideProductForm();
                    loadProductsManagement();
                    loadProducts();
                    showNotification(productId ? 'تم تحديث المنتج بنجاح' : 'تم حفظ المنتج بنجاح', 'success');
                    hideLoading();
                },
                error: function(xhr) {
                    showNotification('خطأ في حفظ المنتج', 'error');
                    hideLoading();
                }
            });
        }

        function editProduct(id) {
            showLoading();
            $.ajax({
                url: `/api/meat-inventory/products/${id}`,
                type: 'GET',
                success: function(response) {
                    const product = response;
                    $('#productFormTitle').text('تعديل المنتج');
                    $('#productFormSubmitText').text('تحديث المنتج');
                    $('#productId').val(product.id);
                    $('#productName').val(product.name);
                    $('#productBarcode').val(product.barcode);
                    $('#productStock').val(product.current_stock);
                    $('#productCost').val(product.cost_price);
                    $('#productPrice').val(product.selling_price);
                    $('#productWaste').val(product.waste_percentage);
                    $('#productDescription').val(product.description || '');
                    $('#productFormSection').fadeIn(300);
                    hideLoading();
                },
                error: function(xhr) {
                    showNotification('خطأ في تحميل بيانات المنتج', 'error');
                    hideLoading();
                }
            });
        }

        function deleteProduct(id) {
            if (confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
                showLoading();
                $.ajax({
                    url: `/api/meat-inventory/products/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        loadProductsManagement();
                        loadProducts();
                        showNotification('تم حذف المنتج بنجاح', 'success');
                        hideLoading();
                    },
                    error: function(xhr) {
                        showNotification('خطأ في حذف المنتج', 'error');
                        hideLoading();
                    }
                });
            }
        }

        // ========== فواتير الشراء ==========
        function showPurchaseInvoiceForm() {
            $('#purchaseInvoiceFormSection').fadeIn(300);
            addPurchaseItemRow(); // إضافة سطر فارغ عند فتح النموذج
        }

        function hidePurchaseInvoiceForm() {
            $('#purchaseInvoiceFormSection').fadeOut(300);
            $('#purchaseInvoiceForm')[0].reset();
            $('#purchaseItemsTable').empty();
            purchaseItemCounter = 0;
        }

        function addPurchaseItemRow() {
            const rowId = ++purchaseItemCounter;
            const row = `
                <tr class="purchase-item-row border-b">
                    <td class="px-4 py-3">${rowId}</td>
                    <td class="px-4 py-3">
                        <select name="items[${rowId}][meat_product_id]" class="product-select w-full border rounded px-2 py-1" required>
                            <option value="">اختر المنتج</option>
                            ${currentProducts.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" name="items[${rowId}][quantity]" class="quantity-input w-full border rounded px-2 py-1" step="0.1" min="0.1" required onchange="calculatePurchaseTotal()">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" name="items[${rowId}][unit_cost]" class="unit-price-input w-full border rounded px-2 py-1" step="0.01" min="0" required onchange="calculatePurchaseTotal()">
                    </td>
                    <td class="px-4 py-3">
                        <span class="item-total">0.00</span> $
                    </td>
                    <td class="px-4 py-3">
                        <button type="button" onclick="$(this).closest('tr').remove(); calculatePurchaseTotal();" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#purchaseItemsTable').append(row);
            setTimeout(() => {
                reinitializeSelect2();
            }, 100);
        }

        function calculatePurchaseTotal() {
            let total = 0;
            $('.purchase-item-row').each(function() {
                const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
                const unitPrice = parseFloat($(this).find('.unit-price-input').val()) || 0;
                const itemTotal = quantity * unitPrice;
                $(this).find('.item-total').text(itemTotal.toFixed(2));
                total += itemTotal;
            });
            $('#purchaseTotalAmount').text(total.toFixed(2) + ' $');
        }

        function loadPurchaseInvoices() {
            showLoading();
            $.ajax({
                url: '{{ route('meat-inventory.purchases.index') }}',
                type: 'GET',
                success: function(response) {
                    let tableBody = '';
                    if (response.length === 0) {
                        tableBody =
                            '<tr><td colspan="5" class="px-6 py-4 text-center">لا توجد فواتير شراء</td></tr>';
                    } else {
                        response.forEach(invoice => {
                            const dateObj = new Date(invoice.purchase_date);
                            const purchase_date = dateObj.toISOString().split('T')[0];
                            tableBody += `
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">${invoice.invoice_number || 'INV-' + invoice.id}</td>
                                    <td class="px-6 py-4">${invoice.supplier_name || '-'}</td>
                                    <td class="px-6 py-4">${purchase_date}</td>
                                    <td class="px-6 py-4">${invoice.total_amount} $</td>
                                    <td class="px-6 py-4">
                                        <button onclick="viewPurchaseInvoice(${invoice.id})" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm flex items-center gap-1">
                                            <i class="fas fa-eye"></i>
                                            عرض
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    $('#purchaseInvoicesTable').html(tableBody);
                    hideLoading();
                },
                error: function(xhr) {
                    showNotification('خطأ في تحميل فواتير الشراء', 'error');
                    hideLoading();
                }
            });
        }

        function savePurchaseInvoice() {
            showLoading();

            // جمع البيانات الرئيسية من الفورم
            const formData = {
                _token: $('input[name="_token"]').val(),
                supplier_name: $('input[name="supplier_name"]').val(),
                purchase_date: $('input[name="purchase_date"]').val(),
                notes: $('textarea[name="notes"]').val(),
                items: []
            };

            // جمع بيانات العناصر
            $('.purchase-item-row').each(function() {
                const productId = $(this).find('.product-select').val();
                const quantity = $(this).find('.quantity-input').val();
                const unitCost = $(this).find('.unit-price-input').val();

                if (productId && quantity && unitCost) {
                    formData.items.push({
                        meat_product_id: productId,
                        quantity: parseFloat(quantity),
                        unit_cost: parseFloat(unitCost)
                    });
                }
            });

            if (formData.items.length === 0) {
                showNotification('يجب إضافة عنصر واحد على الأقل', 'error');
                hideLoading();
                return;
            }

            $.ajax({
                url: '{{ route('meat-inventory.purchases.store') }}',
                type: 'POST',
                data: JSON.stringify(formData),
                processData: false,
                contentType: 'application/json',
                success: function(response) {
                    hidePurchaseInvoiceForm();
                    loadPurchaseInvoices();
                    loadProducts();
                    showNotification('تم حفظ فاتورة الشراء بنجاح', 'success');
                    hideLoading();
                },
                error: function(xhr) {
                    showNotification('خطأ في حفظ فاتورة الشراء', 'error');
                    hideLoading();
                }
            });
        }

        function viewPurchaseInvoice(id) {
            showLoading();

            $.ajax({
                url: `/api/meat-inventory/purchases/${id}/details`,
                type: 'GET',
                success: function(response) {
                    showPurchaseInvoiceModal(response.invoice);
                    hideLoading();
                },
                error: function(xhr) {
                    showNotification('خطأ في تحميل تفاصيل الفاتورة', 'error');
                    hideLoading();
                }
            });
        }

        function showPurchaseInvoiceModal(invoice) {
            // إنشاء محتوى المودال
            const modalContent = `
                <div dir="rtl" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
                        <!-- الهيدر -->
                        <div class="bg-blue-500 text-white p-6 rounded-t-lg">
                            <div class="flex justify-between items-center">
                                <h2 class="text-2xl font-bold">فاتورة الشراء #${invoice.invoice_number}</h2>
                                <button onclick="closeModal()" class="text-white hover:text-gray-200 text-2xl">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                                <div>
                                    <strong>المورد:</strong> ${invoice.supplier_name || 'غير محدد'}
                                </div>
                                <div>
                                    <strong>التاريخ:</strong> ${invoice.purchase_date}
                                </div>
                                <div>
                                    <strong>المبلغ الإجمالي:</strong> ${invoice.total_amount} $
                                </div>
                            </div>
                            ${invoice.notes ? `<div class="mt-2"><strong>ملاحظات:</strong> ${invoice.notes}</div>` : ''}
                        </div>

                        <!-- محتوى المودال -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-4 text-gray-800">عناصر الفاتورة</h3>

                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3">#</th>
                                            <th class="px-6 py-3">المنتج</th>
                                            <th class="px-6 py-3">الكمية (كغ)</th>
                                            <th class="px-6 py-3">سعر الوحدة ($)</th>
                                            <th class="px-6 py-3">الإجمالي ($)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${invoice.items.map((item, index) => `
                                                                                                                                                                                                                <tr class="border-b hover:bg-gray-50">
                                                                                                                                                                                                                    <td class="px-6 py-4">${index + 1}</td>
                                                                                                                                                                                                                    <td class="px-6 py-4 font-medium text-gray-900">
                                                                                                                                                                                                                        ${item.product?.name || 'منتج محذوف'}
                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                    <td class="px-6 py-4">${item.quantity}</td>
                                                                                                                                                                                                                    <td class="px-6 py-4">${item.unit_cost}</td>
                                                                                                                                                                                                                    <td class="px-6 py-4 font-semibold">${(item.quantity * item.unit_cost).toFixed(2)}</td>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                            `).join('')}
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-right font-bold">المجموع الكلي:</td>
                                            <td class="px-6 py-4 font-bold text-lg text-blue-600">${invoice.total_amount} $</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mt-6 flex justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    <strong>عدد العناصر:</strong> ${invoice.items.length}
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="printInvoice(${invoice.id})"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                        <i class="fas fa-print"></i>
                                        طباعة
                                    </button>
                                    <button onclick="closeModal()"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                        إغلاق
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // إضافة المودال إلى الصفحة
            $('body').append(modalContent);
        }

        function closeModal() {
            $('.fixed.inset-0').remove();
        }

        function printInvoice(invoiceId) {
            // استخدام المسار الجديد
            window.open(`/api/meat-inventory/purchases/${invoiceId}/print`, '_blank');
        }

        // ========== إدارة المخزون ==========
        function recordSale() {
            showLoading();
            const formData = new FormData($('#saleForm')[0]);

            $.ajax({
                url: '{{ route('meat-inventory.inventory.sales.record') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#saleForm')[0].reset();
                    loadDailyReport();
                    loadCurrentStock();
                    loadRecentMovements();
                    showNotification('تم تسجيل البيع بنجاح', 'success');
                    hideLoading();
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.error || 'خطأ في تسجيل البيع';
                    showNotification(error, 'error');
                    hideLoading();
                }
            });
        }

        function recordReturn() {
            showLoading();
            const formData = new FormData($('#returnForm')[0]);

            $.ajax({
                url: '{{ route('meat-inventory.inventory.returns.record') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#returnForm')[0].reset();
                    loadDailyReport();
                    loadCurrentStock();
                    loadRecentMovements();
                    showNotification('تم تسجيل الإرجاع بنجاح', 'success');
                    hideLoading();
                    // if ($('#calc_default_waste_persent').is(':checked') && $('#default_waste_persent_field')
                    //     .val() != 0) {
                    //     console.log($('#default_waste_persent_field').val());
                    //         $('#wasteProductsSelect').val($('#returnProductsSelect').val()).trigger('change');
                    //         $('#wasteProductsQuantity').val($('#returnProductsSelect').val()).trigger('change');
                    //         $('#wasteProductsMovementDate').val($('#returnProductsSelect').val()).trigger('change');
                    // }
                },
                error: function(xhr) {
                    showNotification('خطأ في تسجيل الإرجاع', 'error');
                    hideLoading();
                }
            });
        }

        function recordWaste() {
            showLoading();
            const formData = new FormData($('#wasteForm')[0]);

            $.ajax({
                url: '{{ route('meat-inventory.inventory.waste.record') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#wasteForm')[0].reset();
                    loadDailyReport();
                    loadCurrentStock();
                    loadRecentMovements();
                    showNotification('تم تسجيل الهدر بنجاح', 'success');
                    hideLoading();
                },
                error: function(xhr) {
                    showNotification('خطأ في تسجيل الهدر', 'error');
                    hideLoading();
                }
            });
        }

        // ========== التقارير والبيانات ==========
        function loadDailyReport() {
            $.ajax({
                url: '{{ route('meat-inventory.inventory.reports.daily') }}',
                type: 'GET',
                success: function(response) {
                    $('#totalSales').text((response.total_sales || 0).toFixed(2) + ' $');
                    $('#soldWeight').text((response.sold_weight || 0).toFixed(2) + ' كغ');
                    $('#wasteWeight').text((response.waste_weight || 0).toFixed(2) + ' كغ');

                    // 🔥 جديد: استخدام الربح الصافي الحقيقي
                    $('#netProfit').text((response.net_profit || 0).toFixed(2) + ' $');
                },
                error: function(xhr) {
                    console.log('Error loading daily report');
                }
            });
        }

        function loadRecentMovements() {
            $.ajax({
                url: '{{ route('meat-inventory.inventory.movements.index') }}',
                type: 'GET',
                success: function(response) {
                    let tableBody = '';
                    if (response.length === 0) {
                        tableBody =
                            '<tr><td colspan="5" class="px-6 py-4 text-center">لا توجد حركات حديثة</td></tr>';
                    } else {
                        response.forEach(movement => {
                            let badgeClass = '';
                            let typeText = '';
                            const dateObj = new Date(movement.movement_date);
                            const movementDate = dateObj.toISOString().split('T')[0];

                            switch (movement.movement_type) {
                                case 'out':
                                    badgeClass = 'movement-out';
                                    typeText = 'بيع';
                                    break;
                                case 'return':
                                    badgeClass = 'movement-return';
                                    typeText = 'إرجاع';
                                    break;
                                case 'waste':
                                    badgeClass = 'movement-waste';
                                    typeText = 'هدر';
                                    break;
                                default:
                                    badgeClass = 'movement-in';
                                    typeText = 'شراء';
                            }

                            tableBody += `
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">${movementDate}</td>
                                    <td class="px-6 py-4">${movement.product?.name || 'غير معروف'}</td>
                                    <td class="px-6 py-4">
                                        <span class="movement-badge ${badgeClass}">${typeText}</span>
                                    </td>
                                    <td class="px-6 py-4">${movement.quantity} كغ</td>
                                    <td class="px-6 py-4">${movement.total_price || '-'} $</td>
                                </tr>
                            `;
                        });
                    }
                    $('#recentMovementsTable').html(tableBody);
                },
                error: function(xhr) {
                    console.log('Error loading movements');
                }
            });
        }

        function loadCurrentStock() {
            $.ajax({
                url: '{{ route('meat-inventory.products.index') }}',
                type: 'GET',
                success: function(response) {
                    let tableBody = '';
                    if (response.length === 0) {
                        tableBody =
                            '<tr><td colspan="3" class="px-6 py-4 text-center">لا توجد منتجات في المخزون</td></tr>';
                    } else {
                        response.forEach(product => {
                            tableBody += `
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">${product.barcode}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">${product.name}</td>
                                    <td class="px-6 py-4">${product.current_stock} كغ</td>
                                    <td class="px-6 py-4">${product.selling_price} $</td>
                                </tr>
                            `;
                        });
                    }
                    $('#currentStockTable').html(tableBody);
                },
                error: function(xhr) {
                    console.log('Error loading current stock');
                }
            });
        }

        function loadProducts() {
            $.ajax({
                url: '{{ route('meat-inventory.products.index') }}',
                type: 'GET',
                success: function(response) {
                    currentProducts = response;
                    updateProductSelects(response);
                },
                error: function(xhr) {
                    console.log('Error loading products');
                }
            });
        }

        function loadReports() {
            showLoading();
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();

            $.ajax({
                url: '{{ route('meat-inventory.inventory.reports.range') }}',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    let reportHtml = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">إجمالي المبيعات</h3>
                        <p class="text-2xl font-bold text-green-600">${(response.total_sales || 0).toFixed(2)} $</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">إجمالي الهدر</h3>
                        <p class="text-2xl font-bold text-red-600">${(response.waste_weight || 0).toFixed(2)} كغ</p>
                        <p class="text-sm text-gray-600">تكلفة: ${(response.waste_cost || 0).toFixed(2)} $</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">إجمالي التكلفة</h3>
                        <p class="text-2xl font-bold text-orange-600">${(response.total_cost || 0).toFixed(2)} $</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">صافي الربح</h3>
                        <p class="text-2xl font-bold ${(response.net_profit || 0) >= 0 ? 'text-blue-600' : 'text-red-600'}">
                            ${(response.net_profit || 0).toFixed(2)} $
                        </p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow mb-4">
                    <h3 class="font-semibold text-gray-700 mb-3">تفاصيل الأداء</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-sm text-gray-600">اللحم المباع</p>
                            <p class="font-bold">${(response.actual_sold_weight || 0).toFixed(2)} كغ</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">الإرجاعات</p>
                            <p class="font-bold">${(response.returned_weight || 0).toFixed(2)} كغ</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">الربح الإجمالي</p>
                            <p class="font-bold ${(response.gross_profit || 0) >= 0 ? 'text-green-600' : 'text-red-600'}">
                                ${(response.gross_profit || 0).toFixed(2)} $
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">عدد المبيعات</p>
                            <p class="font-bold">${response.sales_count || 0}</p>
                        </div>
                    </div>
                </div>
            `;

                    $('#reportsResults').html(reportHtml);
                    hideLoading();
                },
                error: function(xhr) {
                    showNotification('خطأ في تحميل التقارير', 'error');
                    hideLoading();
                }
            });
        }

        // ========== دوال مساعدة ==========
        function updateProductSelects(products) {
            const options = products.map(p => `<option value="${p.id}">${p.name}</option>`).join('');

            $('#saleProductsSelect').html('<option value="">اختر المنتج</option>' + options);
            $('#returnProductsSelect').html('<option value="">اختر المنتج</option>' + options);
            $('#wasteProductsSelect').html('<option value="">اختر المنتج</option>' + options);

            $('#saleProductsSelect, #returnProductsSelect, #wasteProductsSelect').trigger('change.select2');

            // إعداد التعامل مع نسبة الهدر الافتراضية في نموذج الإرجاع
            var waste_percentage_values = {};
            products.forEach((product, index, array) => {
                waste_percentage_values[product['id']] = product['waste_percentage'];
            });
            $('#calc_default_waste_persent').on('change', () => {
                if (!$('#calc_default_waste_persent').is(':checked')) {
                    $('#default_waste_persent_div').fadeOut();
                    return;
                }
                $('#default_waste_persent_div').fadeIn();
                const selectedProductId = $('#returnProductsSelect').val();
                const wastePercentage = waste_percentage_values[selectedProductId] || 0;
                $('#default_waste_persent_field').val(wastePercentage);
            });
            $('#returnProductsSelect').on('change', function() {
                if ($('#calc_default_waste_persent').is(':checked')) {
                    const selectedProductId = $(this).val();
                    const wastePercentage = waste_percentage_values[selectedProductId] || 0;
                    $('#default_waste_persent_field').val(wastePercentage);
                }
            });
        }

        function showNotification(message, type = 'info') {
            const notification = $(`
                <div class="fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white ${
                    type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                } z-50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `);

            $('body').append(notification);

            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        function showLoading() {
            $('.loading-overlay').fadeIn();
            $('.loading-overlay').css('display', 'flex');
        }

        function hideLoading() {
            $('.loading-overlay').fadeOut();
        }

        // إضافة CSRF token لجميع طلبات AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endpush
