<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\Folder;
use App\Models\Department;
use App\Models\DocumentPermission;
use Illuminate\Http\Request;
 use App\Jobs\UploadToGoogleDrive;
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
            $documents = $query->where(function ($q) use ($user) {
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
    // Fungsi Baru: Menampilkan HANYA dokumen milik user yang login
    public function myDocuments(Request $request)
    {
        $user = auth()->user();

        // Kunci query HANYA untuk dokumen yang dibuat oleh user ini
        $query = Document::where('owner_id', $user->id);

        // --- FITUR PENCARIAN & SORTIR (Sama seperti index) ---
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }
        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        $documents = $query->with(['folder', 'permissions.user', 'permissions.department'])
            ->latest()
            ->paginate(20);

        // Data untuk Dropdown di UI
        $users = User::where('id', '!=', $user->id)->get();
        $folders = Folder::with('department')->get();
        $departments = Department::all();

        // Kita buat view baru bernama 'my-documents'
        return view('documents.my-documents', compact('documents', 'users', 'folders', 'departments'));
    }

   
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'file' => 'required|file|max:202400', // 50MB Max
                'folder_id' => 'required|exists:folders,id',
            ]);

            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            // 1. Simpan ke gudang sementara di server lokal
            $tempPath = $file->store('temp_uploads');

            // 2. Ambil ID Folder Tujuan Google
            $folderDb = \App\Models\Folder::find($request->folder_id);
            $targetFolderId = $folderDb->google_folder_id ?? env('GOOGLE_DRIVE_FOLDER_ID');

            // 3. Simpan data awal ke Database (Tandai status masih memproses)
            $docData = [
                'title' => $request->title,
                'folder_id' => $request->folder_id,
                'extension' => $extension,
                'file_size' => $file->getSize(),
                'owner_id' => auth()->id(),
                'file_path' => 'Processing...', // Tanda bahwa sedang antre
            ];
            
            $doc = Document::create($docData);

            // 4. CEK FORMAT: Apakah bisa di-Live Edit (Konversi)?
            $googleTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'];
            $isConvertible = in_array($extension, $googleTypes);

            // 5. PANGGIL KURIR (Masukkan ke Antrean)!
            // Dispatch akan membebaskan user tanpa harus menunggu Google API selesai
            UploadToGoogleDrive::dispatch($doc->id, $tempPath, $targetFolderId, $isConvertible);

            auth()->user()->logAction("Mengantrekan upload: " . $doc->title);

            // User langsung diarahkan balik dalam waktu 0.1 detik!
            return back()->with('success', __('File sedang diunggah ke awan. Anda bisa menutup halaman atau melakukan hal lain!'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    // --- FUNGSI: Bikin File Tanpa Upload ---
    public function storeBlank(Request $request, \App\Services\GoogleDriveService $googleDriveService)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:doc,xls,ppt',
                'folder_id' => 'required|exists:folders,id',
            ]);

            // PENYESUAIAN: Ambil ID Google Drive dari folder yang dipilih
            $folderDb = \App\Models\Folder::find($request->folder_id);
            // Jika folder tersebut punya ID Google, gunakan. Jika tidak, lempar ke folder root utama di .env
            $targetFolderId = $folderDb->google_folder_id ?? env('GOOGLE_DRIVE_FOLDER_ID');

            // Bikin file kosong di dalam folder tersebut secara presisi
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
    // --- FUNGSI: Edit Dokumen ---
    public function update(Request $request, Document $document, \App\Services\GoogleDriveService $googleDriveService)
    {
        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'folder_id' => 'required|exists:folders,id',
        ]);

        // 1. Logika Pindah Folder (Move)
        if ($document->folder_id != $request->folder_id && $document->google_file_id) {
            // Ambil data folder tujuan yang baru
            $newFolderDb = \App\Models\Folder::find($request->folder_id);

            // Ambil ID Google Folder-nya (jika kosong, lempar ke folder utama di .env)
            $targetGoogleFolderId = $newFolderDb->google_folder_id ?? env('GOOGLE_DRIVE_FOLDER_ID');

            // Suruh robot memindahkan file ke folder tujuan yang SUDAH ADA
            $googleDriveService->moveFile($document->google_file_id, $targetGoogleFolderId);
        }

        // 2. BONUS: Logika Ganti Nama (Rename)
        if ($document->title !== $request->title && $document->google_file_id) {
            // Karena fungsi renameItem di service kita itu universal, 
            // kita bisa memakainya untuk me-rename file juga!
            $googleDriveService->renameItem($document->google_file_id, $request->title);
        }

        // 3. Simpan perubahan ke Database
        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'folder_id' => $request->folder_id,
        ]);

        auth()->user()->logAction("Updated document ID: " . $document->id);
        return redirect()->route('docs.index')->with('success', __('Dokumen berhasil diperbarui dan dipindahkan di Google Drive!'));
    }

    public function destroy(Document $document)
    {
        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin')
            abort(403);

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
    public function download(Document $document)
    {
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