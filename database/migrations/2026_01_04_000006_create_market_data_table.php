<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')->constrained()->onDelete('cascade');
            $table->string('market_size_estimate')->nullable();
            $table->decimal('growth_rate', 5, 2)->nullable();
            $table->enum('market_maturity', ['emerging', 'growing', 'mature', 'declining'])->nullable();
            $table->enum('competition_level', ['low', 'medium', 'high'])->nullable();
            $table->json('target_audience')->nullable();
            $table->json('trends')->nullable();
            $table->json('technology_trends')->nullable();
            $table->json('barriers_to_entry')->nullable();
            $table->text('market_overview')->nullable();
            $table->timestamps();
            
            $table->index('research_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_data');
    }
};
