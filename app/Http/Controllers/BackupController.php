<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct(private readonly BackupService $backups)
    {
    }

    public function index()
    {
        return view('backups.index', ['backups' => $this->backups->list()]);
    }

    public function store()
    {
        try {
            $this->backups->create();
        } catch (\Throwable $e) {
            return back()->withErrors(['backup' => 'Zálohu nelze vytvořit: '.$e->getMessage()]);
        }

        return back()->with('success', 'Záloha byla vytvořena.');
    }

    public function download(string $filename)
    {
        return response()->download($this->backups->path($filename));
    }

    public function destroy(Request $request, string $filename)
    {
        $this->backups->delete($filename);

        return back()->with('success', 'Záloha byla smazána.');
    }
}
