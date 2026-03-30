<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;
use Statamic\Facades\Role;

// Create super role if not exists
$role = Role::where('handle', 'super')->first();
if (! $role) {
    $role = Role::make()->handle('super')->title('Super')->save();
    echo "Created super role\n";
}

// Create user if not exists
$user = User::where('email', 'admin@example.com')->first();
if (! $user) {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('admin123'),
    ]);
    echo "Created user\n";
}

// Assign role
$user->assignRole('super');
echo "Assigned super role\n";

echo "Done! Login: admin@example.com / admin123\n";
