<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\RegistrationRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // 1. Statistiques Globales
        $autoEcolesCount = Team::query()->where('is_personal', false)->count();
        $usersCount = User::query()->count();
        $learnersCount = User::query()->where('role', UserRole::Apprenant->value)->count();
        $instructorsCount = User::query()->where('role', UserRole::Moniteur->value)->count();
        $verifiedUsersCount = User::query()->whereNotNull('email_verified_at')->count();
        $administratorsCount = User::query()->where('role', UserRole::Administrateur->value)->count();

        $totalRequestsCount = RegistrationRequest::count();
        $pendingRequestsCount = RegistrationRequest::where('status', 'pending')->count();
        $validatedRequestsCount = RegistrationRequest::where('status', 'validated')->count();

        // 2. Auto-écoles avec répartition précise des demandes
        $teams = Team::query()
            ->where('is_personal', false)
            ->withCount([
                'members',
                'registrationRequests as total_requests_count',
                'registrationRequests as pending_requests_count' => function ($query) {
                    $query->where('status', 'pending');
                },
                'registrationRequests as validated_requests_count' => function ($query) {
                    $query->where('status', 'validated');
                },
            ])
            ->latest()
            ->get();

        // 3. Dernières demandes d'inscription sur la plateforme
        $recentRequests = RegistrationRequest::with('team')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'autoEcolesCount' => $autoEcolesCount,
            'usersCount' => $usersCount,
            'learnersCount' => $learnersCount,
            'instructorsCount' => $instructorsCount,
            'verifiedUsersCount' => $verifiedUsersCount,
            'administratorsCount' => $administratorsCount,
            'totalRequestsCount' => $totalRequestsCount,
            'pendingRequestsCount' => $pendingRequestsCount,
            'validatedRequestsCount' => $validatedRequestsCount,
            'teams' => $teams,
            'recentRequests' => $recentRequests,
        ]);
    }

    public function preview(): View
    {
        return view('admin.dashboard', [
            'autoEcolesCount' => 12,
            'usersCount' => 248,
            'learnersCount' => 180,
            'instructorsCount' => 45,
            'verifiedUsersCount' => 219,
            'administratorsCount' => 3,
            'totalRequestsCount' => 64,
            'pendingRequestsCount' => 14,
            'validatedRequestsCount' => 50,
            'teams' => collect([
                (object) [
                    'name' => 'Auto-école Horizon',
                    'members_count' => 34,
                    'total_requests_count' => 28,
                    'pending_requests_count' => 5,
                    'validated_requests_count' => 23,
                    'created_at' => now()->subDays(2),
                ],
                (object) [
                    'name' => 'Permis Réussite',
                    'members_count' => 21,
                    'total_requests_count' => 19,
                    'pending_requests_count' => 3,
                    'validated_requests_count' => 16,
                    'created_at' => now()->subDays(5),
                ],
                (object) [
                    'name' => 'Drive Academy',
                    'members_count' => 47,
                    'total_requests_count' => 17,
                    'pending_requests_count' => 6,
                    'validated_requests_count' => 11,
                    'created_at' => now()->subDays(9),
                ],
            ]),
            'recentRequests' => collect([]),
            'preview' => true,
        ]);
    }
}
