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
        Schema::create('crypto_basket_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_basket_id')
                ->constrained('crypto_baskets')
                ->onDelete('cascade');

            $table->string('coin_id'); // CoinGecko ID, e.g. 'bitcoin'
            $table->string('symbol');  // e.g. 'BTC'
            $table->string('name');    // e.g. 'Bitcoin'

            $table->decimal('percentage', 5, 2); // e.g. 25.50%
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_basket_items');
    }
};
