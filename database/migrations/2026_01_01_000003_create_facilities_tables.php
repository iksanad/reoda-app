<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable()->comment('Icon class misal: fa-wifi');
            $table->enum('category', ['basic', 'bathroom', 'kitchen', 'security', 'entertainment', 'other'])->default('other');
            $table->timestamps();
        });

        Schema::create('property_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['property_id', 'facility_id']);
        });

        Schema::create('unit_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['unit_id', 'facility_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit_facilities');
        Schema::dropIfExists('property_facilities');
        Schema::dropIfExists('facilities');
    }
};
