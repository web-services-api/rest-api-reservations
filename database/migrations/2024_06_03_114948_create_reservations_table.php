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
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('rank')->nullable(false)->unsigned();
            $table->enum('status', ['open', 'expired', 'confirmed'])->nullable(false);
            $table->integer('seat')->nullable(false)->unsigned();
            $table->foreignUuid('seance_id')->nullable(false);
            $table->timestamps();

            $table->foreign('seance_id')->references('id')->on('seances');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
