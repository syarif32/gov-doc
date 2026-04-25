<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\Folder;
use App\Models\Department;
use App\Models\DocumentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
 public function index(Request $request)
    {
        $user = auth()->user();
        $query = Document::query();

        // --- 1. FITUR PENCARIAN & SORTIR SUPER LENGKAP ---
        // Cari berdasarkan nama dokumen
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Sortir berdasarkan Tanggal Spesifik
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // Sortir berdasarkan Kategori/Folder
        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        // --- 2. LOGIKA HAK AKSES ---
        if ($user->role_level === 'admin') {
            $documents = $query->with(['owner', 'folder', 'permissions.user', 'permissions.department'])->latest()->paginate(20);
        } else {
            $documents = $query->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('permissions', function ($pq) use ($user) {
                      $pq->where('user_id', $user->id)
                         ->orWhere('department_id', $user->department_id);
                  });
            })
            ->with(['owner', 'folder', 'permissions.user', 'permissions.department'])
            ->latest()
            ->paginate(20);
        }
        
        // Data untuk Dropdown di UI
        $users = User::where('id', '!=', $user->id)->get();
        $folders = Folder::with('department')->get();
        $departments = Department::all();

        return view('documents.index', compact('documents', 'users', 'folders', 'departments'));
    }

    // --- FUNGSI BARU: Bikin File Tanpa Upload ---
    public function store(Request $request, \App\Services\GoogleDriveService $googleDriveService)
    {
        // SOLUSI MASALAH 3 (Infinite Loading): Bungkus dengan Try-Catch
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'file' => 'required|file|max:51200',
                'folder_id' => 'required|exists:folders,id',
            ]);

            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $googleTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'];

            // Dapatkan nama Departemen & Tahun untuk Auto-Folder
            $folderDb = \App\Models\Folder::with('department')->find($request->folder_id);
            $autoFolderName = ($folderDb->department->name ?? 'Umum') . " - " . date('Y');

            $docData = [
                'title' => $request->title,
                'folder_id' => $request->folder_id,
                'extension' => $extension,
                'file_size' => $file->getSize(),
                'owner_id' => auth()->id(),
            ];

            if (in_array($extension, $googleTypes)) {
                // Cari atau buat foldernya di Google Drive
                $targetFolderId = $googleDriveService->getOrCreateFolder($autoFolderName);
                
                // Lempar ID folder tersebut agar file masuk ke dalamnya
                $googleFileId = $googleDriveService->uploadAndConvert($file, $request->title, $targetFolderId);
                
                $docData['google_file_id'] = $googleFileId;
                $docData['file_path'] = 'Cloud/GoogleDrive';
            } else {
                $path = $file->store('private/documents/' . $request->folder_id);
                $docData['file_path'] = $path;
            }

            $doc = Document::create($docData);
            auth()->user()->logAction("Uploaded document: " . $doc->title);
            
            return back()->with('success', __('Dokumen berhasil diunggah dan terorganisir.'));

        } catch (\Exception $e) {
            // Jika ada error (termasuk untuk user biasa), tampilkan errornya! Tidak loading terus.
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function storeBlank(Request $request, \App\Services\GoogleDriveService $googleDriveService)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:doc,xls,ppt',
                'folder_id' => 'required|exists:folders,id',
            ]);

            $folderDb = \App\Models\Folder::with('department')->find($request->folder_id);
            $autoFolderName = ($folderDb->department->name ?? 'Umum') . " - " . date('Y');

            // Cari atau buat foldernya
            $targetFolderId = $googleDriveService->getOrCreateFolder($autoFolderName);

            // Bikin file kosong di dalam folder tersebut
            $googleFileId = $googleDriveService->createBlankFile($request->title, $request->type, $targetFolderId);

            $doc = Document::create([
                'title' => $request->title,
                'folder_id' => $request->folder_id,
                'extension' => $request->type . 'x',
                'file_size' => 0,
                'owner_id' => auth()->id(),
                'google_file_id' => $googleFileId,
                'file_path' => 'Cloud/GoogleDrive',
            ]);

            auth()->user()->logAction("Created new blank document: " . $doc->title);
            return redirect()->route('docs.editor', $doc->id)->with('success', __('Dokumen baru berhasil dibuat!'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Document $document)
    {
        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'folder_id' => 'required|exists:folders,id',
        ]);

        // SOLUSI MASALAH 4: Jika folder_id berubah, pindahkan juga di Google Drive
        if ($document->folder_id != $request->folder_id && $document->google_file_id) {
            $googleDriveService = app(\App\Services\GoogleDriveService::class);
            
            $newFolderDb = \App\Models\Folder::with('department')->find($request->folder_id);
            $newFolderName = ($newFolderDb->department->name ?? 'Umum') . " - " . date('Y');
            
            $newGoogleFolderId = $googleDriveService->getOrCreateFolder($newFolderName);
            $googleDriveService->moveFile($document->google_file_id, $newGoogleFolderId);
        }

        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'folder_id' => $request->folder_id,
        ]);

        auth()->user()->logAction("Updated document ID: " . $document->id);
        return redirect()->route('docs.index')->with('success', __('Dokumen berhasil diperbarui.'));
    }

    public function destroy(Document $document)
    {
        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') abort(403);
        
        // SOLUSI MASALAH 5: Hapus juga dari Google Drive (Masuk Trash)
        if ($document->google_file_id) {
            $googleDriveService = app(\App\Services\GoogleDriveService::class);
            $googleDriveService->deleteFile($document->google_file_id);
        }

        if ($document->file_path && $document->file_path !== 'Cloud/GoogleDrive') {
            Storage::delete($document->file_path);
        }
        
        $document->delete();
        return back()->with('success', __('Dokumen berhasil dihapus dari sistem dan Google Drive.'));
    }
   
    public function editor(Document $document)
    {
        // Cek Keamanan: Apakah user berhak melihat file ini?
        $user = auth()->user();
        $hasPermission = \App\Models\DocumentPermission::where('document_id', $document->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('department_id', $user->department_id);
            })->exists();

        if ($user->role_level !== 'admin' && $document->owner_id !== $user->id && !$hasPermission) {
            abort(403, 'Akses Ditolak');
        }

        if (!$document->google_file_id) {
            abort(404, 'Dokumen ini bukan format Google Editor.');
        }

        return view('documents.editor', compact('document'));
    }

    // Fungsi download, edit, update, destroy, dan share tetap sama seperti kode aslimu
    public function download(Document $document) {
        $user = auth()->user();
        $hasPermission = DocumentPermission::where('document_id', $document->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('department_id', $user->department_id);
            })->exists();

        if ($user->role_level !== 'admin' && $document->owner_id !== $user->id && !$hasPermission) {
            abort(403);
        }
        return Storage::download($document->file_path, $document->title . '.' . $document->extension);
    }

    public function edit(Document $document)
{
    // Hanya owner atau admin yang bisa edit metadata
    if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
        abort(403, 'Unauthorized access.');
    }

    // Ambil daftar semua folder untuk dropdown
    $folders = \App\Models\Folder::with('department')->get();

    return view('documents.edit', compact('document', 'folders'));
}


   public function share(Request $request, Document $document)
{
    if ($document->owner_id !== auth()->id()) {
        abort(403);
    }

    $request->validate([
        'share_type' => 'required|in:user,department',
        'user_id' => 'required_if:share_type,user|exists:users,id',
        'department_id' => 'required_if:share_type,department|exists:departments,id',
        'access_level' => 'required|in:read,write'
    ]);

    DocumentPermission::updateOrCreate(
        [
            'document_id' => $document->id, 
            'user_id' => $request->share_type === 'user' ? $request->user_id : null,
            'department_id' => $request->share_type === 'department' ? $request->department_id : null,
        ],
        ['access_level' => $request->access_level]
    );

    return back()->with('success', __('Berhasil membagikan dokumen ke grup/user'));
}
/**
 * Mencabut akses (Unshare) dari user atau departemen tertentu.
 */
public function unshare(\App\Models\DocumentPermission $permission)
{
    // Cek keamanan: Pastikan hanya pemilik asli dokumen atau admin yang bisa mencabut akses
    if ($permission->document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
        abort(403, 'Anda tidak memiliki izin untuk mencabut akses dokumen ini.');
    }

    $permission->delete();

    auth()->user()->logAction("Revoked access permission ID: " . $permission->id . " for document ID: " . $permission->document_id);

    return back()->with('success', __('Akses dokumen berhasil dicabut.'));
}
}