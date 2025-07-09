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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Customer
            $table->text('shipping_address'); // Could be JSON for structured address
            $table->text('billing_address')->nullable(); // Could be JSON

            $table->decimal('sub_total', 10, 2);
            $table->decimal('vat_amount', 10, 2)->default(0.00);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);

            $table->string('status')->default('pending')->comment('e.g., pending, processing, shipped, delivered, cancelled, returned');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending')->comment('e.g., pending, paid, failed');
            $table->string('payment_reference')->nullable(); // For Flutterwave/Paystack ref

            $table->text('notes')->nullable(); // Customer or admin notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
