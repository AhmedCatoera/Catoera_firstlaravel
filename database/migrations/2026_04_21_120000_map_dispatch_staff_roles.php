<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'staff')
            ->where('email', 'like', '%dispatch%')
            ->update(['role' => 'dispatcher']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'dispatcher')
            ->update(['role' => 'staff']);
    }
};
