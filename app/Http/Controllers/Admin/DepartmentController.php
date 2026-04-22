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
            'name' => 'required|string|max:255',
        ]);

        // TRIK: Simpan input 'name' ke semua kolom bahasa bawaan database
        Department::create([
            'name_tk' => $request->name,
            'name_ru' => $request->name,
            'name_en' => $request->name,
        ]);
        
        auth()->user()->logAction("Created new department: " . $request->name);
        
        return back()->with('success', 'Bidang berhasil ditambahkan!');
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // TRIK: Update semua kolom bahasa sekaligus
        $department->update([
            'name_tk' => $request->name,
            'name_ru' => $request->name,
            'name_en' => $request->name,
        ]);

        auth()->user()->logAction("Updated department: " . $request->name);
        
        return back()->with('success', 'Nama bidang berhasil diperbarui!');
    }

    public function destroy(Department $department)
    {
        // Panggil fungsi getNameAttribute() bawaan modelmu untuk log
        $name = $department->name; 
        $department->delete();

        auth()->user()->logAction("Deleted department: " . $name);
        
        return back()->with('success', 'Bidang berhasil dihapus!');
    }
}