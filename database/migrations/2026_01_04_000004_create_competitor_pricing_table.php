<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->string('tier_name')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('billing_period')->nullable(); // monthly, yearly, one-time
            $table->string('pricing_model')->nullable(); // Changed from enum to string for flexibility
            $table->json('features')->nullable();
            $table->integer('user_limit')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->string('currency')->default('USD');
            $table->timestamps();
            
            $table->index('competitor_id');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_pricing');
    }
};
