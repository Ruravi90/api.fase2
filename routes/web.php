<?php

use App\Http\Controllers\DatabaseBackupController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/backup/database', function () {
    return app(DatabaseBackupController::class)->form();
});

Route::post('/backup/database', [DatabaseBackupController::class, 'create'])
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        ValidateCsrfToken::class,
    ]);

Route::post('/backup/database/migrate', [DatabaseBackupController::class, 'backupAndMigrate'])
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        ValidateCsrfToken::class,
    ]);
