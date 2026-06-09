<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Support\DatabaseBackup;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('db:backup {--path=}', function () {
    $path = app(DatabaseBackup::class)->run($this->option('path') ?: null);
    $this->info("Backup created at: {$path}");
})->purpose('Create a SQL backup in storage/app/backups');
