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
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Document::query();
        
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }
        if ($request->filled('folder_id')) {
            $targetFolderId = $request->folder_id;
            $folderIds = [$targetFolderId]; // Array untuk menampung ID folder utama & anak-anaknya

            // Ambil semua data folder untuk dilacak
            $allFolders = Folder::select('id', 'parent_id')->get();
            
            // Fungsi rekursif (berulang) untuk melacak sub-folder
            $getChildren = function ($parentId) use (&$getChildren, &$folderIds, $allFolders) {
                $children = $allFolders->where('parent_id', $parentId);
                foreach ($children as $child) {
                    $folderIds[] = $child->id; // Masukkan ID anak ke dalam kantong pencarian
                    $getChildren($child->id); // Cari lagi apakah anak ini punya anak (cucu)
                }
            };
            
            $getChildren($targetFolderId); // Jalankan pelacakan!

            // Ubah pencarian: "Cari dokumen yang folder_id-nya ada di dalam daftar $folderIds"
            $query->whereIn('folder_id', $folderIds);
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
                    })
                    ->orWhere('is_public', true); // Pastikan dokumen publik juga ikut terbaca jika ada
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

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        if ($request->filled('folder_id')) {
            $targetFolderId = $request->folder_id;
            $folderIds = [$targetFolderId];

            $allFolders = Folder::select('id', 'parent_id')->get();
            $getChildren = function ($parentId) use (&$getChildren, &$folderIds, $allFolders) {
                $children = $allFolders->where('parent_id', $parentId);
                foreach ($children as $child) {
                    $folderIds[] = $child->id;
                    $getChildren($child->id);
                }
            };
            $getChildren($targetFolderId);

            $query->whereIn('folder_id', $folderIds);
        }

        $documents = $query->with(['folder', 'permissions.user', 'permissions.department'])
            ->latest()
            ->paginate(20);

        // Data untuk Dropdown di UI
        $users = User::where('id', '!=', $user->id)->get();
        $folders = Folder::with('department')->get();
        $departments = Department::all();

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
                'file_path' => $tempPath,
                'status' => 'processing', 
            ];
            
            $doc = Document::create($docData);

            // 4. CEK FORMAT: Apakah bisa di-Live Edit (Konversi)?
            $googleTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'];
            $isConvertible = in_array($extension, $googleTypes);

            // 5. PANGGIL KURIR (Masukkan ke Antrean)!
            // Dispatch akan membebaskan user tanpa harus menunggu Google API selesai
            UploadToGoogleDrive::dispatch($doc->id, $tempPath, $targetFolderId, $isConvertible);

            auth()->user()->logAction("Upload: " . $doc->title);

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

    // CHUNKED UPLOAD 
    public function uploadChunk(\Illuminate\Http\Request $request)
    {
        $receiver = new \Pion\Laravel\ChunkUpload\Receiver\FileReceiver('file', $request, \Pion\Laravel\ChunkUpload\Handler\HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded() === false) {
            return response()->json(['error' => 'Tidak ada file yang diunggah'], 400);
        }
        $save = $receiver->receive();

        if ($save->isFinished()) {
            $file = $save->getFile();
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = $file->hashName(); 
            $tempPath = $file->storeAs('temp_uploads', $fileName);
            $folderDb = \App\Models\Folder::find($request->folder_id);
            $targetFolderId = $folderDb->google_folder_id ?? env('GOOGLE_DRIVE_FOLDER_ID');

            $doc = \App\Models\Document::create([
                'title' => $request->title ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'folder_id' => $request->folder_id,
                'extension' => $extension,
                'file_size' => $file->getSize(),
                'owner_id' => auth()->id(),
                'file_path' => $tempPath,
                // 'status' => 'processing', 
                'is_public' => $request->is_public == '1' ? true : false, 

            ]);

            $googleTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'];
            $isConvertible = in_array($extension, $googleTypes);
            \App\Jobs\UploadToGoogleDrive::dispatch($doc->id, $tempPath, $targetFolderId, $isConvertible);
            return response()->json([
                'success' => true,
                'message' => 'Upload selesai & Antrean Google Drive dimulai!'
            ]);
        }
        $handler = $save->handler();
        return response()->json([
            'done' => $handler->getPercentageDone(),
            'status' => true
        ]);
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

        if ($document->folder_id != $request->folder_id && $document->google_file_id) {
            $newFolderDb = \App\Models\Folder::find($request->folder_id);
            $targetGoogleFolderId = $newFolderDb->google_folder_id ?? env('GOOGLE_DRIVE_FOLDER_ID');
            $googleDriveService->moveFile($document->google_file_id, $targetGoogleFolderId);
        }
        if ($document->title !== $request->title && $document->google_file_id) {
            $googleDriveService->renameItem($document->google_file_id, $request->title);
        }
        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'folder_id' => $request->folder_id,
        ]);

        auth()->user()->logAction("Updated document ID: " . $document->id);
        return redirect()->route('docs.index')->with('success', __('Dokumen berhasil diperbarui dan dipindahkan di Google Drive!'));
    }

    // 1. Tampilkan Halaman Sampah
    public function trash(\Illuminate\Http\Request $request)
    {
        $query = Document::onlyTrashed()->where('owner_id', auth()->id());

        // Jika user melakukan pencarian nama dokumen
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Jika user melakukan filter berdasarkan tanggal dihapus
        if ($request->filled('tanggal')) {
            $query->whereDate('deleted_at', $request->tanggal);
        }

        $documents = $query->orderBy('deleted_at', 'desc')->paginate(10);

        return view('documents.trash', compact('documents'));
    }

    // 2. Ubah fungsi destroy (Menghapus Sementara / Buang ke Sampah)
    public function destroy(Document $document, \App\Services\GoogleDriveService $googleDriveService)
    {
        if ($document->google_file_id) {
            $googleDriveService->trashFile($document->google_file_id);
        }

        $document->delete(); // Ini otomatis hanya mengisi kolom deleted_at

        return back()->with('success', 'Dokumen berhasil dipindahkan ke Sampah.');
    }

    // 3. Fungsi Restore (Mengembalikan dari Sampah)
    public function restore($id, \App\Services\GoogleDriveService $googleDriveService)
    {
        $document = Document::withTrashed()->findOrFail($id);

        if ($document->google_file_id) {
            $googleDriveService->restoreFile($document->google_file_id);
        }

        $document->restore(); // Mengosongkan kembali kolom deleted_at

        return redirect()->route('docs.trash')->with('success', 'Dokumen berhasil dikembalikan.');
    }

    // 4. Hapus Permanen
    public function forceDelete($id, \App\Services\GoogleDriveService $googleDriveService)
    {
        $document = Document::withTrashed()->findOrFail($id);

        if ($document->google_file_id) {
            $googleDriveService->permanentlyDeleteFile($document->google_file_id);
        }

        $document->forceDelete(); // Benar-benar hapus dari database

        return redirect()->route('docs.trash')->with('success', 'Dokumen dihapus secara permanen.');
    }

    public function editor(Document $document)
    {
        
        $user = auth()->user();
        $hasPermission = \App\Models\DocumentPermission::where('document_id', $document->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('department_id', $user->department_id);
            })->exists();

        // !$document->is_public 
        if ($user->role_level !== 'admin' && $document->owner_id !== $user->id && !$hasPermission && !$document->is_public) {
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


 public function share(\Illuminate\Http\Request $request, $id, \App\Services\GoogleDriveService $googleDriveService)
    {
        $doc = \App\Models\Document::findOrFail($id);

        // --- 1. JIKA MEMILIH SHARE PUBLIK ---
        if ($request->share_type === 'public') {
            $doc->update(['is_public' => true]);
            $googleRole = $request->access_level === 'write' ? 'writer' : 'reader';
            
            if ($doc->google_file_id) {
                // TANGKAP STATUS DARI GOOGLE
                $apiSuccess = $googleDriveService->grantPublicAccess($doc->google_file_id, $googleRole); 
                
                // JIKA GOOGLE MENOLAK
                if (!$apiSuccess) {
                    return back()->with('error', 'Status lokal berhasil diubah, TAPI gagal sinkronisasi ke Google Drive. Silakan cek/perbarui Token API Google Anda!');
                }
            }
            $statusText = $googleRole === 'writer' ? 'Editor' : 'Viewer';
            return back()->with('success', "Dokumen sekarang bersifat Publik ($statusText) dan tersinkronisasi di Google Drive!");
        }

        // --- 2. CABUT PUBLIK JIKA MENGUBAH JADI PRIVAT ---
        if ($doc->is_public) {
            $doc->update(['is_public' => false]);
            if ($doc->google_file_id) {
                $apiSuccess = $googleDriveService->removePublicAccess($doc->google_file_id);
                if (!$apiSuccess) {
                    return back()->with('error', 'Status lokal berhasil diubah, TAPI gagal mencabut akses publik di Google Drive. Silakan cek/perbarui Token API Google Anda!');
                }
            }
        }

        // --- 3. SIMPAN IZIN KE DATABASE LOKAL ---
        $doc->permissions()->create([
            'user_id' => $request->share_type === 'user' ? $request->user_id : null,
            'department_id' => $request->share_type === 'department' ? $request->department_id : null,
            'access_level' => $request->access_level, 
        ]);

        // --- 4. SINKRONISASI MASSAL KE GOOGLE DRIVE (TERMASUK DEPARTEMEN) ---
        $apiSuccess = true; // Set default true
        if ($doc->google_file_id) {
            $googleRole = $request->access_level === 'write' ? 'writer' : 'reader';
            
            if ($request->share_type === 'user' && $request->user_id) {
                $user = \App\Models\User::find($request->user_id);
                if ($user && $user->email) {
                    $apiSuccess = $googleDriveService->grantAccess($doc->google_file_id, $user->email, $googleRole);
                }
            } elseif ($request->share_type === 'department' && $request->department_id) {
                // SAKTI: Ambil SEMUA pegawai di dalam departemen itu yang punya email!
                $deptUsers = \App\Models\User::where('department_id', $request->department_id)->whereNotNull('email')->get();
                foreach ($deptUsers as $dUser) {
                    $result = $googleDriveService->grantAccess($doc->google_file_id, $dUser->email, $googleRole);
                    if (!$result) { $apiSuccess = false; } // Jika ada satu saja yang gagal, tandai error
                }
            }
        }

        // TAMPILKAN ALERT SESUAI HASILNYA
        if (!$apiSuccess) {
            return back()->with('error', 'Akses tersimpan di sistem, TAPI gagal disinkronkan ke Google Drive (Token API Kadaluarsa / Email Tidak Valid).');
        }

        return back()->with('success', 'Akses spesifik berhasil diberikan dan disinkronkan ke Google Drive!');
    }

    public function unshare(\App\Models\DocumentPermission $permission, \App\Services\GoogleDriveService $googleDriveService)
    {
        if ($permission->document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mencabut akses dokumen ini.');
        }

        $apiSuccess = true; // Set default true

        // --- SINKRONISASI PENCABUTAN MASSAL DARI GOOGLE DRIVE ---
        if ($permission->document->google_file_id) {
            if ($permission->user_id) {
                $user = \App\Models\User::find($permission->user_id);
                if ($user && $user->email) {
                    $apiSuccess = $googleDriveService->removeAccess($permission->document->google_file_id, $user->email);
                }
            } elseif ($permission->department_id) {
                // Cabut akses SEMUA orang di dalam departemen tersebut!
                $deptUsers = \App\Models\User::where('department_id', $permission->department_id)->whereNotNull('email')->get();
                foreach ($deptUsers as $dUser) {
                    $result = $googleDriveService->removeAccess($permission->document->google_file_id, $dUser->email);
                    if (!$result) { $apiSuccess = false; } // Jika ada satu saja yang gagal, tandai error
                }
            }
        }
        
        $permission->delete();
        auth()->user()->logAction("Revoked access permission ID: " . $permission->id . " for document ID: " . $permission->document_id);

        // TAMPILKAN ALERT JIKA GOOGLE MENOLAK
        if (!$apiSuccess) {
            return back()->with('error', 'Akses dicabut dari sistem, TAPI gagal dicabut dari Google Drive. Silakan cek/perbarui Token API Google Anda!');
        }

        return back()->with('success', __('Akses berhasil dicabut dari sistem dan dari Google Drive!'));
    }

    public function unsharePublic($id, \App\Services\GoogleDriveService $googleDriveService)
    {
        $document = \App\Models\Document::findOrFail($id);

        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengubah dokumen ini.');
        }

        // Ubah di database lokal
        $document->is_public = false;
        $document->save();

        $apiSuccess = true;

        // SAATNYA MENGHUBUNGI GOOGLE UNTUK MENCABUT KUNCINYA
        if ($document->google_file_id) {
            $apiSuccess = $googleDriveService->removePublicAccess($document->google_file_id);
        }

        // TAMPILKAN ALERT JIKA GOOGLE MENOLAK
        if (!$apiSuccess) {
            return back()->with('error', 'Akses publik dicabut dari sistem, TAPI gagal dicabut dari Google Drive. Silakan cek/perbarui Token API Google Anda!');
        }

        return back()->with('success', 'Akses publik berhasil dicabut dari sistem dan Google Drive.');
    }

    
    public function retrySync($id)
    {
        $doc = \App\Models\Document::findOrFail($id);

        // 1. Jika sudah ada ID Google, berarti sudah aman
        if ($doc->google_file_id) {
            return back()->with('success', 'Dokumen ini sudah tersinkronisasi dengan aman di Cloud.');
        }

        $tempPath = $doc->file_path;

        // 2. Jika path hilang (karena sistem lama) atau file fisik sudah lenyap di server
        if ($tempPath == 'Processing...' || !\Illuminate\Support\Facades\Storage::exists($tempPath)) {
             $doc->update(['status' => 'failed', 'file_path' => 'Gagal: File lokal hilang']);
             return back()->with('error', 'File fisik di server lokal sudah hilang. Silakan hapus data ini dan unggah ulang dokumen.');
        }

        // 3. Panggil ulang Kurirnya (Job)!
        $googleTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'];
        $isConvertible = in_array(strtolower($doc->extension), $googleTypes);
        $targetFolderId = $doc->folder->google_folder_id ?? env('GOOGLE_DRIVE_FOLDER_ID');

        // Kembalikan status ke processing
        $doc->update(['status' => 'processing']); 

        \App\Jobs\UploadToGoogleDrive::dispatch($doc->id, $tempPath, $targetFolderId, $isConvertible);

        return back()->with('success', 'Sistem sedang memancing ulang sinkronisasi ke Google Drive...');
    }

    // ====================================================================
    // FITUR BARU: FILE EXPLORER (TREE VIEW & AJAX)
    // ====================================================================
    
    public function explorer(Request $request)
    {
        // Ambil semua departemen beserta foldernya untuk membentuk "Hutan" (Pohon Direktori)
        $departments = Department::with(['folders' => function($q) {
            $q->orderBy('name', 'asc');
        }])->get();

        return view('documents.explorer', compact('departments'));
    }

    public function fetchExplorerFiles(Request $request, $folder_id)
    {
        $user = auth()->user();
        $folder = Folder::with('department')->findOrFail($folder_id);

        // Kunci pencarian hanya pada folder yang di-klik
        $query = Document::where('folder_id', $folder_id);

        // LOGIKA HAK AKSES (Sama persis dengan halaman Index)
        if ($user->role_level !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhereHas('permissions', function ($pq) use ($user) {
                        $pq->where('user_id', $user->id)
                            ->orWhere('department_id', $user->department_id);
                    })
                    ->orWhere('is_public', true);
            });
        }

        $documents = $query->with(['owner', 'folder', 'permissions.user', 'permissions.department'])
                           ->latest()
                           ->get(); 

        // Render HTML kepingan tabel file
        $html = view('partials.explorer-files', compact('documents', 'folder'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'folder_name' => $folder->name,
            'department_name' => $folder->department->name ?? 'Umum'
        ]);
    }
    
}