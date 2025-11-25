<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Assign all permissions to Super Admin
$user = User::find(1);
$permissions = Permission::all();

foreach ($permissions as $permission) {
    $user->givePermissionTo($permission);
}

echo "Permissions assigned successfully to Super Admin!\n";
echo "User: " . $user->name . " (" . $user->email . ")\n";
echo "Total permissions: " . $permissions->count() . "\n";