@extends('layout.customer.app')

@section('title', 'ملخص الموظفين - المدير')

@push('css')
    <style>
        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .attendance-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
        }

        select {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        select:focus {
            outline: none;
            border-color: #f59e0b;
            ring: 2px;
            ring-color: #fef3c7;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-white">
                    <h1 class="text-3xl font-bold mb-2">لوحة المدير - ملخص الموظفين</h1>
                    <p class="text-yellow-100 text-lg">عرض التقارير الشهرية للموظفين</p>
                    <p class="text-yellow-100 mt-2">📅
                        {{ $data['current_month'] ?? now('Asia/Beirut')->translatedFormat('F Y') }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4 mt-4 md:mt-0">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Employee Selection -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-yellow-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-800">اختر الموظف</h2>
            </div>

            <form method="GET" action="{{ route('employee.all') }}" id="employeeForm" class="space-y-4">
                <!-- اختيار الموظف -->
                <div class="relative">
                    <select name="employee_id" id="employeeSelect" class="employee-select"
                        onchange="document.getElementById('employeeForm').submit()">
                        <option value="">-- اختر موظف --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                {{ $selectedEmployee && $selectedEmployee->id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_code }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-3 top-8 transform -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <!-- اختيار الشهر -->
                <div class="relative">
                    <select name="month" id="monthSelect" onchange="document.getElementById('employeeForm').submit()">
                        <option value="">-- اختر الشهر --</option>
                        @php
                            $months = [
                                1 => 'يناير',
                                2 => 'فبراير',
                                3 => 'مارس',
                                4 => 'أبريل',
                                5 => 'مايو',
                                6 => 'يونيو',
                                7 => 'يوليو',
                                8 => 'أغسطس',
                                9 => 'سبتمبر',
                                10 => 'أكتوبر',
                                11 => 'نوفمبر',
                                12 => 'ديسمبر',
                            ];
                        @endphp
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                {{ $num }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- اختيار السنة -->
                <div class="relative">
                    <select name="year" id="yearSelect" onchange="document.getElementById('employeeForm').submit()">
                        <option value="">-- اختر السنة --</option>
                        @php
                            $currentYear = \Carbon\Carbon::now()->year;
                            $years = range($currentYear - 1, $currentYear + 5); // خمس سنوات للخلف وسنة مستقبلية
                        @endphp
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

        </div>

        @if ($selectedEmployee && $data)
            <!-- Employee Data Section -->
            <div id="employeeData">
                <!-- Header Section for Selected Employee -->
                <div class="bg-gradient-to-r from-blue-400 to-blue-500 rounded-2xl shadow-lg p-6 mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="text-white">
                            <h1 class="text-3xl font-bold mb-2">{{ $data['employee']['name'] }}</h1>
                            <p class="text-blue-100 text-lg">كود الموظف: {{ $data['employee']['employee_code'] }}</p>
                            <p class="text-blue-100 mt-2">📅 {{ $data['current_month'] }}</p>
                        </div>
                        <div class="bg-white rounded-2xl p-4 mt-4 md:mt-0">
                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate($data['employee']['employee_code']) !!}
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Attendance Days -->
                    <div class="stat-card bg-white rounded-xl shadow-md border-l-4 border-yellow-400 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">أيام الحضور</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $data['summary']['attendance_days'] }}
                                </p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Absent Days -->
                    <div class="stat-card bg-white rounded-xl shadow-md border-l-4 border-red-400 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">أيام الغياب</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $data['summary']['absent_days'] }}</p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Hours -->
                    <div class="stat-card bg-white rounded-xl shadow-md border-l-4 border-green-400 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">الساعات المنجزة</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2">
                                    {{ $data['summary']['total_actual_hours'] }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Required Hours -->
                    <div class="stat-card bg-white rounded-xl shadow-md border-l-4 border-blue-400 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">الساعات المطلوبة</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2">
                                    {{ $data['summary']['total_required_hours'] }}</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Achievement Rate -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">معدل الإنجاز</h2>
                        <span
                            class="text-2xl font-bold text-yellow-600">{{ $data['summary']['achievement_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="max-w-full bg-gradient-to-r from-yellow-400 to-yellow-500 h-4 rounded-full"
                            style="width: {{ $data['summary']['achievement_rate'] }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Absent Dates -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-red-500 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h2 class="text-xl font-bold text-gray-800">أيام الغياب</h2>
                        </div>

                        @if ($data['absent_dates']->count() > 0)
                            <div class="space-y-3">
                                @foreach ($data['absent_dates'] as $absent)
                                    <div
                                        class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-200">
                                        <span class="text-red-700 font-medium">{{ $absent['formatted_date'] }}</span>
                                        <span class="text-red-600 text-sm">{{ $absent['day_name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-600 text-lg">لا توجد أيام غياب لهذا الشهر 🎉</p>
                            </div>
                        @endif
                    </div>

                    <!-- Daily Records -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-yellow-500 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            <h2 class="text-xl font-bold text-gray-800">السجل اليومي</h2>
                        </div>

                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @foreach ($data['daily_records'] as $record)
                                @dd($record)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="flex items-center">
                                            <span
                                                class="font-bold text-gray-800 ml-2">{{ $record['formatted_date'] }}</span>
                                            <span class="text-sm text-gray-600">{{ $record['day_name'] }}</span>
                                        </div>
                                        <span
                                            class="attendance-badge rounded-full px-3 py-1 text-xs font-medium
                                            @if ($record['status'] == 'حاضر') bg-green-100 text-green-800
                                            @elseif($record['status'] == 'غائب') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $record['status'] }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div class="text-center">
                                            <p class="text-gray-600">المطلوب</p>
                                            <p class="font-bold text-gray-800">{{ $record['required_hours'] }} س</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-600">المنجز</p>
                                            <p class="font-bold text-gray-800">{{ $record['actual_hours'] }} س</p>
                                        </div>
                                    </div>

                                    @if (isset($record['attendance_logs']) && $record['attendance_logs']->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            @foreach ($record['attendance_logs'] as $log)
                                                <div class="flex justify-between items-center text-xs text-gray-600 mb-1">
                                                    <span>دخول: {{ $log['check_in'] }}</span>
                                                    <span>خروج: {{ $log['check_out'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Period Info -->
                <div class="mt-8 text-center text-gray-600">
                    <p>فترة التقرير: من {{ \Carbon\Carbon::parse($data['period']['start_date'])->format('d/m/Y') }} إلى
                        {{ \Carbon\Carbon::parse($data['period']['end_date'])->format('d/m/Y') }}</p>
                </div>
            </div>
        @elseif($selectedEmployee)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
                <svg class="w-16 h-16 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
                <h3 class="text-xl font-bold text-yellow-800 mb-2">لا توجد بيانات</h3>
                <p class="text-yellow-600">لا توجد بيانات متاحة للموظف المحدد لهذا الشهر.</p>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center">
                <svg class="w-16 h-16 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-bold text-blue-800 mb-2">اختر موظفاً</h3>
                <p class="text-blue-600">يرجى اختيار موظف من القائمة المنسدلة أعلاه لعرض تقريره الشهري.</p>
            </div>
        @endif
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Manager employee summary loaded successfully');

            // إضافة تأثيرات تفاعلية للبطاقات
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow =
                        '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '';
                });
            });

            // إمكانية إضافة AJAX لتحسين التجربة إذا أردت
            /*
            const employeeSelect = document.getElementById('employeeSelect');
            employeeSelect.addEventListener('change', function() {
                // يمكنك هنا استخدام AJAX لجلب البيانات بدون إعادة تحميل الصفحة
                const employeeId = this.value;
                if (employeeId) {
                    // جلب البيانات عبر AJAX وعرضها
                    fetch(`/employee/summary?employee_id=${employeeId}`)
                        .then(response => response.json())
                        .then(data => {
                            // تحديث واجهة المستخدم بالبيانات الجديدة
                        });
                }
            });
            */
        });
    </script>
@endpush
