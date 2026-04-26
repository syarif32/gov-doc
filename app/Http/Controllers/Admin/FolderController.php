<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Department;
use App\Services\GoogleDriveService; // Panggil servis cerdas kita

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Ambil Departemen beserta Folder yang ada di dalamnya
        $departments = Department::with('folders')->get();

        // 2. Ambil SEMUA folder untuk pilihan "Folder Induk" di modal tambah
        $folders = Folder::all();

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
     * (DIUPDATE: Sinkronisasi Buat Folder ke Google Drive)
     */
    public function store(Request $request, GoogleDriveService $googleDriveService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        // 1. Cek apakah ini Sub-Folder (Punya Parent)
        $parentGoogleId = null;
        if ($request->filled('parent_id')) {
            $parentFolder = Folder::find($request->parent_id);
            $parentGoogleId = $parentFolder->google_folder_id; // Ambil ID Google folder induknya
        }

        // 2. Suruh Google membuat folder fisik di Drive
        $googleFolderId = $googleDriveService->createSpecificFolder($request->name, $parentGoogleId);

        // 3. Simpan data ke Database Laravel beserta ID dari Google
        Folder::create([
            'name' => $request->name,
            'department_id' => $request->department_id,
            'parent_id' => $request->parent_id,
            'google_folder_id' => $googleFolderId // Ini kunci penghubungnya!
        ]);

        return back()->with('success', 'Folder berhasil dibuat di Sistem dan terintegrasi dengan Google Drive!');
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
        $departments = Department::all();
        $allFolders = Folder::where('id', '!=', $folder->id)->get(); // Agar folder tidak jadi parent dirinya sendiri

        return view('admin.folders.edit', compact('folder', 'departments', 'allFolders'));
    }

    /**
     * Update the specified resource in storage.
     * (DIUPDATE: Sinkronisasi Ubah Nama di Google Drive)
     */
    public function update(Request $request, Folder $folder, GoogleDriveService $googleDriveService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        // Jika namanya diubah oleh admin DAN folder ini terhubung ke Google Drive
        if ($folder->name !== $request->name && $folder->google_folder_id) {
            // Suruh robot mengubah nama folder langsung di Cloud
            $googleDriveService->renameItem($folder->google_folder_id, $request->name);
        }

        // Simpan perubahan ke Database
        $folder->update($request->all());

        return redirect()->route('admin.folders.index')->with('success', 'Folder berhasil diperbarui di Sistem dan Google Drive!');
    }

    /**
     * Remove the specified resource from storage.
     * (DIUPDATE: Hapus folder juga masuk ke Trash Google Drive)
     */
    public function destroy(string $id, GoogleDriveService $googleDriveService)
    {
        $folder = Folder::findOrFail($id);

        // Cek apakah folder punya isi dokumen sebelum dihapus
        if ($folder->documents()->count() > 0) {
            return back()->with('error', 'Gagal! Folder tidak bisa dihapus karena masih berisi dokumen.');
        }

        // Jika folder kosong dan terhubung ke Drive, buang ke tong sampah Google!
        if ($folder->google_folder_id) {
            $googleDriveService->deleteFile($folder->google_folder_id);
        }

        $folder->delete();

        return back()->with('success', 'Folder berhasil dihapus secara permanen dari Sistem dan Google Drive.');
    }
}