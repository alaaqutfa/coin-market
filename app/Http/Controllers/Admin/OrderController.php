<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب العميل (البحث بالاسم أو الهاتف)
        if ($request->filled('customer')) {
            $search = $request->customer;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // فلترة حسب نطاق التواريخ
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // بحث سريع (رقم الطلب)
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'asc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'customer')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status'           => 'required|in:accepted,rejected',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $order->status = $request->status;
        if ($request->status === 'rejected') {
            $order->rejection_reason = $request->rejection_reason;
        }
        $order->save();

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    public function contactCustomer($id)
    {
        $order    = Order::with('customer')->findOrFail($id);
        $customer = $order->customer;
        if ($customer->phone) {
            $whatsappUrl = "https://wa.me/{$customer->phone}?text=" . urlencode("مرحباً {$customer->name}، لديك طلب رقم {$order->order_number} ");
            return redirect()->away($whatsappUrl);
        }
        return back()->with('error', 'رقم هاتف العميل غير متوفر');
    }
}
