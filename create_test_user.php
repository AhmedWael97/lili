<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@test.com',
    'password' => bcrypt('password'),
]);

echo "User created successfully!\n";
echo "Email: test@test.com\n";
echo "Password: password\n";
echo "User ID: {$user->id}\n";
