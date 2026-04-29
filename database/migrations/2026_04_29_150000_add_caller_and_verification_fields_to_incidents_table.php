<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            if (! Schema::hasColumn('incidents', 'caller_name')) {
                $table->string('caller_name', 120)->nullable()->after('description');
            }
            if (! Schema::hasColumn('incidents', 'caller_phone')) {
                $table->string('caller_phone', 40)->nullable()->after('caller_name');
            }
            if (! Schema::hasColumn('incidents', 'caller_relation')) {
                $table->string('caller_relation', 40)->nullable()->after('caller_phone');
            }
            if (! Schema::hasColumn('incidents', 'verification_status')) {
                $table->string('verification_status', 32)->default('unverified')->after('caller_relation');
            }
            if (! Schema::hasColumn('incidents', 'verification_sources')) {
                $table->json('verification_sources')->nullable()->after('verification_status');
            }
            if (! Schema::hasColumn('incidents', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('verification_sources');
            }
            if (! Schema::hasColumn('incidents', 'confidence_score')) {
                $table->unsignedTinyInteger('confidence_score')->nullable()->after('verification_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            foreach ([
                'confidence_score',
                'verification_notes',
                'verification_sources',
                'verification_status',
                'caller_relation',
                'caller_phone',
                'caller_name',
            ] as $col) {
                if (Schema::hasColumn('incidents', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

