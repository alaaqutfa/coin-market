<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Coin Market Social Stock') }} - لوحة التحكم</title>

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

    <style>
        /* تحسينات التصميم */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            /* display: flex; */
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #f59e0b;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .auth-card {
            background: linear-gradient(135deg, #ffffff 0%, #fefce8 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(245, 158, 11, 0.2);
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.1);
        }

        .gradient-text {
            /* background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); */
            background: linear-gradient(135deg, #ecc631 0%, #ecc631 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
        }

        .form-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        /* تحسينات للشعار */
        .logo-glow {
            filter: drop-shadow(0 0 10px rgba(245, 158, 11, 0.3));
        }

        /* تحسينات للرسائل */
        .alert-success {
            border-left: 4px solid #10b981;
        }

        .alert-error {
            border-left: 4px solid #ef4444;
        }
    </style>

    @stack('css')

</head>

<body class="bg-gradient-to-br from-amber-50 to-yellow-100" style="font-family: 'Tajawal', sans-serif;">

    <!-- Loading Overlay -->
    <div class="loading-overlay hidden" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div id="app" class="w-full min-h-screen flex justify-center items-center p-4">

        <main dir="rtl" class="auth-card flex flex-col gap-8 w-full max-w-md p-8 rounded-2xl relative overflow-hidden">

            <!-- تأثير خلفي جمالي -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-yellow-500"></div>

            <!-- دوائر ديكورية في الخلفية -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-amber-200 rounded-full opacity-20"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-yellow-200 rounded-full opacity-20"></div>

            <div class="top-auth flex flex-col sm:flex-row justify-center items-center gap-6 relative z-10">

                <div class="logo">
                    <img src="{{ asset('assets/img/logo-light.png') }}"
                         class="w-24 h-24 object-contain logo-glow"
                         alt="logo" />
                </div>

                <div class="name text-center sm:text-right">
                    <h1 class="text-3xl font-bold gradient-text">Coin Market</h1>
                    <p class="text-gray-600 text-sm mt-2">لوحة تحكم الأدمن</p>
                </div>

            </div>

            @yield('content')

            <!-- معلومات إضافية -->
            <div class="text-center mt-6 relative z-10">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} Coin Market. جميع الحقوق محفوظة.
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    نظام آمن ومحمي
                </p>
            </div>

        </main>

    </div>

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <script>
        // إدارة حالة التحميل
        document.addEventListener('DOMContentLoaded', function() {
            const loadingOverlay = document.getElementById('loadingOverlay');

            // إخفاء التحميل بعد تحميل الصفحة
            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');
            setTimeout(() => {
                loadingOverlay.classList.remove('flex');
                loadingOverlay.classList.add('hidden');
            }, 1000);

            // إظهار التحميل عند إرسال النموذج
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    loadingOverlay.classList.remove('hidden');
                    loadingOverlay.classList.add('flex');
                });
            });
        });

        // عرض Toast محسن
        function showToast(message, type = 'success') {
            const backgroundColors = {
                success: 'linear-gradient(135deg, #10B981, #059669)',
                error: 'linear-gradient(135deg, #EF4444, #DC2626)',
                warning: 'linear-gradient(135deg, #F59E0B, #D97706)',
                info: 'linear-gradient(135deg, #3B82F6, #2563EB)'
            };

            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };

            Toastify({
                text: `${icons[type]} ${message}`,
                duration: 4000,
                gravity: "top",
                position: "left",
                style: {
                    background: backgroundColors[type],
                    borderRadius: '8px',
                    boxShadow: '0 4px 12px rgba(0, 0, 0, 0.1)'
                },
                stopOnFocus: true,
            }).showToast();
        }

        // عرض رسائل الـ Session تلقائياً
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast("{{ $error }}", 'error');
            @endforeach
        @endif
    </script>

    @stack('script')

</body>

</html>
