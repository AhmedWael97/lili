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
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->integer('days');
            $table->json('content_calendar');
            $table->json('strategic_recommendations')->nullable();
            $table->json('brand_context');
            $table->enum('status', ['draft', 'in_progress', 'completed'])->default('draft');
            $table->integer('content_generated')->default(0);
            $table->integer('content_total')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategies');
    }
};
