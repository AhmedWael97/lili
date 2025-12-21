<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AgentType;
use Illuminate\Support\Facades\DB;

class AgentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (disable foreign key checks)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('agent_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Marketing Agent
        AgentType::create([
            'code' => 'marketing',
            'name' => 'Marketing Expert',
            'category' => 'business',
            'description' => 'Senior Marketing Strategist with 20 years of experience. Provides comprehensive market research, competitor analysis, strategic content planning, budget allocation, and performance optimization.',
            'icon' => '📊',
            'color' => '#3B82F6',
            'is_active' => true,
            'features' => [
                'Marketing Strategy Development',
                'Content Calendar Planning',
                'Competitor Analysis',
                'Target Audience Research',
                'Budget Optimization',
                'Performance Analytics',
                'Content Generation',
                'Ad Campaign Management',
            ],
            'ai_model' => 'gpt-4o', // Strategy needs deep reasoning
            'model_config' => [
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'strategy_model' => 'gpt-4o',
                'copywriting_model' => 'gpt-4o-mini',
                'creative_model' => 'gpt-4o-mini',
            ],
            'sort_order' => 1,
        ]);

        // QA Agent
        AgentType::create([
            'code' => 'qa',
            'name' => 'QA Specialist',
            'category' => 'technical',
            'description' => 'Senior Quality Assurance Engineer with 20 years of experience. Provides test strategy development, automated testing, bug detection, quality metrics, and comprehensive testing documentation.',
            'icon' => '🔍',
            'color' => '#10B981',
            'is_active' => true,
            'features' => [
                'Test Plan Creation',
                'Automated Test Development',
                'Bug Detection & Reporting',
                'Test Case Design',
                'Performance Testing',
                'Security Testing',
                'Quality Metrics',
                'Test Documentation',
            ],
            'ai_model' => 'gpt-4o', // Technical analysis needs reasoning
            'model_config' => [
                'temperature' => 0.5,
                'max_tokens' => 3000,
            ],
            'sort_order' => 2,
        ]);

        // Developer Agent
        AgentType::create([
            'code' => 'developer',
            'name' => 'Senior Developer',
            'category' => 'technical',
            'description' => 'Full Stack Developer with 20 years of experience. Provides code architecture, bug fixes, feature implementation, code review, performance optimization, and technical consulting.',
            'icon' => '💻',
            'color' => '#8B5CF6',
            'is_active' => true,
            'features' => [
                'Code Architecture Design',
                'Feature Development',
                'Bug Fixing',
                'Code Review',
                'Performance Optimization',
                'Database Design',
                'API Development',
                'Technical Documentation',
            ],
            'ai_model' => 'gpt-4o', // Code needs reasoning and accuracy
            'model_config' => [
                'temperature' => 0.3,
                'max_tokens' => 4000,
            ],
            'sort_order' => 3,
        ]);

        // Accountant Agent
        AgentType::create([
            'code' => 'accountant',
            'name' => 'Senior Accountant',
            'category' => 'financial',
            'description' => 'Certified Public Accountant with 20 years of experience. Provides financial analysis, tax planning, budgeting, expense tracking, financial reporting, and compliance guidance.',
            'icon' => '💰',
            'color' => '#F59E0B',
            'is_active' => true,
            'features' => [
                'Financial Analysis',
                'Tax Planning & Compliance',
                'Budget Management',
                'Expense Tracking',
                'Financial Reporting',
                'Cash Flow Management',
                'Investment Analysis',
                'Audit Support',
            ],
            'ai_model' => 'gpt-4o', // Financial analysis needs accuracy
            'model_config' => [
                'temperature' => 0.2,
                'max_tokens' => 3000,
            ],
            'sort_order' => 4,
        ]);

        // Customer Service Agent
        AgentType::create([
            'code' => 'customer_service',
            'name' => 'Customer Service Expert',
            'category' => 'support',
            'description' => 'Customer Service Manager with 20 years of experience. Provides customer support strategies, complaint resolution, satisfaction improvement, service scripts, and customer retention tactics.',
            'icon' => '🎧',
            'color' => '#EC4899',
            'is_active' => true,
            'features' => [
                'Customer Support Strategy',
                'Complaint Resolution',
                'Service Script Development',
                'Customer Satisfaction Analysis',
                'Retention Strategies',
                'Support Team Training',
                'FAQ Development',
                'Live Chat Support',
            ],
            'ai_model' => 'gpt-4o-mini', // Customer service can be fast
            'model_config' => [
                'temperature' => 0.8,
                'max_tokens' => 2000,
            ],
            'sort_order' => 5,
        ]);

        $this->command->info('Agent types seeded successfully!');
    }
}
