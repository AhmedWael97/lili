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
        // Table already exists, just add new columns for market research
        if (!Schema::hasTable('competitors')) {
            Schema::create('competitors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('research_request_id')->nullable()->constrained('research_requests')->onDelete('cascade');
                $table->string('business_name');
                $table->string('website')->nullable();
                $table->string('facebook_url')->nullable();
                $table->string('facebook_handle')->nullable();
                $table->string('instagram_handle')->nullable();
                $table->string('twitter_handle')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->text('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('category')->nullable();
                $table->integer('relevance_score')->default(0);
                $table->timestamps();
                
                $table->index('research_request_id');
                $table->index('relevance_score');
            });
        } else {
            // Add only new columns if table exists
            Schema::table('competitors', function (Blueprint $table) {
                if (!Schema::hasColumn('competitors', 'research_request_id')) {
                    $table->foreignId('research_request_id')->nullable()->after('id')->constrained('research_requests')->onDelete('cascade');
                }
                if (!Schema::hasColumn('competitors', 'business_name')) {
                    $table->string('business_name')->nullable()->after('research_request_id');
                }
                if (!Schema::hasColumn('competitors', 'facebook_url')) {
                    $table->string('facebook_url')->nullable()->after('website');
                }
                if (!Schema::hasColumn('competitors', 'facebook_handle')) {
                    $table->string('facebook_handle')->nullable()->after('facebook_url');
                }
                if (!Schema::hasColumn('competitors', 'instagram_handle')) {
                    $table->string('instagram_handle')->nullable()->after('facebook_handle');
                }
                if (!Schema::hasColumn('competitors', 'twitter_handle')) {
                    $table->string('twitter_handle')->nullable()->after('instagram_handle');
                }
                if (!Schema::hasColumn('competitors', 'linkedin_url')) {
                    $table->string('linkedin_url')->nullable()->after('twitter_handle');
                }
                if (!Schema::hasColumn('competitors', 'address')) {
                    $table->text('address')->nullable()->after('linkedin_url');
                }
                if (!Schema::hasColumn('competitors', 'phone')) {
                    $table->string('phone')->nullable()->after('address');
                }
                if (!Schema::hasColumn('competitors', 'category')) {
                    $table->string('category')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('competitors', 'relevance_score')) {
                    $table->integer('relevance_score')->default(0)->after('category');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop columns we added, don't drop the table
        if (Schema::hasTable('competitors')) {
            Schema::table('competitors', function (Blueprint $table) {
                $table->dropColumn([
                    'research_request_id',
                    'business_name',
                    'facebook_url',
                    'facebook_handle',
                    'instagram_handle',
                    'twitter_handle',
                    'linkedin_url',
                    'address',
                    'phone',
                    'category',
                    'relevance_score'
                ]);
            });
        }
    }
};
