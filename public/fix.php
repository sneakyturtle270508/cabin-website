<?php

$dbPath = __DIR__.'/../database/database.sqlite';
$pdo = new PDO('sqlite:'.$dbPath);

// Delete all users
$pdo->exec('DELETE FROM users');
echo "Deleted all users\n";

// Delete roles and role_user if they exist
$pdo->exec('DELETE FROM role_user');
$pdo->exec('DELETE FROM roles');
echo "Deleted roles\n";

// Create super role
$pdo->exec('CREATE TABLE IF NOT EXISTS roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    handle VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)');
$pdo->exec("INSERT INTO roles (title, handle) VALUES ('Super', 'super')");
echo "Created super role\n";

// Create super role permissions
$pdo->exec('CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INTEGER NOT NULL,
    permission VARCHAR(255) NOT NULL,
    PRIMARY KEY (role_id, permission)
)');
$pdo->exec("INSERT INTO role_permissions (role_id, permission) VALUES (1, 'super')");
echo "Created super permissions\n";

// Create role_user pivot
$pdo->exec('CREATE TABLE IF NOT EXISTS role_user (
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, role_id)
)');
echo "Created role_user table\n";

// Create new admin user
$hash = password_hash('admin123', PASSWORD_BCRYPT);
$pdo->exec("INSERT INTO users (name, email, password, created_at, updated_at) 
    VALUES ('Admin', 'admin@example.com', '$hash', datetime('now'), datetime('now'))");
echo "Created admin user: admin@example.com / admin123\n";

// Assign super role
$pdo->exec('INSERT INTO role_user (user_id, role_id) VALUES (1, 1)');
echo "Assigned super role to admin\n";

echo "\nDone! Login at /cp with:\n";
echo "Email: admin@example.com\n";
echo "Password: admin123\n";
