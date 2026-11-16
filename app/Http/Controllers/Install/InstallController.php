<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Services\InstallerService;
use Illuminate\Http\Request;

class InstallController extends Controller
{
    public function __construct(private readonly InstallerService $installer)
    {
    }

    public function welcome()
    {
        return view('install.welcome', [
            'requirements' => $this->installer->requirements(),
            'requirementsMet' => $this->installer->requirementsMet(),
        ]);
    }

    public function databaseForm()
    {
        return view('install.database');
    }

    public function databaseSave(Request $request)
    {
        $data = $request->validate([
            'driver' => ['required', 'in:mysql,sqlite'],
            'host' => ['nullable', 'string', 'max:255'],
            'port' => ['nullable', 'string', 'max:10'],
            'database' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->installer->testDatabase(
                $data['driver'],
                $data['host'] ?? '127.0.0.1',
                $data['port'] ?? '3306',
                $data['database'] ?? '',
                $data['username'] ?? '',
                $data['password'] ?? '',
            );
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['database' => $e->getMessage()]);
        }

        $this->installer->writeDatabaseConfig(
            $data['driver'],
            $data['host'] ?? '127.0.0.1',
            $data['port'] ?? '3306',
            $data['database'] ?? '',
            $data['username'] ?? '',
            $data['password'] ?? '',
        );

        return redirect()->route('install.account');
    }

    public function accountForm()
    {
        return view('install.account');
    }

    public function accountSave(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $this->installer->runMigrations();
            $this->installer->createAdminUser($data['name'], $data['email'], $data['password']);
            $this->installer->markInstalled();
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['install' => 'Instalace selhala: '.$e->getMessage()]);
        }

        return redirect()->route('install.finish');
    }

    public function finish()
    {
        if (! $this->installer->isInstalled()) {
            return redirect()->route('install.welcome');
        }

        return view('install.finish');
    }
}
