<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiOrderController extends Controller
{
    // قائمة الطلبات مع فلترة (حالة، تاريخ، بحث برقم الطلب)
    public function index(Request $request)
    {
        $query = Order::with('customer', 'items.product');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['success' => true, 'data' => $orders]);
    }

    // تفاصيل طلب معين
    public function show($id)
    {
        $order = Order::with('customer', 'items.product')->find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'الطلب غير موجود'], 404);
        }
        return response()->json(['success' => true, 'data' => $order]);
    }

    // تحديث حالة الطلب (قبول / رفض مع سبب)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'الطلب غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $order->status = $request->status;
        if ($request->status === 'rejected') {
            $order->rejection_reason = $request->rejection_reason;
        }
        $order->save();

        return response()->json(['success' => true, 'message' => 'تم تحديث الحالة']);
    }

    // إحصائيات سريعة (عدد الطلبات حسب الحالة وإجمالي المبيعات اليومية/الشهرية)
    public function statistics(Request $request)
    {
        $pendingCount = Order::where('status', 'pending')->count();
        $acceptedCount = Order::where('status', 'accepted')->count();
        $rejectedCount = Order::where('status', 'rejected')->count();

        $today = now()->toDateString();
        $todayTotal = Order::whereDate('created_at', $today)->sum('total');

        $month = now()->format('Y-m');
        $monthTotal = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        return response()->json([
            'success' => true,
            'data' => [
                'pending' => $pendingCount,
                'accepted' => $acceptedCount,
                'rejected' => $rejectedCount,
                'today_total' => number_format($todayTotal, 2),
                'month_total' => number_format($monthTotal, 2),
            ]
        ]);
    }
}
