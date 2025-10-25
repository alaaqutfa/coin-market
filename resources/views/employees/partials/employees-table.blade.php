@foreach ($employees as $employee)
    <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200" data-id="{{ $employee->id }}">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
            <input type="checkbox" name="" id="" class="border border-gray-400 rounded" />
        </th>
        <th scope="row" class="px-6 py-4 text-center font-medium text-gray-900 whitespace-nowrap">
            {{ $employee->employee_code }}
        </th>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="name">
                {{ $employee->name }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="price">
                {{ $employee->salary }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="email">
                {{ $employee->email ?? 'لا يوجد' }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="phone">
                {{ $employee->phone ?? 'لا يوجد' }}
            </div>
        </td>
        <td class="min-w-64 px-6 py-4">
            <form id="resetPasswordForm{{ $employee->id }}" class="resetPasswordForm" method="POST" action="{{ route('employees.reset-password', $employee->id) }}">
                @csrf
                <input type="password" name="new_password" id="new_password" style="min-width: 200px;"
                    class="min-w-64 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm p-2.5"
                    placeholder="أدخل كلمة المرور جديدة" required>
            </form>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="start_date">
                {{ $employee->start_date->format('Y-m-d') }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="end_date"
                data-original-value="{{ $employee->end_date ? $employee->end_date->format('Y-m-d') : '' }}"
                data-empty-text="قيد العمل">
                @if ($employee->end_date)
                    {{ $employee->end_date->format('Y-m-d') }}
                @else
                    قيد العمل
                @endif
            </div>
            <small class="text-gray-500 text-xs">اتركه فارغاً لحذف تاريخ الانتهاء</small>
        </td>
        <td class="px-6 py-4">
            <div class="flex justify-center items-center gap-2">
                <button onclick="deleteEmployee({{ $employee->id }})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
                <a href="{{ route('employee.qr', $employee->id) }}" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid fa-qrcode"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach
