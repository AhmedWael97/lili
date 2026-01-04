<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')->constrained()->onDelete('cascade');
            $table->json('customer_personas')->nullable();
            $table->json('pain_points')->nullable();
            $table->json('needs')->nullable();
            $table->json('feature_requests')->nullable();
            $table->json('buying_factors')->nullable();
            $table->json('satisfaction_drivers')->nullable();
            $table->json('common_complaints')->nullable();
            $table->json('purchase_decision_process')->nullable();
            $table->json('marketing_channels')->nullable();
            $table->text('sentiment_summary')->nullable();
            $table->timestamps();
            
            $table->index('research_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_insights');
    }
};
