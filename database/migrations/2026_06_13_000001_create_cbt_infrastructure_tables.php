<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('address');
            $table->string('city');
            $table->integer('capacity');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('status')->default('active'); // active, maintenance
            $table->timestamps();
        });

        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_center_id')->constrained('cbt_centers')->onDelete('cascade');
            $table->string('seat_number');
            $table->string('status')->default('available'); // available, broken, occupied
            $table->timestamps();
        });

        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seat_id')->nullable()->constrained('seats')->onDelete('set null');
            $table->string('ip_address');
            $table->string('mac_address')->unique()->nullable();
            $table->string('device_name');
            $table->string('system_status')->default('online'); // online, offline, testing
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
        Schema::dropIfExists('seats');
        Schema::dropIfExists('cbt_centers');
    }
};
