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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit', 'deposit', 'withdrawal']);
            $table->decimal('amount', 12, 2);
            $table->boolean('amount_added')->default(false); 

            $table->string('utr')->nullable();
            $table->string('screenshot')->nullable();

            // Only applicable for credit (adding funds)
            $table->enum('status', ['pending', 'approved', 'rejected'])->nullable();
            $table->text('admin_comment')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            // Useful for tracking deductions (like basket purchases)
            $table->string('source')->nullable();         // e.g., manual, basket_purchase
            $table->unsignedBigInteger('reference_id')->nullable();  // e.g., basket ID
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
