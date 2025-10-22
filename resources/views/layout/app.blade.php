<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Coin Market Social Stock') }}</title>

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

    @stack('css')

</head>

<body class="bg-gray-50">

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    @include('layout.partials.aside')

    <div id="app" class="sm:ml-64">

        @include('layout.partials.header')

        <main dir="rtl">
            @yield('content')
        </main>

        @include('layout.partials.footer')

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
