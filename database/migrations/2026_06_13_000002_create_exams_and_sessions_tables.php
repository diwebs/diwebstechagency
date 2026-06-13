<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('code')->unique();
            $table->integer('duration_minutes');
            $table->integer('total_questions');
            $table->decimal('passing_score', 5, 2)->default(50.00);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // randomized, webcam_monitoring, tab_limits, browser_lock
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->text('question_text');
            $table->string('question_type')->default('single_choice'); // single_choice, multiple_choice, theory
            $table->json('options'); // [{ "id": "A", "text": "Answer 1" }, ...]
            $table->json('correct_answers'); // ["A"]
            $table->text('explanation')->nullable();
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            $table->timestamps();
        });

        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cbt_center_id')->nullable()->constrained('cbt_centers')->onDelete('set null');
            $table->foreignId('device_id')->nullable()->constrained('devices')->onDelete('set null');
            $table->string('status')->default('scheduled'); // scheduled, active, completed, flagged, void
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('anti_cheat_flags')->default(0);
            $table->json('logs')->nullable(); // detailed timed logs (webcam snaps, focus switches, etc.)
            $table->timestamps();
        });

        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('exam_session_id')->nullable()->constrained('exam_sessions')->onDelete('set null');
            $table->string('event_type'); // tab_switch, auth_failed, webcam_alert, session_lockout
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exams');
    }
};
