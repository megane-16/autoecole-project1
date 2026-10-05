<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\RegistrationRequest;
use App\Models\Team;
use App\Models\User;
use App\Support\AutoEcoleNameMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                'unique:users,email',
                \Illuminate\Validation\Rule::unique('registration_requests', 'email')->where(fn ($q) => $q->whereIn('status', ['pending', 'validated'])),
            ],
            'password' => 'required|string|min:8|confirmed',
            'telephone' => [
                'nullable', 'string', 'max:30',
                'unique:users,telephone',
                \Illuminate\Validation\Rule::unique('registration_requests', 'telephone')->where(fn ($q) => $q->whereIn('status', ['pending', 'validated'])),
            ],
            'ville' => 'nullable|string|max:255',
            'sexe' => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date',
            'categorie_permis' => 'nullable|string|max:50',
            'horaire_formation' => 'nullable|string|max:255',
            'has_cni' => 'nullable|boolean',
            'auto_ecole_id' => 'nullable|integer|exists:teams,id',
            'auto_ecole' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'Un compte ou une demande d’inscription existe déjà avec cette adresse email.',
            'telephone.unique' => 'Un compte ou une demande d’inscription existe déjà avec ce numéro de téléphone.',
        ]);

        $teamId = $request->input('auto_ecole_id');
        $team = $teamId
            ? Team::query()
                ->whereKey($teamId)
                ->whereHas('members', fn ($query) => $query->where('users.role', 'auto_ecole'))
                ->first()
            : null;

        // Priorité 2 (fallback legacy) : correspondance par texte
        if (! $team && $request->filled('auto_ecole')) {
            $autoEcoleName = trim((string) $request->input('auto_ecole'));
            $team = Team::query()
                ->withCount('members')
                ->whereHas('members', fn ($query) => $query->where('users.role', 'auto_ecole'))
                ->get()
                ->filter(fn (Team $candidate) => AutoEcoleNameMatcher::matches($candidate->name, $autoEcoleName))
                ->sortByDesc('members_count')
                ->first();
        }

        if (! $team) {
            return response()->json(['message' => 'Veuillez sélectionner une auto-école valide.'], 422);
        }

        $registrationRequest = RegistrationRequest::create([
            ...$request->only([
                'name', 'email', 'telephone', 'ville', 'sexe',
                'date_naissance', 'categorie_permis',
                'horaire_formation', 'has_cni',
            ]),
            'team_id' => $team->id,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Votre demande a été envoyée avec succès. Vous recevrez un email après validation.',
            'request' => $registrationRequest,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        if (! $user->role instanceof UserRole) {
            return response()->json(['message' => 'Le rôle utilisateur est invalide'], 403);
        }

        return response()->json([
            'message' => 'User logged in successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function loginByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->code));
        $registrationRequest = RegistrationRequest::where('validation_code', $code)
            ->where('status', 'validated')
            ->latest()
            ->first();

        if (! $registrationRequest) {
            return response()->json(['message' => 'Code de validation invalide ou expiré.'], 404);
        }

        $user = User::where('email', $registrationRequest->email)->first();

        if (! $user) {
            return response()->json(['message' => 'Aucun compte associé à ce code de validation.'], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'User logged out successfully']);
    }
}
