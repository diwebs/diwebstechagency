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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('deadline');
            $table->string('payment_status')->default('unpaid')->after('payment_method'); // unpaid, pending, paid
            $table->decimal('payment_amount', 12, 2)->nullable()->after('payment_status');
            $table->string('payment_proof')->nullable()->after('payment_amount');
            $table->string('payment_txid')->nullable()->after('payment_proof');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'payment_amount', 'payment_proof', 'payment_txid']);
        });
    }
};
