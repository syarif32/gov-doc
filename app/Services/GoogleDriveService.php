<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Exception;

class GoogleDriveService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->refreshToken(env('GOOGLE_REFRESH_TOKEN'));
        
        $this->client->addScope(Google_Service_Drive::DRIVE);
        $this->service = new Google_Service_Drive($this->client);
    }

    /**
     * SOLUSI MASALAH 1 & 2: Cek apakah folder sudah ada. Jika belum, buat baru.
     * Ini mencegah folder menumpuk dengan nama yang sama.
     */
    public function getOrCreateFolder($folderName)
    {
        $parentId = env('GOOGLE_DRIVE_FOLDER_ID');

        // 1. Cari dulu apakah foldernya sudah ada
        $query = "mimeType='application/vnd.google-apps.folder' and name='" . str_replace("'", "\'", $folderName) . "' and '" . $parentId . "' in parents and trashed=false";
        
        try {
            $response = $this->service->files->listFiles(['q' => $query, 'fields' => 'files(id, name)']);
            
            if (count($response->files) > 0) {
                return $response->files[0]->id; // Kembalikan ID folder yang sudah ada
            }

            // 2. Jika tidak ada, buat folder baru
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$parentId]
            ]);

            $folder = $this->service->files->create($fileMetadata, ['fields' => 'id']);
            return $folder->id;

        } catch (Exception $e) {
            \Log::error("Gagal mencari/membuat folder: " . $e->getMessage());
            return $parentId; // Fallback ke folder utama jika gagal
        }
    }

    /**
     * Upload File (Sekarang menerima parameter Custom Folder ID)
     */
    public function uploadAndConvert($file, $title, $customFolderId = null)
    {
        $folderId = $customFolderId ?? env('GOOGLE_DRIVE_FOLDER_ID');

        try {
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $title,
                'parents' => [$folderId], // File sekarang masuk ke dalam sub-folder yang benar!
            ]);

$extension = method_exists($file, 'getClientOriginalExtension') 
                ? strtolower($file->getClientOriginalExtension()) 
                : strtolower($file->getExtension());
            $mimeMap = [
                'doc' => 'application/vnd.google-apps.document', 'docx' => 'application/vnd.google-apps.document',
                'xls' => 'application/vnd.google-apps.spreadsheet', 'xlsx' => 'application/vnd.google-apps.spreadsheet',
                'csv' => 'application/vnd.google-apps.spreadsheet',
                'ppt' => 'application/vnd.google-apps.presentation', 'pptx' => 'application/vnd.google-apps.presentation',
            ];

            if (isset($mimeMap[$extension])) {
                $fileMetadata->setMimeType($mimeMap[$extension]);
            }

            $content = file_get_contents($file->getRealPath());

            $uploadedFile = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $file->getMimeType(),
                'uploadType' => 'multipart',
                'fields' => 'id',
            ]);

            $fileId = $uploadedFile->id;

            // $permission = new Google_Service_Drive_Permission(['type' => 'anyone', 'role' => 'writer']);
            // $this->service->permissions->create($fileId, $permission);

            return $fileId;

        } catch (Exception $e) {
            throw new Exception("Gagal terhubung ke Google Drive: " . $e->getMessage());
        }
    }

    /**
     * Buat Dokumen Kosong (Sekarang menerima parameter Custom Folder ID)
     */
    public function createBlankFile($title, $type, $customFolderId = null)
    {
        $folderId = $customFolderId ?? env('GOOGLE_DRIVE_FOLDER_ID');
        $mimeMap = ['doc' => 'application/vnd.google-apps.document', 'xls' => 'application/vnd.google-apps.spreadsheet', 'ppt' => 'application/vnd.google-apps.presentation'];
        $mimeType = $mimeMap[$type] ?? $mimeMap['doc'];

        try {
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $title,
                'parents' => [$folderId],
                'mimeType' => $mimeType
            ]);

            $uploadedFile = $this->service->files->create($fileMetadata, ['fields' => 'id']);
            $fileId = $uploadedFile->id;

            // $permission = new Google_Service_Drive_Permission(['type' => 'anyone', 'role' => 'writer']);
            // $this->service->permissions->create($fileId, $permission);

            return $fileId;
        } catch (Exception $e) {
            throw new Exception("Gagal membuat dokumen baru: " . $e->getMessage());
        }
    }

    /**
     * SOLUSI MASALAH 4: Memindah file dari folder lama ke folder baru di Drive
     */
    public function moveFile($fileId, $newParentId)
    {
        try {
            $emptyFile = new \Google_Service_Drive_DriveFile();
            // Ambil ID parent (folder) sebelumnya
            $file = $this->service->files->get($fileId, ['fields' => 'parents']);
            $previousParents = join(',', $file->parents);

            // Pindahkan ke folder baru
            $this->service->files->update($fileId, $emptyFile, [
                'addParents' => $newParentId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents'
            ]);
        } catch (Exception $e) {
            \Log::error("Gagal memindah file di Google Drive: " . $e->getMessage());
        }
    }

    /**
     * SOLUSI MASALAH 5: Membuang file ke Tempat Sampah (Trash) Google Drive
     */
    public function deleteFile($fileId)
    {
        try {
            // Mengubah status file menjadi Trashed (Bisa dipulihkan admin via web Drive jika darurat)
            $emptyFile = new \Google_Service_Drive_DriveFile(['trashed' => true]);
            $this->service->files->update($fileId, $emptyFile);
        } catch (Exception $e) {
            \Log::error("Gagal menghapus file di Google Drive: " . $e->getMessage());
        }
    }
