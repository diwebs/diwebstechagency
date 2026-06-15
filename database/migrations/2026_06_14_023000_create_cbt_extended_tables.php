<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Safe alteration/creation of cbt_centers
        if (Schema::hasTable('cbt_centers')) {
            Schema::table('cbt_centers', function (Blueprint $table) {
                if (!Schema::hasColumn('cbt_centers', 'owner_id')) {
                    $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('cbt_centers', 'center_type')) {
                    $table->string('center_type')->nullable(); // jamb, waec, school, corporate, government
                }
                if (!Schema::hasColumn('cbt_centers', 'has_physical_location')) {
                    $table->string('has_physical_location')->nullable(); // yes, no, in_progress
                }
                if (!Schema::hasColumn('cbt_centers', 'systems_count')) {
                    $table->string('systems_count')->nullable(); // 10-20, 20-50, 50-100, 100+
                }
                if (!Schema::hasColumn('cbt_centers', 'internet_quality')) {
                    $table->string('internet_quality')->nullable(); // basic, stable, enterprise
                }
                if (!Schema::hasColumn('cbt_centers', 'power_backup')) {
                    $table->string('power_backup')->nullable(); // no, generator, inverter, full_redundancy
                }
                if (!Schema::hasColumn('cbt_centers', 'commission_rate')) {
                    $table->decimal('commission_rate', 5, 2)->default(10.00);
                }
                if (!Schema::hasColumn('cbt_centers', 'revenue')) {
                    $table->decimal('revenue', 10, 2)->default(0.00);
                }
            });
        } else {
            Schema::create('cbt_centers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('name');
                $table->string('code')->unique();
                $table->string('address');
                $table->string('city');
                $table->integer('capacity');
                $table->string('contact_email');
                $table->string('contact_phone');
                $table->string('status')->default('active');
                $table->string('center_type')->nullable();
                $table->string('has_physical_location')->nullable();
                $table->string('systems_count')->nullable();
                $table->string('internet_quality')->nullable();
                $table->string('power_backup')->nullable();
                $table->decimal('commission_rate', 5, 2)->default(10.00);
                $table->decimal('revenue', 10, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 2. cbt_live_exams
        Schema::create('cbt_live_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->timestamp('scheduled_at');
            $table->foreignId('proctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('camera_required')->default(true);
            $table->boolean('mic_required')->default(true);
            $table->boolean('browser_lock_required')->default(true);
            $table->string('status')->default('scheduled'); // scheduled, active, ended
            $table->timestamps();
        });

        // 3. cbt_exam_sessions
        Schema::create('cbt_exam_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cbt_center_id')->nullable()->constrained('cbt_centers')->onDelete('set null');
            $table->foreignId('cbt_live_exam_id')->nullable()->constrained('cbt_live_exams')->onDelete('set null');
            $table->string('exam_mode')->default('practice'); // practice, scheduled, live
            $table->string('status')->default('active'); // active, completed, flagged, void
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('anti_cheat_flags')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        // 4. cbt_exam_attempts
        Schema::create('cbt_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('cbt_exam_session_id')->constrained('cbt_exam_sessions')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->json('submitted_answers')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamps();
        });

        // 5. cbt_proctor_logs
        Schema::create('cbt_proctor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('cbt_exam_session_id')->constrained('cbt_exam_sessions')->onDelete('cascade');
            $table->foreignId('proctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action_type'); // warn, pause, resume, terminate, note
            $table->text('message')->nullable();
            $table->timestamps();
        });

        // 6. cbt_center_enrollments
        Schema::create('cbt_center_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('organization_name');
            $table->string('center_type');
            $table->string('has_physical_location');
            $table->string('systems_count');
            $table->string('internet_quality');
            $table->string('power_backup');
            $table->string('status')->default('pending'); // pending, under_review, approved, rejected
            $table->timestamps();
        });

        // 7. cbt_center_devices
        Schema::create('cbt_center_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_center_id')->constrained('cbt_centers')->onDelete('cascade');
            $table->string('seat_number');
            $table->string('device_name');
            $table->string('ip_address');
            $table->string('system_status')->default('online'); // online, offline, testing
            $table->integer('cpu_usage')->default(12);
            $table->integer('ram_usage')->default(32);
            $table->string('webcam_status')->default('active'); // active, inactive
            $table->integer('battery_level')->default(100);
            $table->timestamps();
        });

        // 8. cbt_candidate_flags
        Schema::create('cbt_candidate_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('cbt_exam_session_id')->constrained('cbt_exam_sessions')->onDelete('cascade');
            $table->string('violation_type'); // tab_switch, window_switch, no_face, multiple_faces, dev_tools
            $table->text('details')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        // 9. cbt_certificates
        Schema::create('cbt_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->decimal('grade', 5, 2);
            $table->date('issue_date');
            $table->text('qr_code_content');
            $table->string('signature_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_certificates');
        Schema::dropIfExists('cbt_candidate_flags');
        Schema::dropIfExists('cbt_center_devices');
        Schema::dropIfExists('cbt_center_enrollments');
        Schema::dropIfExists('cbt_proctor_logs');
        Schema::dropIfExists('cbt_exam_attempts');
        Schema::dropIfExists('cbt_exam_sessions');
        Schema::dropIfExists('cbt_live_exams');
        
        // Safe roll back of cbt_centers modifications
        if (Schema::hasTable('cbt_centers')) {
            Schema::table('cbt_centers', function (Blueprint $table) {
                $cols = ['owner_id', 'center_type', 'has_physical_location', 'systems_count', 'internet_quality', 'power_backup', 'commission_rate', 'revenue'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('cbt_centers', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
