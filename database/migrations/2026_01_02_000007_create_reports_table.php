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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')->constrained('research_requests')->onDelete('cascade');
            $table->text('executive_summary')->nullable();
            $table->json('report_data')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('action_plan')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            
            $table->index('research_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
