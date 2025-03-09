<?php

use App\Enums\MeetingStatuses;
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
        Schema::create('meeting_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users');
            $table->date('meeting_date');
            $table->string('start_time', 64);
            $table->string('end_time', 64);
            $table->string('notes', 255)->nullable();
            $table->string('status')->default(MeetingStatuses::PENDING->value);
            $table->boolean('is_opened')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_slots');
    }
};
