<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use ZipArchive;

class BackupService
{
    public function create(): string
    {
        $dir = storage_path('app/backups');

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = 'zaloha-'.now()->format('Ymd-His').'.zip';
        $path = $dir.'/'.$filename;

        $zip = new ZipArchive();

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Nelze vytvořit soubor zálohy.');
        }

        $zip->addFromString('database.sql', $this->dumpDatabase());
        $zip->addFile(base_path('.env'), '.env');
        $this->addDirectory($zip, storage_path('app'), 'storage/app', ['backups']);
        $zip->close();

        return $path;
    }

    public function list(): array
    {
        $dir = storage_path('app/backups');
        $backups = [];

        if (! is_dir($dir)) {
            return $backups;
        }

        foreach (glob($dir.'/*.zip') ?: [] as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size' => filesize($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        usort($backups, fn ($a, $b) => strcmp($b['filename'], $a['filename']));

        return $backups;
    }

    public function path(string $filename): string
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/'.$filename);

        if (! preg_match('/^zaloha-\d{8}-\d{6}\.zip$/', $filename) || ! is_file($path)) {
            abort(404);
        }

        return $path;
    }

    public function delete(string $filename): void
    {
        $path = $this->path($filename);
        @unlink($path);
    }

    private function dumpDatabase(): string
    {
        $driver = DB::getDriverName();
        $sql = "-- Záloha databáze ".now()->toDateTimeString()."\n";
        $sql .= "-- Databázový systém: {$driver}\n\n";

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        }

        foreach ($this->tables() as $table) {
            $sql .= $this->dumpTable($table, $driver);
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        }

        return $sql;
    }

    private function tables(): array
    {
        if (DB::getDriverName() === 'sqlite') {
            return collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"))
                ->map(fn ($row) => $row->name)
                ->values()
                ->all();
        }

        return collect(DB::select('SHOW TABLES'))
            ->map(fn ($row) => array_values((array) $row)[0])
            ->values()
            ->all();
    }

    private function dumpTable(string $table, string $driver): string
    {
        $sql = "\n-- -----------------------------\n-- Tabulka: {$table}\n-- -----------------------------\n";

        if ($driver === 'sqlite') {
            $create = DB::select("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ?", [$table]);
            $schemaSql = $create[0]->sql ?? null;
        } else {
            $create = DB::select("SHOW CREATE TABLE `{$table}`");
            $schemaSql = $create[0]->{'Create Table'} ?? null;
        }

        if ($schemaSql) {
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n{$schemaSql};\n";
        }

        $rows = DB::table($table)->orderBy(...$this->orderColumn($table))->get();

        foreach ($rows as $row) {
            $values = collect((array) $row)
                ->map(fn ($value) => $value === null ? 'NULL' : DB::getPdo()->quote((string) $value))
                ->implode(', ');

            $sql .= "INSERT INTO `{$table}` VALUES ({$values});\n";
        }

        return $sql;
    }

    private function orderColumn(string $table): array
    {
        $columns = collect(DB::getSchemaBuilder()->getColumnListing($table));

        return [$columns->contains('id') ? 'id' : $columns->first() ?? 'rowid', 'asc'];
    }

    private function addDirectory(ZipArchive $zip, string $source, string $local, array $exclude = []): void
    {
        if (! is_dir($source)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            $pathname = (string) $file;
            $relative = ltrim(str_replace($source, '', $pathname), '/\\');

            foreach ($exclude as $excluded) {
                if (str_starts_with($relative, $excluded.'/')) {
                    continue 2;
                }
            }

            $zip->addFile($pathname, $local.'/'.$relative);
        }
    }
}
