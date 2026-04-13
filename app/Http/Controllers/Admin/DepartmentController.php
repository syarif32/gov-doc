<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users')->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_tk' => 'required',
            'name_ru' => 'required',
            'name_en' => 'required',
        ]);

        Department::create($request->all());
        auth()->user()->logAction("Created new department");
        return back();
    }
}
