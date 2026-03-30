<?php

$dbPath = __DIR__.'/../database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);

echo "Checking users table...\n";

$result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
if (! $result->fetch()) {
    echo "Creating users table...\n";
    $pdo->exec('
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP,
            updated_at TIMESTAMP
        )
    ');
    echo "Users table created\n";
}

// Check for admin user
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute(['admin@cabins.com']);
$user = $stmt->fetch();

if ($user) {
    echo "Admin user exists!\n";
    echo 'Password hash: '.substr($user['password'], 0, 30)."...\n";

    // Verify password
    if (password_verify('admin123', $user['password'])) {
        echo "Password verification: SUCCESS\n";
    } else {
        echo "Password verification: FAILED - updating password\n";
        $hash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
        $stmt->execute([$hash, 'admin@cabins.com']);
        echo "Password updated\n";
    }
} else {
    echo "Creating admin user...\n";
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
    $stmt->execute(['Admin User', 'admin@cabins.com', $hash]);
    echo "Admin user created: admin@cabins.com / admin123\n";
}

echo "\nDone! Try logging in at /cp\n";
