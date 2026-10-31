<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\BackupService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    /**
     * Tampilkan halaman daftar file backup.
     */
    public function index(Request $request)
    {
        $cari = $request->query('cari');
        $backups = $this->backupService->getBackups($cari);

        return view('backup.index', compact('backups', 'cari'));
    }

    /**
     * Buat backup database baru (.sql).
     */
    public function store(): RedirectResponse
    {
        try {
            $backup = $this->backupService->createBackup();

            ActivityLog::log(
                'Buat Backup Database',
                "File: {$backup['name']} ({$backup['size']})",
                ActivityLog::CATEGORY_SISTEM
            );

            return back()->with('success', "Backup database berhasil dibuat: {$backup['name']}");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    /**
     * Unduh file backup ke local device pengguna.
     */
    public function download(string $filename): BinaryFileResponse|RedirectResponse
    {
        $path = $this->backupService->getBackupPath($filename);

        if (!$path) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        ActivityLog::log(
            'Unduh Backup Database',
            "File: {$filename}",
            ActivityLog::CATEGORY_SISTEM
        );

        return response()->download($path, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Restore database dari file backup.
     */
    public function restore(Request $request): RedirectResponse
    {
        try {
            $filePath = null;
            $fileName = '';

            // 1. Restore dari file yang diunggah dari komputer lokal
            if ($request->hasFile('file_sql')) {
                $request->validate([
                    'file_sql' => 'required|file|max:102400', // max 100MB
                ], [
                    'file_sql.required' => 'Pilih file backup (.sql) terlebih dahulu.',
                    'file_sql.file' => 'File yang diunggah tidak valid.',
                    'file_sql.max' => 'Ukuran file backup maksimal 100MB.',
                ]);

                $file = $request->file('file_sql');
                $ext = strtolower($file->getClientOriginalExtension());
                if ($ext !== 'sql') {
                    return back()->with('error', 'Hanya file dengan ekstensi .sql yang diperbolehkan.');
                }

                $fileName = $file->getClientOriginalName();
                $filePath = $file->getRealPath();
            }
            // 2. Restore dari file backup yang tersimpan di server
            elseif ($request->filled('filename')) {
                $fileName = $request->input('filename');
                $filePath = $this->backupService->getBackupPath($fileName);

                if (!$filePath) {
                    return back()->with('error', 'File backup yang dipilih tidak ditemukan di server.');
                }
            } else {
                return back()->with('error', 'Pilih file backup yang ingin dipulihkan.');
            }

            $this->backupService->restoreBackup($filePath);

            ActivityLog::log(
                'Restore Database',
                "Database berhasil dipulihkan dari file: {$fileName}",
                ActivityLog::CATEGORY_SISTEM
            );

            return redirect()->route('backup.index')->with('success', "Database berhasil dipulihkan dari file: {$fileName}");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    /**
     * Hapus file backup dari server.
     */
    public function destroy(string $filename): RedirectResponse
    {
        try {
            $deleted = $this->backupService->deleteBackup($filename);

            if ($deleted) {
                ActivityLog::log(
                    'Hapus Backup Database',
                    "File: {$filename}",
                    ActivityLog::CATEGORY_SISTEM
                );

                return back()->with('success', "File backup {$filename} berhasil dihapus.");
            }

            return back()->with('error', "File backup {$filename} tidak ditemukan.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus file backup: ' . $e->getMessage());
        }
    }
}
