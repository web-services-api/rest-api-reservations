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
        Schema::create('alerts_mail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email', 255)->nullable(false);
            $table->string('type', 255)->nullable(false);
            $table->unsignedInteger('user_id')->nullable(false);
            $table->string('status', 255)->nullable(false)->default('pending');
            $table->text('debug_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts_mail');
    }
};
