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
        Schema::create('meeting_slot_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_slot_id');
            $table->foreign('meeting_slot_id')
                ->references('id')
                ->on('meeting_slots');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_slot_users');
    }
};
