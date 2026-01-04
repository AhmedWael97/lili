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
        Schema::create('competitor_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('research_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Feedback type
            $table->enum('feedback_type', [
                'relevance',
                'data_quality',
                'accuracy',
                'completeness',
                'duplicate',
                'spam'
            ]);
            
            // User verdict
            $table->boolean('is_useful')->nullable(); // true = useful, false = not useful
            $table->boolean('is_relevant')->nullable(); // true = relevant, false = irrelevant
            $table->boolean('is_accurate')->nullable(); // true = accurate, false = inaccurate
            $table->boolean('is_duplicate')->default(false);
            $table->boolean('is_spam')->default(false);
            
            // Specific field feedback
            $table->json('field_corrections')->nullable(); // Store corrections per field
            
            // Rating 1-5
            $table->tinyInteger('overall_rating')->nullable();
            
            // Comments
            $table->text('comments')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable(); // Store original vs corrected data
            $table->timestamp('verified_at');
            $table->timestamps();
            
            // Indexes
            $table->index(['competitor_id', 'feedback_type']);
            $table->index('is_useful');
            $table->index('is_relevant');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitor_feedback');
    }
};
