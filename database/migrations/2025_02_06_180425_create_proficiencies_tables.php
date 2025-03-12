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
        Schema::create('proficiencies', function (Blueprint $table) {
            $table->id();
            $table->string('level_code', 16);
            $table->string('name', 64);
            $table->string('description', 128);
            $table->timestamps();
        });

        Schema::create('proficiencies_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proficiency_id');
            $table->foreign('proficiency_id')
                ->references('id')
                ->on('proficiencies');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreign('student_id')
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
        Schema::dropIfExists('proficiencies_users');

        Schema::dropIfExists('proficiencies');
    }
};
