<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 'marketing', 'qa', 'developer', etc.
            $table->string('name'); // 'Marketing Agent'
            $table->string('category'); // 'business', 'technical', 'creative'
            $table->text('description');
            $table->string('icon')->nullable(); // icon class or emoji
            $table->string('color')->default('#3B82F6'); // brand color
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable(); // list of features this agent provides
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_types');
    }
};
