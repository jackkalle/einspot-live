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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User who performed action
            $table->string('action'); // e.g., 'created', 'updated', 'deleted', 'loggedin', 'loggedout'
            $table->morphs('loggable'); // Polymorphic relation to the model being acted upon (e.g., Product, Order)
            $table->text('description')->nullable(); // e.g., "User John Doe updated Product 'XYZ'"
            $table->json('properties')->nullable(); // Store old/new attributes or other relevant data
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
