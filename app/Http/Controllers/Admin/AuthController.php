<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
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

    public function showLoginForm()
    {
        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        // محاولة تسجيل الدخول
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // التحقق من أن المستخدم لديه صلاحية أدمن (1,2,3)
            if (in_array($user->role_id, [1, 2, 3])) {
                $request->session()->regenerate();
                return redirect()->route('dashboard')->with('success', 'مرحباً بعودتك! تم تسجيل الدخول بنجاح.');
            } else {
                Auth::logout();
                return back()->with('error', 'ليس لديك صلاحية للوصول إلى لوحة التحكم.');
            }
        }

        return back()->with('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة.');
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
