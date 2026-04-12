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
        Schema::create('shipping_charges', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Standard Shipping - US", "Express Shipping - International"
            $table->string('country')->nullable(); // NULL means all countries, or specific country code
            $table->decimal('min_order_value', 10, 2)->nullable(); // Minimum order value for this rule
            $table->decimal('max_order_value', 10, 2)->nullable(); // Maximum order value for this rule
            $table->decimal('charge', 10, 2); // Shipping charge amount
            $table->enum('charge_type', ['fixed', 'percentage'])->default('fixed'); // Fixed amount or percentage of order
            $table->integer('priority')->default(0); // Higher priority rules are checked first
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('country');
            $table->index('is_active');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_charges');
    }
};
