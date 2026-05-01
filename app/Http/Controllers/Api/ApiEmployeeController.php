<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApiEmployeeController extends Controller
{
    // ======================= customers =======================
    public function customers(Request $request)
    {
        $query = Customer::query();
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        }
        $customers = $query->orderBy('name')->paginate(20);
        return response()->json(['success' => true, 'data' => $customers]);
    }

    public function showCustomer($id)
    {
        $customer = Customer::find($id);
        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود'], 404);
        }
        return response()->json(['success' => true, 'data' => $customer]);
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'phone'    => 'sometimes|string|max:20|unique:customers,phone,' . $id,
            'address'  => 'nullable|string',
            'map_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->has('phone')) {
            $phone = formatLebanesePhoneNumber($request->phone);
            if (! $phone) {
                return response()->json(['success' => false, 'message' => 'رقم الهاتف غير صالح'], 422);
            }
            $customer->phone = $phone;
        }
        $customer->fill($request->only(['name', 'address', 'map_link']));
        $customer->save();

        return response()->json(['success' => true, 'message' => 'تم تحديث بيانات العميل']);
    }

    public function updateCustomerPassword(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $customer->password = Hash::make($request->password);
        $customer->save();

        return response()->json(['success' => true, 'message' => 'تم تغيير كلمة المرور']);
    }

    // ======================= carts =======================
    public function getCart($customerId)
    {
        $customer = Customer::find($customerId);
        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود'], 404);
        }
        $cart = $customer->active_cart;
        if (! $cart) {
            $cart = Cart::create(['customer_id' => $customer->id, 'status' => 'pending']);
        }
        $cart->load('items.product');
        return response()->json(['success' => true, 'data' => $cart]);
    }

    public function addCartItem(Request $request, $customerId)
    {
        $customer = Customer::find($customerId);
        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cart = $customer->active_cart;
        if (! $cart) {
            $cart = Cart::create(['customer_id' => $customer->id, 'status' => 'pending']);
        }

        $product  = Product::find($request->product_id);
        $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();
        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->price,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'تمت الإضافة', 'cart_count' => $cart->items->sum('quantity')]);
    }

    public function updateCartItem(Request $request, $itemId)
    {
        $cartItem = CartItem::find($itemId);
        if (! $cartItem) {
            return response()->json(['success' => false, 'message' => 'العنصر غير موجود'], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['success' => true, 'message' => 'تم تحديث الكمية']);
    }

    public function deleteCartItem($itemId)
    {
        $cartItem = CartItem::find($itemId);
        if (! $cartItem) {
            return response()->json(['success' => false, 'message' => 'العنصر غير موجود'], 404);
        }
        $cartItem->delete();
        return response()->json(['success' => true, 'message' => 'تم الحذف']);
    }

    public function checkoutCart($customerId)
    {
        $customer = Customer::find($customerId);
        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'العميل غير موجود'], 404);
        }
        $cart = $customer->active_cart;
        if (! $cart || $cart->items->count() == 0) {
            return response()->json(['success' => false, 'message' => 'السلة فارغة'], 422);
        }

        $total = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id'  => $customer->id,
            'cart_id'      => $cart->id,
            'status'       => 'pending',
            'total'        => $total,
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->price,
            ]);
        }

        $cart->status = 'converted_to_order';
        $cart->save();

        return response()->json(['success' => true, 'message' => 'تم إنشاء الطلب', 'order_id' => $order->id]);
    }

    // ======================= order items modification =======================
    public function addOrderItem(Request $request, $orderId)
    {
        $order = Order::with('items')->find($orderId);
        if (! $order) {
            return response()->json(['success' => false, 'message' => 'الطلب غير موجود'], 404);
        }
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'لا يمكن تعديل طلب غير معلق'], 422);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product  = Product::find($request->product_id);
        $existing = OrderItem::where('order_id', $order->id)->where('product_id', $product->id)->first();
        if ($existing) {
            $existing->quantity += $request->quantity;
            $existing->save();
        } else {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->price,
            ]);
        }

        // recalculate total
        $newTotal     = $order->items()->sum(DB::raw('quantity * price'));
        $order->total = $newTotal;
        $order->save();

        return response()->json(['success' => true, 'message' => 'تمت الإضافة']);
    }

    public function updateOrderItem(Request $request, $itemId)
    {
        $orderItem = OrderItem::with('order')->find($itemId);
        if (! $orderItem) {
            return response()->json(['success' => false, 'message' => 'العنصر غير موجود'], 404);
        }
        if ($orderItem->order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'لا يمكن تعديل طلب غير معلق'], 422);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $orderItem->quantity = $request->quantity;
        $orderItem->save();

        // recalculate total
        $order        = $orderItem->order;
        $newTotal     = $order->items()->sum(DB::raw('quantity * price'));
        $order->total = $newTotal;
        $order->save();

        return response()->json(['success' => true, 'message' => 'تم تحديث الكمية']);
    }

    public function deleteOrderItem($itemId)
    {
        $orderItem = OrderItem::with('order')->find($itemId);
        if (! $orderItem) {
            return response()->json(['success' => false, 'message' => 'العنصر غير موجود'], 404);
        }
        if ($orderItem->order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'لا يمكن حذف عنصر من طلب غير معلق'], 422);
        }
        $orderItem->delete();

        $order        = $orderItem->order;
        $newTotal     = $order->items()->sum(DB::raw('quantity * price'));
        $order->total = $newTotal;
        $order->save();

        return response()->json(['success' => true, 'message' => 'تم الحذف']);
    }
}
