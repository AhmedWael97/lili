<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get packages
        $packages = Package::all()->keyBy('name');

        // Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@aiagents.com',
            'password' => Hash::make('123456789'),
            'company' => 'AI Agents Platform',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
        $this->createSubscription($admin, $packages['Agency']);

        // Manager User
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@aiagents.com',
            'password' => Hash::make('123456789'),
            'company' => 'Marketing Agency',
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('manager');
        $this->createSubscription($manager, $packages['Professional']);

        // Regular User 1
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('123456789'),
            'company' => 'TechStart Inc',
            'email_verified_at' => now(),
        ]);
        $user1->assignRole('user');
        $this->createSubscription($user1, $packages['Professional']);

        // Regular User 2
        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('123456789'),
            'company' => 'E-Commerce Pro',
            'email_verified_at' => now(),
        ]);
        $user2->assignRole('user');
        $this->createSubscription($user2, $packages['Starter']);

        // Regular User 3
        $user3 = User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'password' => Hash::make('123456789'),
            'company' => 'Local Business',
            'email_verified_at' => now(),
        ]);
        $user3->assignRole('user');
        $this->createSubscription($user3, $packages['Free']);

        // Viewer User
        $viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
            'password' => Hash::make('123456789'),
            'company' => 'Client Company',
            'email_verified_at' => now(),
        ]);
        $viewer->assignRole('viewer');
        $this->createSubscription($viewer, $packages['Free']);

        $this->command->info('Created 6 demo users with password: 123456789');
        $this->command->info('Admin: admin@aiagents.com (Agency Plan)');
        $this->command->info('Manager: manager@aiagents.com (Professional Plan)');
        $this->command->info('Users: john@example.com (Professional), jane@example.com (Starter), bob@example.com (Free)');
        $this->command->info('Viewer: viewer@example.com (Free)');
    }

    /**
     * Create subscription for user
     */
    private function createSubscription(User $user, Package $package)
    {
        Subscription::create([
            'user_id' => $user->id,
            'package_name' => $package->name,
            'price' => $package->price,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);
    }
}
