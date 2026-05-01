<?php
namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CustomerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'email'    => 'nullable|email|unique:customers,email',
        ]);

        $phone = formatLebanesePhoneNumber($request->phone);
        if (! $phone) {
            return back()->withErrors(['phone' => 'رقم الهاتف غير صالح (لبناني)'])->withInput();
        }

        if (Customer::where('phone', $phone)->exists()) {
            return back()->withErrors(['phone' => 'رقم الهاتف مسجل مسبقاً'])->withInput();
        }

        $customer = Customer::create([
            'name'     => $request->name,
            'phone'    => $phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Session::put('customer_id', $customer->id);

        return redirect()->route('customer.home')->with('success', 'تم إنشاء الحساب بنجاح. أهلاً بك!');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        $phone = formatLebanesePhoneNumber($request->phone);
        if (! $phone) {
            return back()->withErrors(['phone' => 'رقم الهاتف غير صالح'])->withInput();
        }

        $customer = Customer::where('phone', $phone)->first();

        if ($customer && Hash::check($request->password, $customer->password)) {
            Session::put('customer_id', $customer->id);
            return redirect()->intended(route('customer.home'))->with('success', 'تم تسجيل الدخول بنجاح');
        }

        return back()->withErrors(['phone' => 'البيانات غير صحيحة'])->withInput();
    }

    public function registerAjax(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'password'    => 'required|string|min:6|confirmed',
            'address'     => 'nullable|string',
            'map_link'    => 'nullable|url',
            'product_id'  => 'required|exists:products,id',
            'quantity'    => 'required|integer|min:1',
            'action_type' => 'required|in:cart,whatsapp',
        ]);

        $phone = formatLebanesePhoneNumber($request->phone);
        if (! $phone) {
            return response()->json(['success' => false, 'message' => 'رقم الهاتف غير صالح (لبناني)'], 422);
        }

        if (Customer::where('phone', $phone)->exists()) {
            return response()->json(['success' => false, 'message' => 'رقم الهاتف مسجل مسبقاً'], 422);
        }

        $customer = Customer::create([
            'name'     => $request->name,
            'phone'    => $phone,
            'address'  => $request->address,
            'map_link' => $request->map_link,
            'password' => Hash::make($request->password),
        ]);

        session(['customer_id' => $customer->id]);

        // تحديد الإجراء الأصلي (إضافة للسلة أو واتساب)
        $actionUrl  = $request->action_type === 'cart' ? route('customer.cart.add') : route('customer.order.whatsapp');
        $actionData = [
            'product_id'       => $request->product_id,
            'quantity'         => $request->quantity,
            'customer_name'    => $customer->name,
            'customer_phone'   => $customer->phone,
            'customer_address' => $customer->address,
            'map_link'         => $customer->map_link,
            '_token'           => csrf_token(),
        ];

        return response()->json([
            'success'     => true,
            'message'     => 'تم إنشاء الحساب بنجاح',
            'action_url'  => $actionUrl,
            'action_data' => $actionData,
        ]);
    }

    public function loginAjax(Request $request)
    {
        $request->validate([
            'phone'       => 'required|string',
            'password'    => 'required|string',
            'product_id'  => 'required|exists:products,id',
            'quantity'    => 'required|integer|min:1',
            'action_type' => 'required|in:cart,whatsapp',
        ]);

        $phone = formatLebanesePhoneNumber($request->phone);
        if (! $phone) {
            return response()->json(['success' => false, 'message' => 'رقم الهاتف غير صالح'], 422);
        }

        $customer = Customer::where('phone', $phone)->first();
        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return response()->json(['success' => false, 'message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        session(['customer_id' => $customer->id]);

        $actionUrl  = $request->action_type === 'cart' ? route('customer.cart.add') : route('customer.order.whatsapp');
        $actionData = [
            'product_id'       => $request->product_id,
            'quantity'         => $request->quantity,
            'customer_name'    => $customer->name,
            'customer_phone'   => $customer->phone,
            'customer_address' => $customer->address,
            'map_link'         => $customer->map_link,
            '_token'           => csrf_token(),
        ];

        return response()->json([
            'success'     => true,
            'message'     => 'تم تسجيل الدخول بنجاح',
            'action_url'  => $actionUrl,
            'action_data' => $actionData,
        ]);
    }

    public function logout()
    {
        Session::forget('customer_id');
        return redirect()->route('customer.login')->with('success', 'تم تسجيل الخروج');
    }
}
