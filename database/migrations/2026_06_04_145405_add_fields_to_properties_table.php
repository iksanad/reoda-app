<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->text('property_terms')->nullable()->comment('Ketentuan/peraturan hunian dari pengelola');
            $table->decimal('yearly_discount_percent', 5, 2)->default(0)->comment('Diskon (%) jika dibayar tahunan, khusus kontrakan/apartemen');
            $table->string('maps_url')->nullable()->comment('Link Google Maps atau OpenStreetMap');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['property_terms', 'yearly_discount_percent', 'maps_url']);
        });
    }
};
