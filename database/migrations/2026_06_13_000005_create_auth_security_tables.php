<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. User Devices (Trusted Devices tracking)
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('device_uuid')->index(); // stored in cookie
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });

        // 2. User Passkeys (WebAuthn credentials registry)
        Schema::create('user_passkeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('credential_id', 512)->unique();
            $table->text('public_key');
            $table->integer('sign_count')->default(0);
            $table->string('name')->default('Security Key');
            $table->timestamps();
        });

        // 3. One-Time Passwords (OTP Verification Registry)
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('email_or_phone')->index()->nullable();
            $table->string('type'); // email_otp, sms_otp, registration_otp, 2fa_otp
            $table->string('code');
            $table->integer('retries')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        // 4. Detailed Security Audit Logs (Broadened logging)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('event_type'); // login_success, login_failure, password_change, device_added, session_revoked, role_change, 2fa_enabled, passkey_registered
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('user_passkeys');
        Schema::dropIfExists('user_devices');
    }
};
