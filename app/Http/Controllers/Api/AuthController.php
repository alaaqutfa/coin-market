<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('logout');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string', // يمكن أن يكون email أو employee_code أو phone
            'password' => 'required|string',
        ]);

        $identifier = $data['identifier'];

        // نبحث بالـ email أو employee_code أو phone
        $employee = Employee::where('email', $identifier)
            ->orWhere('employee_code', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$employee || !Hash::check($data['password'], $employee->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        // أنشئ توكن (Sanctum)
        $token = $employee->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'employee' => $employee,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
