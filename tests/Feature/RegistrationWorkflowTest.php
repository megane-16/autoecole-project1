<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Mail\RegistrationRejected;
use App\Mail\RegistrationValidated;
use App\Models\RegistrationRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_ecole_can_validate_request_and_send_code_email(): void
    {
        Mail::fake();

        $autoEcole = User::factory()->create([
            'role' => UserRole::AutoEcole,
        ]);
        $team = Team::create([
            'name' => 'Auto Ecole Centrale',
            'slug' => 'auto-ecole-centrale-1',
            'is_personal' => false,
        ]);
        $team->members()->attach($autoEcole, ['role' => 'owner']);
        $autoEcole->current_team_id = $team->id;
        $autoEcole->save();

        $request = RegistrationRequest::create([
            'name' => 'Jean Apprenant',
            'email' => 'jean@example.com',
            'password' => bcrypt('password123'),
            'telephone' => '699001122',
            'team_id' => $team->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($autoEcole)
            ->post(route('demandes.validate', ['current_team' => $team->slug, 'registration_request' => $request->id]));

        $response->assertRedirect(route('demandes.index', ['current_team' => $team->slug]));

        $this->assertDatabaseHas('registration_requests', [
            'id' => $request->id,
            'status' => 'validated',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
            'role' => 'apprenant',
        ]);

        Mail::assertSent(RegistrationValidated::class, function ($mail) {
            return $mail->user->email === 'jean@example.com';
        });

        // Test login by code
        $updatedRequest = RegistrationRequest::find($request->id);
        $loginResponse = $this->postJson('/api/login-by-code', [
            'code' => $updatedRequest->validation_code,
        ]);

        $loginResponse->assertOk()
            ->assertJsonStructure(['access_token', 'user']);
    }

    public function test_auto_ecole_can_reject_request_and_send_refusal_email(): void
    {
        Mail::fake();

        $autoEcole = User::factory()->create([
            'role' => UserRole::AutoEcole,
        ]);
        $team = Team::create([
            'name' => 'Auto Ecole Centrale Refus',
            'slug' => 'auto-ecole-centrale-refus',
            'is_personal' => false,
        ]);
        $team->members()->attach($autoEcole, ['role' => 'owner']);
        $autoEcole->current_team_id = $team->id;
        $autoEcole->save();

        $request = RegistrationRequest::create([
            'name' => 'Alain Candidat',
            'email' => 'alain@example.com',
            'password' => bcrypt('password123'),
            'telephone' => '699887766',
            'team_id' => $team->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($autoEcole)
            ->post(route('demandes.reject', ['current_team' => $team->slug, 'registration_request' => $request->id]));

        $response->assertRedirect(route('demandes.index', ['current_team' => $team->slug]));

        $this->assertDatabaseHas('registration_requests', [
            'id' => $request->id,
            'status' => 'rejected',
        ]);

        Mail::assertSent(RegistrationRejected::class, function ($mail) {
            return $mail->request->email === 'alain@example.com';
        });
    }
}
