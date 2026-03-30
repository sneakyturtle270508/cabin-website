#!/bin/bash

mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database
mkdir -p public/img
mkdir -p /tmp/laravel
chmod -R 777 storage bootstrap/cache database public /tmp/laravel

# Set temp directory for PHP
export TMPDIR=/tmp/laravel

# Run migrations
php artisan migrate --force >> /tmp/entrypoint.log 2>&1

# Create super role using PHP
php artisan tinker --execute="
use Statamic\Facades\Role;
if (!Role::where('handle', 'super')->first()) {
    Role::make()->handle('super')->title('Super')->save();
    echo 'Super role created\n';
} else {
    echo 'Super role exists\n';
}
" >> /tmp/entrypoint.log 2>&1

# Create admin user
php artisan make:user --email=admin@example.com --name="Admin" --password=admin123 --super --force >> /tmp/entrypoint.log 2>&1

# Also try direct database insertion as backup
php << 'PHPSCRIPT' >> /tmp/entrypoint.log 2>&1
<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dbPath = base_path('database/database.sqlite');
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute(['admin@example.com']);
if ($stmt->fetch()) {
    echo "Admin user exists\n";
} else {
    // Run migrations if users table doesn't exist
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
    
    // Create super role table
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
    
    // Insert super role
    $pdo->exec("INSERT OR IGNORE INTO roles (title, handle) VALUES ('Super', 'super')");
    
    // Insert user
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
    $stmt->execute(['Admin', 'admin@example.com', $hash]);
    
    // Get role and user IDs
    $stmt = $pdo->query("SELECT id FROM roles WHERE handle = 'super'");
    $role = $stmt->fetch();
    $stmt = $pdo->query("SELECT id FROM users WHERE email = 'admin@example.com'");
    $user = $stmt->fetch();
    
    if ($role && $user) {
        $pdo->exec("INSERT OR IGNORE INTO role_user (user_id, role_id) VALUES ({$user['id']}, {$role['id']})");
        $pdo->exec("INSERT OR IGNORE INTO role_permissions (role_id, permission) VALUES ({$role['id']}, 'super')");
    }
    
    echo "Admin user created: admin@example.com / admin123\n";
}
PHPSCRIPT

echo "Entrypoint done" >> /tmp/entrypoint.log

exec apache2-foreground
