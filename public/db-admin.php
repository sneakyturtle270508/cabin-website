<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Setup script to create admin user
    $dbPath = __DIR__.'/../database/database.sqlite';

    echo "DB Path: $dbPath\n";
    echo 'DB exists: '.(file_exists($dbPath) ? 'YES' : 'NO')."\n";

    $pdo = new PDO('sqlite:'.$dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Setting up database...\n";

    // Create users table
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
    echo "Users table created\n";

    // Create roles table
    $pdo->exec('CREATE TABLE IF NOT EXISTS roles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) NOT NULL,
        handle VARCHAR(255) UNIQUE NOT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    )');
    echo "Roles table created\n";

    // Create role_user pivot
    $pdo->exec('CREATE TABLE IF NOT EXISTS role_user (
        user_id INTEGER NOT NULL,
        role_id INTEGER NOT NULL,
        PRIMARY KEY (user_id, role_id)
    )');
    echo "Role_user table created\n";

    // Create role_permissions
    $pdo->exec('CREATE TABLE IF NOT EXISTS role_permissions (
        role_id INTEGER NOT NULL,
        permission VARCHAR(255) NOT NULL,
        PRIMARY KEY (role_id, permission)
    )');
    echo "Role_permissions table created\n";

    // Create super role
    $pdo->exec("INSERT OR IGNORE INTO roles (title, handle) VALUES ('Super', 'super')");
    echo "Super role created\n";

    // Create admin user
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute(['admin@example.com']);
    if (! $stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
        $stmt->execute(['Admin', 'admin@example.com', $hash]);
        echo "Admin user created\n";
    } else {
        echo "Admin user already exists\n";
    }

    // Assign super role to admin
    $stmt = $pdo->query("SELECT id FROM roles WHERE handle = 'super'");
    $role = $stmt->fetch();
    $stmt = $pdo->query("SELECT id FROM users WHERE email = 'admin@example.com'");
    $user = $stmt->fetch();

    if ($role && $user) {
        $pdo->exec("INSERT OR IGNORE INTO role_user (user_id, role_id) VALUES ({$user['id']}, {$role['id']})");
        $pdo->exec("INSERT OR IGNORE INTO role_permissions (role_id, permission) VALUES ({$role['id']}, 'super')");
        echo "Super role assigned to admin\n";
    }

    echo "\nDone! Login at /cp with:\n";
    echo "Email: admin@example.com\n";
    echo "Password: admin123\n";

} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    echo 'Trace: '.$e->getTraceAsString()."\n";
}
