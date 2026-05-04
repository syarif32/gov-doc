<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Menampilkan halaman Maintenance
     */
    public function index()
    {
        // Proteksi: Hanya Admin yang boleh masuk
        if (auth()->user()->role_level !== 'admin') {
            abort(403, 'Akses Ditolak. Halaman khusus Administrator.');
        }

        // Menghitung ukuran folder Log dan Temp Uploads (Opsional agar UI lebih interaktif)
        $logPath = storage_path('logs');
        $tempPath = storage_path('app/temp_uploads');

        $logSize = File::exists($logPath) ? $this->getFolderSize($logPath) : 0;
        $tempSize = File::exists($tempPath) ? $this->getFolderSize($tempPath) : 0;

        return view('maintenance.index', compact('logSize', 'tempSize'));
    }

    /**
     * Membersihkan berbagai macam Cache Laravel
     */
    public function clearCache($type)
    {
        if (auth()->user()->role_level !== 'admin') abort(403);

        $message = '';
        try {
            switch ($type) {
                case 'config':
                    Artisan::call('config:clear');
                    $message = 'Cache konfigurasi berhasil dibersihkan.';
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    $message = 'Cache rute berhasil dibersihkan.';
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    $message = 'Cache view berhasil dibersihkan.';
                    break;
                case 'optimize':
                    Artisan::call('optimize:clear');
                    $message = 'Semua cache (Config, Route, View) berhasil dibersihkan secara massal.';
                    break;
                case 'rebuild':
                    Artisan::call('optimize');
                    return redirect()->route('admin.maintenance.index')->with('success', 'Sistem berhasil di-rebuild dan dioptimasi!');
                default:
                    return back()->with('error', 'Perintah maintenance tidak valid.');
            }
            
            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Membersihkan file .log di storage/logs
     */
    public function clearLogs()
    {
        if (auth()->user()->role_level !== 'admin') abort(403);

        try {
            $logPath = storage_path('logs');
            $files = File::glob($logPath . '/*.log');

            foreach ($files as $file) {
                File::delete($file); 
            }

            auth()->user()->logAction("Membersihkan System Logs");
            return back()->with('success', 'Semua file riwayat log sistem berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan log: ' . $e->getMessage());
        }
    }

    /**
     * Membersihkan sampah kepingan file upload di storage/app/temp_uploads
     */
    public function clearTempFiles()
    {
        if (auth()->user()->role_level !== 'admin') abort(403);

        try {
            $tempPath = storage_path('app/temp_uploads');
            
            if (File::exists($tempPath)) {
                // Hapus semua isi folder temp_uploads, tapi biarkan foldernya tetap ada
                File::cleanDirectory($tempPath);
            }

            auth()->user()->logAction("Membersihkan Temp Uploads Storage");
            return back()->with('success', 'Ruang penyimpanan lokal (Temp Uploads) berhasil dibersihkan dari sampah file!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan penyimpanan sementara: ' . $e->getMessage());
        }
    }

    /**
     * Fungsi bantuan untuk menghitung ukuran folder
     */
    private function getFolderSize($dir)
    {
        $size = 0;
        foreach (File::allFiles($dir) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }
}