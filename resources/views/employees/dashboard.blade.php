{{-- @dd($data) --}}
@php
    $employee = $data['employee'];
    $current_month = $data['current_month'];
    $summary = $data['summary'];
    $absent_dates = $data['absent_dates'];
    $daily_records = $data['daily_records'];
    $period = $data['period'];
@endphp

@extends('layout.customer.app')

@section('title', $employee['employee_code'] . ' - ' . $employee['name'])

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
    </style>
@endpush

@section('content')
    <div dir="rtl" class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-white">
                    <h1 class="text-3xl font-bold mb-2">{{ $employee['name'] }}</h1>
                    <p class="text-yellow-100 text-lg">كود الموظف: {{ $employee['employee_code'] }}</p>
                    <p class="text-yellow-100 mt-2">📅 {{ $current_month }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 mt-4 md:mt-0">
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate($employee['employee_code']) !!}
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('employee.data', $data['employee']['employee_code']) }}"
            class="flex items-center gap-3 mb-6">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">الشهر</label>
                <select id="month" name="month" class="border rounded-lg p-2">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ isset($month) && $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">السنة</label>
                <select id="year" name="year" class="border rounded-lg p-2">
                    @for ($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ isset($year) && $year == $y ? 'selected' : '' }}>
                            {{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="mt-5">
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                    عرض التقرير
                </button>
            </div>
        </form>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Attendance Days -->
            <div class="stat-card bg-white rounded-xl shadow-md border-l-4 border-yellow-400 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">أيام الحضور</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $summary['attendance_days'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $summary['absent_days'] }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed Hours -->
            <div class="stat-card bg-white rounded-xl shadow-md border-l-4 border-green-400 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">الساعات المنجزة</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $summary['total_actual_hours'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $summary['total_required_hours'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <span class="text-2xl font-bold text-yellow-600">{{ $summary['achievement_rate'] }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="max-w-full bg-gradient-to-r from-yellow-400 to-yellow-500 h-4 rounded-full"
                    style="width: {{ $summary['achievement_rate'] }}%"></div>
            </div>
        </div>

        <!-- Salary Calculator Section -->
        <div class="mt-12 bg-white rounded-xl shadow-lg p-6 max-w-xl mx-auto">
            <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">💰 حساب المستحقات</h2>

            <div class="space-y-4">
                <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
                    <label for="salary" class="block text-lg font-semibold text-gray-800 mb-3">
                        💰 أدخل الراتب الشهري (بالدولار)
                    </label>

                    <div class="relative">
                        <span class="absolute inset-y-0 right-3 flex items-center text-yellow-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8v10m0-10a9 9 0 110 18 9 9 0 010-18z" />
                            </svg>
                        </span>
                        <input type="number" id="salary" placeholder="مثلاً: 400"
                            class="w-full text-lg font-medium text-gray-700 rounded-lg border border-yellow-300
                   focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 pl-4 pr-10 py-2.5
                   transition-all duration-200 ease-in-out placeholder:text-gray-400">
                    </div>

                    <p class="text-sm text-gray-500 mt-2">
                        أدخل المبلغ بالدولار الأمريكي لحساب المستحقات بناءً على ساعات العمل المنجزة.
                    </p>
                </div>

                <button id="calculate-btn"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    حساب المستحقات
                </button>

                <div id="result"
                    class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4 text-gray-800 text-center">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Absent Dates -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-800">أيام الغياب</h2>
                </div>

                @if ($absent_dates->count() > 0)
                    <div class="space-y-3">
                        @foreach ($absent_dates as $absent)
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-200">
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
                    <svg class="w-6 h-6 text-yellow-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-800">السجل اليومي</h2>
                </div>

                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach ($daily_records as $record)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center">
                                    <span
                                        class="font-bold text-gray-800 ml-2">{{ $record['formatted_date'] ?? \Carbon\Carbon::parse($record['date'])->format('d/m/Y') }}</span>
                                    <span class="text-sm text-gray-600">{{ $record['day_name'] }}</span>
                                </div>
                                <span
                                    class="attendance-badge rounded-full px-3 py-1 text-xs font-medium
                                            @if ($record['status'] == 'حاضر') bg-green-100 text-green-800
                                            @elseif($record['status'] == 'غائب')
                                                bg-red-100 text-red-800
                                            @elseif($record['status'] == 'إجازة')
                                                bg-blue-100 text-blue-800
                                            @else
                                                bg-yellow-100 text-yellow-800 @endif">
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
                                    {{-- @foreach ($record['attendance_logs'] as $log)
                                        <div class="flex justify-between items-center text-xs text-gray-600 mb-1">
                                            <span>دخول: {{ $log['check_in'] }}</span>
                                            <span>خروج: {{ $log['check_out'] }}</span>
                                        </div>
                                    @endforeach --}}
                                    @php
                                        $lastLog = collect($record['attendance_logs'])->last();
                                    @endphp
                                    @if ($lastLog)
                                        <div class="flex justify-between items-center text-xs text-gray-600 mb-1">
                                            <span class="text-justify font-medium text-gray-900 whitespace-pre-line">
                                                الملاحظات: <br /> {{ $lastLog['note'] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Period Info -->
        <div class="mt-8 text-center text-gray-600">
            <p>فترة التقرير: من {{ \Carbon\Carbon::parse($period['start_date'])->format('d/m/Y') }} إلى
                {{ \Carbon\Carbon::parse($period['end_date'])->format('d/m/Y') }}</p>
        </div>

    </div>
@endsection

@push('script')
    <script>
        // يمكنك إضافة أي scripts تفاعلية هنا
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Employee dashboard loaded successfully');

            // إضافة تأثيرات تفاعلية إضافية إذا لزم الأمر
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
        });

        // حساب المستحقات بناءً على الراتب
        document.getElementById('calculate-btn').addEventListener('click', function() {
            const salaryInput = document.getElementById('salary');
            const resultDiv = document.getElementById('result');

            const salary = parseFloat(salaryInput.value);
            const required = parseFloat("{{ $summary['total_required_hours'] }}");
            const actual = parseFloat("{{ $summary['total_actual_hours'] }}");

            if (isNaN(salary) || salary <= 0) {
                resultDiv.textContent = "الرجاء إدخال راتب صالح أولاً.";
                resultDiv.classList.remove('hidden');
                return;
            }

            if (required === 0) {
                resultDiv.textContent = "لا يمكن الحساب لأن الساعات المطلوبة تساوي صفر.";
                resultDiv.classList.remove('hidden');
                return;
            }

            const hourRate = salary / required;
            const totalEarned = hourRate * actual;

            resultDiv.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-center shadow-sm">
                    <p class="text-lg font-semibold text-gray-800 mb-4">📘 تفاصيل العملية الحسابية:</p>

                    <p class="text-base text-gray-700 leading-relaxed mb-3">
                        <span class="font-bold text-yellow-700">سعر الساعة =</span>
                        <span class="text-gray-900 font-semibold">الراتب ÷ الساعات المطلوبة</span><br>
                        <span class="text-gray-600">(${salary} ÷ ${required}) =
                            <span class="text-blue-700 font-bold">${(salary / required).toFixed(2)}</span>
                        </span>
                    </p>

                    <p class="text-base text-gray-700 leading-relaxed mb-3">
                        <span class="font-bold text-yellow-700">المستحق =</span>
                        <span class="text-gray-900 font-semibold">سعر الساعة × الساعات المنجزة</span><br>
                        <span class="text-gray-600">
                            (${(salary / required).toFixed(2)} × ${actual}) =
                            <span class="text-green-700 font-bold">${totalEarned.toFixed(2)}</span>
                        </span>
                    </p>

                    <div class="mt-5 border-t border-yellow-300 pt-3">
                        <p class="text-xl font-extrabold text-green-700">
                            💵 إجمالي المستحق: <span class="text-green-800">${totalEarned.toFixed(2)}</span>
                        </p>
                    </div>
                </div>
            `;
            resultDiv.classList.remove('hidden');
        });
    </script>
@endpush
