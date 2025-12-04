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
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 40);
            $table->text('description');
            $table->string('location');
            $table->string('city');
            $table->dateTime('start_date');
            $table->unsignedInteger('capacity')->default(0);
            $table->unsignedInteger('available_seats')->default(0);
            $table->unsignedInteger('ticket_price')->default(0);
            $table->enum('status', ['scheduled', 'cancelled', 'completed'])->default('scheduled');
            $table->boolean('is_featured')->default(false);
            $table->softDeletes();
            $table->timestamps();
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
