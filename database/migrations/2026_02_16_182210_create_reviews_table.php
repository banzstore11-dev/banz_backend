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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name'); // For guest reviews
            $table->string('email')->nullable(); // For guest reviews
            $table->integer('rating')->default(5);
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(false); // If user purchased the product
            $table->timestamps();
            
            $table->index('product_id');
            $table->index('user_id');
            $table->index('rating');
            $table->index(['product_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
