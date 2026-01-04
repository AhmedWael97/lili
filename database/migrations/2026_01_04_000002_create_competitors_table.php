<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('g2_url')->nullable();
            $table->string('capterra_url')->nullable();
            $table->string('trustpilot_url')->nullable();
            $table->string('producthunt_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->decimal('overall_rating', 3, 2)->nullable();
            $table->integer('review_count')->default(0);
            $table->integer('relevance_score')->default(0);
            $table->timestamps();
            
            $table->index('research_request_id');
            $table->index('relevance_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitors');
    }
};
