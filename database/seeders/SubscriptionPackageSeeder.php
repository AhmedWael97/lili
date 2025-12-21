<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Free',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'stripe_plan_id' => 'price_free_plan',
                'features' => json_encode([
                    'facebook_pages' => 1,
                    'posts_per_month' => 10,
                    'comment_replies_per_month' => 50,
                    'messages_per_month' => 0,
                    'ad_campaign_proposals' => false,
                    'ad_campaign_execution' => false,
                    'ad_spend_limit' => 0,
                    'analytics' => 'basic',
                    'support' => 'community',
                    'agents' => ['copywriter'],
                    'api_access' => false,
                    'team_members' => 1,
                    'white_label' => false,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Starter',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'stripe_plan_id' => 'price_starter_plan',
                'features' => json_encode([
                    'facebook_pages' => 3,
                    'posts_per_month' => 100,
                    'comment_replies_per_month' => 500,
                    'messages_per_month' => 100,
                    'ad_campaign_proposals' => true,
                    'ad_campaign_execution' => false,
                    'ad_spend_limit' => 0,
                    'analytics' => 'standard',
                    'support' => 'email',
                    'agents' => ['strategist', 'copywriter', 'community-manager'],
                    'api_access' => false,
                    'team_members' => 1,
                    'white_label' => false,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'price' => 99.00,
                'billing_cycle' => 'monthly',
                'stripe_plan_id' => 'price_professional_plan',
                'features' => json_encode([
                    'facebook_pages' => 10,
                    'posts_per_month' => 500,
                    'comment_replies_per_month' => -1, // unlimited
                    'messages_per_month' => -1, // unlimited
                    'ad_campaign_proposals' => true,
                    'ad_campaign_execution' => true,
                    'ad_spend_limit' => 5000,
                    'analytics' => 'advanced',
                    'support' => 'priority-email',
                    'agents' => ['strategist', 'copywriter', 'creative', 'community-manager', 'ads'],
                    'api_access' => false,
                    'team_members' => 3,
                    'white_label' => false,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Agency',
                'price' => 299.00,
                'billing_cycle' => 'monthly',
                'stripe_plan_id' => 'price_agency_plan',
                'features' => json_encode([
                    'facebook_pages' => -1, // unlimited
                    'posts_per_month' => -1, // unlimited
                    'comment_replies_per_month' => -1, // unlimited
                    'messages_per_month' => -1, // unlimited
                    'ad_campaign_proposals' => true,
                    'ad_campaign_execution' => true,
                    'ad_spend_limit' => -1, // unlimited
                    'analytics' => 'advanced',
                    'support' => 'phone-email-dedicated',
                    'agents' => ['strategist', 'copywriter', 'creative', 'community-manager', 'ads'],
                    'api_access' => true,
                    'team_members' => 10,
                    'white_label' => true,
                    'custom_agent_training' => true,
                    'multi_agent_orchestration' => true,
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }

        $this->command->info('✅ Created 4 subscription packages: Free, Starter, Professional, Agency');
    }
}
