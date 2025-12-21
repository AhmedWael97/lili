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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('facebook_page_id')->constrained()->onDelete('cascade');
            $table->string('content_type'); // post, story, reel
            $table->text('caption')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'published', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('platform_post_id')->nullable();
            $table->string('agent_used')->nullable(); // strategist, copywriter, creative
            $table->json('metadata')->nullable(); // Stores reach, engagement, etc.
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('facebook_page_id');
            $table->index('status');
            $table->index('scheduled_at');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
