<?php

// Direct database approach without Laravel bootstrap
$dbPath = __DIR__.'/../database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Setting up admin user...\n";

// Create users table if not exists
$pdo->exec('CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
)');

// Check if user exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute(['admin@example.com']);
$existing = $stmt->fetch();

if ($existing) {
    // Update password
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
    $stmt->execute([$hash, 'admin@example.com']);
    echo "Updated password for admin@example.com\n";
} else {
    // Create user
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
    $stmt->execute(['Admin', 'admin@example.com', $hash]);
    echo "Created user admin@example.com\n";
}

echo "Done! Try logging in at /cp with admin@example.com / admin123\n";
