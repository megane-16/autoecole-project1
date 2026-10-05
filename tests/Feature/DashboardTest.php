<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $user = User::factory()->create();
        $team = $user->currentTeam;

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create(['role' => UserRole::AutoEcole]);
        $team = $user->currentTeam;

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_auto_school_dashboard_lists_the_team_learners_from_the_database(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AutoEcole]);
        $team = Team::factory()->create([
            'name' => 'Auto École Paris',
            'slug' => 'auto-ecole-paris',
            'is_personal' => false,
        ]);

        $team->memberships()->create([
            'user_id' => $owner->id,
            'role' => TeamRole::Owner,
        ]);
        $owner->switchTeam($team);

        $learnerOne = User::factory()->create(['name' => 'Alice Apprentie', 'role' => UserRole::Apprenant]);
        $learnerTwo = User::factory()->create(['name' => 'Bob Apprenant', 'role' => UserRole::Apprenant]);

        $team->members()->attach($learnerOne, ['role' => TeamRole::Member->value]);
        $team->members()->attach($learnerTwo, ['role' => TeamRole::Member->value]);

        $this->actingAs($owner)
            ->get(route('dashboard', ['current_team' => $team->slug]))
            ->assertOk()
            ->assertSee('Alice Apprentie')
            ->assertSee('Bob Apprenant');
    }

    public function test_auto_school_owner_can_add_a_learner_to_the_team(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AutoEcole]);
        $team = Team::factory()->create([
            'name' => 'Auto École Paris',
            'slug' => 'auto-ecole-paris',
            'is_personal' => false,
        ]);

        $team->memberships()->create([
            'user_id' => $owner->id,
            'role' => TeamRole::Owner,
        ]);
        $owner->switchTeam($team);

        $response = $this->actingAs($owner)->post(route('dashboard.learners.store', ['current_team' => $team->slug]), [
            'name' => 'Claire Apprentie',
            'email' => 'claire@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'categorie_permis' => 'B',
            'ville' => 'Paris',
        ]);

        $response->assertRedirect();

        $learner = User::where('email', 'claire@example.com')->first();
        $this->assertNotNull($learner);
        $this->assertSame(UserRole::Apprenant, $learner->role);
        $this->assertTrue($team->members()->whereKey($learner->id)->exists());
    }

    public function test_only_administrators_can_visit_the_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_administrators_can_see_platform_statistics(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrateur]);
        User::factory()->count(2)->create();

        $this->actingAs($administrator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Administration SaaS')
            ->assertSee('Auto-écoles')
            ->assertSee('Utilisateurs');
    }

    public function test_dashboard_uses_the_auto_ecole_team_when_the_user_has_a_personal_team(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AutoEcole]);
        $personalTeam = $owner->personalTeam();

        $autoEcoleTeam = Team::factory()->create([
            'name' => 'Auto École Paris',
            'slug' => 'auto-ecole-paris',
            'is_personal' => false,
        ]);

        $autoEcoleTeam->memberships()->create([
            'user_id' => $owner->id,
            'role' => TeamRole::Owner,
        ]);
        $owner->switchTeam($personalTeam);

        $learner = User::factory()->create(['name' => 'Alice Apprentie', 'role' => UserRole::Apprenant]);
        $autoEcoleTeam->members()->attach($learner, ['role' => TeamRole::Member->value]);

        $this->actingAs($owner)
            ->get(route('dashboard', ['current_team' => $autoEcoleTeam->slug]))
            ->assertOk()
            ->assertSee('Alice Apprentie');
    }

    public function test_dashboard_shows_learners_linked_by_auto_ecole_name_even_without_team_membership(): void
    {
        $owner = User::factory()->create(['role' => UserRole::AutoEcole]);
        $team = Team::factory()->create([
            'name' => 'Auto École Paris',
            'slug' => 'auto-ecole-paris',
            'is_personal' => false,
        ]);

        $team->memberships()->create([
            'user_id' => $owner->id,
            'role' => TeamRole::Owner,
        ]);
        $owner->switchTeam($team);

        $learner = User::factory()->create([
            'name' => 'Claire Apprentie',
            'role' => UserRole::Apprenant,
            'auto_ecole' => 'Auto École Paris',
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard', ['current_team' => $team->slug]))
            ->assertOk()
            ->assertSee('Claire Apprentie');
    }

    public function test_admin_dashboard_preview_requires_admin_authentication(): void
    {
        // Un invité est redirigé vers le login
        $this->get(route('admin.preview'))
            ->assertRedirect(route('login'));

        // Un utilisateur normal est rejeté
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('admin.preview'))
            ->assertForbidden();

        // Un administrateur connecté y accède
        $admin = User::factory()->create(['role' => UserRole::Administrateur]);
        $this->actingAs($admin)
            ->get(route('admin.preview'))
            ->assertOk()
            ->assertSee('Mode prévisualisation')
            ->assertSee('Flux des demandes', false);
    }
}




