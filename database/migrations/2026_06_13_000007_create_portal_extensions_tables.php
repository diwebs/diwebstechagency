<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Service Requests
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('service_type');
            $table->text('description');
            $table->string('budget_range');
            $table->date('deadline');
            $table->string('status')->default('submitted'); // submitted, under_review, proposal_sent, approved, in_development
            $table->text('attachments')->nullable(); // JSON list
            $table->text('ai_recommendations')->nullable();
            $table->timestamps();
        });

        // 2. Contracts
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->string('status')->default('draft'); // draft, pending_signature, signed, expired
            $table->timestamp('signed_at')->nullable();
            $table->text('signature_data')->nullable(); // e-signature label or base64 representation
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // 3. Project Files / Deliverables
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('filename');
            $table->string('filepath');
            $table->bigInteger('file_size');
            $table->string('folder'); // contracts, assets, deliverables, reports, backups
            $table->integer('version')->default(1);
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });

        // 4. Messages / Real-Time Collaboration Hub
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->string('file_path')->nullable();
            $table->string('voice_note_path')->nullable();
            $table->string('department')->default('support'); // pm, support, finance, technical
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // 5. Team Access Management
        Schema::create('team_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role'); // manager, reviewer, finance_viewer
            $table->text('project_permissions')->nullable(); // JSON list
            $table->timestamps();
        });

        // 6. Milestone Logs (for audits)
        Schema::create('milestone_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('milestones')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // approved, rejected, revision_requested
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_logs');
        Schema::dropIfExists('team_access');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('service_requests');
    }
};
