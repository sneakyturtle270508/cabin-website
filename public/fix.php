<?php

$dbPath = __DIR__.'/../database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);

// Check users
$result = $pdo->query('SELECT * FROM users');
$users = $result->fetchAll(PDO::FETCH_ASSOC);

echo 'Users found: '.count($users)."\n";
foreach ($users as $user) {
    echo 'Email: '.$user['email']."\n";
    echo 'Password hash: '.$user['password']."\n";

    // Test password
    if (password_verify('admin123', $user['password'])) {
        echo "Password verify: SUCCESS\n";
    } else {
        echo "Password verify: FAILED\n";
    }
}

// Update password to be sure
$hash = password_hash('admin123', PASSWORD_BCRYPT);
$stmt = $pdo->prepare('UPDATE users SET password = ?');
$stmt->execute([$hash]);
echo "Password reset to admin123\n";
