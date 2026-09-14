<?php

namespace App\Http\Controllers;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Mail\RegistrationValidated;
use App\Models\Membership;
use App\Models\RegistrationRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegistrationRequestController extends Controller
{
    public function index(Team $currentTeam)
    {
        $requests = RegistrationRequest::where('team_id', $currentTeam->id)
            ->latest()
            ->paginate(10);

        return view('demandes.index', [
            'requests' => $requests,
            'team' => $currentTeam,
        ]);
    }

    public function show(Team $currentTeam, RegistrationRequest $registrationRequest)
    {
        $this->ensureRequestBelongsToTeam($currentTeam, $registrationRequest);

        return view('demandes.show', [
            'request' => $registrationRequest,
            'team' => $currentTeam,
        ]);
    }

    public function validateRequest(Team $currentTeam, RegistrationRequest $registrationRequest)
    {
        $this->ensureRequestBelongsToTeam($currentTeam, $registrationRequest);

        if ($registrationRequest->status !== 'pending') {
            return back()->withErrors(['request' => 'Cette demande a déjà été traitée.']);
        }

        if (User::where('email', $registrationRequest->email)->exists()) {
            return back()->withErrors(['request' => 'Cette adresse e-mail est déjà utilisée par un apprenant.']);
        }

        $code = strtoupper(Str::random(6));

        $user = User::create([
            'name' => $registrationRequest->name,
            'email' => $registrationRequest->email,
            'password' => $registrationRequest->password, // Password already hashed
            'telephone' => $registrationRequest->telephone,
            'ville' => $registrationRequest->ville,
            'sexe' => $registrationRequest->sexe,
            'date_naissance' => $registrationRequest->date_naissance,
            'categorie_permis' => $registrationRequest->categorie_permis,
            'horaire_formation' => $registrationRequest->horaire_formation,
            'has_cni' => $registrationRequest->has_cni,
            'auto_ecole' => $currentTeam->name,
            'role' => UserRole::Apprenant,
            'email_verified_at' => now(),
        ]);

        Membership::query()->firstOrCreate([
            'team_id' => $currentTeam->id,
            'user_id' => $user->id,
        ], [
            'role' => TeamRole::Member,
        ]);

        $user->switchTeam($currentTeam);

        $registrationRequest->update([
            'status' => 'validated',
            'validation_code' => $code,
        ]);

        // Send email
        try {
            Mail::to($user->email)->send(new RegistrationValidated($user, $code));
        } catch (\Exception $e) {
            logger()->error('Erreur envoi mail validation: '.$e->getMessage());
        }

        return redirect()->route('demandes.index', ['current_team' => $currentTeam->slug])
            ->with('success', "Demande validée avec succès. L'apprenant a reçu son code : $code");
    }

    public function destroy(Team $currentTeam, RegistrationRequest $registrationRequest)
    {
        $this->ensureRequestBelongsToTeam($currentTeam, $registrationRequest);
        $registrationRequest->delete();

        return redirect()->route('demandes.index', ['current_team' => $currentTeam->slug])
            ->with('success', 'Demande supprimée avec succès.');
    }

    private function ensureRequestBelongsToTeam(Team $team, RegistrationRequest $registrationRequest): void
    {
        abort_unless($registrationRequest->team_id === $team->id, 404);
    }
}
