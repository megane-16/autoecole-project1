<?php

namespace Database\Seeders;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        foreach ([
            ['name' => 'Auto-école Paris', 'email' => 'paris@driveflow.test'],
            ['name' => 'Auto-école Kopa', 'email' => 'kopa@driveflow.test'],
        ] as $school) {
            $owner = User::query()->firstOrCreate(
                ['email' => $school['email']],
                [
                    'name' => $school['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::AutoEcole,
                    'email_verified_at' => now(),
                    'auto_ecole' => $school['name'],
                ],
            );

            $team = Team::query()->firstOrCreate(
                ['name' => $school['name']],
                ['is_personal' => false],
            );

            Membership::query()->firstOrCreate(
                ['team_id' => $team->id, 'user_id' => $owner->id],
                ['role' => TeamRole::Owner],
            );

            if (! $owner->current_team_id) {
                $owner->switchTeam($team);
            }
        }
    }
}
