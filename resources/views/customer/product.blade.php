@extends('layout.customer.app')

@section('title', 'المتجر الإلكتروني')

@push('css')
<style>
    :root {
        --primary: #ECC631;
        --secondary: #333127;
        --text: #222222;
        --bg: #f0f0f0;
    }

    .filter-section {
        background: linear-gradient(135deg, var(--secondary) 0%, #2a2820 100%);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .category-section {
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(5px);
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .scroll-to-top {
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 50px;
        height: 50px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(236, 198, 49, 0.3);
    }

    .scroll-to-top.show {
        opacity: 1;
        transform: translateY(0);
    }

    .scroll-to-top:hover {
        background: #d8b12a;
        transform: translateY(-3px);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- الفلترة -->
    <div dir="rtl" class="filter-section p-8 mb-12 text-white">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2">المتجر الإلكتروني</h1>
                <p class="text-gray-300">تصفح منتجاتنا المميزة حسب الفئات</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="bg-yellow-500 text-white px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-box ml-2"></i>
                    {{ $categories->sum(fn($cat) => $cat->products->count()) }} منتج
                </span>
            </div>
        </div>

        <form id="filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- البحث بالاسم -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">البحث بالاسم</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="name" placeholder="ابحث عن منتج..."
                        value="{{ $filters['name'] ?? '' }}"
                        class="bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-gray-400 text-sm rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-3">
                </div>
            </div>

            <!-- الفئة -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">الفئة</label>
                <select name="category" id="category"
                    class="bg-white/10 backdrop-blur-sm border border-white/20 text-white text-sm rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 block w-full p-3">
                    <option value="">كل الفئات</option>
                    @foreach ($allCategories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- العلامة التجارية -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">العلامة التجارية</label>
                <select name="brand" id="brand"
                    class="bg-white/10 backdrop-blur-sm border border-white/20 text-white text-sm rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 block w-full p-3">
                    <option value="">كل العلامات</option>
                    @foreach ($allBrands as $brand)
                        <option value="{{ $brand->id }}" @selected(request('brand') == $brand->id)>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- السعر -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">السعر</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-dollar-sign text-gray-400"></i>
                    </div>
                    <input type="number" name="price" step="0.01" placeholder="السعر"
                        value="{{ $filters['price'] ?? '' }}"
                        class="bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-gray-400 text-sm rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-10 p-3">
                </div>
            </div>

            <!-- زر التصفية -->
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                    <i class="fas fa-filter ml-2"></i>
                    تصفية المنتجات
                </button>
            </div>
        </form>
    </div>

    <!-- شريط الفئات السريعة -->
    <div class="mb-8">
        <div class="flex flex-wrap gap-2">
            <a href="{{ url('/') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-yellow-500 hover:text-white text-gray-700 rounded-full text-sm font-medium transition-colors duration-200">
                <i class="fas fa-th-large ml-2"></i>
                الكل
            </a>
            @foreach ($allCategories as $category)
                <a href="?category={{ $category->id }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-yellow-500 hover:text-white text-gray-700 rounded-full text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-folder ml-2"></i>
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- عرض الفئات والمنتجات -->
    <div id="products-container">
        @include('customer.partials.categories-products', ['categories' => $categories])
    </div>

    <!-- زر العودة للأعلى -->
    <div id="scrollToTop" class="scroll-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>
</div>

<!-- نافذة التحميل -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="text-center">
        <div class="spinner mb-4"></div>
        <p class="text-gray-600 font-medium">جاري تحميل البيانات...</p>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // تطبيق الفلاتر
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });

        // عند تغيير أي حقل فلترة
        $('.filter-input').on('input change', function() {
            // يمكنك إضافة debounce هنا إذا أردت
            // applyFilters();
        });
    });

    // دالة تطبيق الفلاتر
    function applyFilters() {
        $('#loadingOverlay').fadeIn();

        const formData = {
            name: $("input[name='name']").val(),
            price: $("input[name='price']").val(),
            weight: $("input[name='weight']").val(),
            brand: $("select[name='brand']").val(),
            category: $("select[name='category']").val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "{{ route('customer.filter') }}",
            type: "GET",
            data: formData,
            success: function(response) {
                $('#products-container').fadeOut(200, function() {
                    $(this).html(response).fadeIn(300);
                    $('#loadingOverlay').fadeOut();
                });
            },
            error: function(xhr, status, error) {
                console.error('حدث خطأ أثناء جلب البيانات:', error);
                $('#loadingOverlay').fadeOut();
                alert('حدث خطأ أثناء جلب البيانات. يرجى المحاولة مرة أخرى.');
            }
        });
    }

    // زر العودة للأعلى
    const scrollToTopBtn = $('#scrollToTop');

    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            scrollToTopBtn.addClass('show');
        } else {
            scrollToTopBtn.removeClass('show');
        }
    });

    scrollToTopBtn.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 500);
    });
</script>
@endpush
