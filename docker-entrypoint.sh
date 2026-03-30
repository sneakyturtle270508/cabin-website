#!/bin/bash

mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database
mkdir -p public/img
mkdir -p /tmp/laravel
chmod -R 777 storage bootstrap/cache database public /tmp/laravel

# Create admin user directly with PHP
php << 'EOF'
<?php
$dbPath = '/var/www/html/database/database.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create users table
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
)");

// Create roles table
$pdo->exec("CREATE TABLE IF NOT EXISTS roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    handle VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
)");

// Create role_user pivot
$pdo->exec("CREATE TABLE IF NOT EXISTS role_user (
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, role_id)
)");

// Create role_permissions
$pdo->exec("CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INTEGER NOT NULL,
    permission VARCHAR(255) NOT NULL,
    PRIMARY KEY (role_id, permission)
)");

// Create super role
$pdo->exec("INSERT OR IGNORE INTO roles (title, handle) VALUES ('Super', 'super')");

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute(['admin@example.com']);
if (!$stmt->fetch()) {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
    $stmt->execute(['Admin', 'admin@example.com', $hash]);
    echo "Created admin user\n";
} else {
    echo "Admin user exists\n";
}

// Assign super role
$stmt = $pdo->query("SELECT id FROM roles WHERE handle = 'super'");
$role = $stmt->fetch();
$stmt = $pdo->query("SELECT id FROM users WHERE email = 'admin@example.com'");
$user = $stmt->fetch();
if ($role && $user) {
    $pdo->exec("INSERT OR IGNORE INTO role_user (user_id, role_id) VALUES ({$user['id']}, {$role['id']})");
    $pdo->exec("INSERT OR IGNORE INTO role_permissions (role_id, permission) VALUES ({$role['id']}, 'super')");
    echo "Assigned super role\n";
}
echo "Done!\n";
EOF

exec apache2-foreground
