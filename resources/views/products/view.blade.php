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
    </style>
@endpush

@section('content')

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">نظام إدارة المنتجات</h1>
        <p class="text-center text-gray-600 mb-8">قم بتصفية المنتجات حسب المعايير المختلفة</p>

        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 text-yellow-400 border-b-2 border-yellow-400 rounded-t-lg active"
                        data-target=".products-list">قائمة المنتجات</button>
                </li>
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target=".add-products">
                        أضافة منتجات
                    </button>
                </li>
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target=".products-images">
                        صور المنتجات
                    </button>
                </li>
                {{-- <li>
                    <a
                        class="inline-block p-4 text-gray-400 rounded-t-lg cursor-not-allowed">Disabled</a>
                </li> --}}
            </ul>
        </div>

        <!-- جدول المنتجات -->
        <div class="nav-item products-list table-container bg-white rounded-lg">

            <!-- بطاقة الفلترة -->
            <div class="filter-section p-6 mb-8 text-white">
                <h2 class="text-xl font-semibold mb-4 flex justify-start items-center gap-2">
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

                    <!-- نطاق التواريخ -->
                    <div>
                        <label class="block mb-2 text-sm font-medium">من تاريخ</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-calendar-day text-gray-400"></i>
                            </div>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">إلى تاريخ</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-calendar-day text-gray-400"></i>
                            </div>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-2.5">
                        </div>
                    </div>

                    <!-- خيارات تاريخ سريعة -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium">خيارات سريعة</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="setDateFilter('today')"
                                class="quick-filter-btn bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar-day ml-2"></i> اليوم
                            </button>
                            <button type="button" onclick="setDateFilter('yesterday')"
                                class="quick-filter-btn bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar-minus ml-2"></i> البارحة
                            </button>
                            <button type="button" onclick="setDateFilter('week')"
                                class="quick-filter-btn bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar-week ml-2"></i> آخر أسبوع
                            </button>
                            <button type="button" onclick="setDateFilter('month')"
                                class="quick-filter-btn bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar-alt ml-2"></i> آخر شهر
                            </button>
                            <button type="button" onclick="clearDateFilter()"
                                class="quick-filter-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-times ml-2"></i> مسح التواريخ
                            </button>
                        </div>
                    </div>

                    <!-- نطاق تواريخ سجلات الباركود -->
                    <div>
                        <label class="block mb-2 text-sm font-medium">من تاريخ (سجلات الباركود)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-calendar-day text-gray-400"></i>
                            </div>
                            <input type="date" name="barcode_date_from"
                                value="{{ $filters['barcode_date_from'] ?? '' }}"
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

                    <!-- خيارات تاريخ سريعة لسجلات الباركود -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium">خيارات سريعة (سجلات الباركود)</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="setBarcodeDateFilter('today')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar-day ml-2"></i> اليوم
                            </button>
                            <button type="button" onclick="setBarcodeDateFilter('yesterday')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar-minus ml-2"></i> البارحة
                            </button>
                            <button type="button" onclick="setBarcodeDateFilter('week')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar-week ml-2"></i> آخر أسبوع
                            </button>
                            <button type="button" onclick="setBarcodeDateFilter('month')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar-alt ml-2"></i> آخر شهر
                            </button>
                            <button type="button" onclick="setBarcodeDateFilter('this_month')"
                                class="quick-filter-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-calendar ml-2"></i> هذا الشهر
                            </button>
                            <button type="button" onclick="clearBarcodeDateFilter()"
                                class="quick-filter-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm flex justify-center items-center gap-2">
                                <i class="fas fa-times ml-2"></i> مسح التواريخ
                            </button>
                        </div>
                    </div>

                    <!-- زر التصفية -->
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2.5 px-4 rounded-lg flex items-center justify-center gap-2">
                            <i class="fas fa-filter ml-2"></i>
                            تطبيق الفلترة
                        </button>
                    </div>

                    <!-- خيارات الصور -->
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="have_image" name="have_image" value="1"
                            {{ !empty($filters['have_image']) ? 'checked' : '' }} class="sr-only peer">
                        <div
                            class="have_image_div relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-white">منتجات لديها صور فقط</span>
                    </label>

                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="no_image" name="no_image" value="1"
                            {{ !empty($filters['no_image']) ? 'checked' : '' }} class="sr-only peer">
                        <div
                            class="no_image_div relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-white">منتجات ليس لديها صور فقط</span>
                    </label>
                </form>

            </div>

            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex justify-center items-center gap-2">
                    <i class="fas fa-list ml-2"></i>
                    قائمة المنتجات
                </h2>
                <div class="flex items-center space-x-4 gap-2">
                    <button id="autoRefreshToggle"
                        class="auto-refresh-btn bg-gray-500 hover:bg-yellow-600 text-white font-medium py-1.5 px-4 rounded-lg flex justify-center items-center gap-2">
                        <i class="fas fa-play ml-2"></i> <span id="autoRefreshText">تشغيل التحديث</span>
                    </button>
                    <button onclick="showCatalog()"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                        إنشاء تصميم
                    </button>
                    <span
                        class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full flex justify-center items-center gap-2">
                        <i class="fas fa-boxes ml-2"></i>
                        <span id="products-count">{{ $products->total() }}</span> منتج
                    </span>
                </div>
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">
                                <input type="checkbox" name="" id=""
                                    class="border border-gray-400 rounded" />
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الباركود</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الصورة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">اسم المنتج</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">السعر</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الوزن</span>
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
                            @include('products.partials.products-table', ['products' => $products])
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center">
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

        </div>

        <!-- إضافة منتجات -->
        <div class="nav-item add-products table-container bg-white rounded-lg" style="display: none;">

            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex justify-center items-center gap-2">
                    <i class="fas fa-list ml-2"></i>
                    أضافة منتجات
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
                            <th class="px-6 py-4 text-center">السعر</th>
                            <th class="px-6 py-4 text-center">الوزن</th>
                            <th class="px-6 py-4 text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody id="new-products-body"></tbody>
                </table>

                <div class="my-8 flex gap-2">
                    <button type="button" id="add-row" class="bg-green-500 text-white px-4 py-2 rounded">+ إضافة
                        سطر</button>
                    <button type="button" id="fetch-missing" class="bg-yellow-500 text-white px-4 py-2 rounded">🟡 جلب
                        المنتجات غير الموجودة</button>
                    <button type="button" id="save-all" class="bg-blue-500 text-white px-4 py-2 rounded">💾 حفظ
                        الجميع</button>
                </div>
            </div>

        </div>

        <div class="nav-item products-images table-container bg-white rounded-lg" style="display: none;">
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
                            <p class="mb-2 text-sm text-gray-500 "><span class="font-semibold">Click to
                                    upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                        </div>

                        <input type="file" name="images[]" id="dropzone-file" class="hidden" multiple />
                    </label>
                </div>
            </form>
            <button id="cleanImages" class="m-4 bg-red-600 text-white px-4 py-2 rounded">
                تنظيف الصور غير المرتبطة 🔥
            </button>

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">
                                #
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <input type="checkbox" name="" id=""
                                        class="border border-gray-400 rounded" />
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الاسم</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الصورة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الإجراءات</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="previewTable"></tbody>
                </table>
            </div>

            <button id="saveImages" class="hidden m-4 bg-green-600 text-white px-4 py-2 rounded">حفظ</button>
        </div>

    @endsection

    @push('script')
        <script>
            $(function() {

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
                    barcode: $("input[name='barcode']").val(),
                    name: $("input[name='name']").val(),
                    price: $("input[name='price']").val(),
                    weight: $("input[name='weight']").val(),
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

            // تهيئة الحقول القابلة للتعديل
            function initEditableFields() {
                $('.editable-field').off('blur').on('blur', function() {
                    const field = $(this).data('field');
                    const value = $(this).text().trim();
                    const productId = $(this).closest('tr').data('id');

                    updateProductField(productId, field, value);
                });
            }

            // تحديث حقل منتج
            function updateProductField(productId, field, value) {
                $.ajax({
                    url: `/api/products/${productId}`,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        [field]: value
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

                // إنشاء نموذج وإرساله لتحميل الملف
                let form = $('<form>', {
                    method: 'GET',
                    action: "{{ route('showCatalog') }}",
                    target: '_blank'
                });

                // إضافة CSRF token
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: "{{ csrf_token() }}"
                }));

                // إضافة الـ IDs
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'ids',
                    value: JSON.stringify(ids)
                }));

                // إضافة النموذج إلى الصفحة وإرساله
                $(document.body).append(form);
                form.submit();
                form.remove();
            }

            function addRow(barcode = '', added_at = '', id = "") {
                let rowIndex = $('#new-products-body tr').length;
                let rowHtml = `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-center font-medium text-gray-700">${rowIndex + 1}</td>
                        <td class="px-4 py-3 flex justify-center items-center gap-4">
                            <button type="button" class="remove-row text-red-600 hover:bg-red-200 px-3 py-1 rounded-lg transition">
                                x
                            </button>
                            ${added_at}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <input type="text" name="products[${rowIndex}][barcode]" class="barcode-input w-40 border rounded-lg px-3 py-2" value="${barcode}" required>
                                <span class="barcode-error text-center text-red-500 text-xs mt-1 hidden">⚠️ الباركود موجود مسبقاً</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="products[${rowIndex}][name]" class="w-72 border rounded-lg px-3 py-2" placeholder="أدخل اسم المنتج" required>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" step="0.01" name="products[${rowIndex}][price]" class="w-32 border rounded-lg px-3 py-2" placeholder="السعر" required>
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="products[${rowIndex}][weight]" class="w-32 border rounded-lg px-3 py-2" placeholder="الوزن">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" class="delete-row-new-products bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-lg transition" data-id="${id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                </tr>`;
                $('#new-products-body').append(rowHtml);
            }

            $(document).ready(function() {

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
                            console.log(response);
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

                $('#fetch-missing').click();

                // التحقق من تكرار الباركود أثناء الإدخال
                $(document).on("change", ".barcode-input", function() {
                    let input = $(this);
                    let barcode = input.val();
                    let errorSpan = input.siblings(".barcode-error");

                    if (barcode.trim() !== "") {
                        var barcodeRoute = "{{ route('products.findByBarcode', ':barcode') }}";
                        let url = barcodeRoute.replace(':barcode', barcode);
                        $.ajax({
                            url: url,
                            type: "GET",
                            success: function(response) {
                                // إذا رجع منتج يعني الباركود موجود
                                input.val(""); // افرغ الحقل
                                errorSpan.removeClass("hidden").text("⚠️ الباركود موجود مسبقاً");
                            },
                            error: function(xhr) {
                                if (xhr.status === 404) {
                                    // الباركود غير موجود → خبّي رسالة الخطأ
                                    errorSpan.addClass("hidden");
                                }
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
                                alert(`تم حذف ${data.count} صورة غير ضرورية بنجاح ✅`);
                            } else {
                                alert('حدث خطأ أثناء تنظيف الصور ❌');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            alert('فشل الاتصال بالسيرفر ❌');
                        }
                    });
                });
            });
        </script>
    @endpush
