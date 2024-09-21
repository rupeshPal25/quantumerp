<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployerController extends Controller
{
    public function index()
    {
        $employers = Employer::all();
        return view('admin.employers.index', compact('employers'));
    }


    public function store(Request $request)
    {
        // Validate employer and employee data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:employers,email',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Employer image
            'employee_name.*' => 'required', // Validate each employee name
            'employee_email.*' => 'required|email|unique:employees,email', // Validate each employee email
            'employee_image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Employee images
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Save employer data
        $employer = new Employer();
        $employer->name = $request->name;
        $employer->email = $request->email;

        // Handle employer image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $imageName); // Save in 'images'
            $employer->image = $imageName;
        }

        $employer->save();

        // Save each employee and handle employee image uploads
        foreach ($request->employee_name as $key => $name) {
            $employee = new Employee();
            $employee->name = $name;
            $employee->email = $request->employee_email[$key];

            // Handle employee image upload
            if ($request->hasFile('employee_image.' . $key)) {
                $employeeImage = time() . '_' . $request->file('employee_image')[$key]->getClientOriginalName();
                $request->file('employee_image')[$key]->move(public_path('employees'), $employeeImage); // Save in 'employees'
                $employee->image = $employeeImage;
            }

            // Associate employee with employer
            $employer->employees()->save($employee);
        }

        // Return JSON response
        return response()->json([
            'id' => $employer->id,
            'name' => $employer->name,
            'email' => $employer->email,
            'image' => $employer->image,
        ]);
    }


    public function show($id)
    {
        // Fetch employer and their employees
        $employer = Employer::with('employees')->findOrFail($id);

        return view('admin.employers.show', compact('employer'));
    }


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


    public function validateEmail(Request $request)
    {
        $exists = Employer::where('email', $request->email)->exists() || Employee::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}
