@extends('layout.app')

@section('title', 'إضافة فئة جديدة')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                <i class="fas fa-plus-circle ml-2"></i>
                إضافة فئة جديدة
            </h1>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 mb-2">اسم الفئة</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('categories.index') }}"
                        class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                    <button type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        حفظ الفئة
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
