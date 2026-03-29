<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo '<h1>Diagnostic Report</h1>';
echo '<pre>';

echo "=== PHP Info ===\n";
echo 'Version: '.PHP_VERSION."\n";

echo "\n=== ini_get values ===\n";
echo 'display_errors: '.ini_get('display_errors')."\n";
echo 'error_reporting: '.ini_get('error_reporting')."\n";
echo 'log_errors: '.ini_get('log_errors')."\n";
echo 'error_log: '.ini_get('error_log')."\n";

echo "\n=== Filesystem Paths ===\n";
echo 'DOCUMENT_ROOT: '.($_SERVER['DOCUMENT_ROOT'] ?? 'not set')."\n";
echo 'SCRIPT_FILENAME: '.($_SERVER['SCRIPT_FILENAME'] ?? 'not set')."\n";

echo "\n=== Checking Laravel paths ===\n";
$basePath = dirname(__DIR__);
echo "Base path: $basePath\n";

$pathsToCheck = [
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache',
    'config/app.php',
    'vendor/autoload.php',
    'public/index.php',
];

foreach ($pathsToCheck as $path) {
    $fullPath = $basePath.'/'.$path;
    $exists = file_exists($fullPath);
    $writable = is_writable(dirname($fullPath));
    echo "$path: ".($exists ? 'EXISTS' : 'MISSING').' | writable: '.($writable ? 'YES' : 'NO')."\n";
}

echo "\n=== Laravel Config ===\n";
if (file_exists($basePath.'/vendor/autoload.php')) {
    require_once $basePath.'/vendor/autoload.php';

    if (file_exists($basePath.'/bootstrap/app.php')) {
        $app = require_once $basePath.'/bootstrap/app.php';
        echo "Bootstrap app.php loaded\n";
    }
}

echo "\n=== Test Statamic ===\n";
if (class_exists('Statamic\Facades\Antlers')) {
    echo "Statamic Antlers is available\n";
} else {
    echo "Statamic Antlers NOT available\n";
}

echo "\n=== Env Variables ===\n";
echo 'APP_KEY: '.(getenv('APP_KEY') ? 'SET' : 'NOT SET')."\n";
echo 'APP_DEBUG: '.(getenv('APP_DEBUG') ?: 'NOT SET')."\n";
echo 'APP_ENV: '.(getenv('APP_ENV') ?: 'NOT SET')."\n";

echo "\n=== Last 50 lines of error log ===\n";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $lines = file($errorLog);
    echo implode('', array_slice($lines, -50));
} else {
    echo "Error log file not found or empty\n";
}

echo '</pre>';
