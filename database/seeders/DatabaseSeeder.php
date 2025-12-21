<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in proper order:
        // 1. First create roles and permissions
        $this->call(RolePermissionSeeder::class);
        
        // 2. Then create subscription packages
        $this->call(SubscriptionPackageSeeder::class);
        
        // 3. Finally create users (they need roles to exist)
        $this->call(UserSeeder::class);
        
        $this->command->info('✅ Database seeding completed successfully!');
    }
}
