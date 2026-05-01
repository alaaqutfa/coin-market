<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected function getCustomer()
    {
        if (session()->has('customer_id')) {
            return \App\Models\Customer::find(session('customer_id'));
        }
        return null;
    }

    public function edit()
    {
        $customer = $this->getCustomer();
        if (! $customer) {
            return redirect()->route('customer.login');
        }

        return view('customer.profile.edit', compact('customer'));
    }

    public function update(Request $request)
    {
        $customer = $this->getCustomer();
        if (! $customer) {
            return redirect()->route('customer.login');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'address'  => 'nullable|string',
            'map_link' => 'nullable|url',
        ]);

        $phone = formatLebanesePhoneNumber($request->phone);
        if (! $phone) {
            return back()->withErrors(['phone' => 'رقم الهاتف غير صالح']);
        }

        $customer->update([
            'name'     => $request->name,
            'phone'    => $phone,
            'address'  => $request->address,
            'map_link' => $request->map_link,
        ]);

        return back()->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function updatePassword(Request $request)
    {
        $customer = $this->getCustomer();
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $customer->update(['password' => Hash::make($request->new_password)]);
        return back()->with('success', 'تم تغيير كلمة المرور');
    }
}
