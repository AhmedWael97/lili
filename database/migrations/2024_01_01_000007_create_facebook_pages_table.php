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
        Schema::create('facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('connected_platform_id')->constrained()->onDelete('cascade');
            $table->string('page_id');
            $table->string('page_name');
            $table->text('page_access_token'); // Encrypted
            $table->string('page_category')->nullable();
            $table->integer('followers_count')->default(0);
            $table->enum('status', ['active', 'disconnected'])->default('active');
            $table->json('permissions')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('page_id');
            $table->index('status');
            $table->unique(['user_id', 'page_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_pages');
    }
};
