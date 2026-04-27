<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadToGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $documentId;
    protected $tempFilePath; // Path file sementara di server lokal
    protected $targetFolderId;
    protected $isConvertible;

    // Retry job up to 3 times if Google API fails
    public $tries = 3; 

    /**
     * Create a new job instance.
     */
    public function __construct($documentId, $tempFilePath, $targetFolderId, $isConvertible)
    {
        $this->documentId = $documentId;
        $this->tempFilePath = $tempFilePath;
        $this->targetFolderId = $targetFolderId;
        $this->isConvertible = $isConvertible;
    }

    /**
     * Execute the job. (Ini yang dikerjakan si Kurir di belakang layar)
     */
    public function handle(\App\Services\GoogleDriveService $googleDriveService): void
    {
        // 1. Cari dokumen di Database
        $document = \App\Models\Document::find($this->documentId);
        
        if (!$document) {
            \Illuminate\Support\Facades\Log::error("Job Gagal: Dokumen ID {$this->documentId} tidak ada di database.");
            return;
        }

        try {
            $document->update(['file_path' => 'Sedang mengunggah ke Google Drive...']);

            // --- INI KUNCI JAWABANNYA! ---
            // Jangan tebak manual. Suruh Storage Facade mencari absolute path-nya!
            $fullPath = \Illuminate\Support\Facades\Storage::path($this->tempFilePath);

            if (!file_exists($fullPath)) {
                throw new \Exception("File fisik tidak ditemukan oleh Storage Facade di: " . $fullPath);
            }

            // 3. Buat Object File untuk dikirim ke Service
            $fileObject = new \Illuminate\Http\File($fullPath);

            // 4. Proses Upload ke Google Drive
            if ($this->isConvertible) {
                $googleFileId = $googleDriveService->uploadAndConvert($fileObject, $document->title, $this->targetFolderId);
            } else {
                $googleFileId = $googleDriveService->uploadBasicFile($fileObject, $document->title, $this->targetFolderId);
            }

            // 5. Update Database jika sukses
            $document->update([
                'google_file_id' => $googleFileId,
                'file_path' => 'Cloud/GoogleDrive',
                'status' => 'success' // Pastikan kolom status sudah kamu buat di DB
            ]);

            // 6. Hapus file sementara menggunakan Storage Facade (Lebih aman dari unlink)
            \Illuminate\Support\Facades\Storage::delete($this->tempFilePath);

            \Illuminate\Support\Facades\Log::info("Job Sukses: File {$document->title} sudah di Google Drive.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Detail Error di Job: " . $e->getMessage());
            $document->update([
                'status' => 'failed',
                'file_path' => 'Gagal Upload: Cek Log'
            ]);
            throw $e; 
        }
    }
}