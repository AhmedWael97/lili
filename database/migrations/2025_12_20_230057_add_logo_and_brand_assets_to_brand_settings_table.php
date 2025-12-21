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
            $table->string('logo_path')->nullable()->after('logo_in_images');
            $table->string('secondary_color')->nullable()->after('primary_colors');
            $table->string('font_family')->nullable()->after('visual_style');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_settings', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'secondary_color', 'font_family']);
        });
    }
};
