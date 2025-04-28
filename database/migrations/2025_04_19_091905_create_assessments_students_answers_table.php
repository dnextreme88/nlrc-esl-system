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
        Schema::create('assessments_students_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_student_id');
            $table->foreign('assessment_student_id')
                ->references('id')
                ->on('assessments_students');
            $table->unsignedBigInteger('assessment_choice_id');
            $table->foreign('assessment_choice_id')
                ->references('id')
                ->on('assessments_choices');
            $table->unsignedBigInteger('assessment_question_id');
            $table->foreign('assessment_question_id')
                ->references('id')
                ->on('assessments_questions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments_students_answers');
    }
};
