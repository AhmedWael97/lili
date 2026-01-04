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
        Schema::table('competitors', function (Blueprint $table) {
            // Change website column to text to support longer URLs
            $table->text('website')->nullable()->change();
            
            // Also update linkedin_url to text for consistency
            if (Schema::hasColumn('competitors', 'linkedin_url')) {
                $table->text('linkedin_url')->nullable()->change();
            }
            
            // And facebook_url if it exists
            if (Schema::hasColumn('competitors', 'facebook_url')) {
                $table->text('facebook_url')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitors', function (Blueprint $table) {
            // Revert back to string(255)
            $table->string('website', 255)->nullable()->change();
            
            if (Schema::hasColumn('competitors', 'linkedin_url')) {
                $table->string('linkedin_url', 255)->nullable()->change();
            }
            
            if (Schema::hasColumn('competitors', 'facebook_url')) {
                $table->string('facebook_url', 255)->nullable()->change();
            }
        });
    }
};
