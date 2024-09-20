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
        $validator = Validator::make($request->all(), [
            'employer_name' => 'required',
            'employer_email' => 'required|email|unique:employers,email',
            'employee_name.*' => 'required',
            'employee_email.*' => 'required|email|unique:employees,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Save employer
        $employer = new Employer();
        $employer->name = $request->employer_name;
        $employer->email = $request->employer_email;
        if ($request->hasFile('employer_image')) {
            $employer->image = $request->file('employer_image')->store('employer_images');
        }
        $employer->save();

        // Save employees
        foreach ($request->employee_name as $key => $name) {
            $employee = new Employee();
            $employee->name = $name;
            $employee->email = $request->employee_email[$key];
            if ($request->hasFile('employee_image')) {
                $employee->image = $request->file('employee_image')[$key]->store('employee_images');
            }
            $employer->employees()->save($employee);
        }

        return response()->json(['success' => true]);
    }

    public function validateEmail(Request $request)
    {
        $exists = Employer::where('email', $request->email)->exists() || Employee::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}
?>