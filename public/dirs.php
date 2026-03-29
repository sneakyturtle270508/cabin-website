<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<h1>Directory Check</h1>';
echo '<pre>';

$basePath = '/var/www/html';

echo "=== Listing storage directory ===\n";
if (is_dir($basePath.'/storage')) {
    echo "Contents of storage/:\n";
    foreach (scandir($basePath.'/storage') as $item) {
        if ($item != '.' && $item != '..') {
            echo "  $item\n";
        }
    }
} else {
    echo "storage/ does not exist!\n";
}

echo "\n=== Listing storage/framework directory ===\n";
if (is_dir($basePath.'/storage/framework')) {
    echo "Contents of storage/framework/:\n";
    foreach (scandir($basePath.'/storage/framework') as $item) {
        if ($item != '.' && $item != '..') {
            echo "  $item\n";
        }
    }
} else {
    echo "storage/framework/ does not exist!\n";
}

echo "\n=== Trying to create cache directory ===\n";
$result = mkdir($basePath.'/storage/framework/cache/data', 0777, true);
echo 'mkdir result: '.($result ? 'SUCCESS' : 'FAILED')."\n";

$result = mkdir($basePath.'/storage/framework/sessions', 0777, true);
echo 'mkdir sessions result: '.($result ? 'SUCCESS' : 'FAILED')."\n";

$result = mkdir($basePath.'/bootstrap/cache', 0777, true);
echo 'mkdir bootstrap/cache result: '.($result ? 'SUCCESS' : 'FAILED')."\n";

echo "\n=== Checking if directories now exist ===\n";
echo 'storage/framework/cache: '.(is_dir($basePath.'/storage/framework/cache') ? 'EXISTS' : 'MISSING')."\n";
echo 'storage/framework/sessions: '.(is_dir($basePath.'/storage/framework/sessions') ? 'EXISTS' : 'MISSING')."\n";
echo 'bootstrap/cache: '.(is_dir($basePath.'/bootstrap/cache') ? 'EXISTS' : 'MISSING')."\n";

echo "\n=== App Debug from env ===\n";
echo 'getenv(APP_DEBUG): '.(getenv('APP_DEBUG') ?: 'NOT SET')."\n";
echo '$_ENV["APP_DEBUG"]: '.($_ENV['APP_DEBUG'] ?? 'NOT SET')."\n";

echo '</pre>';
