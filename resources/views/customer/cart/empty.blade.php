@extends('layout.customer.app')
@section('content')
    <div class="text-center py-12">
        <h2>السلة فارغة</h2>
        <a href="{{ route('customer.home') }}" class="text-yellow-600">تسوق الآن</a>
    </div>
@endsection
