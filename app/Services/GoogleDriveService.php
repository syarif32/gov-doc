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

            $extension = strtolower($file->getClientOriginalExtension());
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

            $permission = new Google_Service_Drive_Permission(['type' => 'anyone', 'role' => 'writer']);
            $this->service->permissions->create($fileId, $permission);

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

            $permission = new Google_Service_Drive_Permission(['type' => 'anyone', 'role' => 'writer']);
            $this->service->permissions->create($fileId, $permission);

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
}