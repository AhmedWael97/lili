<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_type_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'paused'])->default('active');
            $table->timestamp('activated_at');
            $table->timestamp('last_used_at')->nullable();
            $table->integer('interaction_count')->default(0);
            $table->json('settings')->nullable(); // agent-specific settings
            $table->timestamps();
            
            $table->unique(['user_id', 'agent_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_agents');
    }
};
