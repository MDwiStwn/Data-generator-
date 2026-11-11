<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$tmp = sys_get_temp_dir();

// Setup folder-folder writable di temp
$dirs = [
    $tmp . '/views',
    $tmp . '/sessions',
    $tmp . '/cache',
    $tmp . '/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Redirect semua cache dan storage Laravel dari root (read-only) ke temp (writable)
$envVars = [
    'APP_STORAGE_PATH' => $tmp,
    'VIEW_COMPILED_PATH' => $tmp . '/views',
    'APP_CONFIG_CACHE' => $tmp . '/config.php',
    'APP_EVENTS_CACHE' => $tmp . '/events.php',
    'APP_PACKAGES_CACHE' => $tmp . '/packages.php',
    'APP_ROUTES_CACHE' => $tmp . '/routes.php',
    'APP_SERVICES_CACHE' => $tmp . '/services.php',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE' => 'array',
    'LOG_CHANNEL' => 'stderr',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => $tmp . '/database.sqlite',
];

foreach ($envVars as $key => $val) {
    if (empty(getenv($key)) && empty($_ENV[$key])) {
        putenv("{$key}={$val}");
        $_ENV[$key] = $val;
    }
}

// Fallback APP_KEY jika belum diset di Vercel Dashboard
if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
    $fallbackKey = 'base64:EdOmWjpSR24miKKtY5CAoW9RdIUohFBVCuDYrZqXNFE=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
}

// Salin database SQLite siap pakai dari repository ke temp
$tmpDb = $tmp . '/database.sqlite';
if (!file_exists($tmpDb)) {
    $bundledDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($bundledDb)) {
        copy($bundledDb, $tmpDb);
    } else {
        touch($tmpDb);
    }
}

require __DIR__ . '/../vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath($tmp);
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // Tangkap error jika terjadi exception agar bisa terbaca jelas di browser
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== LARAVEL RUNTIME ERROR ===\n\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString();
}
