<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "=== DIAGNOSTIC ===\n\n";

// Check database
$dbPath = base_path('database/database.sqlite');
echo "DB path: $dbPath\n";
echo 'DB exists: '.(file_exists($dbPath) ? 'YES' : 'NO')."\n";

try {
    $pdo = new PDO('sqlite:'.$dbPath);
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables: '.implode(', ', $tables)."\n";

    if (in_array('users', $tables)) {
        $stmt = $pdo->query('SELECT id, email FROM users');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo 'Users: '.count($users)."\n";
        foreach ($users as $user) {
            echo '  - ID: '.$user['id'].', Email: '.$user['email']."\n";
        }
    } else {
        echo "No users table found\n";
    }
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}

echo "\n=== ATTEMPTING TO CREATE USER ===\n\n";

// Try to create user
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Check if user exists
$userModel = $app->make(User::class);
$existingUser = $userModel->where('email', 'admin@cabins.com')->first();

if ($existingUser) {
    echo "User already exists: admin@cabins.com\n";
} else {
    echo "User does not exist, creating...\n";
    $user = $userModel->create([
        'name' => 'Admin User',
        'email' => 'admin@cabins.com',
        'password' => bcrypt('admin123'),
    ]);
    echo 'User created: '.$user->email."\n";
}

echo "\n=== DONE ===\n";
