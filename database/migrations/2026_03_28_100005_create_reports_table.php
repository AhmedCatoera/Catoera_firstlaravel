<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('resolution_details');
            $table->string('casualties')->nullable();
            $table->text('damage_assessment')->nullable();
            $table->timestamp('date_submitted')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
