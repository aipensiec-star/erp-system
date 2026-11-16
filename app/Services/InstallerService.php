<?php

namespace App\Services;

class InstallerService
{
    public function isInstalled(): bool
    {
        return file_exists(storage_path('app/installed'));
    }

    public function markInstalled(): void
    {
        if (! $this->isInstalled()) {
            @file_put_contents(storage_path('app/installed'), now()->toIso8601String());
        }
    }

    public function requirements(): array
    {
        return [
            ['label' => 'PHP 8.2 nebo novější', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>='), 'value' => PHP_VERSION],
            ['label' => 'Rozšíření PDO', 'ok' => extension_loaded('pdo'), 'value' => extension_loaded('pdo') ? 'ano' : 'ne'],
            ['label' => 'Rozšíření Mbstring', 'ok' => extension_loaded('mbstring'), 'value' => extension_loaded('mbstring') ? 'ano' : 'ne'],
            ['label' => 'Rozšíření OpenSSL', 'ok' => extension_loaded('openssl'), 'value' => extension_loaded('openssl') ? 'ano' : 'ne'],
            ['label' => 'Rozšíření Zip (zálohy)', 'ok' => extension_loaded('zip'), 'value' => extension_loaded('zip') ? 'ano' : 'ne'],
            ['label' => 'Adresář storage/ je zapisovatelný', 'ok' => is_writable(storage_path()), 'value' => is_writable(storage_path()) ? 'ano' : 'ne'],
            ['label' => 'Adresář bootstrap/cache/ je zapisovatelný', 'ok' => is_writable(base_path('bootstrap/cache')), 'value' => is_writable(base_path('bootstrap/cache')) ? 'ano' : 'ne'],
            ['label' => 'Soubor .env je zapisovatelný', 'ok' => is_writable(base_path('.env')), 'value' => is_writable(base_path('.env')) ? 'ano' : 'ne'],
        ];
    }

    public function requirementsMet(): bool
    {
        foreach ($this->requirements() as $requirement) {
            if (! $requirement['ok']) {
                return false;
            }
        }

        return true;
    }

    public function testDatabase(string $driver, string $host, string $port, string $database, string $username, string $password): void
    {
        if ($driver === 'sqlite') {
            $path = database_path($database ?: 'database.sqlite');
            new \PDO('sqlite:'.$path);

            return;
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            new \PDO($dsn, $username, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Nelze se připojit k databázi: '.$e->getMessage());
        }
    }

    public function writeDatabaseConfig(string $driver, string $host, string $port, string $database, string $username, string $password): void
    {
        $values = $driver === 'sqlite'
            ? ['DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => database_path($database ?: 'database.sqlite')]
            : [
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $host,
                'DB_PORT' => $port,
                'DB_DATABASE' => $database,
                'DB_USERNAME' => $username,
                'DB_PASSWORD' => $password,
            ];

        $this->writeEnv($values);
        $this->ensureAppKey();
    }

    public function ensureAppKey(): void
    {
        $envFile = base_path('.env');

        if (! file_exists($envFile)) {
            $example = base_path('.env.example');
            file_put_contents($envFile, file_exists($example) ? (string) file_get_contents($example) : '');
        }

        \Artisan::call('key:generate', ['--force' => true]);
    }

    public function writeEnv(array $values): void
    {
        $envFile = base_path('.env');
        $content = file_exists($envFile) ? (string) file_get_contents($envFile) : '';

        foreach ($values as $key => $value) {
            $pattern = '/^'.preg_quote($key, '/').'=.*/m';
            $line = $key.'='.$value;

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $line, $content);
            } else {
                $content .= (str_ends_with($content, "\n") || $content === '' ? '' : "\n").$line."\n";
            }
        }

        file_put_contents($envFile, $content);
    }

    public function runMigrations(): void
    {
        \Artisan::call('migrate', ['--force' => true]);
    }

    public function createAdminUser(string $name, string $email, string $password): void
    {
        $user = \App\Models\User::query()->firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->password = \Hash::make($password);
        $user->save();
    }
}
