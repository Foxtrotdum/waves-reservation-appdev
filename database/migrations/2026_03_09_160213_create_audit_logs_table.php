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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // Which table was modified
            $table->string('operation'); // create, update, delete
            $table->unsignedBigInteger('record_id'); // ID of the record that was modified
            $table->string('user_type')->nullable(); // admin, customer, etc.
            $table->unsignedBigInteger('user_id')->nullable(); // ID of the user who made the change
            $table->json('old_values')->nullable(); // Original values before change
            $table->json('new_values')->nullable(); // New values after change
            $table->string('ip_address')->nullable(); // IP address of the user
            $table->text('user_agent')->nullable(); // Browser/device info
            $table->timestamps(); // This adds both created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
