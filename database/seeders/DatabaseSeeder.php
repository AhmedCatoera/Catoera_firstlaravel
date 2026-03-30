<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->create([
            'name' => 'System Admin',
            'email' => 'admin@ertms.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $dispatcher = User::query()->create([
            'name' => 'Case Dispatcher',
            'email' => 'dispatcher@ertms.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_DISPATCHER,
            'status' => 'active',
        ]);

        $leader = User::query()->create([
            'name' => 'Team Leader One',
            'email' => 'leader@ertms.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEAM_LEADER,
            'status' => 'active',
        ]);

        $responder = User::query()->create([
            'name' => 'Responder One',
            'email' => 'responder@ertms.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_RESPONDER,
            'status' => 'active',
        ]);

        $team = Team::query()->create([
            'team_name' => 'Alpha Response Unit',
            'availability_status' => 'available',
            'team_leader_id' => $leader->id,
        ]);

        $team->members()->syncWithoutDetaching([$responder->id, $leader->id]);
    }
}
