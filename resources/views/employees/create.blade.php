@extends('layout.app')

@section('title', 'إضافة موظف')

@push('css')
@endpush

@section('content')

    <div dir="rtl" class="container mx-auto px-4 py-8">

        <form action="{{ route('employees.store') }}" method="POST" class="max-w-sm mx-auto">

            @csrf

            <!-- الرقم الوظيفي -->
            <label for="employee_code" class="block mb-4 text-sm font-medium text-gray-900 ">الرقم الوظيفي</label>
            <div class="flex">
                <span
                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md">
                    <i class="fa-solid fa-fingerprint text-base text-gray-500"></i>
                </span>
                <input type="number" id="employee_code" name="employee_code"
                    class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-yellow-500 focus:border-yellow-500 block flex-1 min-w-0 w-full text-sm p-2.5"
                    placeholder="الرقم الوظيفي" required />
            </div>

            <!-- اسم الموظف -->
            <label for="name" class="block my-4 text-sm font-medium text-gray-900 ">الأسم الكامل</label>
            <div class="flex">
                <span
                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md">
                    <i class="fa fa-user text-base text-gray-500"></i>
                </span>
                <input type="text" id="name" name="name"
                    class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-yellow-500 focus:border-yellow-500 block flex-1 min-w-0 w-full text-sm p-2.5"
                    placeholder="الأسم الكامل" required />
            </div>

            <!-- الراتب -->
            <label for="salary" class="block my-4 text-sm font-medium text-gray-900 ">الراتب</label>
            <div class="flex">
                <span
                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md">
                    <i class="fa-solid fa-money-check-dollar text-base text-gray-500"></i>
                </span>
                <input type="number" id="salary" name="salary"
                    class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-yellow-500 focus:border-yellow-500 block flex-1 min-w-0 w-full text-sm p-2.5"
                    placeholder="الراتب" required />
            </div>

            <!-- البريد الإلكتروني -->
            <label for="email" class="block my-4 text-sm font-medium text-gray-900 ">البريد الإلكتروني</label>
            <div class="flex">
                <span
                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md">
                    <i class="fa-solid fa-envelope text-base text-gray-500"></i>
                </span>
                <input type="email" id="email" name="email"
                    class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-yellow-500 focus:border-yellow-500 block flex-1 min-w-0 w-full text-sm p-2.5"
                    placeholder="البريد الإلكتروني">
            </div>

            <!-- رقم الهاتف -->
            <label for="phone" class="block my-4 text-sm font-medium text-gray-900 ">رقم الهاتف</label>
            <div class="flex">
                <span
                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md">
                    <i class="fa-solid fa-phone text-base text-gray-500"></i>
                </span>
                <input type="phone" id="phone" name="phone"
                    class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-yellow-500 focus:border-yellow-500 block flex-1 min-w-0 w-full text-sm p-2.5"
                    placeholder="رقم الهاتف">
            </div>

            <!-- تاريخ البدء بالعمل -->
            <label for="start_date" class="block my-4 text-sm font-medium text-gray-900 ">تاريخ البدء بالعمل</label>
            <div class="flex">
                <span
                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md">
                    <i class="fa-solid fa-calendar text-base text-gray-500"></i>
                </span>
                <input type="date" id="start_date" name="start_date"
                    class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-yellow-500 focus:border-yellow-500 block flex-1 min-w-0 w-full text-sm p-2.5"
                    placeholder="تاريخ البدء بالعمل ">
            </div>

            <!-- كلمة السر -->
            <label for="password" class="block my-4 text-sm font-medium text-gray-900 ">كلمة السر</label>
            <div class="flex">
                <span
                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md">
                    <i class="fa-solid fa-calendar text-base text-gray-500"></i>
                </span>
                <input type="password" id="password" name="password"
                    class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-yellow-500 focus:border-yellow-500 block flex-1 min-w-0 w-full text-sm p-2.5"
                    placeholder="كلمة السر" required />
            </div>

            <button type="submit "
                class="text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 my-4 text-center">
                أضافة موظف
            </button>
        </form>

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
