@extends('layout.admin.app')
@section('title', 'التقارير')
@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">تقرير المبيعات</h1>
        <div class="bg-white p-6 rounded shadow max-w-md">
            <form action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                @csrf
                <div class="mb-4">
                    <label>من تاريخ</label>
                    <input type="date" name="from_date" required class="border rounded p-2 w-full">
                </div>
                <div class="mb-4">
                    <label>إلى تاريخ</label>
                    <input type="date" name="to_date" required class="border rounded p-2 w-full">
                </div>
                <div class="mb-4">
                    <label>نوع التقرير</label>
                    <select name="type" class="border rounded p-2 w-full">
                        <option value="daily">تفصيلي يومي</option>
                        <option value="monthly">ملخص شهري</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="format" value="excel"
                        class="bg-green-600 text-white px-4 py-2 rounded">Excel</button>
                    <button type="submit" name="format" value="pdf"
                        class="bg-red-600 text-white px-4 py-2 rounded">PDF</button>
                </div>
            </form>
        </div>
    </div>
@endsection
