<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('expertise');
            $table->text('bio');
            $table->json('certifications')->nullable();
            $table->boolean('voice_only_enabled')->default(true);
            $table->boolean('video_enabled')->default(true);
            $table->decimal('hourly_rate', 10, 2)->default(0.00);
            $table->string('role')->default('instructor'); // instructor, mentor, guest_speaker
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('academy_teacher_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('academy_teachers')->onDelete('cascade');
            $table->integer('day_of_week'); // 0 = Sunday, 1 = Monday, etc.
            $table->string('start_time'); // '09:00'
            $table->string('end_time'); // '17:00'
            $table->timestamps();
        });

        Schema::create('academy_audio_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('instructor_name');
            $table->integer('duration_seconds')->default(0);
            $table->string('audio_url');
            $table->string('format')->default('mp3'); // mp3, aac, wav, ogg
            $table->text('summary')->nullable();
            $table->text('transcript')->nullable();
            $table->json('chapters')->nullable();
            $table->boolean('is_downloadable')->default(true);
            $table->timestamps();
        });

        Schema::create('academy_live_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('teacher_id')->nullable()->constrained('academy_teachers')->onDelete('set null');
            $table->string('meeting_provider')->default('google_meet');
            $table->string('meeting_url')->nullable();
            $table->datetime('date');
            $table->integer('duration_minutes')->default(60);
            $table->string('session_type')->default('public_class'); // public_class, private_1_on_1, group_session, corporate_training
            $table->string('status')->default('scheduled'); // scheduled, live, ended, cancelled
            $table->text('description')->nullable();
            $table->string('target_role')->nullable()->default('all');
            $table->timestamps();
        });

        Schema::create('academy_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('academy_teachers')->onDelete('cascade');
            $table->date('booking_date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('call_type')->default('video'); // voice, video
            $table->string('meeting_url')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->timestamps();
        });

        Schema::create('academy_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->nullable()->constrained('academy_live_sessions')->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained('academy_bookings')->onDelete('set null');
            $table->string('title');
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->text('notes')->nullable();
            $table->text('ai_summary')->nullable();
            $table->integer('retention_days')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_recordings');
        Schema::dropIfExists('academy_bookings');
        Schema::dropIfExists('academy_live_sessions');
        Schema::dropIfExists('academy_audio_lessons');
        Schema::dropIfExists('academy_teacher_availability');
        Schema::dropIfExists('academy_teachers');
    }
};
