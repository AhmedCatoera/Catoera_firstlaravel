<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('role', ['dispatcher', 'team_leader', 'responder'])
            ->update(['role' => 'staff']);
    }

    public function down(): void
    {
        // Cannot restore previous role mapping
    }
};
