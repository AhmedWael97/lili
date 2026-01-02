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
        Schema::create('competitor_social_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained('competitors')->onDelete('cascade');
            $table->enum('platform', ['facebook', 'instagram', 'twitter', 'linkedin', 'tiktok']);
            $table->integer('followers')->nullable();
            $table->integer('following')->nullable();
            $table->integer('posts_count')->nullable();
            $table->decimal('avg_engagement_rate', 5, 2)->nullable();
            $table->string('posting_frequency')->nullable();
            $table->date('last_post_date')->nullable();
            $table->timestamp('scraped_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['competitor_id', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitor_social_metrics');
    }
};
