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
        Schema::create('brand_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('brand_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('brand_tone')->default('professional'); // professional, casual, friendly, authoritative
            $table->text('voice_characteristics')->nullable();
            $table->text('target_audience')->nullable();
            $table->text('business_goals')->nullable();
            $table->text('key_messages')->nullable();
            $table->string('forbidden_words')->nullable(); // comma-separated
            $table->string('primary_colors')->nullable(); // comma-separated hex codes
            $table->string('visual_style')->nullable();
            $table->boolean('logo_in_images')->default(false);
            $table->string('website_url')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_settings');
    }
};
