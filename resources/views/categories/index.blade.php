{{-- @dd($categories) --}}
@extends('layout.app')

@section('title', 'إدارة الفئات')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">إدارة الفئات</h1>

        <!-- رسائل النجاح والخطأ -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 text-yellow-400 border-b-2 border-yellow-400 rounded-t-lg active"
                        data-target=".products-list">قائمة الفئات</button>
                </li>
            </ul>
        </div>

        <!-- جدول الفئات -->
        <div class="nav-item products-list table-container bg-white rounded-lg">
            <div class="p-4 border-b flex justify-between items-center">
                <div class="flex items-center space-x-4 gap-4">
                    <h2 class="text-xl font-semibold text-gray-800 flex justify-center items-center gap-2">
                        <i class="fas fa-list ml-2"></i>
                        قائمة الفئات
                    </h2>
                    <a href="{{ route('categories.create') }}"
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        إضافة فئة جديدة
                    </a>
                </div>
                <div class="flex items-center space-x-4 gap-2">
                    <span
                        class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full flex justify-center items-center gap-2">
                        <i class="fas fa-boxes ml-2"></i>
                        <span id="products-count">{{ $categories->total() }}</span> فئة
                    </span>
                </div>
            </div>

            <div class="relative overflow-x-auto">
                @if(count($categories) > 0)
                <form id="delete-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                </form>
                @endif

                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">
                                <input type="checkbox" name="check-all-page-items" id="check-all-page-items"
                                    class="border border-gray-400 rounded" />
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الأسم</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">عدد المنتجات</span>
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
                        @if (count($categories) > 0)
                            @foreach ($categories as $category)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="check-item" class="border border-gray-400 rounded item-checkbox"
                                               data-id="{{ $category->id }}" />
                                    </td>
                                    <td class="px-6 py-4 font-medium text-center text-gray-900 whitespace-nowrap">
                                        {{ $category->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ $category->products_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center items-center gap-4">
                                            <a href="{{ route('categories.edit', $category->id) }}"
                                                class="text-blue-600 hover:underline flex justify-center items-center gap-2">
                                                <i class="fas fa-edit"></i>
                                                تعديل
                                            </a>
                                            <button type="button"
                                                    onclick="confirmDelete('{{ route('categories.destroy', $category->id) }}')"
                                                    class="text-red-600 hover:underline flex justify-center items-center gap-2">
                                                <i class="fas fa-trash-alt"></i>
                                                حذف
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4" class="px-6 py-4">
                                    {{ $categories->links() }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-500 text-lg">لا توجد فئات</p>
                                        <p class="text-gray-400 text-sm">
                                            لم يتم العثور على أي فئات
                                        </p>
                                        <a href="{{ route('categories.create') }}"
                                           class="mt-4 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                            <i class="fas fa-plus"></i>
                                            إضافة فئة جديدة
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // تأكيد الحذف
        function confirmDelete(url) {
            if (confirm('هل أنت متأكد من حذف هذه الفئة؟')) {
                const form = document.getElementById('delete-form');
                form.action = url;
                form.submit();
            }
        }

        // تحديد/إلغاء تحديد جميع العناصر
        document.getElementById('check-all-page-items').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // وظائف التنقل بين التبويبات
        document.querySelectorAll('.nav-btn').forEach(button => {
            button.addEventListener('click', function() {
                // إزالة النشاط من جميع الأزرار
                document.querySelectorAll('.nav-btn').forEach(btn => {
                    btn.classList.remove('active', 'text-yellow-400', 'border-yellow-400');
                    btn.classList.add('text-gray-500', 'hover:text-gray-600', 'hover:border-gray-300');
                });

                // إضافة النشاط للزر الحالي
                this.classList.add('active', 'text-yellow-400', 'border-yellow-400');
                this.classList.remove('text-gray-500', 'hover:text-gray-600', 'hover:border-gray-300');

                // إخفاء جميع العناصر
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.add('hidden');
                });

                // إظهار العنصر المطلوب
                const target = this.getAttribute('data-target');
                document.querySelector(target).classList.remove('hidden');
            });
        });
    </script>
@endsection
