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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->unique()->nullable()->after('remember_token');
            $table->enum('kyc_status', ['pending', 'approved', 'rejected'])->default('pending')->after('referral_code');
            $table->text('kyc_rejection_reason')->nullable()->after('kyc_status');
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete()->after('kyc_rejection_reason');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by', 'kyc_status', 'kyc_rejection_reason']);
        });
    }
};
