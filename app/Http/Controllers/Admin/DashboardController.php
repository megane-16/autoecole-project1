<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $teams = Team::query()
            ->where('is_personal', false)
            ->withCount('members')
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'autoEcolesCount' => Team::query()->where('is_personal', false)->count(),
            'usersCount' => User::query()->count(),
            'verifiedUsersCount' => User::query()->whereNotNull('email_verified_at')->count(),
            'administratorsCount' => User::query()->where('role', UserRole::Administrateur->value)->count(),
            'teams' => $teams,
        ]);
    }

    public function preview(): View
    {
        return view('admin.dashboard', [
            'autoEcolesCount' => 12,
            'usersCount' => 248,
            'verifiedUsersCount' => 219,
            'administratorsCount' => 3,
            'teams' => collect([
                (object) ['name' => 'Auto-école Horizon', 'members_count' => 34, 'created_at' => now()->subDays(2)],
                (object) ['name' => 'Permis Réussite', 'members_count' => 21, 'created_at' => now()->subDays(5)],
                (object) ['name' => 'Drive Academy', 'members_count' => 47, 'created_at' => now()->subDays(9)],
            ]),
            'preview' => true,
        ]);
    }
}
