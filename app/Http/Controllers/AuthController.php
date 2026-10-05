<?php

namespace App\Http\Controllers;

use App\Actions\Teams\CreateTeam;
use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use App\Rules\TeamName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(): mixed
    {
        return view('auth.login');
    }

    public function showRegister(): mixed
    {
        return view('auth.register', [
            'permitCategories' => ['A', 'B', 'C', 'D'],
        ]);
    }

    public function register(Request $request, CreateTeam $createTeam): RedirectResponse
    {
        $validated = $request->validate([
            'team_name' => ['required', 'string', 'max:255', new TeamName],
            'ville' => ['required', 'string', 'max:255'],
            'categories_permis' => ['required', 'array', 'min:2'],
            'categories_permis.*' => ['string', 'in:A,B,C,D'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['team_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'ville' => $validated['ville'],
            'categories_permis' => $validated['categories_permis'],
            'auto_ecole' => $validated['team_name'],
            'email_verified_at' => now(),
            'role' => UserRole::AutoEcole,
        ]);

        $team = $createTeam->handle($user, $validated['team_name'], isPersonal: true);
        $user->createToken('web-session');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard', ['current_team' => $team->slug]);
    }

    public function storeLearner(Request $request, Team $currentTeam): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'ville' => ['nullable', 'string', 'max:255'],
            'categorie_permis' => ['nullable', 'string', 'max:50'],
        ]);

        $learner = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'ville' => $validated['ville'] ?? null,
            'categorie_permis' => $validated['categorie_permis'] ?? null,
            'role' => UserRole::Apprenant,
        ]);

        Membership::query()->firstOrCreate([
            'team_id' => $currentTeam->id,
            'user_id' => $learner->id,
        ], [
            'role' => TeamRole::Member,
        ]);

        $learner->switchTeam($currentTeam);

        return redirect()->route('dashboard', ['current_team' => $currentTeam->slug])
            ->with('success', 'Apprenant ajouté avec succès.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();
        $user->createToken('web-session');

        if ($user->role === UserRole::Administrateur) {
            return redirect()->route('admin.dashboard');
        }

        $team = $user->currentTeam ?? $user->personalTeam();

        if ($user->role === UserRole::Moniteur && $team) {
            return redirect()->route('instructor.schedule.index', ['current_team' => $team->slug]);
        }

        return redirect()->route('dashboard', ['current_team' => $team->slug]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->user()?->tokens()->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
