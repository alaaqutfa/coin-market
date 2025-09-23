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

        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 text-yellow-400 border-b-2 border-yellow-400 rounded-t-lg active"
                        data-target=".attendance-log">
                        سجل الحضور
                    </button>
                </li>
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target=".employee-list">
                        قائمة الموظفين
                    </button>
                </li>
                {{-- <li>
                    <a
                        class="inline-block p-4 text-gray-400 rounded-t-lg cursor-not-allowed">Disabled</a>
                </li> --}}
            </ul>
        </div>

        <div class="nav-item attendance-log table-container bg-white rounded-lg">
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 flex justify-center items-center gap-2">
                    <i class="fa-solid fa-gauge ml-2"></i>
                    لوحة تحكم
                </h2>
                <div class="flex items-center space-x-4 gap-2">
                    <button id="today-log" onclick="location.reload();" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                    </button>
                </div>
            </div>

            <div class="quick-calculates p-4 grid grid-cols-4 gap-8">

                <div
                    class="calc-item h-20 cursor-pointer shadow-lg rounded-lg bg-gray-100 p-4 flex justify-start items-center gap-4">

                    <i class="fa-solid fa-users-rectangle text-2xl text-gray-800"></i>

                    <div class="flex flex-col gap-2">
                        <h2 class="text-gray-800 text-xl font-medium">إجمالي عدد الموظفين :</h2>
                        <h1 class="total_employees text-gray-800 text-2xl font-black">...</h1>
                    </div>

                </div>

                <div
                    class="calc-item h-20 cursor-pointer shadow-lg rounded-lg bg-gray-100 p-4 flex justify-start items-center gap-4">

                    <i class="fa-solid fa-address-book text-2xl text-gray-800"></i>

                    <div class="flex flex-col gap-2">
                        <h2 class="text-gray-800 text-xl font-medium">المطلوب حضورهم اليوم :</h2>
                        <h1 class="expected_employees text-gray-800 text-2xl font-black">...</h1>
                    </div>

                </div>

                <div
                    class="calc-item h-20 cursor-pointer shadow-lg rounded-lg bg-gray-100 p-4 flex justify-start items-center gap-4">

                    <i class="fa-solid fa-users text-2xl text-gray-800"></i>

                    <div class="flex flex-col gap-2">
                        <h2 class="text-gray-800 text-xl font-medium">الحاضرين :</h2>
                        <h1 class="present_employees text-gray-800 text-2xl font-black">...</h1>
                    </div>

                </div>

                <div
                    class="calc-item h-20 cursor-pointer shadow-lg rounded-lg bg-gray-100 p-4 flex justify-start items-center gap-4">

                    <i class="fa-solid fa-users-slash text-2xl text-gray-800"></i>

                    <div class="flex flex-col gap-2">
                        <h2 class="text-gray-800 text-xl font-medium">الغائبين :</h2>
                        <h1 class="absent_employees text-gray-800 text-2xl font-black">...</h1>
                    </div>

                </div>

                <div
                    class="calc-item h-20 cursor-pointer shadow-lg rounded-lg bg-gray-100 p-4 flex justify-start items-center gap-4">

                    <i class="fa-solid fa-percent text-2xl text-gray-800"></i>

                    <div class="flex flex-col gap-2">
                        <h2 class="text-gray-800 text-xl font-medium">نسبة الحضور :</h2>
                        <h1 class="attendance_rate text-gray-800 text-2xl font-black">...</h1>
                    </div>

                </div>

                <div
                    class="calc-item h-20 cursor-pointer shadow-lg rounded-lg bg-gray-100 p-4 flex justify-start items-center gap-4">

                    <i class="fa-solid fa-clock text-2xl text-gray-800"></i>

                    <div class="flex flex-col gap-2">
                        <h2 class="text-gray-800 text-xl font-medium">الساعات الفعلية :</h2>
                        <h1 class="total_actual_hours text-gray-800 text-2xl font-black">...</h1>
                    </div>

                </div>

                <div
                    class="calc-item h-20 cursor-pointer shadow-lg rounded-lg bg-gray-100 p-4 flex justify-start items-center gap-4">

                    <i class="fa-solid fa-stopwatch-20 text-2xl text-gray-800"></i>

                    <div class="flex flex-col gap-2">
                        <h2 class="text-gray-800 text-xl font-medium">الساعات المطلوبة :</h2>
                        <h1 class="total_required_hours text-gray-800 text-2xl font-black">...</h1>
                    </div>

                </div>

                <div
                    class="calc-item h-20 cursor-pointer shadow-lg rounded-lg bg-gray-100 p-4 flex justify-start items-center gap-4">

                    <i class="fa-solid fa-hourglass-start text-2xl text-gray-800"></i>

                    <div class="flex flex-col gap-2">
                        <h2 class="text-gray-800 text-xl font-medium">الفرق :</h2>
                        <h1 class="total_hours_difference text-gray-800 text-2xl font-black">...</h1>
                    </div>

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
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">تسجيل دخول</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">تسجيل خروج</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">المدة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الحالة</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الملاحظات</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4">
                                <div class="flex justify-center items-center flex-col gap-2">
                                    <span class="text-base">الأجراءات</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="attendanceToday-table-body"></tbody>
                </table>
            </div>
        </div>

        <div class="nav-item employee-list table-container bg-white rounded-lg" style="display: none;">
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
                                <td colspan="7" class="px-6 py-4 text-center">
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
        function attendanceToday() {
            const data = {};
            data._token = '{{ csrf_token() }}';
            $.ajax({
                url: `{{ route('attendance.today') }}`,
                type: 'GET',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    var attendance_logs = response['attendance_logs'];
                    console.log(attendance_logs);
                    var data = ``;
                    attendance_logs.forEach((log) => {
                        data += `
                            <tr>
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <input type="checkbox" name="" id="" class="border border-gray-400 rounded" />
                                </th>
                                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                    ${log['employee_code']}
                                </th>
                                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                    ${log['employee_name']}
                                </th>
                                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                    ${log['check_in']}
                                </th>
                                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                    ${log['check_out']}
                                </th>
                                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                    ${log['duration']}
                                </th>
                                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                    ${log['status']}
                                </th>
                                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                    ${log['note']}
                                </th>
                                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">

                                </th>
                            </tr>
                        `;
                    });
                    $('#attendanceToday-table-body').html(data);

                },
                error: function(xhr) {
                    showToast('حدث خطأ أثناء التحديث', 'error');
                    console.log('Error:', xhr.responseText);

                    // عرض أخطاء التحقق إن وجدت
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        console.log('Validation errors:', xhr.responseJSON.errors);
                    }
                },
            });
        }

        function dashboardToday() {
            // إنشاء كائن البيانات
            const data = {};
            data._token = '{{ csrf_token() }}';

            $.ajax({
                url: `{{ route('attendance.dashboard.today') }}`,
                type: 'GET',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    var statistics = response['statistics'];
                    $('#today-log').text(`${response['day_name']} ${response['date']}`);
                    $('.total_employees').text(statistics['total_employees']);
                    $('.expected_employees').text(statistics['expected_employees']);
                    $('.present_employees').text(statistics['present_employees']);
                    $('.absent_employees').text(statistics['absent_employees']);
                    $('.attendance_rate').text(statistics['attendance_rate'] + "%");
                    $('.total_actual_hours').text(statistics['total_actual_hours']);
                    $('.total_required_hours').text(statistics['total_required_hours']);
                    $('.total_hours_difference').text(statistics['total_hours_difference']);

                },
                error: function(xhr) {
                    showToast('حدث خطأ أثناء التحديث', 'error');
                    console.log('Error:', xhr.responseText);

                    // عرض أخطاء التحقق إن وجدت
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        console.log('Validation errors:', xhr.responseJSON.errors);
                    }
                }
            });
        }

        function deleteEmployee(employeeId) {
            if (!confirm('هل أنت متأكد من رغبتك في حذف هذا الموظف؟')) {
                return;
            }

            $.ajax({
                url: `/employees/${employeeId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // حذف الصف من الجدول
                    const row = $(`tr[data-id="${employeeId}"]`);
                    row.fadeOut(300, function() {
                        $(this).remove();

                        // التحقق إذا لم يتبقى أي موظفين
                        checkIfNoEmployees();
                    });

                    showToast('تم حذف الموظف بنجاح', 'success');
                },
                error: function(xhr) {
                    showToast('حدث خطأ أثناء الحذف', 'error');
                    console.log(xhr.responseText);
                }
            });
        }

        // دالة للتحقق إذا لم يتبقى أي موظفين
        function checkIfNoEmployees() {
            const employeeRows = $('tr[data-id]');
            if (employeeRows.length === 0) {
                // إضافة صف الرسالة
                $('#products-table-body').append(`
                    <tr id="no-employees-row">
                        <td colspan="8" class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center justify-center py-8">
                                <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-500 text-lg">لا يوجد موظفين بعد</p>
                            </div>
                        </td>
                    </tr>
                `);
            }
        }

        function initEditableFields() {
            $('.editable-field').off('blur').on('blur', function() {
                const field = $(this).data('field');
                const value = $(this).text().trim();
                const employeeId = $(this).closest('tr').data('id');

                updateEmployeeField(employeeId, field, value);
            });
        }

        function updateEmployeeField(employeeId, field, value) {
            // إنشاء كائن البيانات
            const data = {};
            data[field] = value;
            data._token = '{{ csrf_token() }}';

            $.ajax({
                url: `employees/${employeeId}`,
                type: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    showToast('تم تحديث معلومات الموظف بنجاح', 'success');
                },
                error: function(xhr) {
                    showToast('حدث خطأ أثناء التحديث', 'error');
                    console.log('Error:', xhr.responseText);

                    // عرض أخطاء التحقق إن وجدت
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        console.log('Validation errors:', xhr.responseJSON.errors);
                    }
                }
            });
        }

        $(document).ready(function() {

            initEditableFields();

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

            dashboardToday();
            attendanceToday();
        });
    </script>
@endpush
