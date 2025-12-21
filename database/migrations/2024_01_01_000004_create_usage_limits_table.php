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
        Schema::create('usage_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->integer('facebook_pages_limit');
            $table->integer('posts_per_month_limit'); // -1 = unlimited
            $table->integer('comment_replies_limit'); // -1 = unlimited
            $table->integer('messages_limit'); // -1 = unlimited
            $table->boolean('ad_campaigns_enabled')->default(false);
            $table->decimal('ad_spend_limit', 10, 2)->default(0); // 0 = unlimited
            $table->timestamps();

            $table->index('subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_limits');
    }
};
