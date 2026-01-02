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
        Schema::create('market_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')->constrained('research_requests')->onDelete('cascade');
            $table->string('market_size_estimate')->nullable();
            $table->decimal('growth_rate', 5, 2)->nullable();
            $table->enum('competition_level', ['low', 'medium', 'high'])->nullable();
            $table->json('target_audience')->nullable();
            $table->json('trends')->nullable();
            $table->json('opportunities')->nullable();
            $table->json('threats')->nullable();
            $table->json('barriers_to_entry')->nullable();
            $table->text('ai_analysis')->nullable();
            $table->timestamps();
            
            $table->index('research_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_analysis');
    }
};
