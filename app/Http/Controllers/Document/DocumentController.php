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

        // --- FITUR SORTIR ---
        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($user->role_level === 'admin') {
            $documents = $query->with(['owner', 'folder'])->latest()->paginate(20);
        } else {
            $documents = $query->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('permissions', function ($pq) use ($user) {
                      $pq->where('user_id', $user->id)
                        ->orWhere('department_id', $user->department_id);
                  });
            })
            ->with(['owner', 'folder'])
            ->latest()
            ->paginate(20);
        }
        $documents = $query->with(['owner', 'folder', 'permissions.user', 'permissions.department'])
                       ->latest()
                       ->paginate(20);
        // Data untuk Dropdown di UI
        $users = User::where('id', '!=', $user->id)->get();
        $folders = Folder::with('department')->get();
        $departments = Department::all();
        
        // List tahun untuk filter (dari 5 tahun lalu sampai sekarang)
        $years = range(date('Y'), date('Y') - 5);

        return view('documents.index', compact('documents', 'users', 'folders', 'departments', 'years'));
    }

    // Inject Service langsung ke fungsi store agar lebih efisien memori
    public function store(Request $request, \App\Services\GoogleDriveService $googleDriveService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:51200', // 50MB Max
            'folder_id' => 'required|exists:folders,id',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $googleTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'];

        // 1. Siapkan Kerangka Data (DRY Principle - Jangan diulang)
        $docData = [
            'title' => $request->title,
            'folder_id' => $request->folder_id,
            'extension' => $extension,
            'file_size' => $file->getSize(),
            'owner_id' => auth()->id(),
        ];

        // 2. Dynamic Routing (Cabang Keputusan)
        if (in_array($extension, $googleTypes)) {
            // Lempar ke Google Drive Bot
            $googleFileId = $googleDriveService->uploadAndConvert($file, $request->title);
            $docData['google_file_id'] = $googleFileId;
            $docData['file_path'] = 'Cloud/GoogleDrive';
        } else {
            // Simpan Lokal (PDF, ZIP, Image, dll)
            $path = $file->store('private/documents/' . $request->folder_id);
            $docData['file_path'] = $path;
        }

        // 3. Eksekusi Database cukup 1 kali saja
        $doc = Document::create($docData);

        auth()->user()->logAction("Uploaded document: " . $doc->title);
        return back()->with('success', __('Dokumen berhasil diunggah.'));
    }

    // TAMBAHKAN FUNGSI BARU INI UNTUK MENAMPILKAN EDITOR GOOGLE DOCS
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

    public function update(Request $request, Document $document)
{
    if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
        abort(403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'folder_id' => 'required|exists:folders,id', // Tambahkan validasi folder
    ]);

    $document->update([
        'title' => $request->title,
        'description' => $request->description,
        'folder_id' => $request->folder_id, // Update folder tujuan
    ]);

    auth()->user()->logAction("Updated document and moved to folder ID: " . $document->folder_id);

    return redirect()->route('docs.index')->with('success', __('Document updated and moved successfully'));
}

    public function destroy(Document $document) {
        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') abort(403);
        Storage::delete($document->file_path);
        $document->delete();
        return back()->with('success', __('Document deleted successfully'));
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