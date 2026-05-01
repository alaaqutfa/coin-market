@extends('layout.customer.app')
@section('title', 'إنشاء حساب')
@section('content')
    <div dir="rtl" class="container mx-auto px-4 py-8 max-w-md">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-6">إنشاء حساب جديد</h1>
            <form method="POST" action="{{ route('customer.register.submit') }}">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1">الاسم الكامل *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg p-2">
                    @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">رقم الهاتف (لبناني) *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required class="w-full border rounded-lg p-2">
                    <p class="text-gray-500 text-xs">مثال: 03457320 أو +9613457320</p>
                    @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">البريد الإلكتروني (اختياري)</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg p-2">
                    @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">كلمة المرور *</label>
                    <input type="password" name="password" required class="w-full border rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block mb-1">تأكيد كلمة المرور *</label>
                    <input type="password" name="password_confirmation" required class="w-full border rounded-lg p-2">
                </div>
                <button type="submit"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg">تسجيل</button>
            </form>
            <p class="mt-4 text-center">لديك حساب؟ <a href="{{ route('customer.login') }}" class="text-yellow-600">تسجيل
                    الدخول</a></p>
        </div>
    </div>
@endsection
