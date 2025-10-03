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
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="start_date">
                {{ $employee->start_date->format('Y-m-d') }}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="editable-field" contenteditable="true" data-field="end_date">

                @if ($employee->end_date)
                    @dd($employee->end_date)
                    {{ $employee->end_date->format('Y-m-d') }}
                @else
                    قيد العمل
                @endif
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="flex justify-center items-center gap-2">
                <button onclick="deleteEmployee({{ $employee->id }})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
