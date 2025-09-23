<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EmployeeController extends Controller
{

    public function index()
    {
        $employees = Employee::orderBy('employee_code', 'desc')->get();
        return view('employees.view', compact('employees'));
    }

    public function show_employee_code_list()
    {
        $employees = Employee::orderBy('employee_code', 'desc')->get();
        return view('employees.employee_code_list', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    // عرض تفاصيل موظف معين
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.show', compact('employee'));
    }

    // عرض نموذج تعديل الموظف
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    // مولد كود وظيفي فريد
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

    // تابع تحديث بيانات الموظف
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        // قواعد التحقق الأساسية
        $rules = [
            'name'          => 'sometimes|required|string|max:255',
            'employee_code' => ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('employees', 'employee_code')->ignore($employee->id)],
            'salary'        => 'sometimes|required|numeric',
            'start_date'    => 'sometimes|required|date',
            'email'         => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employee->id)],
            'phone'         => ['sometimes', 'nullable', 'string', 'max:30', Rule::unique('employees', 'phone')->ignore($employee->id)],
            'password'      => 'sometimes|nullable|string|min:6',
        ];

        // التحقق من البيانات المرسلة فقط
        $data = $request->validate($rules);

        $updateData = [];

        // إضافة الحقول المرسلة فقط
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['employee_code'])) {
            $updateData['employee_code'] = $data['employee_code'];
        }

        if (isset($data['salary'])) {
            $updateData['salary'] = $data['salary'];
        }

        if (isset($data['start_date'])) {
            $updateData['start_date'] = $data['start_date'];
        }

        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }

        if (isset($data['phone'])) {
            $updateData['phone'] = $data['phone'];
        }

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $employee->update($updateData);

        return response()->json([
            'message'  => 'تم تحديث بيانات الموظف',
            'employee' => $employee,
        ], 200);
    }

    // تابع حذف الموظف
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'message' => 'تم حذف الموظف بنجاح',
        ], 200);
    }

    public function isWorkingDay($employee, Carbon $date)
{
    $schedule = $employee->workSchedules()
        ->where('day_of_week', $date->dayOfWeek) // 0-6
        ->get();

    foreach ($schedule as $rule) {
        if (!$rule->is_alternate) {
            return true; // دوام ثابت
        }

        // لو دوام أسبوع بعد أسبوع
        if ($date->weekOfYear % 2 == 0) {
            return true; // مثال: الأسابيع الزوجية
        }
    }

    return false;
}
}
