<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('service_needed');
            $table->text('message');
            $table->string('status')->default('new'); // new, contacted, qualified, lost, won
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('initiated'); // initiated, planning, active, review, completed
            $table->decimal('budget', 15, 2);
            $table->timestamp('agreement_signed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending'); // pending, working, review, approved
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('milestone_id')->nullable()->constrained('milestones')->onDelete('set null');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('invoice_number')->unique();
            $table->string('status')->default('unpaid'); // unpaid, paid, overdue
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->string('priority')->default('medium'); // low, medium, high
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('leads');
    }
};
