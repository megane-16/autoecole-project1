<?php

namespace App\Http\Controllers;

use App\Models\RegistrationRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PublicApplicationController extends Controller
{
    public function home()
    {
        return view('welcome', [
            'partnerSchools' => Team::query()
                ->whereHas('members', fn ($query) => $query->where('users.role', 'auto_ecole'))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Team $team)
    {
        abort_unless($team->members()->where('users.role', 'auto_ecole')->exists(), 404);

        return view('pages.postuler', compact('team'));
    }

    public function store(Request $request, Team $team)
    {
        abort_unless($team->members()->where('users.role', 'auto_ecole')->exists(), 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255', 'unique:users,email',
                Rule::unique('registration_requests', 'email')->where('status', 'pending'),
            ],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'telephone' => 'nullable|string|max:30',
            'ville' => 'nullable|string|max:255',
            'sexe' => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'categorie_permis' => 'nullable|string|max:50',
            'auto_ecole_id' => 'nullable|integer|exists:teams,id',
            'auto_ecole' => 'nullable|string|max:255',
            'horaire_formation' => 'nullable|string|max:255',
            'has_cni' => 'nullable|boolean',
        ]);

        if (isset($validated['auto_ecole_id']) && (int) $validated['auto_ecole_id'] !== $team->id) {
            return back()->withErrors(['auto_ecole_id' => 'L’auto-école sélectionnée ne correspond pas à cette candidature.'])->withInput();
        }

        RegistrationRequest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($request->password),
            'telephone' => $validated['telephone'] ?? null,
            'ville' => $validated['ville'] ?? null,
            'sexe' => $validated['sexe'] ?? null,
            'date_naissance' => $validated['date_naissance'] ?? null,
            'categorie_permis' => $validated['categorie_permis'] ?? null,
            'team_id' => $team->id,
            'horaire_formation' => $validated['horaire_formation'] ?? null,
            'has_cni' => $request->boolean('has_cni'),
            'status' => 'pending',
        ]);

        return redirect()->route('home')->with('success', 'Votre demande a été envoyée avec succès à '.$team->name.'. Vous recevrez un e-mail après validation.');
    }
}
