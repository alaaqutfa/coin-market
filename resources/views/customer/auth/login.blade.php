@extends('layout.customer.app')
@section('title', 'تسجيل دخول العميل')
@section('content')
    <div dir="rtl" class="container mx-auto px-4 py-8 max-w-md">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-6">تسجيل الدخول</h1>
            <form method="POST" action="{{ route('customer.login.submit') }}">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1">رقم الهاتف *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                        class="w-full border rounded-lg p-2 @error('phone') border-red-500 @enderror">
                    @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">كلمة المرور *</label>
                    <input type="password" name="password" required
                        class="w-full border rounded-lg p-2 @error('password') border-red-500 @enderror">
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg">دخول</button>
            </form>
            <p class="mt-4 text-center">ليس لديك حساب؟ <a href="{{ route('customer.register') }}"
                    class="text-yellow-600">سجل الآن</a></p>
        </div>
    </div>
@endsection
