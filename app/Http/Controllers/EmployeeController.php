<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function addEmployee(Request $request, $employerId)
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required',
            'employee_email' => 'required|email|unique:employees,email',
            'employee_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = new Employee();
        $employee->name = $request->employee_name;
        $employee->email = $request->employee_email;

        if ($request->hasFile('employee_image')) {
            $employeeImage = time() . '_' . $request->file('employee_image')->getClientOriginalName();
            $request->file('employee_image')->move(public_path('employees'), $employeeImage);
            $employee->image = $employeeImage;
        }

        $employer = Employer::findOrFail($employerId);
        $employer->employees()->save($employee);

        return response()->json($employee);
    }
}

