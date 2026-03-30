<?php

$dbPath = '/var/www/html/database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);
$stmt = $pdo->query("SELECT * FROM users WHERE email = 'admin@example.com'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    echo 'User: '.$user['email']."\n";
    echo 'Password hash: '.substr($user['password'], 0, 30)."...\n";
    if (password_verify('admin123', $user['password'])) {
        echo "Password check: OK\n";
    } else {
        echo "Password check: FAILED - updating...\n";
        $hash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
        $stmt->execute([$hash, 'admin@example.com']);
        echo "Password updated\n";
    }
} else {
    echo "User not found\n";
}
