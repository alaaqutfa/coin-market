<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Coin Market Social Stock') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo-light.png') }}" sizes="32x32">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    @stack('css')

</head>

<body class="bg-gray-50">

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>


    <div id="app">


        <main class="flex justify-center items-center">

            <div class="nav-item employee-list table-container bg-white rounded-lg">
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800 flex justify-center items-center gap-2">
                        <i class="fas fa-list ml-2"></i>
                        قائمة الموظفين
                    </h2>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500" style="max-width: 425px;">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
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
                            </tr>
                        </thead>
                        <tbody id="products-table-body">
                            @if (count($employees) > 0)
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                            {{ $employee->employee_code }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
                                            <div class="editable-field" contenteditable="true" data-field="name">
                                                {{ $employee->name }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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

        </main>


    </div>





    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>\
    <script>
        // عرض Toast
        function showToast(message, type = 'success') {
            const backgroundColor = type === 'success' ? '#10B981' : '#EF4444';

            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: backgroundColor,
                stopOnFocus: true,
            }).showToast();
        }
    </script>
    @stack('script')

</body>

</html>
