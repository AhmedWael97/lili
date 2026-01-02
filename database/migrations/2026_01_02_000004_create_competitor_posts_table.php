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
        Schema::create('competitor_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained('competitors')->onDelete('cascade');
            $table->enum('platform', ['facebook', 'instagram', 'twitter', 'linkedin', 'tiktok']);
            $table->text('post_url')->nullable();
            $table->text('post_text')->nullable();
            $table->timestamp('post_date')->nullable();
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->decimal('engagement_rate', 5, 2)->nullable();
            $table->string('content_type')->nullable(); // photo, video, carousel, text
            $table->json('hashtags')->nullable();
            $table->timestamps();
            
            $table->index('competitor_id');
            $table->index('engagement_rate');
            $table->index('post_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitor_posts');
    }
};
