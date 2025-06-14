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
        Schema::table('assessments_students', function (Blueprint $table) {
            $table->uuid('assessment_uuid')
                ->after('student_id')
                ->default(uniqid());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments_students', function (Blueprint $table) {
            $table->dropColumn(['assessment_uuid']);
        });
    }
};
