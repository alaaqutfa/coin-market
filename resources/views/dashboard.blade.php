@extends('layout.app')

@section('title', 'لوحة التحكم')

@push('css')
@endpush

@section('content')

    <div dir="rtl" class="container mx-auto px-4 py-8">

        <div class="attendance-log table-container bg-white rounded-lg">
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

            <div class="quick-calculates p-4 lg:grid grid-cols-4 gap-8">

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
        </div>

    </div>

@endsection


@push('scripts')
    <script>
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

        dashboardToday();
    </script>
@endpush
