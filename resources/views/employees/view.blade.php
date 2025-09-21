@extends('layout.app')

@section('title', 'إدارة الموظفين')

@push('css')
<style>
    .editable-field {
        text-align: center;
    }
</style>
@endpush

@section('content')

    <div dir="rtl" class="container mx-auto px-4 py-8">
        <div class="nav-item products-list table-container bg-white rounded-lg">
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex justify-center items-center gap-2">
                    <i class="fas fa-list ml-2"></i>
                    قائمة الموظفين
                </h2>
                <div class="flex items-center space-x-4 gap-2">
                    <a href="{{ route('employees.create') }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                        أضافة موظف
                    </a>

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
                                    <span class="text-base">الرقم الوظيفي</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الأسم</span>
                                </div>
                            </th>
                            {{-- <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الراتب</span>
                                </div>
                            </th> --}}
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">البريد الإلكتروني</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">رقم الهاتف</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">تاريخ البدء بالعمل</span>
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
                        @if (count($employees) > 0)
                            @include('employees.partials.employees-table', ['employees' => $employees])
                        @else
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-500 text-lg">لا يوجد موظفين بعد</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();

                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        showToast(response.message);
                        console.log(response.employee);
                        form.trigger("reset");
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // أخطاء التحقق من Laravel
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = '';
                            $.each(errors, function(key, value) {
                                errorMessages += value[0] + "\n";
                            });
                            showToast(errorMessages, 'error');
                        } else {
                            showToast("حدث خطأ غير متوقع. حاول لاحقاً.", 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
