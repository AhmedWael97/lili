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
        Schema::create('usage_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('month_year'); // Format: 2025-06
            $table->integer('posts_count')->default(0);
            $table->integer('comment_replies_count')->default(0);
            $table->integer('messages_count')->default(0);
            $table->decimal('ad_spend_total', 10, 2)->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('month_year');
            $table->unique(['user_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_tracking');
    }
};
