<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <link rel="icon" type="image/png" href="{{ asset('public/assets/img/logo-light.png') }}" sizes="32x32">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.5.0/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.5.0/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.5.0/build/js/utils.js"></script>


    @stack('css')
    <style>
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* تنسيق حاوية الـ intl-tel-input بشكل عام */
        .iti {
            display: block;
            width: 100%;
            direction: rtl;
        }

        /* تنسيق حقل الإدخال نفسه */
        .iti .iti__tel-input {
            direction: ltr;
            /* الأرقام تبقى من اليسار لليمين */
            text-align: right;
            /* توسيط النص في الحقل إلى اليمين */
            padding-right: 90px;
            /* مساحة لرمز البلد على اليمين */
            padding-left: 12px;
            /* مسافة بسيطة من اليسار */
            width: 100%;
            box-sizing: border-box;
        }

        /* حاوية رمز البلد والقائمة (عادةً على اليمين في RTL) */
        .iti .iti__flag-container {
            right: 0;
            left: auto;
        }

        /* سهم القائمة المنسدلة (في اليمين) */
        .iti .iti__selected-flag {
            padding: 0 12px 0 8px;
            border-radius: 8px 0 0 8px;
        }

        /* تنسيق القائمة المنسدلة للدول */
        .iti .iti__country-list {
            direction: ltr;
            /* أسماء الدول تبقى من اليسار لليمين */
            text-align: left;
            right: 0;
            left: auto;
            width: 300px;
            max-width: calc(100vw - 32px);
        }

        /* تنسيق عناصر الدول داخل القائمة */
        .iti .iti__country {
            direction: ltr;
            text-align: left;
            padding: 8px 12px;
        }

        /* تنسيق اسم الدولة (النص العربي يكون طبيعياً) */
        .iti .iti__country-name {
            direction: rtl;
            text-align: right;
            flex: 1;
        }

        /* رمز البلد والدليل */
        .iti .iti__dial-code {
            direction: ltr;
            margin-left: 0;
            margin-right: 6px;
        }

        /* ضبط أيقونة البحث داخل القائمة إذا ظهرت */
        .iti .iti__search-input {
            direction: rtl;
            text-align: right;
            padding-right: 36px;
            padding-left: 12px;
        }

        /* تحسين المسافات في وضع RTL عند التركيز */
        .iti.iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* تصحيح موضع الـ dropdown في الشاشات الصغيرة */
        @media (max-width: 640px) {
            .iti .iti__country-list {
                right: 0;
                left: auto;
                width: 280px;
            }
        }

        /* إذا أردت إبقاء الأرقام باتجاه LTR داخل الحقل فقط */
        .iti .iti__tel-input:focus {
            outline: none;
        }

        /* ضبط حدود الحاوية الخارجية في وضع RTL */
        .iti--allow-dropdown .iti__flag-container .iti__selected-flag {
            border-radius: 8px 0 0 8px;
        }
    </style>

</head>

<body class="bg-gray-50">

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="w-full h-full flex justify-center items-center">
            <div class="text-center flex justify-center items-center flex-col gap-4">
                <div class="spinner mb-4"></div>
                <p class="text-gray-600 font-medium">جاري تحميل البيانات...</p>
            </div>
        </div>
    </div>

    <div id="app">
        @if (!session('mobile'))
            @include('layout.customer.header')
        @endif
        <main>
            @yield('content')
        </main>
        @if (!session('mobile'))
            @include('layout.partials.footer')
        @endif
    </div>

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

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
