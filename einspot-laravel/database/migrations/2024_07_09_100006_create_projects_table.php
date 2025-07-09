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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('client')->nullable();
            $table->string('location')->nullable();
            $table->string('duration')->nullable();
            $table->string('status')->default('Completed');
            $table->string('type')->nullable()->comment('e.g., HVAC, Fire Safety');
            $table->text('description');
            $table->string('image_url')->nullable(); // Main project image
            $table->json('images')->nullable(); // For multiple project images
            $table->json('brands_used')->nullable(); // List of strings
            $table->json('technologies')->nullable(); // List of strings
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
