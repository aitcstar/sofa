<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController; // ✅ ضيف السطر ده

class DashboardController extends  BaseController
{
    public function __construct()
    {
        // لازم الموظف يكون مسجل دخول
        $this->middleware('auth:employee');
    }

    public function index()
    {
        $employee = Auth::guard('employee')->user();
        //$permissions = $employee->role->permissions->pluck('name')->toArray();

        //dd($permissions);

        return view('employee.dashboard', compact('employee'));
    }
}
