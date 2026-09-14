<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Mail\RegistrationValidated;
use App\Models\RegistrationRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_school_can_validate_its_own_application(): void
    {
        Mail::fake();

        $school = Team::create(['name' => 'Auto-école Paris']);
        $owner = User::factory()->create(['role' => UserRole::AutoEcole]);
        $school->members()->attach($owner, ['role' => TeamRole::Owner]);
        $owner->switchTeam($school);

        $this->post(route('postuler.store', $school), [
            'name' => 'Alice Apprentie',
            'email' => 'alice@example.com',
            'password' => 'motdepasse',
            'password_confirmation' => 'motdepasse',
            'telephone' => '+237 600000000',
            'ville' => 'Douala',
            'categorie_permis' => 'B',
            'auto_ecole_id' => $school->id,
            'auto_ecole' => $school->name,
            'has_cni' => true,
        ])->assertRedirect(route('home'));

        $application = RegistrationRequest::query()->sole();
        $this->assertSame($school->id, $application->team_id);
        $this->assertSame('pending', $application->status);

        $this->actingAs($owner)
            ->get(route('demandes.index', ['current_team' => $school->slug]))
            ->assertOk()
            ->assertSee('Alice Apprentie');

        $this->actingAs($owner)
            ->post(route('demandes.validate', [
                'current_team' => $school->slug,
                'registration_request' => $application->id,
            ]))
            ->assertRedirect(route('demandes.index', ['current_team' => $school->slug]));

        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
        $this->assertDatabaseHas('registration_requests', [
            'id' => $application->id,
            'status' => 'validated',
        ]);
        Mail::assertSent(RegistrationValidated::class, fn (RegistrationValidated $mail) => $mail->hasTo('alice@example.com'));
    }
}
