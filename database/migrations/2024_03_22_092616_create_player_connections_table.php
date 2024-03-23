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
        Schema::create('player_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('player_ip_id')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();

            $table->timestamp('connect_at');
            $table->timestamp('disconnect_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('player_ip_id')->references('id')->on('player_ips')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_logs');
    }
};
