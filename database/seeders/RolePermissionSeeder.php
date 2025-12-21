<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Content Management
            'create-content',
            'edit-content',
            'delete-content',
            'publish-content',
            'schedule-content',
            
            // Facebook Pages
            'connect-facebook-page',
            'disconnect-facebook-page',
            'manage-facebook-pages',
            
            // AI Agents - Marketing
            'use-marketing-agent',
            'use-strategist-agent',
            'use-copywriter-agent',
            'use-creative-agent',
            'use-community-manager-agent',
            'use-ads-agent',
            
            // AI Agents - Technical
            'use-qa-agent',
            'use-developer-agent',
            
            // AI Agents - Business
            'use-accountant-agent',
            'use-customer-service-agent',
            
            // Agent Management
            'activate-agents',
            'deactivate-agents',
            'configure-agents',
            'view-agent-analytics',
            
            // Comments & Messages
            'reply-to-comments',
            'reply-to-messages',
            'auto-approve-replies',
            
            // Ads Management
            'create-ad-campaigns',
            'manage-ad-campaigns',
            'view-ad-analytics',
            
            // Analytics
            'view-analytics',
            'export-analytics',
            
            // Settings
            'manage-brand-settings',
            'manage-subscription',
            'manage-billing',
            
            // Admin
            'manage-users',
            'manage-roles',
            'manage-permissions',
            'view-audit-logs',
            'manage-packages',
            'view-system-health',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role - has all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Manager role - can manage content and users but not system settings
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'create-content',
            'edit-content',
            'delete-content',
            'publish-content',
            'schedule-content',
            'connect-facebook-page',
            'disconnect-facebook-page',
            'manage-facebook-pages',
            'use-marketing-agent',
            'use-strategist-agent',
            'use-copywriter-agent',
            'use-creative-agent',
            'use-community-manager-agent',
            'use-ads-agent',
            'use-qa-agent',
            'use-developer-agent',
            'use-accountant-agent',
            'use-customer-service-agent',
            'activate-agents',
            'deactivate-agents',
            'configure-agents',
            'view-agent-analytics',
            'reply-to-comments',
            'reply-to-messages',
            'auto-approve-replies',
            'create-ad-campaigns',
            'manage-ad-campaigns',
            'view-ad-analytics',
            'view-analytics',
            'export-analytics',
            'manage-brand-settings',
            'manage-subscription',
            'manage-billing',
            'manage-users',
        ]);

        // User role - basic content creation and viewing
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'create-content',
            'edit-content',
            'schedule-content',
            'connect-facebook-page',
            'use-marketing-agent',
            'use-strategist-agent',
            'use-copywriter-agent',
            'use-creative-agent',
            'use-community-manager-agent',
            'use-qa-agent',
            'use-developer-agent',
            'use-accountant-agent',
            'use-customer-service-agent',
            'activate-agents',
            'deactivate-agents',
            'reply-to-comments',
            'reply-to-messages',
            'view-analytics',
            'manage-brand-settings',
        ]);

        // Viewer role - read-only access
        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'view-analytics',
        ]);
    }
}
