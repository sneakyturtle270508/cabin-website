<?php

$dbPath = '/var/www/html/database/database.sqlite';
if (! file_exists($dbPath)) {
    echo "DB does not exist\n";
    exit;
}
$pdo = new PDO('sqlite:'.$dbPath);
$stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo 'Tables: '.implode(', ', $tables)."\n";
$stmt = $pdo->query('SELECT * FROM users');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo 'Users: '.count($users)."\n";
foreach ($users as $u) {
    echo '- '.$u['email']."\n";
}
