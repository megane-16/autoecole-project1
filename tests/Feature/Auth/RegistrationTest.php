<?php

namespace Tests\Feature\Auth;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'John Doe',
            'team_name' => 'Auto-Ecole Horizon',
            'adresse' => '12 rue de la Route',
            'ville' => 'Douala',
            'categories_permis' => ['A', 'B'],
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'auto_ecole',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertSame(UserRole::AutoEcole, $user->role);
        $this->assertNotNull($user->current_team_id);
        $this->assertNotNull($user->personalTeam());
        $this->assertSame('Auto-Ecole Horizon', $user->personalTeam()->name);
    }

    public function test_learner_registration_is_attached_to_the_auto_ecole_dashboard(): void
    {
        $autoEcole = Team::create([
            'name' => 'Auto-Ecole Horizon',
            'slug' => 'auto-ecole-horizon',
            'is_personal' => false,
        ]);

        $owner = User::factory()->create([
            'name' => 'Proprio',
            'email' => 'owner@example.com',
            'role' => UserRole::AutoEcole,
        ]);

        $autoEcole->memberships()->create([
            'user_id' => $owner->id,
            'role' => TeamRole::Owner,
        ]);
        $owner->switchTeam($autoEcole);

        $response = $this->post('/api/register', [
            'name' => 'Alice Apprentie',
            'email' => 'alice@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'auto_ecole' => 'Auto-Ecole Horizon',
        ]);

        $learner = User::where('email', 'alice@example.com')->first();

        $response->assertStatus(201);
        $this->assertSame(UserRole::Apprenant, $learner->role);
        $this->assertTrue($learner->belongsToTeam($autoEcole));
        $this->assertTrue($autoEcole->members()->whereKey($learner->id)->exists());
    }

    public function test_learner_registration_matches_auto_ecole_names_with_different_formatting(): void
    {
        $autoEcole = Team::create([
            'name' => 'Auto École Horizon',
            'slug' => 'auto-ecole-horizon',
            'is_personal' => false,
        ]);

        $owner = User::factory()->create([
            'name' => 'Proprio',
            'email' => 'owner2@example.com',
            'role' => UserRole::AutoEcole,
        ]);

        $autoEcole->memberships()->create([
            'user_id' => $owner->id,
            'role' => TeamRole::Owner,
        ]);
        $owner->switchTeam($autoEcole);

        $response = $this->post('/api/register', [
            'name' => 'Bob Apprenant',
            'email' => 'bob@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'auto_ecole' => 'Auto-Ecole Horizon',
        ]);

        $response->assertStatus(201);
        $learner = User::where('email', 'bob@example.com')->first();
        $this->assertNotNull($learner);
        $this->assertTrue($learner->belongsToTeam($autoEcole));
    }

    public function test_learner_registration_matches_a_team_name_when_the_input_only_contains_the_city(): void
    {
        $autoEcole = Team::create([
            'name' => 'Auto École Paris',
            'slug' => 'auto-ecole-paris',
            'is_personal' => false,
        ]);

        $owner = User::factory()->create([
            'name' => 'Proprio Paris',
            'email' => 'owner3@example.com',
            'role' => UserRole::AutoEcole,
        ]);

        $autoEcole->memberships()->create([
            'user_id' => $owner->id,
            'role' => TeamRole::Owner,
        ]);
        $owner->switchTeam($autoEcole);

        $response = $this->post('/api/register', [
            'name' => 'Charlie Apprenant',
            'email' => 'charlie@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'auto_ecole' => 'Paris',
        ]);

        $response->assertStatus(201);
        $learner = User::where('email', 'charlie@example.com')->first();
        $this->assertNotNull($learner);
        $this->assertTrue($learner->belongsToTeam($autoEcole));
    }
}
