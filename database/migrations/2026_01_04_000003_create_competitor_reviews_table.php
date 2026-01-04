<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['g2', 'capterra', 'trustpilot', 'google', 'yelp']);
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_role')->nullable();
            $table->decimal('rating', 3, 2);
            $table->text('title')->nullable();
            $table->text('review_text');
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->json('pain_points')->nullable();
            $table->json('praise_points')->nullable();
            $table->string('review_url')->nullable();
            $table->date('review_date')->nullable();
            $table->timestamps();
            
            $table->index('competitor_id');
            $table->index(['platform', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_reviews');
    }
};
