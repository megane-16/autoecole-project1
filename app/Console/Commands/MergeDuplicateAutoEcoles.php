<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use App\Support\AutoEcoleNameMatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeDuplicateAutoEcoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-ecoles:merge-duplicates {--dry-run : Inspect without writing changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge duplicate auto-écoles teams by normalized name and reassign users and memberships.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $teams = Team::query()->where('is_personal', false)->orderBy('id')->get();

        $groups = [];

        foreach ($teams as $team) {
            $groupKey = null;

            foreach ($groups as $key => $groupTeams) {
                foreach ($groupTeams as $groupTeam) {
                    if (AutoEcoleNameMatcher::matches($groupTeam->name, $team->name)) {
                        $groupKey = $key;
                        break 2;
                    }
                }
            }

            if ($groupKey === null) {
                $groups[] = [$team];

                continue;
            }

            $groups[$groupKey][] = $team;
        }

        $duplicateGroups = array_filter($groups, fn (array $group) => count($group) > 1);

        if ($this->option('dry-run')) {
            $this->info('Dry run: '.count($duplicateGroups).' duplicate groups found.');

            foreach ($duplicateGroups as $index => $group) {
                $names = implode(' | ', array_map(fn (Team $team) => $team->name, $group));
                $this->line(($index + 1).'. '.$names);
            }

            return self::SUCCESS;
        }

        $mergedCount = 0;

        foreach ($duplicateGroups as $group) {
            $primary = $group[0];
            $duplicates = array_slice($group, 1);

            foreach ($duplicates as $duplicate) {
                DB::transaction(function () use ($primary, $duplicate) {
                    $users = User::query()->where('current_team_id', $duplicate->id)->get();

                    foreach ($users as $user) {
                        $user->update(['current_team_id' => $primary->id]);
                    }

                    $duplicateUsers = User::query()
                        ->whereHas('teamMemberships', fn ($query) => $query->where('team_id', $duplicate->id))
                        ->get();

                    foreach ($duplicateUsers as $user) {
                        $existingMembership = $primary->memberships()->where('user_id', $user->id)->first();

                        if (! $existingMembership) {
                            $primary->memberships()->create([
                                'team_id' => $primary->id,
                                'user_id' => $user->id,
                                'role' => $duplicate->memberships()->where('user_id', $user->id)->first()?->role ?? 'member',
                            ]);
                        }
                    }

                    $duplicate->memberships()->delete();
                    $duplicate->delete();
                });

                $mergedCount++;
            }
        }

        $this->info('Merged '.$mergedCount.' duplicate auto-écoles.');

        return self::SUCCESS;
    }
}
