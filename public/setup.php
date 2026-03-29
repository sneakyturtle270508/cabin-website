<?php

use Illuminate\Contracts\Console\Kernel;

// Run migrations first
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$dbPath = __DIR__.'/../database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);

// Check if users table exists
$result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
if ($result->fetch()) {
    echo "Users table exists\n";

    // Check if admin user exists
    $stmt = $pdo->query("SELECT * FROM users WHERE email = 'admin@cabins.com'");
    if ($stmt->fetch()) {
        echo "Admin user already exists\n";
    } else {
        echo "Creating admin user...\n";
        $hash = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->exec("INSERT INTO users (name, email, password, created_at, updated_at) VALUES ('Admin User', 'admin@cabins.com', '$hash', datetime('now'), datetime('now'))");
        echo "Admin user created: admin@cabins.com / admin123\n";
    }
} else {
    echo "Users table does not exist. Running migrations...\n";

    // Create users table manually
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

    // Create admin user
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->exec("INSERT INTO users (name, email, password, created_at, updated_at) VALUES ('Admin User', 'admin@cabins.com', '$hash', datetime('now'), datetime('now'))");
    echo "Admin user created: admin@cabins.com / admin123\n";
}
