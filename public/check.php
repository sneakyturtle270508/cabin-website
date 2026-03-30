<?php

$dbPath = __DIR__.'/../database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);

$stmt = $pdo->query('SELECT * FROM users');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo 'Users: '.count($users)."\n";
foreach ($users as $user) {
    echo '- Email: '.$user['email']."\n";
    echo '  Password hash: '.substr($user['password'], 0, 30)."...\n";

    if (password_verify('admin123', $user['password'])) {
        echo "  Password check: OK\n";
    } else {
        echo "  Password check: FAILED\n";
    }
}
