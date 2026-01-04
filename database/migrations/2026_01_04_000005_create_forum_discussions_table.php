<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')->constrained()->onDelete('cascade');
            $table->enum('source', ['reddit', 'quora', 'hackernews', 'indiehackers', 'forum']);
            $table->string('url');
            $table->string('title');
            $table->text('content');
            $table->json('pain_points')->nullable();
            $table->json('feature_requests')->nullable();
            $table->integer('upvotes')->default(0);
            $table->integer('comments_count')->default(0);
            $table->date('posted_date')->nullable();
            $table->timestamps();
            
            $table->index('research_request_id');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_discussions');
    }
};
