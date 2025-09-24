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
                <li class="me-2">
                    <button type="button"
                        class="nav-btn inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                        data-target=".schedule-work">
                        أوقات العمل
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
                    <button id="today-log" onclick="location.reload();"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
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

        <div class="nav-item schedule-work table-container bg-white rounded-lg" style="display: none;">
            <div class="container mx-auto px-4 py-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="p-4 border-b flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800 flex justify-center items-center gap-2">
                            <i class="fas fa-list ml-2"></i>
                            تحديد جدول الدوام للموظف
                        </h2>
                    </div>

                    <form id="scheduleForm">
                        @csrf

                        <div class="mb-6">
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">اختر
                                الموظف:</label>
                            <select name="employee_id" id="employee_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                                <option value="">-- اختر الموظف --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}
                                        ({{ $employee->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative overflow-x-auto">
                            <table id="schedule-container" class="w-full text-sm text-left rtl:text-right text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">
                                            <input type="checkbox" name="" id=""
                                                class="border border-gray-400 rounded" />
                                        </th>
                                        <th scope="col" class="px-6 py-4">
                                            <div class="flex justify-center items-center flex-col gap-2">
                                                <span class="text-base">اليوم</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4">
                                            <div class="flex justify-center items-center flex-col gap-2">
                                                <span class="text-base">جدول متناوب</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4">
                                            <div class="flex justify-center items-center flex-col gap-2">
                                                <span class="text-base">وقت البدء</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4">
                                            <div class="flex justify-center items-center flex-col gap-2">
                                                <span class="text-base">وقت الانتهاء</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4">
                                            <div class="flex justify-center items-center flex-col gap-2">
                                                <span class="text-base">ساعات العمل</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4">
                                            <div class="flex justify-center items-center flex-col gap-2">
                                                <span class="text-base">إجراءات</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="schedule-rows"></tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button type="button" id="add-row"
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                + إضافة يوم
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                حفظ الجدول
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        const daysOfWeek = [{
                id: 0,
                name: 'الأحد'
            },
            {
                id: 1,
                name: 'الإثنين'
            },
            {
                id: 2,
                name: 'الثلاثاء'
            },
            {
                id: 3,
                name: 'الأربعاء'
            },
            {
                id: 4,
                name: 'الخميس'
            },
            {
                id: 5,
                name: 'الجمعة'
            },
            {
                id: 6,
                name: 'السبت'
            }
        ];

        let rowCount = 0;

        function addScheduleRow() {
            rowCount++;
            const rowHtml = `
                <tr id="row-${rowCount}" class="schedule-row">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    <input type="checkbox" name="" id="" class="border border-gray-400 rounded" />
                </th>
                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                    <select name="schedules[${rowCount}][day_of_week]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        ${daysOfWeek.map(day => `<option value="${day.id}">${day.name}</option>`).join('')}
                    </select>
                </th>
                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <input type="checkbox" name="schedules[${rowCount}][is_alternate]" value="1" class="h-5 w-5 border border-black rounded">
                    </div>
                </th>
                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" name="schedules[${rowCount}][start_time]" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" min="09:00" max="18:00" value="00:00" required />
                    </div>
                </th>
                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" name="schedules[${rowCount}][end_time]" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" min="09:00" max="18:00" value="00:00" required />
                    </div>
                </th>
                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                    <input type="number" step="0.5" min="0" max="24" name="schedules[${rowCount}][work_hours]"
                            class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="8" required>
                </th>
                <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                    <button type="button" class="remove-row bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                حذف
                            </button>
                        </th>
                </tr>
            `;

            $('#schedule-rows').append(rowHtml);

            // إضافة event listener لزر الحذف
            $(`#row-${rowCount} .remove-row`).on('click', function() {
                $(this).closest('.schedule-row').remove();
            });

            // حساب ساعات العمل تلقائياً عند تغيير وقت البدء أو النهاية
            $(`#row-${rowCount} input[name="schedules[${rowCount}][start_time]"],
            #row-${rowCount} input[name="schedules[${rowCount}][end_time]"]`).on('change', function() {
                calculateWorkHours(rowCount);
            });
        }

        function calculateWorkHours(rowId) {
            const startTime = $(`input[name="schedules[${rowId}][start_time]"]`).val();
            const endTime = $(`input[name="schedules[${rowId}][end_time]"]`).val();

            if (startTime && endTime) {
                const start = new Date(`2000-01-01T${startTime}`);
                const end = new Date(`2000-01-01T${endTime}`);
                const diff = (end - start) / (1000 * 60 * 60);

                if (diff > 0) {
                    $(`input[name="schedules[${rowId}][work_hours]"]`).val(diff.toFixed(2));
                }
            }
        }

        function submitScheduleForm() {
            const employeeId = $('#employee_id').val();

            if (!employeeId) {
                alert('يرجى اختيار موظف');
                return;
            }

            if ($('.schedule-row').length === 0) {
                alert('يرجى إضافة أيام للجدول');
                return;
            }

            // جمع البيانات من النموذج
            const formData = new FormData($('#scheduleForm')[0]);

            // إرسال البيانات باستخدام AJAX
            $.ajax({
                url: '{{ route('attendance.update.schedule') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('تم حفظ الجدول بنجاح');
                        console.log('Success:', response);

                        // إعادة تعيين النموذج إذا لزم الأمر
                        // $('#scheduleForm')[0].reset();
                        // $('#schedule-rows').empty();
                        // rowCount = 0;
                        // addScheduleRow();
                    } else {
                        alert('حدث خطأ: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء الحفظ: ' + error);
                }
            });
        }

        // دالة إضافية: تحميل الجدول الحالي للموظف إذا كان موجوداً
        function loadEmployeeSchedule(employeeId) {
            if (!employeeId) return;
            const url = `{{ route('attendance.employee.schedule') }}/${employeeId}`;
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.schedules && response.schedules.length > 0) {
                        // مسح الصفوف الحالية
                        $('#schedule-rows').empty();
                        rowCount = 0;

                        // إضافة الصفوف من البيانات المسترجعة
                        response.schedules.forEach(function(schedule) {
                            addScheduleRow();

                            // تعبئة البيانات
                            $(`select[name="schedules[${rowCount}][day_of_week]"]`).val(schedule
                                .day_of_week);
                            $(`input[name="schedules[${rowCount}][is_alternate]"]`).prop('checked',
                                schedule.is_alternate);
                            $(`input[name="schedules[${rowCount}][start_time]"]`).val(schedule
                                .start_time);
                            $(`input[name="schedules[${rowCount}][end_time]"]`).val(schedule.end_time);
                            $(`input[name="schedules[${rowCount}][work_hours]"]`).val(schedule
                                .work_hours);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading schedule:', error);
                }
            });
        }

        // اختيار موظف مختلف
        $('#employee_id').on('change', function() {
            const employeeId = $(this).val();
            if (employeeId) {
                loadEmployeeSchedule(employeeId);
            }
        });

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
                                    ${log['check_out'] ? log['check_out'] : 'لم يغادر بعد'}
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

            // إضافة صف جديد
            $('#add-row').on('click', addScheduleRow);

            // إرسال النموذج
            $('#scheduleForm').on('submit', function(e) {
                e.preventDefault();
                submitScheduleForm();
            });

            // إضافة أول صف عند تحميل الصفحة
            addScheduleRow();
        });
    </script>
@endpush
