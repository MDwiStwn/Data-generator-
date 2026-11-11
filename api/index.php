<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

// Pastikan semua direktori writable di /tmp tersedia
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set environment defaults untuk serverless Vercel jika belum diset
putenv('APP_STORAGE_PATH=/tmp/storage');
$_ENV['APP_STORAGE_PATH'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Inisialisasi file SQLite di /tmp jika belum ada
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
}
putenv("DB_DATABASE={$dbPath}");
$_ENV['DB_DATABASE'] = $dbPath;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

// Jalankan migrasi & seeder otomatis jika container Lambda baru pertama kali boot
if (!file_exists('/tmp/.migrated')) {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
        touch('/tmp/.migrated');
    } catch (\Throwable $e) {
        // Lanjutkan request agar tidak memblokir render jika seeder sudah pernah jalan
    }
}

$app->handleRequest(Request::capture());
