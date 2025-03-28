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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')
                ->references('id')
                ->on('modules');
            $table->string('name', 128);
            $table->text('slug');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('units_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')
                ->references('id')
                ->on('units');
            $table->string('file_name', 128);
            $table->string('file_type', 32);
            $table->text('file_path');
            $table->string('description', 128)
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units_attachments');

        Schema::dropIfExists('units');
    }
};
