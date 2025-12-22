<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Marketing OS Phase 1 Schema
     */
    public function up(): void
    {
        // Brands table - Core business information
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('website')->nullable();
            $table->string('industry');
            $table->string('country');
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->json('target_audience')->nullable();
            $table->json('value_proposition')->nullable();
            $table->json('products_services')->nullable();
            $table->decimal('monthly_budget', 12, 2)->nullable();
            $table->string('budget_tier')->default('small'); // small, medium, large
            $table->timestamps();

            $table->index('user_id');
            $table->index('industry');
            $table->index('country');
        });

        // Markets table - Market intelligence data
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('industry');
            $table->string('country');
            $table->string('maturity_level')->nullable(); // emerging, growing, mature, declining
            $table->integer('search_volume')->nullable();
            $table->json('trends')->nullable();
            $table->json('opportunities')->nullable();
            $table->json('risks')->nullable();
            $table->json('seasonality')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();

            $table->index('brand_id');
            $table->index(['industry', 'country']);
        });

        // Country profiles - Country-specific marketing data
        Schema::create('country_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique();
            $table->string('country_name');
            $table->json('platform_popularity')->nullable(); // FB, IG, TikTok rankings
            $table->json('cpm_benchmarks')->nullable();
            $table->decimal('avg_cpm', 8, 2)->nullable();
            $table->json('purchasing_power')->nullable();
            $table->json('languages')->nullable();
            $table->json('cultural_notes')->nullable();
            $table->json('regulations')->nullable();
            $table->timestamps();

            $table->index('country_code');
        });

        // Competitors table - Competitor intelligence
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('website')->nullable();
            $table->json('social_profiles')->nullable(); // URLs for all platforms
            $table->json('positioning')->nullable();
            $table->json('messaging')->nullable();
            $table->json('pricing_signals')->nullable();
            $table->json('channels')->nullable(); // Active marketing channels
            $table->json('seo_data')->nullable(); // Traffic, keywords, backlinks
            $table->json('content_strategy')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();

            $table->index('brand_id');
        });

        // Strategy plans - Complete marketing strategies
        Schema::create('strategy_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('status')->default('draft'); // draft, active, completed, archived
            $table->json('swot_analysis')->nullable();
            $table->json('positioning')->nullable();
            $table->json('channel_strategy')->nullable();
            $table->json('funnel_design')->nullable();
            $table->json('budget_allocation')->nullable();
            $table->json('content_themes')->nullable();
            $table->json('messaging_pillars')->nullable();
            $table->json('kpis')->nullable();
            $table->json('execution_priorities')->nullable();
            $table->json('risks_compliance')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index('brand_id');
            $table->index('status');
        });

        // KPI benchmarks - Industry and country benchmarks
        Schema::create('kpi_benchmarks', function (Blueprint $table) {
            $table->id();
            $table->string('industry');
            $table->string('country')->nullable();
            $table->string('channel'); // facebook, instagram, google, etc
            $table->decimal('avg_engagement_rate', 8, 4)->nullable();
            $table->decimal('avg_ctr', 8, 4)->nullable();
            $table->decimal('avg_cpm', 8, 2)->nullable();
            $table->decimal('avg_cpc', 8, 2)->nullable();
            $table->decimal('avg_conversion_rate', 8, 4)->nullable();
            $table->integer('posts_per_week')->nullable();
            $table->decimal('follower_growth_rate', 8, 2)->nullable();
            $table->json('best_content_types')->nullable();
            $table->json('best_posting_times')->nullable();
            $table->string('source')->nullable(); // internal, industry_report, api
            $table->timestamps();

            $table->index(['industry', 'country', 'channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_benchmarks');
        Schema::dropIfExists('strategy_plans');
        Schema::dropIfExists('competitors');
        Schema::dropIfExists('country_profiles');
        Schema::dropIfExists('markets');
        Schema::dropIfExists('brands');
    }
};
