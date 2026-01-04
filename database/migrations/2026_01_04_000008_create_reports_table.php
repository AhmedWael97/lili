<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')->constrained()->onDelete('cascade');
            $table->text('executive_summary')->nullable();
            $table->json('report_sections')->nullable(); // All 12 sections structured
            $table->json('opportunities')->nullable();
            $table->json('risks')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('action_plan')->nullable();
            $table->string('pdf_path')->nullable();
            $table->integer('competitor_count')->default(0);
            $table->integer('review_count')->default(0);
            $table->timestamps();
            
            $table->index('research_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
