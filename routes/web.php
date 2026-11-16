<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactPersonController;
use App\Http\Controllers\Install\InstallController;
use Illuminate\Support\Facades\Route;

Route::middleware('not.installed')->prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'welcome'])->name('welcome');
    Route::get('/databaze', [InstallController::class, 'databaseForm'])->name('database');
    Route::post('/databaze', [InstallController::class, 'databaseSave'])->name('database.save');
    Route::get('/ucet', [InstallController::class, 'accountForm'])->name('account');
    Route::post('/ucet', [InstallController::class, 'accountSave'])->name('account.save');
    Route::get('/hotovo', [InstallController::class, 'finish'])->name('finish');
});

Route::middleware('installed')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/prihlaseni', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/prihlaseni', [LoginController::class, 'login'])->name('login.attempt');
    Route::post('/odhlaseni', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/firmy/ares/{ico}', [CompanyController::class, 'aresLookup'])
            ->where('ico', '[0-9]{8}')
            ->name('companies.ares');

        Route::resource('firmy', CompanyController::class)
            ->parameters(['firmy' => 'company'])
            ->names('companies');

        Route::resource('firmy.kontakty', ContactPersonController::class)
            ->parameters(['firmy' => 'company', 'kontakty' => 'contactPerson'])
            ->only(['store', 'update', 'destroy'])
            ->shallow();

        Route::get('/zalohy', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/zalohy', [BackupController::class, 'store'])->name('backups.store');
        Route::get('/zalohy/{filename}/stahnout', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/zalohy/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });
});
