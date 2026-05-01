<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    /**
     * الحصول على العميل المسجل حاليًا (من الجلسة)
     */
    protected function getCurrentCustomer()
    {
        if (session()->has('customer_id')) {
            return Customer::find(session('customer_id'));
        }
        return null;
    }
    public function index()
    {
        $customer = $this->getCurrentCustomer();
        if (! $customer) {
            return view('customer.orders.index', ['orders' => collect()]);
        }
        $orders = Order::where('customer_id', $customer->id)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.orders.index', compact('orders'));
    }

    public function show($orderNumber)
    {
        $customer = $this->getCurrentCustomer();
        if (! $customer) {
            abort(404);
        }
        $order = Order::where('order_number', $orderNumber)
            ->where('customer_id', $customer->id)
            ->with('items.product')
            ->firstOrFail();
        return view('customer.orders.show', compact('order'));
    }
}
