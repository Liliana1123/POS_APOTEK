<?php

namespace App\Services;

use App\Models\InfoApotek;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PDO;

class BackupService
{
    /**
     * Folder penyimpanan backup database.
     */
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($this->backupDir);
    }

    /**
     * Buat backup database berbentuk file .sql.
     * Format nama file: {nama_apotek}_{tanggal_backup}.sql
     * Contoh: Apotek_Sehat_2026-10-26_14-30-00.sql
     */
    public function createBackup(): array
    {
        $infoApotek = InfoApotek::singleton();
        $namaApotek = $infoApotek->nama_apotek ?? 'Apotek';

        // Bersihkan nama apotek untuk nama file yang valid
        $safeNamaApotek = preg_replace('/[^A-Za-z0-9_\-]/', '_', trim($namaApotek));
        $safeNamaApotek = preg_replace('/_+/', '_', $safeNamaApotek);
        $safeNamaApotek = trim($safeNamaApotek, '_');

        $tz = config('app.timezone') === 'UTC' ? 'Asia/Jakarta' : config('app.timezone');
        $timestamp = Carbon::now($tz)->format('Y-m-d_H-i-s');
        $fileName = "{$safeNamaApotek}_{$timestamp}.sql";
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $fileName;

        // Coba gunakan mysqldump terlebih dahulu
        $dumped = $this->dumpUsingMysqldump($filePath);

        // Jika mysqldump gagal atau tidak tersedia, gunakan fallback PHP PDO
        if (!$dumped || !file_exists($filePath) || filesize($filePath) === 0) {
            $this->dumpUsingPhpPdo($filePath);
        }

        if (!file_exists($filePath) || filesize($filePath) === 0) {
            throw new Exception('Gagal membuat file backup database.');
        }

        return [
            'name' => $fileName,
            'path' => $filePath,
            'size' => $this->formatFileSize(filesize($filePath)),
            'raw_size' => filesize($filePath),
            'date' => Carbon::createFromTimestamp(filemtime($filePath))->setTimezone($tz)->translatedFormat('d/m/Y H:i:s'),
        ];
    }

    /**
     * Ambil daftar file backup di direktori.
     */
    public function getBackups(?string $search = null): array
    {
        if (!File::isDirectory($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];
        $tz = config('app.timezone') === 'UTC' ? 'Asia/Jakarta' : config('app.timezone');

        foreach ($files as $file) {
            if ($file->getExtension() !== 'sql') {
                continue;
            }

            $fileName = $file->getFilename();

            // Filter pencarian jika ada
            if (!empty($search) && stripos($fileName, $search) === false) {
                continue;
            }

            $sizeInBytes = $file->getSize();
            $modifiedTime = $file->getMTime();

            $backups[] = [
                'name' => $fileName,
                'path' => $file->getPathname(),
                'size' => $this->formatFileSize($sizeInBytes),
                'raw_size' => $sizeInBytes,
                'date' => Carbon::createFromTimestamp($modifiedTime)->setTimezone($tz)->translatedFormat('d/m/Y H:i:s'),
                'raw_date' => $modifiedTime,
            ];
        }

        // Urutkan dari yang paling baru
        usort($backups, fn ($a, $b) => $b['raw_date'] <=> $a['raw_date']);

        return $backups;
    }

    /**
     * Ambil path file backup berdasarkan nama file yang aman.
     */
    public function getBackupPath(string $filename): ?string
    {
        $safeName = basename($filename);
        $fullPath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        return file_exists($fullPath) ? $fullPath : null;
    }

    /**
     * Hapus file backup dari server.
     */
    public function deleteBackup(string $filename): bool
    {
        $path = $this->getBackupPath($filename);
        if ($path && file_exists($path)) {
            return File::delete($path);
        }

        return false;
    }

    /**
     * Restore database dari file .sql.
     */
    public function restoreBackup(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            throw new Exception('File backup tidak ditemukan.');
        }

        // Tingkatkan limit waktu dan memori untuk proses restore
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        // Coba restore via mysql command line terlebih dahulu
        $restored = $this->restoreUsingMysqlCli($filePath);

        // Jika CLI gagal, lakukan restore via PHP PDO
        if (!$restored) {
            $this->restoreUsingPhpPdo($filePath);
        }

        // Reset cache aplikasi agar perubahan langsung aktif
        Cache::flush();

        return true;
    }

    /**
     * Export database menggunakan utility mysqldump.
     */
    protected function dumpUsingMysqldump(string $targetPath): bool
    {
        try {
            $connection = Config::get('database.default', 'mysql');
            $host = Config::get("database.connections.{$connection}.host", '127.0.0.1');
            $port = Config::get("database.connections.{$connection}.port", '3306');
            $database = Config::get("database.connections.{$connection}.database");
            $username = Config::get("database.connections.{$connection}.username", 'root');
            $password = Config::get("database.connections.{$connection}.password", '');

            $mysqldumpBinary = $this->findMysqldumpBinary();
            if (!$mysqldumpBinary) {
                return false;
            }

            // Bangun perintah mysqldump
            $passwordOption = $password !== '' && $password !== null ? "--password=" . escapeshellarg($password) : '';
            $command = sprintf(
                '"%s" --user=%s %s --host=%s --port=%s --default-character-set=utf8mb4 --single-transaction --quick --skip-lock-tables --routines --triggers %s > "%s"',
                $mysqldumpBinary,
                escapeshellarg($username),
                $passwordOption,
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                $targetPath
            );

            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            return $returnVar === 0 && file_exists($targetPath) && filesize($targetPath) > 0;
        } catch (\Throwable $e) {
            Log::warning('mysqldump failed, falling back to PHP PDO: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Export database murni menggunakan PHP PDO (Universal fallback).
     */
    protected function dumpUsingPhpPdo(string $targetPath): void
    {
        /** @var PDO $pdo */
        $pdo = DB::connection()->getPdo();
        $databaseName = DB::connection()->getDatabaseName();

        $handle = fopen($targetPath, 'w');
        if (!$handle) {
            throw new Exception("Gagal membuat file backup di: {$targetPath}");
        }

        // Header SQL
        fwrite($handle, "-- POS Apotek Database Backup\n");
        fwrite($handle, "-- Tanggal: " . now()->toDateTimeString() . "\n");
        fwrite($handle, "-- Database: {$databaseName}\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

        // Ambil semua tabel
        $tables = [];
        $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        foreach ($tables as $table) {
            // Drop Table
            fwrite($handle, "\n-- --------------------------------------------------------\n");
            fwrite($handle, "-- Struktur tabel: `{$table}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

            // Create Table
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $createRow = $createStmt->fetch(PDO::FETCH_ASSOC);
            $createSql = $createRow['Create Table'] ?? ($createRow['Create View'] ?? null);

            if ($createSql) {
                fwrite($handle, $createSql . ";\n\n");
            }

            // Dump Data (Data tabel)
            $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $totalRows = (int) $countStmt->fetchColumn();

            if ($totalRows > 0) {
                fwrite($handle, "-- Data untuk tabel: `{$table}`\n");
                $offset = 0;
                $batchSize = 250;

                while ($offset < $totalRows) {
                    $selectStmt = $pdo->query("SELECT * FROM `{$table}` LIMIT {$batchSize} OFFSET {$offset}");
                    $rows = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($rows)) {
                        $columnNames = array_map(fn ($col) => "`" . str_replace("`", "``", $col) . "`", array_keys($rows[0]));
                        $columnsList = implode(', ', $columnNames);

                        fwrite($handle, "INSERT INTO `{$table}` ({$columnsList}) VALUES\n");

                        $valueRows = [];
                        foreach ($rows as $row) {
                            $quotedValues = [];
                            foreach ($row as $val) {
                                if (is_null($val)) {
                                    $quotedValues[] = 'NULL';
                                } else {
                                    $quotedValues[] = $pdo->quote($val);
                                }
                            }
                            $valueRows[] = '(' . implode(', ', $quotedValues) . ')';
                        }

                        fwrite($handle, implode(",\n", $valueRows) . ";\n");
                    }

                    $offset += $batchSize;
                }
                fwrite($handle, "\n");
            }
        }

        // Footer SQL
        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fwrite($handle, "-- Akhir dari backup\n");

        fclose($handle);
    }

    /**
     * Restore database menggunakan mysql CLI.
     */
    protected function restoreUsingMysqlCli(string $filePath): bool
    {
        try {
            $connection = Config::get('database.default', 'mysql');
            $host = Config::get("database.connections.{$connection}.host", '127.0.0.1');
            $port = Config::get("database.connections.{$connection}.port", '3306');
            $database = Config::get("database.connections.{$connection}.database");
            $username = Config::get("database.connections.{$connection}.username", 'root');
            $password = Config::get("database.connections.{$connection}.password", '');

            $mysqlBinary = $this->findMysqlBinary();
            if (!$mysqlBinary) {
                return false;
            }

            $passwordOption = $password !== '' && $password !== null ? "--password=" . escapeshellarg($password) : '';
            $command = sprintf(
                '"%s" --user=%s %s --host=%s --port=%s --default-character-set=utf8mb4 %s < "%s"',
                $mysqlBinary,
                escapeshellarg($username),
                $passwordOption,
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                $filePath
            );

            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            return $returnVar === 0;
        } catch (\Throwable $e) {
            Log::warning('mysql CLI restore failed, falling back to PHP PDO: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore database via PHP PDO dengan mengeksekusi statement SQL.
     */
    protected function restoreUsingPhpPdo(string $filePath): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Tidak dapat membaca file backup: {$filePath}");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $currentQuery = '';

        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);

            // Lewati komentar dan baris kosong
            if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*')) {
                continue;
            }

            $currentQuery .= $line;

            // Jika baris diakhiri titik koma, eksekusi query
            if (str_ends_with($trimmed, ';')) {
                try {
                    DB::unprepared($currentQuery);
                } catch (\Throwable $e) {
                    Log::error("Error executing SQL statement during restore: " . $e->getMessage());
                    // Lanjutkan jika ada statement non-kritis
                }
                $currentQuery = '';
            }
        }

        // Jika masih ada sisa query tanpa semicolon di akhir
        if (trim($currentQuery) !== '') {
            try {
                DB::unprepared($currentQuery);
            } catch (\Throwable $e) {
                Log::error("Error executing remaining SQL statement: " . $e->getMessage());
            }
        }

        fclose($handle);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * Cari lokasi binary mysqldump.
     */
    protected function findMysqldumpBinary(): ?string
    {
        $possiblePaths = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql*\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server*\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];

        foreach ($possiblePaths as $path) {
            if (str_contains($path, '*')) {
                $matches = glob($path);
                if (!empty($matches) && file_exists($matches[0])) {
                    return $matches[0];
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        // Cek apakah ada di PATH sistem
        $whereCmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where mysqldump' : 'which mysqldump';
        $out = @shell_exec($whereCmd);
        if ($out && trim($out) !== '') {
            $lines = explode("\n", trim($out));
            return trim($lines[0]);
        }

        return null;
    }

    /**
     * Cari lokasi binary mysql client.
     */
    protected function findMysqlBinary(): ?string
    {
        $possiblePaths = [
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\wamp64\\bin\\mysql\\mysql*\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server*\\bin\\mysql.exe',
            '/usr/bin/mysql',
            '/usr/local/bin/mysql',
        ];

        foreach ($possiblePaths as $path) {
            if (str_contains($path, '*')) {
                $matches = glob($path);
                if (!empty($matches) && file_exists($matches[0])) {
                    return $matches[0];
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        $whereCmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where mysql' : 'which mysql';
        $out = @shell_exec($whereCmd);
        if ($out && trim($out) !== '') {
            $lines = explode("\n", trim($out));
            return trim($lines[0]);
        }

        return null;
    }

    /**
     * Format ukuran file ke format yang mudah dibaca (KB / MB).
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }
}
