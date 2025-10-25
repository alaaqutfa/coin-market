@extends('layout.auth.app')

@section('title', 'تسجيل دخول الموظف')

@section('content')

<form class="w-full max-w-sm mx-auto" method="POST" action="{{ route('login.submit') }}">
    @csrf
    <input type="hidden" name="type" value="employee">

    <!-- الرسائل -->
    @if(session('success'))
        <div class="flex p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <i class="fas fa-check-circle mt-0.5 ml-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <i class="fas fa-exclamation-circle mt-0.5 ml-2"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- الرمز الوظيفي -->
    <div class="mb-5">
        <label for="employee_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            <i class="fas fa-id-badge ml-2"></i>
            الرمز الوظيفي
        </label>
        <input type="text" id="employee_code" name="employee_code"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
            placeholder="أدخل الرمز الوظيفي الخاص بك"
            value="{{ old('employee_code') }}"
            required autofocus />

        @error('employee_code')
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
            placeholder="••••••••"
            required />
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
    <script>
        $(document).ready(function() {
            $("form").on("submit", function(e) {
                const code = $("#employee_code").val().trim();
                const password = $("#password").val().trim();

                if (code === "" || password === "") {
                    e.preventDefault();
                    showToast("الرجاء إدخال الرمز الوظيفي وكلمة المرور.","error");
                    return false;
                }

                // تحقق من تنسيق الرمز الوظيفي (اختياري)
                if (!/^[A-Za-z0-9_-]+$/.test(code)) {
                    e.preventDefault();
                    showToast("الرمز الوظيفي يجب أن يحتوي فقط على أحرف وأرقام بدون مسافات.","error");
                    return false;
                }
            });
        });
    </script>
@endpush
