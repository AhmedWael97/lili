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
        Schema::create('connected_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // facebook, instagram, twitter
            $table->string('platform_user_id');
            $table->string('platform_username')->nullable();
            $table->text('access_token'); // Encrypted
            $table->text('refresh_token')->nullable(); // Encrypted
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'disconnected'])->default('active');
            $table->json('permissions')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('platform');
            $table->index('status');
            $table->unique(['user_id', 'platform', 'platform_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connected_platforms');
    }
};
