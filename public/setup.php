<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "=== RUNNING MIGRATIONS ===\n\n";

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Run migrations
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Statamic\Facades\Role;

Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();

echo "\n=== CREATING USER ===\n\n";

// Create user
$userModel = $app->make(User::class);
$existingUser = $userModel->where('email', 'admin@cabins.com')->first();

if ($existingUser) {
    echo "User already exists: admin@cabins.com\n";
} else {
    echo "Creating user...\n";
    try {
        $user = $userModel->create([
            'name' => 'Admin User',
            'email' => 'admin@cabins.com',
            'password' => bcrypt('admin123'),
        ]);
        echo 'User created: '.$user->email."\n";

        // Make super user
        $superRole = Role::create()
            ->handle('super')
            ->title('Super')
            ->permissions(['super'])
            ->save();

        $user->assignRole('super');
        echo "User assigned super role\n";
    } catch (Exception $e) {
        echo 'Error: '.$e->getMessage()."\n";
    }
}

echo "\n=== DONE ===\n";
