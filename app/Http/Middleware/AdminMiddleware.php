<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // تحقق إن المستخدم مسجل دخول
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        // السماح فقط للأدوار التالية
        $allowedRoles = [1, 2, 3];
        // dd(Auth::user()->role->id);
        if (!in_array(Auth::user()->role->id, $allowedRoles)) {
            abort(403, 'ليس لديك صلاحية لدخول لوحة التحكم');
        }

        return $next($request);
    }
}
