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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id')->nullable()->index();
            $table->unsignedBigInteger('city_id')->nullable()->index();
            $table->string('title', 500)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('event_date_time')->nullable();
            $table->time('event_duration')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('location', 100)->nullable();
            $table->enum('status', ['active', 'inactive', 'completed', 'cancelled'])->default('inactive');
            $table->timestamps();

            // Foreign keys
            $table->foreign('organizer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
