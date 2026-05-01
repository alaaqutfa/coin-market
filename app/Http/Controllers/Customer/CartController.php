<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
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

    /**
     * إضافة منتج إلى السلة (لعملاء مسجلين فقط)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id'       => 'required|exists:products,id',
            'quantity'         => 'nullable|integer|min:1',
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'map_link'         => 'nullable|url',
        ]);

        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'يجب تسجيل الدخول أولاً'], 401);
        }

        // تحديث بيانات العميل (قد تكون تغيرت في النموذج)
        $phone = $request->customer_phone ? formatLebanesePhoneNumber($request->customer_phone) : $customer->phone;
        $customer->update([
            'name'     => $request->customer_name,
            'phone'    => $phone,
            'address'  => $request->customer_address ?? $customer->address,
            'map_link' => $request->map_link ?? $customer->map_link,
        ]);

        // الحصول على السلة النشطة أو إنشاؤها
        $cart = $customer->active_cart;
        if (!$cart) {
            $cart = Cart::create([
                'customer_id' => $customer->id,
                'status'      => 'pending',
            ]);
        }

        $product = Product::findOrFail($request->product_id);
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += ($request->quantity ?? 1);
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity ?? 1,
                'price'      => $product->price,
            ]);
        }

        return response()->json([
            'success'      => true,
            'message'      => 'تم إضافة المنتج إلى السلة',
            'cart_count'   => $cart->items->sum('quantity'),
            'checkout_url' => route('customer.checkout'),
        ]);
    }

    /**
     * طلب سريع عبر واتساب (منتج واحد، للعملاء المسجلين فقط)
     */
    public function orderViaWhatsApp(Request $request)
    {
        $request->validate([
            'product_id'       => 'required|exists:products,id',
            'quantity'         => 'nullable|integer|min:1',
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'map_link'         => 'nullable|url',
        ]);

        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'يجب تسجيل الدخول أولاً'], 401);
        }

        // تحديث بيانات العميل
        $phone = $request->customer_phone ? formatLebanesePhoneNumber($request->customer_phone) : $customer->phone;
        $customer->update([
            'name'     => $request->customer_name,
            'phone'    => $phone,
            'address'  => $request->customer_address ?? $customer->address,
            'map_link' => $request->map_link ?? $customer->map_link,
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;
        $total = $product->price * $quantity;

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id'  => $customer->id,
            'status'       => 'pending',
            'total'        => $total,
            'notes'        => "طلب سريع عبر واتساب - المنتج: {$product->name} (x{$quantity})",
        ]);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'price'      => $product->price,
        ]);

        // إرسال رسالة واتساب لرقم المتجر
        $whatsappNumber = '96171349793'; // رقم واتساب الخاص بالمتجر
        $message = "طلب جديد رقم: {$order->order_number}\n";
        $message .= "العميل: {$customer->name}\n";
        $message .= "الهاتف: {$customer->phone}\n";
        $message .= "العنوان: {$customer->address}\n";
        $message .= "المنتج: {$product->name}\n";
        $message .= "الكمية: {$quantity}\n";
        $message .= "الإجمالي: {$total} {$product->symbol}\n";
        $message .= "رابط الخريطة: {$customer->map_link}\n";
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . rawurlencode($message);

        return response()->json([
            'success'  => true,
            'redirect' => $whatsappUrl,
            'message'  => 'سيتم توجيهك إلى واتساب لإرسال الطلب.'
        ]);
    }

    /**
     * عرض السلة
     */
    public function viewCart()
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer || !$customer->active_cart) {
            return view('customer.cart.empty');
        }
        $cart = $customer->active_cart;
        $cart->load('items.product');
        return view('customer.cart.index', compact('cart'));
    }

    /**
     * تحديث كمية منتج معين في السلة
     */
    public function updateCartItem(Request $request, $itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        // التحقق من ملكية السلة للعميل الحالي
        $customer = $this->getCurrentCustomer();
        if (!$customer || $cartItem->cart->customer_id !== $customer->id) {
            return back()->with('error', 'غير مسموح');
        }
        $cartItem->update(['quantity' => $request->quantity]);
        return back()->with('success', 'تم تحديث الكمية');
    }

    /**
     * حذف منتج من السلة
     */
    public function removeCartItem($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        $customer = $this->getCurrentCustomer();
        if (!$customer || $cartItem->cart->customer_id !== $customer->id) {
            return back()->with('error', 'غير مسموح');
        }
        $cartItem->delete();
        return back()->with('success', 'تم حذف المنتج');
    }

    /**
     * إتمام الطلب (تحويل السلة إلى طلب)
     */
    public function checkout(Request $request)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer || !$customer->active_cart) {
            return redirect()->route('customer.home')->with('error', 'لا توجد سلة نشطة');
        }

        $cart = $customer->active_cart;
        if ($cart->items->count() === 0) {
            return back()->with('error', 'السلة فارغة');
        }

        $total = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id'  => $customer->id,
            'cart_id'      => $cart->id,
            'status'       => 'pending',
            'total'        => $total,
            'notes'        => $request->notes,
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

        return redirect()->route('customer.orders')->with('success', 'تم إرسال طلبك بنجاح. رقم الطلب: ' . $order->order_number);
    }

    /**
     * جلب بيانات العميل الحالي (للتعبئة التلقائية في المودال)
     */
    public function getCurrentCustomerData()
    {
        $customer = $this->getCurrentCustomer();
        if ($customer) {
            return response()->json([
                'exists'   => true,
                'name'     => $customer->name,
                'phone'    => $customer->phone,
                'address'  => $customer->address,
                'map_link' => $customer->map_link,
            ]);
        }
        return response()->json(['exists' => false]);
    }

    /**
     * التحقق من حالة تسجيل الدخول (AJAX)
     */
    public function checkAuth()
    {
        return response()->json(['logged_in' => session()->has('customer_id')]);
    }
}
