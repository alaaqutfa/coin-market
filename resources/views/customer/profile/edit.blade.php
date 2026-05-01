@extends('layout.customer.app')

@section('title', 'ملفي الشخصي')

@section('content')
    <div dir="rtl" class="container mx-auto px-4 py-8 max-w-2xl">
        <h1 class="text-2xl font-bold mb-6">ملفي الشخصي</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- نموذج تعديل البيانات -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">تعديل البيانات الشخصية</h2>
            <form method="POST" action="{{ route('customer.profile.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block mb-1">الاسم الكامل *</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                        class="w-full border rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block mb-1">رقم الهاتف (لبناني) *</label>
                    <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" required
                        class="w-full border rounded-lg p-2">
                    <p class="text-gray-500 text-xs">مثال: 03457320 أو 9613457320</p>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">العنوان التفصيلي</label>
                    <textarea name="address" rows="2"
                        class="w-full border rounded-lg p-2">{{ old('address', $customer->address) }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">رابط الخريطة (Google Maps)</label>
                    <input type="url" name="map_link" value="{{ old('map_link', $customer->map_link) }}"
                        class="w-full border rounded-lg p-2" placeholder="https://maps.app.goo.gl/...">
                </div>
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">حفظ التغييرات</button>
            </form>
        </div>

        <!-- نموذج تغيير كلمة المرور -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">تغيير كلمة المرور</h2>
            <form method="POST" action="{{ route('customer.profile.password') }}">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block mb-1">كلمة المرور الحالية *</label>
                    <input type="password" name="current_password" required class="w-full border rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block mb-1">كلمة المرور الجديدة *</label>
                    <input type="password" name="new_password" required class="w-full border rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block mb-1">تأكيد كلمة المرور الجديدة *</label>
                    <input type="password" name="new_password_confirmation" required class="w-full border rounded-lg p-2">
                </div>
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">تغيير كلمة المرور</button>
            </form>
        </div>
    </div>
@endsection
