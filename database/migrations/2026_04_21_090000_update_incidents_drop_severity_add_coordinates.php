<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            if (! Schema::hasColumn('incidents', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('location');
            }
            if (! Schema::hasColumn('incidents', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (Schema::hasColumn('incidents', 'severity_level')) {
                $table->dropColumn('severity_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            if (! Schema::hasColumn('incidents', 'severity_level')) {
                $table->string('severity_level', 32)->default('high')->after('description');
            }
            if (Schema::hasColumn('incidents', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('incidents', 'latitude')) {
                $table->dropColumn('latitude');
            }
        });
    }
};
