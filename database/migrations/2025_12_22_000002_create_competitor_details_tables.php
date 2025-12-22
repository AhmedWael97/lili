<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Competitor organic keywords
        Schema::create('competitor_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('organic'); // organic or paid
            $table->string('keyword');
            $table->integer('position')->nullable();
            $table->integer('search_volume')->nullable();
            $table->decimal('cpc', 8, 2)->nullable();
            $table->string('url')->nullable();
            $table->integer('traffic')->nullable();
            $table->decimal('traffic_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['competitor_id', 'type']);
            $table->index('keyword');
        });

        // Competitor backlinks
        Schema::create('competitor_backlinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->string('source_url', 500);
            $table->string('target_url', 500);
            $table->string('anchor_text')->nullable();
            $table->integer('domain_rating')->nullable();
            $table->integer('url_rating')->nullable();
            $table->string('link_type')->nullable(); // dofollow, nofollow
            $table->timestamp('first_seen')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();

            $table->index('competitor_id');
        });

        // Competitor social profiles (detailed)
        Schema::create('competitor_social_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // facebook, instagram, twitter, linkedin, tiktok
            $table->string('profile_url');
            $table->string('username')->nullable();
            $table->integer('followers')->nullable();
            $table->integer('following')->nullable();
            $table->integer('posts_count')->nullable();
            $table->decimal('engagement_rate', 5, 2)->nullable();
            $table->integer('avg_likes')->nullable();
            $table->integer('avg_comments')->nullable();
            $table->string('posting_frequency')->nullable(); // daily, weekly, etc.
            $table->json('content_themes')->nullable();
            $table->json('top_posts')->nullable(); // Store top 5 posts
            $table->timestamp('last_scraped')->nullable();
            $table->timestamps();

            $table->index(['competitor_id', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitor_social_profiles');
        Schema::dropIfExists('competitor_backlinks');
        Schema::dropIfExists('competitor_keywords');
    }
};
