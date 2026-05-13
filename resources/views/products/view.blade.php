@extends('layout.app')

@section('title', 'إدارة المنتجات')

@push('css')
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
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .editable-field {
            border: 1px dashed transparent;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .editable-field:hover {
            border-color: #cbd5e0;
            background-color: #f7fafc;
        }

        .editable-field:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
        }

        .auto-refresh-btn {
            transition: all 0.3s;
        }

        .new-product-indicator {
            background-color: #10B981;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-left: 5px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .category-editable-container {
            position: relative;
            min-height: 40px;
        }

        .category-editable-container .display-mode {
            padding: 0.375rem 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }

        .category-editable-container .display-mode:hover {
            background-color: #f9fafb;
        }

        .category-select {
            min-width: 180px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .category-select:focus {
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .edit-actions {
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- العنوان الرئيسي -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">
            نظام إدارة المنتجات
        </h1>
        <p class="text-center text-gray-600 mb-8">
            قم بتصفية المنتجات حسب المعايير المختلفة
        </p>

        <!-- علامات التبويب -->
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 text-yellow-400 border-b-2 border-yellow-400 rounded-t-lg active"
                        data-target=".products-list">
                        قائمة المنتجات
                    </button>
                </li>
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target=".add-products">
                        إضافة منتجات
                    </button>
                </li>
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target=".products-images">
                        صور المنتجات
                    </button>
                </li>
            </ul>
        </div>

        <!-- ========== قسم قائمة المنتجات ========== -->
        <div class="nav-item products-list table-container bg-white rounded-lg">
            <!-- فلترة المنتجات -->
            <div class="filter-section p-6 mb-8 text-white">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
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
                                value="{{ $filters['barcode'] ?? '' }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-2.5">
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
                                value="{{ $filters['name'] ?? '' }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-2.5">
                        </div>
                    </div>

                    <!-- حقل السعر -->
                    <div>
                        <label class="block mb-2 text-sm font-medium">السعر</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-dollar-sign text-gray-400"></i>
                            </div>
                            <input type="number" name="price" placeholder="السعر" value="{{ $filters['price'] ?? '' }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-2.5">
                        </div>
                    </div>

                    <!-- حقل الوزن -->
                    <div>
                        <label class="block mb-2 text-sm font-medium">الوزن</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-weight text-gray-400"></i>
                            </div>
                            <input type="number" name="weight" placeholder="الوزن" value="{{ $filters['weight'] ?? '' }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-2.5">
                        </div>
                    </div>

                    <!-- التصنيفات -->
                    <div>
                        <label for="category" class="block mb-2 text-sm font-medium">التصنيفات</label>
                        <select name="category" id="category"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                            <option value="">---</option>
                            <option value="noCategory">بدون فئة</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if (isset($filters['category']) && $category->id == $filters['category']) selected @endif>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- العلامات التجارية -->
                    <div>
                        <label for="brand" class="block mb-2 text-sm font-medium">العلامات التجارية</label>
                        <select name="brand" id="brand"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                            <option value="">---</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @if (isset($filters['brand']) && $brand->id == $filters['brand']) selected @endif>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- تواريخ سجلات الباركود -->
                    <div>
                        <label class="block mb-2 text-sm font-medium">من تاريخ (سجلات الباركود)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-calendar-day text-gray-400"></i>
                            </div>
                            <input type="date" name="barcode_date_from" value="{{ $filters['barcode_date_from'] ?? '' }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">إلى تاريخ (سجلات الباركود)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-calendar-day text-gray-400"></i>
                            </div>
                            <input type="date" name="barcode_date_to" value="{{ $filters['barcode_date_to'] ?? '' }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-2.5">
                        </div>
                    </div>

                    <!-- خيارات سريعة لسجلات الباركود -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium">خيارات سريعة (سجلات الباركود)</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="setBarcodeDateFilter('today')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <i class="fas fa-calendar-day ml-2"></i> اليوم
                            </button>
                            <button type="button" onclick="setBarcodeDateFilter('yesterday')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <i class="fas fa-calendar-minus ml-2"></i> البارحة
                            </button>
                            <button type="button" onclick="setBarcodeDateFilter('week')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <i class="fas fa-calendar-week ml-2"></i> آخر أسبوع
                            </button>
                            <button type="button" onclick="setBarcodeDateFilter('month')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <i class="fas fa-calendar-alt ml-2"></i> آخر شهر
                            </button>
                            <button type="button" onclick="setBarcodeDateFilter('this_month')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <i class="fas fa-calendar ml-2"></i> هذا الشهر
                            </button>
                            <button type="button" onclick="clearBarcodeDateFilter()"
                                class="quick-filter-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                                <i class="fas fa-times ml-2"></i> مسح التواريخ
                            </button>
                        </div>
                    </div>

                    <!-- التبديلات -->
                    <div class="toggles flex justify-start items-center gap-6 md:col-span-4">
                        <!-- الترتيب الأبجدي -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="alphabetical" name="alphabetical"
                                value="{{ !empty($filters['alphabetical']) ? '1' : '0' }}"
                                {{ !empty($filters['alphabetical']) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="alphabetical_div relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600">
                            </div>
                            <span class="ms-3 text-sm font-medium text-white">ترتيب أبجدي</span>
                        </label>

                        <!-- المنتجات ذات الصور -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="have_image" name="have_image"
                                value="{{ !empty($filters['have_image']) ? '1' : '0' }}"
                                {{ !empty($filters['have_image']) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="have_image_div relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600">
                            </div>
                            <span class="ms-3 text-sm font-medium text-white">منتجات لديها صور فقط</span>
                        </label>

                        <!-- المنتجات بدون صور -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="no_image" name="no_image"
                                value="{{ !empty($filters['no_image']) ? '1' : '0' }}"
                                {{ !empty($filters['no_image']) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="no_image_div relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600">
                            </div>
                            <span class="ms-3 text-sm font-medium text-white">منتجات ليس لديها صور فقط</span>
                        </label>
                    </div>

                    <!-- زر تطبيق الفلترة -->
                    <div class="max-w-2xl flex justify-start items-end col-span-4">
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2.5 px-4 rounded-lg flex items-center justify-center gap-2">
                            <i class="fas fa-filter ml-2"></i>
                            تطبيق الفلترة
                        </button>
                    </div>
                </form>
            </div>

            <!-- شريط الأدوات -->
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-list ml-2"></i>
                    قائمة المنتجات
                </h2>

                <div class="flex items-center space-x-4 gap-2">
                    <!-- زر التحديث التلقائي -->
                    <button id="autoRefreshToggle"
                        class="auto-refresh-btn bg-gray-500 hover:bg-yellow-600 text-white font-medium py-1.5 px-4 rounded-lg flex items-center gap-2">
                        <i class="fas fa-play ml-2"></i>
                        <span id="autoRefreshText">تشغيل التحديث</span>
                    </button>

                    <!-- إنشاء تصميم -->
                    <button data-modal-target="catalogModal" data-modal-toggle="catalogModal"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg" type="button">
                        إعدادات التصميم
                    </button>

                    <!-- زر تصدير JSON -->
                    <div class="relative inline-block text-left">
                        <button id="exportJsonDropdownBtn" type="button"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-1.5 px-4 rounded-lg inline-flex items-center gap-2">
                            <i class="fas fa-download ml-2"></i>
                            تصدير JSON
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- القائمة المنسدلة -->
                        <div id="exportJsonMenu" class="hidden absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                            <ul class="py-2 text-sm text-gray-700">
                                <li>
                                    <a href="#" onclick="exportJson('basic')" class="block px-4 py-2 hover:bg-gray-100">📋 تصدير JSON أساسي (للمنتجات)</a>
                                </li>
                                <li>
                                    <a href="#" onclick="exportJson('ai')" class="block px-4 py-2 hover:bg-gray-100">🤖 تصدير JSON تسويقي (للذكاء الاصطناعي)</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- عداد المنتجات -->
                    <span
                        class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full flex items-center gap-2">
                        <i class="fas fa-boxes ml-2"></i>
                        <span id="products-count">{{ $products->total() }}</span> منتج
                    </span>
                </div>
            </div>

            <!-- جدول المنتجات -->
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">
                                <input type="checkbox" name="check-all-page-items" id="check-all-page-items"
                                    class="border border-gray-400 rounded" />
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">الباركود</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">الصورة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">اسم المنتج</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">الوزن</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">السعر</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">العملة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">الفئة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">العلامة التجارية</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">تاريخ الإضافة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">الإجراءات</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body">
                        @if (count($products) > 0)
                            @include('products.partials.products-table', [
                                'products' => $products,
                                'categories' => $categories,
                            ])
                        @else
                            <tr>
                                <td colspan="11" class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-500 text-lg">لا توجد منتجات</p>
                                        <p class="text-gray-400 text-sm">
                                            لم يتم العثور على أي منتجات تطابق معايير البحث
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========== قسم إضافة المنتجات ========== -->
        <div class="nav-item add-products table-container bg-white rounded-lg" style="display: none;">
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-list ml-2"></i>
                    إضافة منتجات
                </h2>
            </div>

            <div class="relative overflow-x-auto">
                <table id="new-products-table" class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-center">#</th>
                            <th class="px-6 py-4 text-center">التاريخ</th>
                            <th class="px-6 py-4 text-center">الباركود</th>
                            <th class="px-6 py-4 text-center">اسم المنتج</th>
                            <th class="px-6 py-4 text-center">الوزن</th>
                            <th class="px-6 py-4 text-center">السعر</th>
                            <th class="px-6 py-4 text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody id="new-products-body"></tbody>
                </table>

                <!-- أزرار التحكم -->
                <div class="my-8 flex gap-2">
                    <button type="button" id="add-row" class="bg-green-500 text-white px-4 py-2 rounded">
                        + إضافة سطر
                    </button>
                    <button type="button" id="fetch-missing" class="bg-yellow-500 text-white px-4 py-2 rounded">
                        🟡 جلب المنتجات غير الموجودة
                    </button>
                    <!-- زر رفع ملف Excel لفواتير اليوم -->
                    <form class="importTodayInvoicesForm hidden" action="{{ route('products.importTodayInvoices') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="files[]" class="importTodayInvoicesInput" accept=".csv,.xlsx"
                            multiple required>
                    </form>
                    <button onclick="importTodayInvoices()"
                        class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                        أضافة فواتير اليوم (Excel)
                    </button>
                    <!-- زر رفع ملف Excel -->
                    <form class="importFileForm hidden" action="{{ route('products.import') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" class="importFileInput" accept=".csv,.xlsx" required>
                    </form>
                    <button onclick="importFile()"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                        رفع منتجات (Excel)
                    </button>
                    <button type="button" id="save-all" class="bg-blue-500 text-white px-4 py-2 rounded">
                        💾 حفظ الجميع
                    </button>
                </div>
            </div>
        </div>

        <!-- ========== قسم صور المنتجات ========== -->
        <div class="nav-item products-images table-container bg-white rounded-lg" style="display: none;">
            <!-- رفع الصور -->
            <form id="previewForm" class="my-6" enctype="multipart/form-data">
                <div class="flex items-center justify-center w-full p-4">
                    <label for="dropzone-file"
                        class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 m-4">
                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                            </svg>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">انقر للرفع</span> أو اسحب وأفلت
                            </p>
                            <p class="text-xs text-gray-500">SVG, PNG, JPG أو GIF (الحد الأقصى: 800x400px)</p>
                        </div>
                        <input type="file" name="images[]" id="dropzone-file" class="hidden" multiple />
                    </label>
                </div>
            </form>

            <!-- زر تنظيف الصور -->
            <button id="cleanImages" class="m-4 bg-red-600 text-white px-4 py-2 rounded">
                تنظيف الصور غير المرتبطة 🔥
            </button>

            <!-- جدول الصور -->
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">#</th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <input type="checkbox" class="border border-gray-400 rounded" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">الاسم</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">الصورة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-base">الإجراءات</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="previewTable"></tbody>
                </table>
            </div>

            <!-- زر حفظ الصور -->
            <button id="saveImages" class="hidden m-4 bg-green-600 text-white px-4 py-2 rounded">
                حفظ
            </button>
        </div>
    </div>

    <!-- Main modal -->
    <div id="catalogModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-black/30 transition-all duration-300">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white backdrop-blur-md border border-default rounded-2xl shadow-2xl p-6 md:p-7 transition-all">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-5">
                    <h3 class="text-xl font-semibold text-heading tracking-tight">
                        إعدادات التصميم
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary/70 hover:text-heading rounded-full text-sm w-10 h-10 inline-flex justify-center items-center transition-all duration-200 hover:scale-110"
                        data-modal-hide="catalogModal">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">إغلاق</span>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="pt-5 md:pt-7 space-y-5">
                    <!-- Row: عدد المنتجات + نوع التصميم -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="design_products_count" class="block mb-2 text-sm font-semibold text-heading">
                                عدد المنتجات في التصميم
                            </label>
                            <input type="number" id="design_products_count"
                                class="bg-neutral-secondary-medium border-2 border-default-medium text-heading text-sm rounded-xl focus:ring-2 focus:ring-brand/60 focus:border-brand block w-full px-4 py-3 shadow-sm placeholder:text-body transition-all duration-200"
                                placeholder="0" value="4" min="1" max="6" required />
                        </div>
                        <div>
                            <label for="design_type" class="block mb-2 text-sm font-semibold text-heading">
                                نوع التصميم
                            </label>
                            <select id="design_type" name="design_type"
                                class="block w-full px-4 py-3 bg-neutral-secondary-medium border-2 border-default-medium text-heading text-sm rounded-xl focus:ring-2 focus:ring-brand/60 focus:border-brand shadow-sm transition-all duration-200">
                                <option value="post">Post</option>
                                <option value="reels" selected>Reels</option>
                                <option value="green_screen_1">Green Screen 1</option>
                                <option value="green_screen_2">Green Screen 2</option>
                            </select>
                        </div>
                    </div>

                    <!-- أزرار التصميم السريع -->
                    <div class="flex flex-wrap justify-end items-center gap-3 pt-2">
                        <button type="button" onclick="showCatalog()"
                            class="bg-yellow-400 text-white px-5 py-2.5 rounded-xl font-medium shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                            🎨 إنشاء من المنتجات المختارة
                        </button>
                    </div>

                    <!-- قسم المجموعات المخصصة (تصميم عصري) -->
                    <div class="border-t border-default-medium pt-5">
                        <div class="flex items-center justify-between mb-4">
                            <label class="text-sm font-semibold text-heading">
                                ✨ مجموعات مخصصة
                            </label>
                            <button type="button" id="add-group-btn"
                                class="bg-neutral-secondary-medium hover:bg-neutral-tertiary text-heading text-xs px-3 py-1.5 rounded-lg border border-default-medium flex items-center gap-1 transition-all duration-200 hover:shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                إضافة مجموعة
                            </button>
                        </div>

                        <!-- حاوية المجموعات -->
                        <div id="groups-container" class="space-y-3">
                            <!-- يتم بناء المجموعات هنا عبر JS -->
                        </div>

                        <p class="mt-3 text-xs text-body/70">
                            أدخل معرفات المنتجات مفصولة بفواصل لكل مجموعة.
                        </p>

                        <button type="button" onclick="showCustomCatalog()"
                            class="mt-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 w-full">
                            📋 إنشاء التصاميم المخصصة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {

            $('input[name="alphabetical"]').on('change', function() {
                if (this.checked) {
                    $('.alphabetical_div').addClass('bg-yellow-500');
                } else {
                    $('.alphabetical_div').removeClass('bg-yellow-500');
                }
                $(this).val(this.checked ? '1' : '0');
            });

            $('input[name="have_image"]').on('change', function() {
                if (this.checked) {
                    $('.have_image_div').addClass('bg-yellow-500');
                } else {
                    $('.have_image_div').removeClass('bg-yellow-500');
                }
                $(this).val(this.checked ? '1' : '0');
            });

            $('input[name="no_image"]').on('change', function() {
                if (this.checked) {
                    $('.no_image_div').addClass('bg-yellow-500');
                } else {
                    $('.no_image_div').removeClass('bg-yellow-500');
                }
                $(this).val(this.checked ? '1' : '0');
            });

            let previewData = []; // نخزن بيانات المعاينة

            $('#dropzone-file').on('change', function() {
                $('#previewForm').submit();
            });

            // رفع للمعاينة
            $('#previewForm').on('submit', function(e) {
                $('#loadingOverlay').css('display', 'flex');
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('products.preview.images') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        previewData = data;
                        let table = $('#previewTable');
                        table.html("");
                        data.forEach((item, index) => {
                            let rowIndex = $('#previewTable tr').length;
                            table.append(`
                    <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200" data-id="${item.id ?? '-'}">
                        <td class="px-6 py-4">
                            <div class="flex justify-center items-center" data-field="id">
                                ${rowIndex + 1}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center items-center" data-field="id">
                                <input type="checkbox" name="" id="" class="border border-gray-400 rounded" />
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-center" data-field="name">
                                ${item.name}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center items-center" data-field="image">
                                <img src="${item.image}" class="w-20 h-20 rounded object-contain" />
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center items-center">
                                <button type="button" class="delete-row text-red-600 hover:text-red-800" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
                        });
                        $('#saveImages').removeClass('hidden');
                        $('#loadingOverlay').hide();
                    }
                });
            });

            // حذف صف من المعاينة
            $(document).on('click', '.delete-row', function() {
                let index = $(this).data('index');
                previewData.splice(index, 1);
                $(this).closest('tr').remove();
            });

            // حفظ نهائي
            $('#saveImages').on('click', function() {
                $('#loadingOverlay').css('display', 'flex');
                $.ajax({
                    url: "{{ route('products.save.images') }}",
                    type: "POST",
                    data: {
                        items: previewData,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        let table = $('#previewTable');
                        table.html("");
                        showToast('تم الحفظ بنجاح ✅');
                        $('#loadingOverlay').hide();
                    }
                });
            });

            $('.nav-btn').on('click', function() {
                // إزالة التنسيقات من الأزرار
                $('.nav-btn').removeClass('text-yellow-400 border-yellow-400 active')
                    .addClass('border-transparent');

                // إضافة تنسيق للزر النشط
                $(this).addClass('text-yellow-400 border-yellow-400 active')
                    .removeClass('border-transparent');

                // إخفاء كل العناصر
                $('.nav-item').fadeOut(200);

                // إظهار العنصر المطلوب
                let target = $(this).data('target');
                $(target).fadeIn(200);
            });
        });

        // تعريف المتغيرات العالمية
        let autoRefreshEnabled = false;
        let autoRefreshInterval = null;
        let productIds = new Set();
        let lastUpdateTime = new Date().getTime();

        // تهيئة معرفات المنتجات الحالية
        @foreach ($products as $product)
            productIds.add({{ $product->id }});
        @endforeach

        function toggleUserInteraction(disable) {
            if (disable) {
                // تعطيل الحقول القابلة للتعديل
                $('.editable-field')
                    .attr('contenteditable', 'false')
                    .addClass('opacity-50 cursor-not-allowed');

                // تعطيل الفلاتر
                $('#filter-form :input').prop('disabled', true);

                // تعطيل أزرار حذف المنتج
                $('button.delete-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');

            } else {
                // إعادة التمكين
                $('.editable-field')
                    .attr('contenteditable', 'true')
                    .removeClass('opacity-50 cursor-not-allowed');

                $('#filter-form :input').prop('disabled', false);
                $('button.delete-btn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
            }
        }

        // دوال خاصة بتواريخ سجلات الباركود
        function setBarcodeDateFilter(range) {
            const today = new Date();
            let fromDate, toDate;

            switch (range) {
                case 'today':
                    fromDate = today.toISOString().split('T')[0];
                    toDate = fromDate;
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    fromDate = yesterday.toISOString().split('T')[0];
                    toDate = fromDate;
                    break;
                case 'week':
                    const weekAgo = new Date(today);
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    fromDate = weekAgo.toISOString().split('T')[0];
                    toDate = today.toISOString().split('T')[0];
                    break;
                case 'month':
                    const monthAgo = new Date(today);
                    monthAgo.setDate(monthAgo.getDate() - 30);
                    fromDate = monthAgo.toISOString().split('T')[0];
                    toDate = today.toISOString().split('T')[0];
                    break;
                case 'this_month':
                    fromDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    toDate = today.toISOString().split('T')[0];
                    break;
            }

            $("input[name='barcode_date_from']").val(fromDate);
            $("input[name='barcode_date_to']").val(toDate);

            // تطبيق الفلترة تلقائياً
            applyFilters();
        }

        function clearBarcodeDateFilter() {
            $("input[name='barcode_date_from']").val('');
            $("input[name='barcode_date_to']").val('');

            // تطبيق الفلترة تلقائياً
            applyFilters();
        }

        // تعريف الدالة في النطاق العام
        window.applyFilters = function(isAutoRefresh) {
            // إظهار مؤشر التحميل فقط إذا لم يكن طلباً تلقائياً
            if (!isAutoRefresh) {
                // toggleUserInteraction(!isAutoRefresh);
                $('#loadingOverlay').css('display', 'flex');
            }

            let data = {
                have_image: $("input[name='have_image']").val(),
                no_image: $("input[name='no_image']").val(),
                alphabetical: $("input[name='alphabetical']").val(),
                barcode: $("input[name='barcode']").val(),
                name: $("input[name='name']").val(),
                price: $("input[name='price']").val(),
                weight: $("input[name='weight']").val(),
                category: $("select[name='category']").val(),
                brand: $("select[name='brand']").val(),
                date_from: $("input[name='date_from']").val(),
                date_to: $("input[name='date_to']").val(),
                barcode_date_from: $("input[name='barcode_date_from']").val(),
                barcode_date_to: $("input[name='barcode_date_to']").val(),
                page: {{ $products->currentPage() }},
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: "{{ route('products.filter') }}",
                type: "GET",
                data: data,
                success: function(response) {
                    // حفظ عدد المنتجات الحالي قبل التحديث
                    const currentCount = $("#products-count").text();

                    // تحديث الجدول
                    $("#products-table-body").html(response);

                    // حساب عدد المنتجات بشكل صحيح
                    let tempDiv = $('<div>').html(response);
                    let newCount = tempDiv.find('tr[data-id]').length;
                    $("#products-count").text(newCount);

                    // التحقق من وجود منتجات جديدة في حالة التحديث التلقائي
                    if (isAutoRefresh && autoRefreshEnabled) {
                        checkForNewProducts(response);
                    }

                    // إعادة تهيئة الحقول القابلة للتعديل
                    initEditableFields();

                    // تحديث وقت آخر تحديث
                    lastUpdateTime = new Date().getTime();

                    // إخفاء مؤشر التحميل
                    $('#loadingOverlay').hide();
                },
                error: function(xhr, status, error) {
                    // إخفاء مؤشر التحميل في حالة الخطأ
                    $('#loadingOverlay').hide();
                    console.log('حدث خطأ أثناء جلب البيانات:', error);

                    if (isAutoRefresh) {
                        showToast('فشل في التحديث التلقائي', 'error');
                    }
                }
            });
        };

        // التحقق من وجود منتجات جديدة
        function checkForNewProducts(response) {
            const tempDiv = $('<div>').html(response);
            const currentIds = new Set();
            let newProductsCount = 0;

            // جمع معرفات المنتجات الحالية
            tempDiv.find('tr[data-id]').each(function() {
                const productId = $(this).data('id');
                currentIds.add(productId);

                // إذا كان المنتج غير موجود في المجموعة السابقة، فهو منتج جديد
                if (!productIds.has(productId)) {
                    newProductsCount++;
                    // تمييز المنتج الجديد
                    $(this).addClass('bg-green-50');
                    $(this).find('td:first').prepend('<span class="new-product-indicator">!</span>');
                }
            });

            // إذا كان هناك منتجات جديدة، عرض إشعار
            if (newProductsCount > 0) {
                showNewProductsNotification(newProductsCount);

                // تحديث زر التحديث التلقائي للإشارة إلى وجود تحديثات جديدة
                $('#autoRefreshToggle').addClass('bg-green-500');
                setTimeout(() => {
                    $('#autoRefreshToggle').removeClass('bg-green-500');
                }, 2000);
            }

            // تحديث مجموعة معرفات المنتجات
            productIds = currentIds;
        }

        // عرض إشعار بوجود منتجات جديدة
        function showNewProductsNotification(count) {
            const message = count === 1 ? 'تمت إضافة منتج جديد' : `تمت إضافة ${count} منتجات جديدة`;

            Toastify({
                text: message,
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10B981",
                stopOnFocus: true,
                onClick: function() {
                    // عند النقر على الإشعار، التمرير إلى أعلى الجدول
                    $('html, body').animate({
                        scrollTop: $('.table-container').offset().top
                    }, 500);
                }
            }).showToast();
        }

        // === دوال خاصة بتعديل الفئة ===

        // تهيئة محرر الفئة
        function initCategoryEditor() {
            // عند النقر على زر تعديل الفئة
            $(document).on('click', '.edit-category-btn', function(e) {
                e.stopPropagation();
                const container = $(this).closest('.category-editable-container');
                switchToCategoryEditMode(container);
            });

            // عند النقر على حقل العرض
            $(document).on('click', '.category-editable-container .display-mode', function(e) {
                if (!$(e.target).closest('.edit-category-btn').length) {
                    const container = $(this).closest('.category-editable-container');
                    switchToCategoryEditMode(container);
                }
            });

            // عند النقر على زر الحفظ
            $(document).on('click', '.save-category-btn', function() {
                const container = $(this).closest('.category-editable-container');
                saveCategoryChange(container);
            });

            // عند النقر على زر الإلغاء
            $(document).on('click', '.cancel-edit-btn', function() {
                const container = $(this).closest('.category-editable-container');
                cancelCategoryEditMode(container);
            });

            // عند الضغط على Enter أو Escape في اختيار الفئة
            $(document).on('keydown', '.category-select', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const container = $(this).closest('.category-editable-container');
                    saveCategoryChange(container);
                }
                if (e.key === 'Escape') {
                    const container = $(this).closest('.category-editable-container');
                    cancelCategoryEditMode(container);
                }
            });
        }

        // التحويل إلى وضع التحرير للفئة
        function switchToCategoryEditMode(container) {
            const displayMode = container.find('.display-mode');
            const editMode = container.find('.edit-mode');
            const select = container.find('.category-select');

            // حفظ القيمة الأصلية إذا لم تكن محفوظة
            if (!select.data('original-category')) {
                select.data('original-category', select.val());
            }

            // التبديل بين الوضعين
            displayMode.addClass('hidden');
            editMode.removeClass('hidden');

            // التركيز على الـ select
            select.focus();
        }

        // حفظ تغيير الفئة
        function saveCategoryChange(container) {
            const productId = container.data('product-id');
            const select = container.find('.category-select');
            const categoryId = select.val();
            const originalCategoryId = select.data('original-category');
            const categoryName = select.find('option:selected').text();

            // إذا لم يتغيرت القيمة
            if (categoryId === originalCategoryId) {
                cancelCategoryEditMode(container);
                return;
            }

            // إظهار حالة التحميل
            const saveBtn = container.find('.save-category-btn');
            const originalBtnHtml = saveBtn.html();
            saveBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري الحفظ');
            saveBtn.prop('disabled', true);

            const updateCategoryRoute = "{{ route('products.update-category', ':productId') }}";
            const url = updateCategoryRoute.replace(':productId', productId);

            // إرسال طلب AJAX
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    category_id: categoryId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // تحديث عرض الفئة
                        container.find('.category-name').text(categoryName || 'بدون فئة');

                        // تحديث القيمة الأصلية
                        select.data('original-category', categoryId);

                        // العودة لوضع العرض
                        cancelCategoryEditMode(container);

                        // إظهار رسالة نجاح
                        showToast('تم تحديث الفئة بنجاح', 'success');
                    } else {
                        showToast(response.message || 'حدث خطأ أثناء التحديث', 'error');
                        select.val(originalCategoryId);
                    }
                },
                error: function(xhr) {
                    showToast('حدث خطأ أثناء تحديث الفئة', 'error');
                    console.log(xhr.responseText);
                    select.val(originalCategoryId);
                },
                complete: function() {
                    // إعادة زر الحفظ لحالته الأصلية
                    saveBtn.html(originalBtnHtml);
                    saveBtn.prop('disabled', false);
                }
            });
        }

        // إلغاء تحرير الفئة
        function cancelCategoryEditMode(container) {
            const displayMode = container.find('.display-mode');
            const editMode = container.find('.edit-mode');
            const select = container.find('.category-select');

            // إرجاع القيمة الأصلية
            const originalCategoryId = select.data('original-category');
            if (originalCategoryId) {
                select.val(originalCategoryId);
            }

            // العودة لوضع العرض
            editMode.addClass('hidden');
            displayMode.removeClass('hidden');
        }

        // تهيئة الحقول القابلة للتعديل
        function initEditableFields() {
            $('.editable-field').off('blur').on('blur', function() {
                const field = $(this).data('field');
                const value = $(this).text().trim();
                const productId = $(this).closest('tr').data('id');

                updateProductField(productId, field, value);
            });

            initCategoryEditor();
        }

        // تحديث حقل منتج
        function updateProductField(productId, field, value) {
            $.ajax({
                url: `/api/products/${productId}`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    [field]: value,
                    role_id: {{ Auth::user()->role_id }},
                },
                success: function(response) {
                    showToast('تم تحديث المنتج بنجاح', 'success');
                },
                error: function(xhr) {
                    showToast('حدث خطأ أثناء التحديث', 'error');
                    console.log(xhr.responseText);
                }
            });
        }

        // حذف منتج
        function deleteProduct(productId) {
            if (!confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج؟')) {
                return;
            }

            $.ajax({
                url: `/api/products/${productId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showToast('تم حذف المنتج بنجاح', 'success');
                    // إعادة تطبيق الفلاتر لتحديث الجدول
                    applyFilters(false);
                },
                error: function(xhr) {
                    showToast('حدث خطأ أثناء الحذف', 'error');
                    console.log(xhr.responseText);
                }
            });
        }

        // تبديل حالة التحديث التلقائي
        function toggleAutoRefresh() {
            autoRefreshEnabled = !autoRefreshEnabled;

            if (autoRefreshEnabled) {
                $('#autoRefreshToggle').html(
                    '<i class="fas fa-pause ml-2"></i> <span id="autoRefreshText">إيقاف التحديث</span>');
                $('#autoRefreshToggle').removeClass('bg-gray-500').addClass('bg-yellow-500');
                showToast('تم تشغيل التحديث التلقائي', 'success');
            } else {
                $('#autoRefreshToggle').html(
                    '<i class="fas fa-play ml-2"></i> <span id="autoRefreshText">تشغيل التحديث</span>');
                $('#autoRefreshToggle').removeClass('bg-yellow-500').addClass('bg-gray-500');
                showToast('تم إيقاف التحديث التلقائي', 'info');
            }
        }

        // تعيين الفلتر حسب التاريخ
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
                    // من بداية البارحة إلى نهايتها
                    fromDate.setDate(today.getDate() - 1);
                    fromDate.setHours(0, 0, 0, 0);
                    toDate.setDate(today.getDate() - 1);
                    toDate.setHours(23, 59, 59, 999);
                    break;
                case 'week':
                    // من بداية الأسبوع إلى اليوم
                    fromDate.setDate(today.getDate() - 7);
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
            applyFilters(false);
        }

        // مسح فلترة التاريخ
        function clearDateFilter() {
            $("input[name='date_from']").val('');
            $("input[name='date_to']").val('');

            // تطبيق الفلترة تلقائياً
            applyFilters(false);
        }

        function copyTitle(element) {
            const textToCopy = element.getAttribute("title");

            navigator.clipboard.writeText(textToCopy)
                .then(() => {
                    showToast(`✅ تم نسخ النص: ${textToCopy}`, 'success');
                })
                .catch(err => {
                    console.log("حدث خطأ أثناء النسخ:", err);
                });
            // مافي return false حتى الرابط يشتغل عادي
        }

        // عند تحميل الصفحة، نضيف مجموعة افتراضية
        $(document).ready(function() {
            addGroupInput(); // مجموعة أولى فارغة
        });

        // إضافة حقل مجموعة جديد
        $('#add-group-btn').on('click', function() {
            addGroupInput();
        });

        // حذف مجموعة
        $(document).on('click', '.remove-group-btn', function() {
            const container = $('#groups-container');
            if (container.children().length > 1) {
                $(this).closest('.group-item').remove();
            } else {
                showToast("يجب الاحتفاظ بمجموعة واحدة على الأقل", 'warning');
            }
        });

        // دالة إنشاء هيكل المجموعة
        function addGroupInput(value = '') {
            const groupHtml = `
                <div class="group-item flex items-start gap-2 p-3 bg-neutral-secondary-medium/50 border border-default-medium rounded-xl hover:border-brand/40 transition-all duration-200">
                    <input type="text"
                        class="group-ids flex-1 bg-white border-0 text-heading text-sm rounded-lg px-3 py-2.5 focus:ring-1 focus:ring-brand/50 shadow-sm placeholder:text-body/50 transition-all"
                        placeholder="مثال: 8718951207233, 5283004335056"
                        value="${value.replace(/"/g, '&quot;')}" />
                    <button type="button" class="remove-group-btn flex-shrink-0 text-body/60 hover:text-red-500 hover:bg-red-50 rounded-lg p-1.5 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            `;
            $('#groups-container').append(groupHtml);
        }

        function showCatalog() {
            let ids = [];
            $('#products-table-body input[type="checkbox"]:checked').each(function() {
                let tr = $(this).closest('tr');
                if (tr.data('id')) {
                    ids.push(tr.data('id'));
                }
            });

            if (ids.length === 0) {
                showToast("رجاءً اختر منتجات أولاً", 'error');
                return;
            }

            const groups = [];
            const count = parseInt($('#design_products_count').val(), 10);

            if (!count || count <= 0) {
                showToast("قيمة عدد المنتجات غير صحيحة", 'error');
                return;
            }

            for (let i = 0; i < ids.length; i += count) {
                groups.push(ids.slice(i, i + count));
            }

            groups.forEach((group, index) => {
                setTimeout(() => {
                    sendCatalogRequest(index, group, 'id');
                }, index * 1000);
            });
        }

        function showCustomCatalog() {
            const groupItems = $('#groups-container .group-item');
            const groups = [];

            groupItems.each(function() {
                const rawVal = $(this).find('.group-ids').val().trim();
                if (rawVal) {
                    const ids = rawVal
                        .split(',')
                        .map(id => parseInt(id.trim()))
                        .filter(id => !isNaN(id));
                    if (ids.length > 0) {
                        groups.push(ids);
                    }
                }
            });

            if (groups.length === 0) {
                showToast("أدخل معرفات في مجموعة واحدة على الأقل", 'error');
                return;
            }

            console.log('المجموعات المخصصة:', groups);

            groups.forEach((group, index) => {
                setTimeout(() => {
                    sendCatalogRequest(index, group, 'barcode');
                }, index * 1000);
            });
        }

        // دالة الإرسال (كما هي)
        function sendCatalogRequest(index, ids, field = 'id') {
            let form = $('<form>', {
                method: 'GET',
                action: "{{ route('showCatalog') }}",
                target: '_blank'
            });

            form.append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: "{{ csrf_token() }}"
            }));

            form.append($('<input>', {
                type: 'hidden',
                name: 'ids',
                value: JSON.stringify(ids)
            }));

            form.append($('<input>', {
                type: 'hidden',
                name: 'design_type',
                value: $('#design_type').val()
            }));

            form.append($('<input>', {
                type: 'hidden',
                name: 'index',
                value: index + 1
            }));

            form.append($('<input>', {
                type: 'hidden',
                name: 'search_field',
                value: field
            }));

            $(document.body).append(form);
            form.submit();
            form.remove();
        }

        function addRow(barcode = '', added_at = '', id = "") {
            let rowIndex = $('#new-products-body tr').length;
            let rowHtml = `
                <tr class="border-b hover:bg-gray-50">
                    <td id="rowIndex${rowIndex}" class="rowIndex px-4 py-3 text-center font-medium text-gray-700">${rowIndex + 1}</td>
                    <td class="px-4 py-3 flex justify-center items-center gap-4">
                        <button type="button" class="remove-row text-red-600 hover:bg-red-200 px-3 py-1 rounded-lg transition">
                            x
                        </button>
                        ${added_at}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col">
                            <input type="text" name="products[${rowIndex}][barcode]" data-row="${rowIndex}" class="barcode-input w-40 border rounded-lg px-3 py-2" value="${barcode}" required>
                            <span class="barcode-error text-center text-red-500 text-xs mt-1 hidden">⚠️ الباركود موجود مسبقاً</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <input type="text" name="products[${rowIndex}][name]" class="w-72 border rounded-lg px-3 py-2" placeholder="أدخل اسم المنتج" required>
                    </td>
                    <td class="px-4 py-3">
                        <input type="text" name="products[${rowIndex}][weight]" class="w-32 border rounded-lg px-3 py-2" placeholder="الوزن">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" step="0.01" name="products[${rowIndex}][price]" class="w-32 border rounded-lg px-3 py-2" placeholder="السعر" required>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="delete-row-new-products bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-lg transition" data-id="${id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
            </tr>`;
            $('#new-products-body').append(rowHtml);
        }

        function importFile() {
            $('.importFileInput').trigger('click');
        }

        // دالة رفع ملفات Excel باستخدام jQuery فقط
        function importTodayInvoices() {
            const $input = $('.importTodayInvoicesInput');

            // إعادة تعيين قيمة الملف
            $input.val('');
            $input.trigger('click');

            $input.off('change').on('change', function(event) {
                const files = event.target.files;

                if (files.length === 0) {
                    showToast('لم يتم اختيار أي ملفات', 'error');
                    return;
                }

                showToast('جاري رفع الملفات...', 'info');

                // إنشاء FormData
                const formData = new FormData();

                for (let i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }

                // الحصول على CSRF token
                const csrfToken = "{{ csrf_token() }}";

                // إرسال الطلب باستخدام $.ajax التقليدي
                $.ajax({
                    url: '{{ route('products.importTodayInvoices') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        showToast(response.message || 'تم رفع الملفات بنجاح', 'success');
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        if (xhr.responseJSON) {
                            const data = xhr.responseJSON;
                            const errorMessage = data.message || 'حدث خطأ أثناء رفع الملفات';
                            showToast(errorMessage, 'error');
                            if (data.errors) {
                                $.each(data.errors, function(index, errorMsg) {
                                    showToast(errorMsg, 'error');
                                });
                            }
                        } else {
                            showToast('حدث خطأ في الاتصال بالخادم', 'error');
                        }
                    }
                });
            });
        }

        // ---- دوال تصدير JSON ----
        // فتح/إغلاق القائمة المنسدلة
        $('#exportJsonDropdownBtn').on('click', function(e) {
            e.stopPropagation();
            $('#exportJsonMenu').toggleClass('hidden');
        });

        // إغلاق القائمة عند النقر خارجها
        $(document).on('click', function() {
            $('#exportJsonMenu').addClass('hidden');
        });

        // منع إغلاق القائمة عند النقر داخلها
        $('#exportJsonMenu').on('click', function(e) {
            e.stopPropagation();
        });

        function exportJson(type) {
            $('#exportJsonMenu').addClass('hidden');

            // جمع كل قيم الفلاتر الحالية من النموذج
            let data = {
                barcode: $("input[name='barcode']").val(),
                name: $("input[name='name']").val(),
                price: $("input[name='price']").val(),
                weight: $("input[name='weight']").val(),
                category: $("select[name='category']").val(),
                brand: $("select[name='brand']").val(),
                have_image: $("input[name='have_image']").val(),
                no_image: $("input[name='no_image']").val(),
                date_from: $("input[name='date_from']").val(),
                date_to: $("input[name='date_to']").val(),
                barcode_date_from: $("input[name='barcode_date_from']").val(),
                barcode_date_to: $("input[name='barcode_date_to']").val(),
                alphabetical: $("input[name='alphabetical']").val(),
                _token: '{{ csrf_token() }}'
            };

            // تحديد الرابط حسب النوع
            let url = type === 'ai'
                ? "{{ route('products.jsonFilters') }}"
                : "{{ route('products.filter') }}"; // أو أي مسار آخر مثل json-products

            // إظهار اللودينق
            $('#loadingOverlay').css('display', 'flex');

            $.get(url, data)
                .done(function(response) {
                    // تحويل JSON إلى string منسق
                    const jsonString = JSON.stringify(response, null, 2);
                    // إنشاء blob للتحميل
                    const blob = new Blob([jsonString], {type: 'application/json'});
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    // تسمية الملف حسب النوع والتاريخ
                    const dateStr = new Date().toISOString().slice(0,10);
                    const fileName = type === 'ai'
                        ? `marketing_products_${dateStr}.json`
                        : `products_${dateStr}.json`;
                    link.download = fileName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(link.href);
                    showToast('تم تحميل ملف JSON بنجاح', 'success');
                })
                .fail(function(xhr) {
                    showToast('حدث خطأ أثناء تصدير البيانات', 'error');
                })
                .always(function() {
                    $('#loadingOverlay').hide();
                });
        }

        $(document).ready(function() {

            $('.importFileInput').on('change', function() {
                if (this.files && this.files.length > 0) {
                    $('.importFileForm').submit();
                }
            });

            // $('.importTodayInvoicesInput').on('change', function() {
            //     if (this.files && this.files.length > 0) {
            //         $('.importTodayInvoicesForm').submit();
            //     }
            // });

            // حدث تحديد/إلغاء تحديد الكل
            $('#check-all-page-items').change(function() {
                const isChecked = $(this).prop('checked');
                $('table input[type="checkbox"]').prop('checked', isChecked);
            });

            // حدث عند تغيير أي checkbox فردي
            $('table input[type="checkbox"]').not('#check-all-page-items').change(function() {
                const allChecked = $('table input[type="checkbox"]').not('#check-all-page-items').length ===
                    $('table input[type="checkbox"]').not('#check-all-page-items').filter(':checked')
                    .length;
                $('#check-all-page-items').prop('checked', allChecked);
            });

            $(document).on('click', '.delete-row-new-products', function() {
                const button = $(this);
                const id = button.data('id');

                if (!confirm('هل أنت متأكد من حذف هذا السجل؟')) return;

                $.ajax({
                    url: '{{ route('product.destroyMissing', ':id') }}'.replace(":id", id),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            button.closest('tr').remove();
                        } else {
                            showToast(response.message || 'حدث خطأ أثناء الحذف', 'showToast');
                        }
                    },
                    error: function(err) {
                        showToast('تعذر الاتصال بالسيرفر', 'error');
                        console.log(err);
                    }
                });
            });

            // ✅ إضافة سطر جديد
            $("#add-row").on("click", function() {
                addRow();
            });

            // ✅ حذف سطر
            $(document).on("click", ".remove-row", function() {
                $(this).closest("tr").remove();
            });

            $('#fetch-missing').click(function() {
                $.ajax({
                    url: '{{ route('products.getMissingProducts') }}',
                    method: 'GET',
                    success: function(response) {
                        showToast('تم جلب المنتجات المفقودة', 'success');
                        response.forEach(barcode => {
                            // تحقق إذا الباركود موجود بالفعل
                            if ($('#new-products-table tbody tr').filter(function() {
                                    return $(this).find('.barcode-input').val() ==
                                        barcode['barcode'];
                                }).length === 0) {
                                addRow(barcode['barcode'], barcode['added_at'], barcode[
                                    'id']);
                            }
                        });
                    },
                    error: function(err) {
                        showToast('حدث خطأ أثناء جلب المنتجات.', 'error');
                        console.log(err);
                    }
                });
            });

            // التحقق من تكرار الباركود أثناء الإدخال
            $(document).on("change", ".barcode-input", function() {
                let input = $(this);
                let barcode = input.val();
                let errorSpan = input.siblings(".barcode-error");

                let row = parseInt(input.attr("data-row"));
                let nextRow = row + 1;

                addRow(); // إضافة صف جديد

                if (barcode.trim() !== "") {
                    let barcodeRoute = "{{ route('products.findByBarcode', ':barcode') }}";
                    let url = barcodeRoute.replace(":barcode", barcode);

                    $.ajax({
                        url: url,
                        type: "GET",

                        success: function(response) {

                            // تعبئة بيانات الصف الحالي
                            input.val(response['barcode']);

                            $(`input[name="products[${row}][name]"]`).val(response['name'] ??
                                "");
                            $(`input[name="products[${row}][price]"]`).val(response['price'] ??
                                "");
                            $(`input[name="products[${row}][weight]"]`).val(response[
                                'weight'] ?? "");

                            // اظهار رسالة الخطأ
                            errorSpan.removeClass("hidden").text("⚠️ الباركود موجود مسبقاً");

                            // وضع المؤشر على الصف التالي
                            $(`input[name="products[${nextRow}][barcode]"]`).focus();
                        },

                        error: function(xhr) {
                            if (xhr.status === 404) {
                                // الباركود غير موجود → اخفاء الخطأ
                                errorSpan.addClass("hidden");
                            }

                            // انتقال إلى الباركود في الصف التالي
                            $(`input[name="products[${nextRow}][barcode]"]`).focus();
                        }
                    });
                }
            });

            // ✅ حفظ الجميع
            $("#save-all").on("click", function() {
                let formData = {};
                $("#new-products-body tr").each(function(i, row) {
                    $(row).find("input").each(function() {
                        formData[$(this).attr("name")] = $(this).val();
                    });
                });

                $.ajax({
                    url: "{{ route('products.bulkStore') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        showToast("✅ تم الحفظ بنجاح (" + res.count + " منتج)", "success");
                        $("#new-products-body").empty(); // تفريغ الجدول
                        rowIndex = 0;
                        $("#add-row").click(); // أول سطر فارغ
                    },
                    error: function(xhr) {
                        showToast("❌ حدث خطأ أثناء الحفظ", "error");
                        console.log(xhr.responseText);
                    }
                });
            });

            // تهيئة الحقول القابلة للتعديل عند تحميل الصفحة
            initEditableFields();

            // فلترة أثناء الكتابة
            $(".filter-input").on("keyup change", function() {
                applyFilters(false);
            });

            // منع إعادة تحميل الصفحة عند submit
            $("#filter-form").on("submit", function(e) {
                e.preventDefault();
                applyFilters(false);
            });

            // إعداد التحديث التلقائي كل 5 ثوان
            autoRefreshInterval = setInterval(() => {
                if (autoRefreshEnabled) {
                    applyFilters(true);
                }
            }, 5000);

            // إعداد حدث النقر على زر التحديث التلقائي
            $('#autoRefreshToggle').click(toggleAutoRefresh);

            $('#cleanImages').on('click', function() {
                if (!confirm('هل أنت متأكد أنك تريد حذف الصور غير المرتبطة؟')) return;

                $.ajax({
                    url: '{{ route('products.cleanUnused') }}',
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            showToast(`تم حذف ${data.count} صورة غير ضرورية بنجاح ✅`,'success');
                        } else {
                            showToast('حدث خطأ أثناء تنظيف الصور ❌','error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        showToast('فشل الاتصال بالسيرفر ❌','error');
                    }
                });
            });
        });
    </script>
@endpush
