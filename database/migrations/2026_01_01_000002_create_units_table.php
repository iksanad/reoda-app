<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('unit_code', 20)->comment('Misal: A1, B2, M230');
            $table->string('name')->comment('Nama unit, misal: Kamar A1');
            $table->enum('type', ['standard', 'deluxe', 'vip'])->default('standard');
            $table->decimal('rent_price', 12, 2)->comment('Harga sewa per bulan');
            $table->integer('area_sqm')->nullable()->comment('Luas dalam m2');
            $table->integer('floor')->nullable()->comment('Lantai');
            $table->enum('status', ['available', 'occupied', 'maintenance', 'inactive'])->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['property_id', 'unit_code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('units');
    }
};
