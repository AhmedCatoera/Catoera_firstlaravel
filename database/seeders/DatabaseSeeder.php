<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->create([
            'name' => 'System Admin',
            'email' => 'admin@ertms.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $staffDispatch = User::query()->create([
            'name' => 'Operations Staff',
            'email' => 'staff.dispatch@ertms.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STAFF,
            'status' => 'active',
        ]);

        $staffLeader = User::query()->create([
            'name' => 'Team Leader (Staff)',
            'email' => 'staff.leader@ertms.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STAFF,
            'status' => 'active',
        ]);

        $staffResponder = User::query()->create([
            'name' => 'Field Responder (Staff)',
            'email' => 'staff.field@ertms.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STAFF,
            'status' => 'active',
        ]);

        $team = Team::query()->create([
            'team_name' => 'Alpha Response Unit',
            'availability_status' => 'available',
            'team_leader_id' => $staffLeader->id,
        ]);

        $team->members()->syncWithoutDetaching([$staffResponder->id, $staffLeader->id]);
    }
}
