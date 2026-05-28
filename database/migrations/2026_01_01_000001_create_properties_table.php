<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
            $table->string('property_code', 20)->unique()->comment('Kode unik properti, misal: REODA-001');
            $table->string('name');
            $table->enum('type', ['kos', 'kontrakan', 'apartemen', 'rumah'])->default('kos');
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('rt_rw', 10)->nullable();
            $table->string('village')->nullable()->comment('Kelurahan');
            $table->string('district')->nullable()->comment('Kecamatan');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('properties');
    }
};
