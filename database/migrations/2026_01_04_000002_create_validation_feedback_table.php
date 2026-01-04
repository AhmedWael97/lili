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
        Schema::create('validation_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // What was validated
            $table->string('validation_type'); // search_result, competitor_data, relevance_score
            $table->string('item_identifier'); // URL, competitor name, etc.
            
            // System's prediction vs user's verdict
            $table->integer('system_score')->nullable(); // What our algorithm predicted
            $table->boolean('system_prediction')->nullable(); // true = pass, false = reject
            $table->boolean('user_verdict')->nullable(); // true = correct, false = incorrect
            
            // Learning data
            $table->json('features')->nullable(); // Features that led to prediction
            $table->json('correction_data')->nullable(); // What should have been predicted
            
            // Timestamps
            $table->timestamp('validated_at');
            $table->timestamps();
            
            // Indexes
            $table->index(['validation_type', 'user_verdict']);
            $table->index('validated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_feedback');
    }
};
