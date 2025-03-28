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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proficiency_id');
            $table->foreign('proficiency_id')
                ->references('id')
                ->on('proficiencies');
            $table->string('name', 128);
            $table->text('slug');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('modules_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->foreign('module_id')
                ->references('id')
                ->on('modules');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreign('student_id')
                ->references('id')
                ->on('users');
            $table->timestamps();
        });

        Schema::create('modules_teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')
                ->references('id')
                ->on('modules');
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules_teachers');

        Schema::dropIfExists('modules_students');

        Schema::dropIfExists('modules');
    }
};
