<?php

$dbPath = __DIR__.'/../database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== DATABASE SETUP ===\n\n";

// Check if users table exists
$result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
if ($result->fetch()) {
    echo "Users table exists\n";
} else {
    echo "Creating users table...\n";
    $pdo->exec('
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )
    ');
    echo "Users table created\n";
}

// Check if admin user exists
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute(['admin@cabins.com']);
if ($stmt->fetch()) {
    echo "Admin user already exists: admin@cabins.com\n";
} else {
    echo "Creating admin user...\n";
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
    $stmt->execute(['Admin User', 'admin@cabins.com', $hash]);
    echo "Admin user created: admin@cabins.com / admin123\n";
}

// Create roles table if needed
$result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='roles'");
if ($result->fetch()) {
    echo "Roles table exists\n";
} else {
    echo "Creating roles table...\n";
    $pdo->exec('
        CREATE TABLE roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            handle VARCHAR(255) UNIQUE NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )
    ');
    echo "Creating role_user pivot table...\n";
    $pdo->exec('
        CREATE TABLE role_user (
            user_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            PRIMARY KEY (user_id, role_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        )
    ');
    echo "Roles table created\n";
}

// Check if super role exists
$stmt = $pdo->prepare('SELECT * FROM roles WHERE handle = ?');
$stmt->execute(['super']);
if ($stmt->fetch()) {
    echo "Super role already exists\n";
} else {
    echo "Creating super role...\n";
    $stmt = $pdo->prepare("INSERT INTO roles (title, handle, created_at, updated_at) VALUES (?, ?, datetime('now'), datetime('now'))");
    $stmt->execute(['Super', 'super']);
    echo "Super role created\n";
}

// Assign super role to admin user
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute(['admin@cabins.com']);
$user = $stmt->fetch();
$stmt = $pdo->prepare('SELECT id FROM roles WHERE handle = ?');
$stmt->execute(['super']);
$role = $stmt->fetch();

if ($user && $role) {
    $stmt = $pdo->prepare('SELECT * FROM role_user WHERE user_id = ? AND role_id = ?');
    $stmt->execute([$user['id'], $role['id']]);
    if ($stmt->fetch()) {
        echo "Admin user already has super role\n";
    } else {
        $stmt = $pdo->prepare('INSERT INTO role_user (user_id, role_id) VALUES (?, ?)');
        $stmt->execute([$user['id'], $role['id']]);
        echo "Admin user assigned super role\n";
    }
}

echo "\n=== DONE ===\n";
echo "You can now login at /cp with:\n";
echo "Email: admin@cabins.com\n";
echo "Password: admin123\n";
