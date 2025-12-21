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
        Schema::create('competitor_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('competitor_name');
            $table->string('facebook_page_id')->nullable();
            $table->string('industry')->nullable();
            $table->json('page_data')->nullable();
            $table->json('engagement_metrics')->nullable();
            $table->json('posting_patterns')->nullable();
            $table->json('content_strategy')->nullable();
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('facebook_page_id');
            $table->unique(['user_id', 'facebook_page_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitor_analyses');
    }
};
