<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partnership_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('website')->nullable();
            $table->string('partnership_type');
            $table->text('business_description');
            $table->text('synergy_goals');
            $table->text('expected_contribution');
            $table->string('signed_name');
            $table->timestamp('signed_at')->nullable();
            $table->string('status')->default('pending'); // pending, approved, declined
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partnership_requests');
    }
};
