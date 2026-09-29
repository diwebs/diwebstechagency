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
        // 1. Departments Table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('head_id')->nullable(); // Set after staff table is created
            $table->timestamps();
        });

        // 2. Staff Roles Table
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->string('title');
            $table->text('permissions')->nullable(); // JSON list of permission strings
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });

        // 3. Staff Members Table
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('profile_picture')->nullable();
            
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            
            $table->string('employment_type')->default('Full-time'); // Full-time / Contract / Remote / Internship
            $table->string('salary_grade')->nullable();
            $table->decimal('base_salary', 10, 2)->default(0.00);
            $table->date('date_hired')->nullable();
            $table->unsignedBigInteger('reporting_manager_id')->nullable();
            $table->string('office_location')->nullable();
            $table->string('status')->default('Active'); // Active / Suspended / Resigned
            
            $table->text('two_factor_secret')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->boolean('force_password_change')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('role_id')->references('id')->on('staff_roles')->onDelete('set null');
        });

        // 4. Update Departments table to link head_id foreign key
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('head_id')->references('id')->on('staff_members')->onDelete('set null');
        });

        // 5. Staff Attendance Table
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->date('date');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->string('status')->default('Present'); // Present / Late / Absent / Remote
            $table->integer('late_minutes')->default(0);
            $table->string('ip_address')->nullable();
            $table->string('device_info')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff_members')->onDelete('cascade');
            $table->unique(['staff_id', 'date']);
        });

        // 6. Staff Payrolls Table
        Schema::create('staff_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('month'); // e.g. "2026-06"
            $table->decimal('base_salary', 10, 2);
            $table->decimal('bonuses', 10, 2)->default(0.00);
            $table->decimal('deductions', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('net_salary', 10, 2);
            $table->string('payment_status')->default('Pending'); // Paid / Pending / Failed
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff_members')->onDelete('cascade');
            $table->unique(['staff_id', 'month']);
        });

        // 7. Staff Performance Reviews Table
        Schema::create('staff_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('reviewer_id')->nullable(); // User ID (admin) or Staff ID
            $table->date('review_date');
            $table->integer('productivity_score')->default(100);
            $table->integer('task_completion_rate')->default(100);
            $table->integer('client_satisfaction_score')->default(100);
            $table->integer('collaboration_score')->default(100);
            $table->text('review_notes')->nullable();
            $table->boolean('promotion_recommended')->default(false);
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff_members')->onDelete('cascade');
        });

        // 8. Staff Leaves Table
        Schema::create('staff_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('leave_type'); // Sick / Annual / Emergency / Maternity/Paternity
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days');
            $table->text('reason')->nullable();
            $table->string('status')->default('Pending'); // Pending / Approved / Rejected
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable(); // Admin user id
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff_members')->onDelete('cascade');
        });

        // 9. Staff Activity Logs Table
        Schema::create('staff_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->string('action');
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff_members')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_activity_logs');
        Schema::dropIfExists('staff_leaves');
        Schema::dropIfExists('staff_performance_reviews');
        Schema::dropIfExists('staff_payrolls');
        Schema::dropIfExists('staff_attendance');
        
        // Remove foreign key check block to drop staff and departments
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropForeign(['head_id']);
            });
        }
        
        Schema::dropIfExists('staff_members');
        Schema::dropIfExists('staff_roles');
        Schema::dropIfExists('departments');
    }
};
