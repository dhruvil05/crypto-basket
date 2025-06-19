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
        Schema::create('basket_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('crypto_basket_id')->constrained()->onDelete('cascade');

            $table->decimal('amount', 18, 8); // Amount invested at time of purchase
            $table->json('snapshot')->nullable(); // Optional: basket structure at purchase time
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basket_purchases');
    }
};
