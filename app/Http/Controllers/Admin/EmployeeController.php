<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{

    public function create()
    {
        return view('employees.create');
    }

    // مولد كود وظيفي فريد (يمكن تعديله حسب رغبتك)
    private function generateUniqueEmployeeCode()
    {
        do {
            $code = 'EMP' . mt_rand(10000, 99999);
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }

    // تابع إنشاء الموظف
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'employee_code' => ['nullable', 'string', 'max:50', Rule::unique('employees', 'employee_code')],
            'salary'        => 'required|numeric',
            'start_date'    => 'required|date',
            'email'         => ['nullable', 'email', 'max:255', Rule::unique('employees', 'email')],
            'phone'         => ['nullable', 'string', 'max:30', Rule::unique('employees', 'phone')],
            'password'      => 'required|string|min:6',
        ]);

        $employeeCode = $data['employee_code'] ?? $this->generateUniqueEmployeeCode();

        $employee = Employee::create([
            'name'          => $data['name'],
            'employee_code' => $employeeCode,
            'salary'        => $data['salary'],
            'start_date'    => $data['start_date'],
            'email'         => $data['email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'password'      => Hash::make($data['password']),
        ]);

        return response()->json([
            'message'  => 'تم إنشاء الموظف',
            'employee' => $employee,
        ], 201);
    }
}