/**
     * Memindahkan file ke Sampah (Trash) Google Drive
     */
    public function trashFile($fileId)
    {
        try {
            $emptyFile = new \Google_Service_Drive_DriveFile(['trashed' => true]);
            $this->service->files->update($fileId, $emptyFile);
            return true;
        } catch (\Exception $e) {
            \Log::error("Gagal membuang ke sampah Drive: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengembalikan file dari Sampah (Restore) Google Drive
     */
    public function restoreFile($fileId)
    {
        try {
            $emptyFile = new \Google_Service_Drive_DriveFile(['trashed' => false]);
            $this->service->files->update($fileId, $emptyFile);
            return true;
        } catch (\Exception $e) {
            \Log::error("Gagal restore file dari Drive: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus file secara permanen dari Google Drive
     */
    public function permanentlyDeleteFile($fileId)
    {
        try {
            $this->service->files->delete($fileId);
            return true;
        } catch (\Exception $e) {
            \Log::error("Gagal hapus permanen di Drive: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Membuat folder dengan Parent ID yang spesifik (Bisa Folder Utama atau Sub-Folder)
     */
    public function createSpecificFolder($folderName, $parentGoogleId = null)
    {
        // Jika tidak ada parent yang dikirim, gunakan folder utama dari .env
        $parentId = $parentGoogleId ?? env('GOOGLE_DRIVE_FOLDER_ID');

        try {
            $fileMetadata = new \Google_Service_Drive_DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$parentId]
            ]);

            $folder = $this->service->files->create($fileMetadata, ['fields' => 'id']);
            // $permission = new \Google_Service_Drive_Permission(['type' => 'anyone', 'role' => 'reader']);
            // $this->service->permissions->create($folder->id, $permission);

            return $folder->id;
        } catch (\Exception $e) {
            \Log::error("Gagal membuat folder di Drive: " . $e->getMessage());
            return null;
        }
    }

    public function renameItem($googleId, $newName)
    {
        try {
            $fileMetadata = new \Google_Service_Drive_DriveFile([
                'name' => $newName
            ]);

            $this->service->files->update($googleId, $fileMetadata);
            return true;
        } catch (\Exception $e) {
            \Log::error("Gagal mengubah nama item di Drive: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Upload File Biasa (PDF, ZIP, JPG) TANPA diubah ke format Google
     */
    public function uploadBasicFile($file, $title, $customFolderId = null)
    {
        $folderId = $customFolderId ?? env('GOOGLE_DRIVE_FOLDER_ID');

        try {
            $fileMetadata = new \Google_Service_Drive_DriveFile([
                'name' => $title,
                'parents' => [$folderId],
            ]);

            $content = file_get_contents($file->getRealPath());

            $uploadedFile = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $file->getMimeType(), 
                'uploadType' => 'multipart',
                'fields' => 'id',
            ]);

            $fileId = $uploadedFile->id;
            // $permission = new \Google_Service_Drive_Permission(['type' => 'anyone', 'role' => 'reader']);
            // $this->service->permissions->create($fileId, $permission);

            return $fileId;
        } catch (\Exception $e) {
            throw new \Exception("Gagal upload file ke Google Drive: " . $e->getMessage());
        }
    }
    
    public function getAccessToken()
    {
        $response = \Illuminate\Support\Facades\Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        \Illuminate\Support\Facades\Log::error('Gagal mengambil Access Token baru: ' . $response->body());
        return null;
    }

    /**
     * 2. Fungsi untuk menyuntikkan email pegawai ke dalam akses file Google Drive
     */
    public function grantAccess($googleFileId, $emailAddress, $role = 'reader')
    {
        // SAKTI: Sapu bersih dulu izin lamanya di Drive agar tidak "nyangkut" atau bentrok!
        $this->removeAccess($googleFileId, $emailAddress);

        $accessToken = $this->getAccessToken();
        if (!$accessToken) return false;

        // Baru tembak API Google Drive untuk memberikan izin baru yang presisi
        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->post("https://www.googleapis.com/drive/v3/files/{$googleFileId}/permissions?sendNotificationEmail=false", [
                'type' => 'user',
                'role' => $role,
                'emailAddress' => $emailAddress, 
            ]);

        return $response->successful();
    }
    /**
     * 3. Fungsi untuk MENCABUT PAKSA akses email dari Google Drive
     */
    public function removeAccess($googleFileId, $emailAddress)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) return false;

        // Cari tahu dulu apa "ID Izin" untuk email tersebut di mata Google
        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$googleFileId}/permissions?fields=permissions(id,emailAddress)");

        if ($response->successful()) {
            $permissions = $response->json('permissions') ?? [];
            
            foreach ($permissions as $perm) {
                // Jika ketemu email yang pas, LANGSUNG HAPUS dari Drive!
                if (isset($perm['emailAddress']) && strtolower($perm['emailAddress']) === strtolower($emailAddress)) {
                    \Illuminate\Support\Facades\Http::withToken($accessToken)
                        ->delete("https://www.googleapis.com/drive/v3/files/{$googleFileId}/permissions/{$perm['id']}");
                }
            }
        }
        return true;
    }
    public function makePublic($fileId)
    {
        try {
            $permission = new \Google_Service_Drive_Permission(['type' => 'anyone', 'role' => 'reader']);
            $this->service->permissions->create($fileId, $permission);
            return true;
        } catch (\Exception $e) {
            \Log::error("Gagal membuat file menjadi publik: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Memberikan akses publik (anyone with the link) pada Google Drive
     */
    public function grantPublicAccess($fileId, $role = 'reader')
    {
        try {
            // SAKTI: Hapus dulu izin publik lama (jika ada) biar gak bentrok
            $this->removePublicAccess($fileId);

            // Buat izin Publik baru dengan hak akses yang dinamis
            $permission = new \Google_Service_Drive_Permission([
                'type' => 'anyone',
                'role' => $role, // Sekarang BISA menerima 'writer'
            ]);
            
            $this->service->permissions->create($fileId, $permission);
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal membuat dokumen menjadi publik di GDrive: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencabut akses publik (anyone) dari Google Drive
     */
    public function removePublicAccess($fileId)
    {
        try {
            // Ambil SEMUA daftar perizinan di file ini
            $permissionsList = $this->service->permissions->listPermissions($fileId, ['fields' => 'permissions(id, type)']);
            $permissions = $permissionsList->getPermissions();
            
            // Lacak izin yang ber-type 'anyone' dan eksekusi mati!
            if ($permissions) {
                foreach ($permissions as $permission) {
                    if ($permission->getType() === 'anyone') {
                        $this->service->permissions->delete($fileId, $permission->getId());
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal mencabut akses publik di GDrive: " . $e->getMessage());
            return false;
        }
    }
}