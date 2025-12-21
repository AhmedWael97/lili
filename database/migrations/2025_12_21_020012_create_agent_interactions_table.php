<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_type_id')->constrained()->onDelete('cascade');
            $table->string('action'); // 'create_strategy', 'generate_content', etc.
            $table->json('input_data'); // user's input/prompt
            $table->longText('output_data')->nullable(); // agent's response
            $table->integer('tokens_used')->default(0);
            $table->integer('execution_time_ms')->nullable();
            $table->enum('feedback', ['positive', 'negative', 'neutral'])->nullable();
            $table->text('feedback_comment')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // additional context
            $table->timestamps();
            
            $table->index(['user_id', 'agent_type_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_interactions');
    }
};
