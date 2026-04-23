<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Exception;

class GoogleDriveService
{
    protected Google_Client $client;
    protected Google_Service_Drive $service;

    public function __construct()
    {
        $clientId     = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $refreshToken = config('services.google.refresh_token');

        if (!$clientId || !$clientSecret || !$refreshToken) {
            throw new Exception(
                "Konfigurasi Google OAuth tidak lengkap. " .
                "Cek GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REFRESH_TOKEN di .env"
            );
        }

        $this->client = new Google_Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->addScope(Google_Service_Drive::DRIVE);

        // Set token secara eksplisit dan paksa refresh agar selalu fresh
        $this->client->setAccessType('offline');
        $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

        $this->service = new Google_Service_Drive($this->client);
    }

    public function uploadAndConvert($file, string $title): string
    {
        $folderId = config('services.google.drive_folder_id');

        if (empty($folderId)) {
            throw new Exception("GOOGLE_DRIVE_FOLDER_ID belum diset di .env");
        }

        $extension = strtolower($file->getClientOriginalExtension());

        $mimeMap = [
            'doc'  => 'application/vnd.google-apps.document',
            'docx' => 'application/vnd.google-apps.document',
            'xls'  => 'application/vnd.google-apps.spreadsheet',
            'xlsx' => 'application/vnd.google-apps.spreadsheet',
            'csv'  => 'application/vnd.google-apps.spreadsheet',
            'ppt'  => 'application/vnd.google-apps.presentation',
            'pptx' => 'application/vnd.google-apps.presentation',
        ];

        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name'    => $title,
            'parents' => [$folderId],
        ]);

        // Set MIME type untuk konversi otomatis ke format Google
        if (isset($mimeMap[$extension])) {
            $fileMetadata->setMimeType($mimeMap[$extension]);
        }

        try {
            $content = file_get_contents($file->getRealPath());

            $uploadedFile = $this->service->files->create($fileMetadata, [
                'data'       => $content,
                'mimeType'   => $file->getMimeType(),
                'uploadType' => 'multipart',
                'fields'     => 'id,name,webViewLink',
            ]);

            $fileId = $uploadedFile->id;

            // Buka akses agar bisa di-embed di iframe
            $permission = new Google_Service_Drive_Permission([
                'type' => 'anyone',
                'role' => 'writer',
            ]);
            $this->service->permissions->create($fileId, $permission);

            \Log::info('Google Drive Upload Success (OAuth)', [
                'file_id' => $fileId,
                'title'   => $title,
            ]);

            return $fileId;

        } catch (Exception $e) {
            \Log::error('Google Drive Upload Error (OAuth)', [
                'message'   => $e->getMessage(),
                'folder_id' => $folderId,
                'title'     => $title,
            ]);

            throw new Exception("Gagal upload ke Google Drive: " . $e->getMessage());
        }
    }
}