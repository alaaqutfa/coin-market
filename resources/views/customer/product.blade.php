@extends('layout.customer.app')

@section('title', 'Home')

@push('css')
    <style>
        :root {
            --primary: #ECC631;
            --secondary: #333127;
            --text: #222222;
            --bg: #f0f0f0;
        }

        .filter-section {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            background: var(--secondary);
            border-radius: 12px;
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
    <div class="container mx-auto py-8">
        <!-- بطاقة الفلترة -->
        <div dir="rtl" class="filter-section p-6 mb-8 text-white">
            <h2 class="text-xl font-semibold mb-4 flex justify-start items-center gap-2">
                <i class="fas fa-filter ml-2"></i>
                <span>تصفية المنتجات</span>
            </h2>

            <form id="filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

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
                        <input type="number" name="price" step="0.01" placeholder="السعر"
                            value="{{ $filters['price'] ?? '' }}"
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

                <div>
                    <label for="category" class="block mb-2 text-sm font-medium">العلامات التجارية</label>
                    <select name="category" id="category">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="brand" class="block mb-2 text-sm font-medium">العلامات التجارية</label>
                    <select name="brand" id="brand">
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <!-- زر التصفية -->
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2.5 px-4 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-filter ml-2"></i>
                        تطبيق الفلترة
                    </button>
                </div>
            </form>

        </div>
        <div class="product-container w-full flex justify-evenly items-start gap-4 flex-wrap">
            @include('customer.partials.product-item', ['products' => $products])
        </div>
    </div>
@endsection

@push('script')
    <script>
        // تعريف الدالة في النطاق العام
        window.applyFilters = function() {
            $('#loadingOverlay').css('display', 'flex');
            let data = {
                name: $("input[name='name']").val(),
                price: $("input[name='price']").val(),
                weight: $("input[name='weight']").val(),
                brand: $("select[name='brand']").val(),
                category: $("select[name='category']").val(),
                page: {{ $products->currentPage() }},
                _token: '{{ csrf_token() }}'
            };
            $.ajax({
                url: "{{ route('customer.filter') }}",
                type: "GET",
                data: data,
                success: function(response) {
                    $(".product-container").html('');
                    $(".product-container").html(response);
                    // إخفاء مؤشر التحميل
                    $('#loadingOverlay').hide();
                },
                error: function(xhr, status, error) {
                    console.log('حدث خطأ أثناء جلب البيانات:', error);
                }
            });
        };

        // منع إعادة تحميل الصفحة عند submit
        $("#filter-form").on("submit", function(e) {
            e.preventDefault();
            applyFilters(false);
        });
    </script>
@endpush
