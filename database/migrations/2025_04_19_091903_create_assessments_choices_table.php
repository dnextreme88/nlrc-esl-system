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
        Schema::create('assessments_choices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_question_id');
            $table->foreign('assessment_question_id')
                ->references('id')
                ->on('assessments_questions');
            $table->text('choice');
            $table->boolean('is_correct')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments_choices');
    }
};
