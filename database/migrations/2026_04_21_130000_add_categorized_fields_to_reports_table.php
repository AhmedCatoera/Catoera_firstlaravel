<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('resolution_outcome', 32)->nullable()->after('resolution_details');
            $table->string('operations_category', 64)->nullable()->after('resolution_outcome');
            $table->string('response_effectiveness', 32)->nullable()->after('operations_category');
            $table->string('casualty_level', 32)->nullable()->after('response_effectiveness');
            $table->string('property_damage_level', 32)->nullable()->after('casualty_level');
            $table->json('actions_taken')->nullable()->after('property_damage_level');
            $table->text('follow_up_actions')->nullable()->after('damage_assessment');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'resolution_outcome',
                'operations_category',
                'response_effectiveness',
                'casualty_level',
                'property_damage_level',
                'actions_taken',
                'follow_up_actions',
            ]);
        });
    }
};
