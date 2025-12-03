@extends('layout.customer.app')

@section('title', 'إضافة عملية بيع/إرجاع')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">

        <!-- رسائل التنبيه -->
        @if (session('success'))
            <div id="successAlert"
                class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle ml-3 text-green-600"></i>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button onclick="closeAlert('successAlert')" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <i class="fas fa-times text-green-600"></i>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div id="errorAlert" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative"
                role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle ml-3 text-red-600"></i>
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button onclick="closeAlert('errorAlert')" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <i class="fas fa-times text-red-600"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة عملية جديدة</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">أضف عملية بيع أو مرتجع للمنتجات</p>
                </div>
                <a href="{{ route('meat-inventory.daily-sales.report') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-list ml-2"></i>
                    عرض التقرير
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- النموذج الرئيسي -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">
                                <i class="fas fa-plus-circle ml-2"></i>
                                بيانات العملية
                            </h2>
                        </div>

                        <form action="{{ route('meat-inventory.daily-sales.store') }}" method="POST" class="p-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- حقل المنتج -->
                                <div>
                                    <label for="meat_product_id"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        المنتج *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-box text-gray-500"></i>
                                        </div>
                                        <select
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            id="meat_product_id" name="meat_product_id" required>
                                            <option value="">اختر المنتج</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->selling_price }}"
                                                    data-barcode="{{ $product->barcode }}">
                                                    {{ $product->name }} - {{ $product->barcode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">يمكنك استخدام الباركود للبحث
                                        السريع</p>
                                </div>

                                <!-- حقل نوع العملية -->
                                <div>
                                    <label for="transaction_type"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        نوع العملية *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-exchange-alt text-gray-500"></i>
                                        </div>
                                        <select
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            id="transaction_type" name="transaction_type" required>
                                            <option value="sale">بيع</option>
                                            <option value="return">مرتجع</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- حقل التاريخ -->
                                <div>
                                    <label for="sale_date"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        تاريخ العملية *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-calendar-alt text-gray-500"></i>
                                        </div>
                                        <input type="date"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            id="sale_date" name="sale_date" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <!-- حقل السعر -->
                                <div>
                                    <label for="sale_price"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        سعر الكيلو ($) *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-money-bill-wave text-gray-500"></i>
                                        </div>
                                        <input type="number" step="0.01" min="0"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            id="sale_price" name="sale_price" placeholder="0.00" required>
                                    </div>
                                </div>

                                <!-- حقل الكمية -->
                                <div>
                                    <label for="quantity"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        الكمية (كجم) *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-weight text-gray-500"></i>
                                        </div>
                                        <input type="number" step="0.001" min="0.001"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            id="quantity" name="quantity" placeholder="0.000" required>
                                    </div>
                                </div>

                                <!-- حقل المبلغ الإجمالي -->
                                <div>
                                    <label for="total_amount"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        المبلغ الإجمالي
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-calculator text-gray-500"></i>
                                        </div>
                                        <input type="text"
                                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pr-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-not-allowed"
                                            id="total_amount" readonly value="0.00 $">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">محسوب تلقائياً</p>
                                </div>
                            </div>

                            <!-- حقل الملاحظات -->
                            <div class="mt-6">
                                <label for="notes"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    ملاحظات
                                </label>
                                <textarea id="notes" name="notes" rows="3"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="أضف ملاحظات حول العملية..."></textarea>
                            </div>

                            <!-- أزرار الإرسال -->
                            <div class="mt-8 flex flex-wrap gap-3">
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    <i class="fas fa-save ml-2"></i>
                                    حفظ العملية
                                </button>

                                <button type="reset" onclick="resetForm()"
                                    class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                    <i class="fas fa-redo ml-2"></i>
                                    إعادة تعيين
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- الجانب الأيمن -->
                <div class="space-y-6">
                    <!-- معلومات المنتج المختار -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-green-600 to-green-800 px-6 py-4">
                            <h2 class="text-lg font-bold text-white">
                                <i class="fas fa-info-circle ml-2"></i>
                                معلومات المنتج
                            </h2>
                        </div>
                        <div class="p-6" id="product-info">
                            <div class="text-center py-8">
                                <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">اختر منتجاً لعرض معلوماته</p>
                            </div>
                            <div class="hidden" id="product-details">
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2"
                                        id="product-name"></h3>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-barcode text-blue-500 ml-2"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">باركود:</span>
                                            <span class="mr-auto font-medium text-gray-900 dark:text-white"
                                                id="product-barcode"></span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-tag text-green-500 ml-2"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">السعر:</span>
                                            <span class="mr-auto font-medium text-gray-900 dark:text-white"
                                                id="product-price"></span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-weight text-purple-500 ml-2"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">المخزون:</span>
                                            <span class="mr-auto font-medium text-gray-900 dark:text-white"
                                                id="product-stock"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- آلة حاسبة سريعة -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-800 px-6 py-4">
                            <h2 class="text-lg font-bold text-white">
                                <i class="fas fa-calculator ml-2"></i>
                                حاسبة سريعة
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        الكمية (كجم)
                                    </label>
                                    <input type="number" step="0.001" min="0.001"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                        id="calc_quantity" placeholder="0.000">
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        السعر ($/كجم)
                                    </label>
                                    <input type="number" step="0.01" min="0"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                        id="calc_price" placeholder="0.00">
                                </div>

                                <button onclick="calculateTotal()"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 focus:outline-none dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800">
                                    <i class="fas fa-calculator ml-2"></i>
                                    احسب الإجمالي
                                </button>

                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">المبلغ الإجمالي</p>
                                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1"
                                            id="calc_result">0.00 $</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تعليمات سريعة -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-3">
                            <i class="fas fa-lightbulb ml-2"></i>
                            نصائح سريعة
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-barcode text-blue-500 mt-1 ml-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">استخدم الباركود للبحث السريع عن
                                    المنتجات</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-calculator text-green-500 mt-1 ml-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">استخدم الحاسبة السريعة لحساب
                                    الإجمالي</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-exchange-alt text-red-500 mt-1 ml-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">اختر نوع العملية (بيع/مرتجع) قبل
                                    الحفظ</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                background-color: #f9fafb;
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                height: 42px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #374151;
                line-height: 42px;
                padding-right: 20px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 40px;
            }

            .select2-container--default.select2-container--focus .select2-selection--single {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .dark .select2-container--default .select2-selection--single {
                background-color: #374151;
                border-color: #4b5563;
            }

            .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #f3f4f6;
            }
        </style>
    @endpush

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // تفعيل Select2
                $('#meat_product_id').select2({
                    placeholder: "اختر المنتج",
                    allowClear: true,
                    language: {
                        noResults: function() {
                            return "لا توجد نتائج";
                        }
                    }
                });

                // تحديث معلومات المنتج عند الاختيار
                $('#meat_product_id').on('change', function() {
                    var selected = $(this).find(':selected');
                    var price = selected.data('price');
                    var barcode = selected.data('barcode');
                    var name = selected.text().split(' - ')[0];

                    if (price) {
                        $('#sale_price').val(price);

                        // عرض معلومات المنتج
                        document.getElementById('product-details').classList.remove('hidden');
                        document.querySelector('#product-info .text-center').classList.add('hidden');

                        document.getElementById('product-name').textContent = name;
                        document.getElementById('product-barcode').textContent = barcode;
                        document.getElementById('product-price').textContent = price + ' $/كجم';

                        // إضافة معلومات المخزون لو كانت متوفرة
                        $.ajax({
                            url: "{{ route('meat-inventory.daily-sales.products.get-stock') }}",
                            method: 'GET',
                            data: {
                                product_id: selected.val()
                            },
                            success: function(response) {
                                if (response.success) {
                                    document.getElementById('product-stock').textContent =
                                        response.stock + ' كجم';
                                }
                            }
                        });

                        // حساب الإجمالي إذا كانت هناك كمية
                        calculateFormTotal();
                    } else {
                        document.getElementById('product-details').classList.add('hidden');
                        document.querySelector('#product-info .text-center').classList.remove('hidden');
                    }
                });

                // حساب الإجمالي عند تغيير السعر أو الكمية
                $('#sale_price, #quantity').on('input', function() {
                    calculateFormTotal();
                });

                // تفعيل المسح الضوئي للباركود
                startBarcodeScanner();
            });

            // وظيفة حساب الإجمالي للنموذج
            function calculateFormTotal() {
                var quantity = parseFloat($('#quantity').val()) || 0;
                var price = parseFloat($('#sale_price').val()) || 0;
                var total = quantity * price;

                $('#total_amount').val(total.toFixed(2) + ' $');
            }

            // وظيفة الحاسبة السريعة
            function calculateTotal() {
                var quantity = parseFloat($('#calc_quantity').val()) || 0;
                var price = parseFloat($('#calc_price').val()) || 0;
                var total = quantity * price;

                document.getElementById('calc_result').textContent = total.toFixed(2) + ' $';

                // تعبئة الحقول تلقائياً
                $('#quantity').val(quantity);
                $('#sale_price').val(price);
                calculateFormTotal();

                // إضافة تأثير
                const calcResult = document.getElementById('calc_result');
                calcResult.classList.add('animate-pulse');
                setTimeout(() => {
                    calcResult.classList.remove('animate-pulse');
                }, 500);
            }

            // إعادة تعيين النموذج
            function resetForm() {
                $('#meat_product_id').val('').trigger('change');
                $('#sale_price').val('');
                $('#quantity').val('');
                $('#total_amount').val('0.00 $');
                $('#notes').val('');

                // إعادة عرض رسالة اختيار المنتج
                document.getElementById('product-details').classList.add('hidden');
                document.querySelector('#product-info .text-center').classList.remove('hidden');

                // عرض رسالة نجاح
                showToast('تم إعادة تعيين النموذج بنجاح', 'success');
            }

            // دعم المسح الضوئي للباركود
            function startBarcodeScanner() {
                let barcode = '';
                let lastTime = Date.now();

                document.addEventListener('keypress', function(e) {
                    const currentTime = Date.now();

                    if (currentTime - lastTime > 100) {
                        barcode = '';
                    }

                    lastTime = currentTime;

                    // قبول الأرقام فقط
                    if (e.key >= '0' && e.key <= '9') {
                        barcode += e.key;
                    }

                    // إنتر للبحث
                    if (e.key === 'Enter' && barcode.length >= 3) {
                        e.preventDefault();
                        searchByBarcode(barcode);
                        barcode = '';
                    }
                });
            }

            // وظيفة لعرض التنبيهات
            function showToast(message, type = 'info') {
                const toastId = 'toast-' + Date.now();
                const bgColor = type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' :
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';

                const icon = type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' :
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

                const toastHTML = `
                <div id="${toastId}" class="fixed bottom-4 left-4 z-50 animate-slide-in">
                    <div class="flex items-center w-full max-w-xs p-4 mb-4 text-white ${bgColor} rounded-lg shadow dark:bg-gray-800 dark:text-white" role="alert">
                        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                            <i class="fas ${icon}"></i>
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

                // إزالة التنبيه تلقائياً بعد 5 ثواني
                setTimeout(() => {
                    const toast = document.getElementById(toastId);
                    if (toast) toast.remove();
                }, 5000);
            }

            // إضافة رسالة تحقق قبل الإرسال
            document.querySelector('form').addEventListener('submit', function(e) {
                const quantity = parseFloat($('#quantity').val()) || 0;
                const price = parseFloat($('#sale_price').val()) || 0;
                const product = $('#meat_product_id').val();

                if (!product) {
                    e.preventDefault();
                    showToast('الرجاء اختيار منتج', 'error');
                    return;
                }

                if (quantity <= 0) {
                    e.preventDefault();
                    showToast('الرجاء إدخال كمية صحيحة', 'error');
                    return;
                }

                if (price <= 0) {
                    e.preventDefault();
                    showToast('الرجاء إدخال سعر صحيح', 'error');
                    return;
                }

                // تأكيد الحفظ
                if (!confirm('هل أنت متأكد من حفظ العملية؟')) {
                    e.preventDefault();
                }
            });
        </script>
    @endpush

@endsection
