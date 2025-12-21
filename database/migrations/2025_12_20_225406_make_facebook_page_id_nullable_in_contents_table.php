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
        Schema::table('contents', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['facebook_page_id']);
            
            // Make the column nullable
            $table->foreignId('facebook_page_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('facebook_page_id')
                  ->references('id')
                  ->on('facebook_pages')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['facebook_page_id']);
            
            // Make the column not nullable again
            $table->foreignId('facebook_page_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('facebook_page_id')
                  ->references('id')
                  ->on('facebook_pages')
                  ->onDelete('cascade');
        });
    }
};
