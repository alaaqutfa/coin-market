@extends('layout.auth.app')

@section('title', 'تسجيل الدخول')

@push('css')
    <style>
        .iti {
            width: 100%;
        }
    </style>
@endpush

@section('content')

    <form class="w-full max-w-sm mx-auto" method="POST" action="{{ route('login.submit') }}">
        @csrf

        <!-- الرسائل -->
        @if (session('success'))
            <div class="flex p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <i class="fas fa-check-circle mt-0.5 ml-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <i class="fas fa-exclamation-circle mt-0.5 ml-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- رقم الهاتف -->
        <div class="mb-5">
            <label for="login" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <i class="fas fa-user ml-2"></i>
                رقم الهاتف
            </label>

            <input type="text" inputmode="tel" id="login" name="login"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                placeholder="مثال: 70123456" value="{{ old('login') }}" required autofocus />

            @error('login')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                    <i class="fas fa-exclamation-triangle ml-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- كلمة المرور -->
        <div class="mb-5">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <i class="fas fa-lock ml-2"></i>
                كلمة المرور
            </label>
            <input type="password" id="password" name="password"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                placeholder="••••••••" required />
            @error('password')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                    <i class="fas fa-exclamation-triangle ml-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- تذكرني -->
        <div class="flex items-start mb-5">
            <div class="flex items-center h-5">
                <input id="remember" name="remember" type="checkbox"
                    class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-yellow-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-yellow-600 dark:ring-offset-gray-800" />
            </div>
            <label for="remember" class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                تذكرني
            </label>
        </div>

        <!-- زر الدخول -->
        <button type="submit"
            class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center transition duration-200 dark:focus:ring-yellow-900">
            <i class="fas fa-sign-in-alt ml-2"></i>
            تسجيل الدخول
        </button>
    </form>

@endsection

@push('script')
    <!-- intl-tel-input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css" />
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

    <script>
        $(document).ready(function() {
            const input = document.querySelector("#login");
            const iti = window.intlTelInput(input, {
                onlyCountries: ["lb"],
                initialCountry: "lb",
                nationalMode: false,
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
            });

            $("form").on("submit", function(e) {
                const value = $("#login").val().trim();
                const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

                if (!isEmail) {
                    const phoneNumber = iti.getNumber();
                    const countryData = iti.getSelectedCountryData();

                    if (countryData.iso2 !== "lb" || !iti.isValidNumber()) {
                        e.preventDefault();
                        showToast(
                            "يرجى إدخال رقم هاتف لبناني صالح بصيغة تبدأ بـ +961 أو بريد إلكتروني صحيح.",
                            'warning');
                        return false;
                    }

                    $("#login").val(phoneNumber);
                }
            });
        });
    </script>
@endpush
