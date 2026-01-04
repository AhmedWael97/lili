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
        Schema::create('learning_metrics', function (Blueprint $table) {
            $table->id();
            
            // Metric type
            $table->string('metric_type'); // accuracy, precision, recall, f1_score
            $table->string('component'); // search_verification, relevance_validation, quality_scoring
            
            // Performance metrics
            $table->decimal('score', 5, 4); // 0.0000 to 1.0000
            $table->integer('true_positives')->default(0);
            $table->integer('true_negatives')->default(0);
            $table->integer('false_positives')->default(0);
            $table->integer('false_negatives')->default(0);
            
            // Sample size
            $table->integer('total_samples')->default(0);
            
            // Time period
            $table->date('period_start');
            $table->date('period_end');
            
            // Configuration at time of measurement
            $table->json('config_snapshot')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['component', 'metric_type', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_metrics');
    }
};
