<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_agent_id')->constrained()->onDelete('cascade');
            $table->string('agent_code'); // marketing, qa, developer, etc.
            
            // Business Foundation
            $table->string('business_name')->nullable();
            $table->string('industry')->nullable();
            $table->text('products_services')->nullable();
            $table->text('unique_value_proposition')->nullable();
            $table->text('competitors')->nullable();
            
            // Brand Identity
            $table->json('brand_colors')->nullable(); // {primary, secondary, accent}
            $table->string('brand_fonts')->nullable();
            $table->string('brand_tone')->nullable(); // professional, casual, playful, luxury
            $table->text('brand_personality')->nullable();
            $table->json('brand_assets')->nullable(); // {logo_url, images}
            $table->text('brand_story')->nullable();
            
            // Target Audience
            $table->json('target_audience')->nullable(); // {age, location, interests, income}
            $table->text('pain_points')->nullable();
            $table->json('online_presence')->nullable(); // platforms they use
            $table->text('buying_motivations')->nullable();
            
            // Marketing Goals & Budget
            $table->json('marketing_goals')->nullable(); // [brand_awareness, leads, sales]
            $table->decimal('monthly_budget', 10, 2)->nullable();
            $table->string('timeline')->nullable();
            $table->json('key_metrics')->nullable(); // [engagement, conversions, reach]
            
            // Current Marketing Status
            $table->json('current_platforms')->nullable(); // {facebook, instagram, linkedin}
            $table->json('existing_accounts')->nullable(); // {platform: account_url}
            $table->text('whats_working')->nullable();
            $table->text('whats_not_working')->nullable();
            
            // Content Strategy
            $table->json('content_types')->nullable(); // [social_posts, blogs, ads, emails]
            $table->string('posting_frequency')->nullable(); // daily, 3x/week, etc.
            $table->json('focus_keywords')->nullable();
            $table->text('topics_to_avoid')->nullable();
            
            // Approval & Communication
            $table->boolean('requires_approval')->default(true);
            $table->string('contact_person')->nullable();
            $table->string('communication_preference')->nullable(); // email, dashboard
            
            // Status
            $table->boolean('is_complete')->default(false);
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'agent_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_configurations');
    }
};
