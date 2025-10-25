<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        $admins = User::with('role')->whereIn('role_id', [1, 2, 3])->get();
        return view('admin.users.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::whereIn('id', [1, 2, 3])->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'address'  => 'nullable|string|max:255',
            'role_id'  => 'required|exists:roles,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'role_id'  => $request->role_id,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function showCustomerLogin()
    {
        return view('auth.customer.login');
    }

    public function showEmployeeLogin()
    {
        return view('auth.employee.login');
    }

    public function showAdminLogin()
    {
        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        $type = $request->input('type', 'customer'); // الافتراضي عميل

        switch ($type) {
            case 'admin':
                $request->validate([
                    'email'    => 'required|email',
                    'password' => 'required|string|min:6',
                ]);

                $credentials = $request->only('email', 'password');

                if (Auth::attempt($credentials, $request->remember)) {
                    $user = Auth::user();
                    if (in_array($user->role_id, [1, 2, 3])) {
                        $request->session()->regenerate();
                        return redirect()->route('dashboard')->with('success', 'مرحباً بعودتك! تم تسجيل الدخول بنجاح.');
                    } else {
                        Auth::logout();
                        return back()->with('error', 'ليس لديك صلاحية للوصول إلى لوحة التحكم.');
                    }
                }

                return back()->with('error', 'بيانات الدخول غير صحيحة.');

            case 'employee':
                $request->validate([
                    'employee_code' => 'required|string',
                    'password'      => 'required|string|min:6',
                ]);

                $employee = Employee::where('employee_code', $request->employee_code)->first();

                if ($employee && Hash::check($request->password, $employee->password)) {
                    session(['employee_id' => $employee->id]);
                    return redirect()->route('attendance.dashboard.today')->with('success', 'تم تسجيل دخول الموظف بنجاح.');
                }

                return back()->with('error', 'الرمز الوظيفي أو كلمة المرور غير صحيحة.');

            case 'customer':
            default:
                $request->validate([
                    'login'    => 'required|string',
                    'password' => 'required|string|min:6',
                ]);

                $loginInput = $request->login;

                // نتحقق إن كان البريد أو رقم هاتف
                if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
                    $user = User::where('email', $loginInput)->first();
                } else {
                    // التحقق من رقم لبناني صالح
                    $phone = preg_replace('/\D/', '', $loginInput);
                    if (! preg_match('/^(961|0)?(3\d{6}|7\d{7}|81\d{6})$/', $phone)) {
                        return back()->with('error', 'رقم الهاتف اللبناني غير صالح.');
                    }
                    $phone = ltrim($phone, '0');
                    if (! str_starts_with($phone, '961')) {
                        $phone = '961' . $phone;
                    }
                    $user = User::where('phone', $phone)->first();
                }

                if ($user && Hash::check($request->password, $user->password) && $user->role_id == 4) {
                    Auth::login($user);
                    $request->session()->regenerate();
                    return redirect()->route('customer.dashboard')->with('success', 'تم تسجيل الدخول بنجاح.');
                }

                return back()->with('error', 'بيانات الدخول غير صحيحة.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role_id === 1) {
            return back()->with('error', 'لا يمكن حذف السوبر أدمن');
        }

        $user->delete();
        return back()->with('success', 'تم حذف المستخدم بنجاح');
    }
}
