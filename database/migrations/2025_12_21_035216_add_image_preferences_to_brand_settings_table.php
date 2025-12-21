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
        Schema::table('brand_settings', function (Blueprint $table) {
            $table->string('image_style')->nullable()->after('visual_style'); // realistic, illustration, abstract, minimalist, 3d, photographic
            $table->string('image_mood')->nullable()->after('image_style'); // energetic, calm, professional, playful, elegant, bold
            $table->text('image_composition')->nullable()->after('image_mood'); // centered, rule-of-thirds, dynamic, symmetrical
            $table->string('text_in_images')->default('minimal')->after('image_composition'); // none, minimal, prominent
            $table->text('preferred_elements')->nullable()->after('text_in_images'); // people, products, nature, abstract, workplace, lifestyle
            $table->text('avoid_elements')->nullable()->after('preferred_elements'); // specific things to avoid in images
            $table->string('image_aspect_ratio')->default('1:1')->after('avoid_elements'); // 1:1 (square), 16:9, 4:5, 9:16
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_settings', function (Blueprint $table) {
            $table->dropColumn([
                'image_style',
                'image_mood',
                'image_composition',
                'text_in_images',
                'preferred_elements',
                'avoid_elements',
                'image_aspect_ratio',
            ]);
        });
    }
};
