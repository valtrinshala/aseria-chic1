<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index() {
        $files = Storage::files('database-backups');
        $backups = collect($files)->map(function ($file) {
            return [
                'name' => basename($file),
                'size_mb' => round(Storage::size($file) / 1048576, 2), // Convert bytes to MB
                'date' => Carbon::createFromTimestamp(Storage::lastModified($file))->toDateTimeString(),
            ];
        })->sortByDesc('date');

        return view('backup/backup-index', compact('backups'));
    }

    public function generateBackup() {
        Artisan::call('backup:run --only-db');
        return redirect()->back();
    }

    public function downloadBackup(string $filename){
        $filePath = 'database-backups/' . $filename;
        if (!Storage::exists($filePath)) {
            return redirect()->back()->withErrors(['error' => 'File not found.']);
        }
        return Storage::download($filePath);
    }

    public function deleteBackup(string $filename){
        $filePath = 'database-backups/' . $filename;
        if (!Storage::exists($filePath)) {
            return redirect()->back()->withErrors(['error' => 'File not found.']);
        }
        Storage::delete($filePath);
        return redirect()->back();
    }

    public function restoreBackup($filename)
    {
        // Define the path to the backup file
        $zipFilePath = storage_path('app/database-backups/' . $filename);
        $extractPath = storage_path('app/database-backups/extracted');
        $sqlFilePath = $extractPath . '/db-dumps/mysql-'.env('DB_DATABASE').'.sql';

        // Check if the file exists
        if (!file_exists($zipFilePath)) {
            return redirect()->back()->withErrors(['error' => 'File not found.']);
        }

        // Unzip the file
        $zip = new \ZipArchive;
        if ($zip->open($zipFilePath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return redirect()->back()->withErrors(['error' => 'Failed to unzip the backup file.']);
        }
        DB::unprepared(file_get_contents($sqlFilePath));

        $this->deleteDirectory($extractPath);

        return redirect()->back()->with('message', 'Database restored successfully.');
    }

    private function deleteDirectory($dirPath)
    {
        if (!is_dir($dirPath)) {
            return;
        }

        $files = array_diff(scandir($dirPath), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }

        rmdir($dirPath);
    }
}
