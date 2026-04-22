<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Department;
class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // 1. Ambil Departemen beserta Folder yang ada di dalamnya
    $departments = \App\Models\Department::with('folders')->get();

    // 2. Ambil SEMUA folder untuk pilihan "Folder Induk" di modal tambah
    // Variabel inilah yang dicari oleh error "Undefined variable $folders"
    $folders = \App\Models\Folder::all();

    // 3. Kirim kedua variabel tersebut ke tampilan
    return view('admin.folders.index', compact('departments', 'folders'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:folders,id',
        'department_id' => 'nullable|exists:departments,id',
    ]);

    \App\Models\Folder::create($request->all());

    return back()->with('success', 'Folder berhasil dibuat!');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Folder $folder)
{
    // Ambil data untuk dropdown di form edit
    $departments = \App\Models\Department::all();
    $allFolders = Folder::where('id', '!=', $folder->id)->get(); // Agar folder tidak jadi parent dirinya sendiri

    return view('admin.folders.edit', compact('folder', 'departments', 'allFolders'));
}

public function update(Request $request, Folder $folder)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'department_id' => 'required|exists:departments,id',
        'parent_id' => 'nullable|exists:folders,id',
    ]);

    $folder->update($request->all());

    return redirect()->route('admin.folders.index')->with('success', 'Folder berhasil diperbarui!');
}

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $folder = \App\Models\Folder::findOrFail($id);

        // Opsional: Cek apakah folder punya isi sebelum dihapus
        if ($folder->documents()->count() > 0) {
            return back()->with('error', 'Gagal! Folder tidak bisa dihapus karena masih berisi dokumen.');
        }

        $folder->delete();

        // INI YANG MEMBUAT HALAMAN TIDAK BLANK LAGI
        return back()->with('success', 'Folder berhasil dihapus secara permanen.');
    }
}
