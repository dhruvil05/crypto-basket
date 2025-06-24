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
        Schema::create('basket_return_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_basket_id')->constrained()->onDelete('cascade');
            $table->integer('months'); // 3, 6, 9, 12
            $table->decimal('return_percentage', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basket_return_cycles');
    }
};
