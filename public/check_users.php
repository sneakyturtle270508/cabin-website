<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "Checking database...\n";
$dbPath = base_path('database/database.sqlite');
echo "DB path: $dbPath\n";
echo 'DB exists: '.(file_exists($dbPath) ? 'YES' : 'NO')."\n";

try {
    $pdo = new PDO('sqlite:'.$dbPath);
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables: '.implode(', ', $tables)."\n";

    if (in_array('users', $tables)) {
        $stmt = $pdo->query('SELECT id, email FROM users');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo 'Users: '.count($users)."\n";
        foreach ($users as $user) {
            echo '  - ID: '.$user['id'].', Email: '.$user['email']."\n";
        }
    } else {
        echo "No users table found\n";
    }
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
