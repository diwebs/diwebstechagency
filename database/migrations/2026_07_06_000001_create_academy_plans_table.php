<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->string('plan_name');
            $table->boolean('includes_live_class')->default(false);
            $table->boolean('includes_audio')->default(true);
            $table->boolean('includes_mentorship')->default(false);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // admin user id
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_plans');
    }
};
