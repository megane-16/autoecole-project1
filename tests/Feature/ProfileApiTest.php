<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Jean Apprenant',
            'email' => 'jean@example.com',
            'password' => Hash::make('oldpassword123'),
            'telephone' => '690000001',
            'ville' => 'Douala',
            'role' => UserRole::Apprenant,
        ]);
    }

    public function test_user_can_update_profile_info(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/profile/update', [
                'name' => 'Jean Nouveau Nom',
                'telephone' => '699999999',
                'ville' => 'Yaoundé',
            ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.telephone', '699999999')
            ->assertJsonPath('user.ville', 'Yaoundé');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Jean Nouveau Nom',
            'telephone' => '699999999',
            'ville' => 'Yaoundé',
        ]);
    }

    public function test_user_cannot_take_another_users_phone_number(): void
    {
        User::factory()->create([
            'email' => 'other@example.com',
            'telephone' => '677777777',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/profile/update', [
                'telephone' => '677777777',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['telephone']);
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/profile/password', [
                'current_password' => 'oldpassword123',
                'password' => 'newpassword456',
                'password_confirmation' => 'newpassword456',
            ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertTrue(Hash::check('newpassword456', $this->user->fresh()->password));
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/profile/password', [
                'current_password' => 'wrongpassword',
                'password' => 'newpassword456',
                'password_confirmation' => 'newpassword456',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error');

        $this->assertTrue(Hash::check('oldpassword123', $this->user->fresh()->password));
    }

    public function test_instructor_can_schedule_practical_lesson_visible_by_learner(): void
    {
        $team = Team::factory()->create(['name' => 'Auto-École Horizon', 'slug' => 'auto-ecole-horizon']);

        $instructor = User::factory()->create([
            'name' => 'Moniteur Paul',
            'email' => 'paul@example.com',
            'role' => UserRole::Moniteur,
            'current_team_id' => $team->id,
        ]);

        Membership::create(['team_id' => $team->id, 'user_id' => $instructor->id, 'role' => TeamRole::Member]);
        Membership::create(['team_id' => $team->id, 'user_id' => $this->user->id, 'role' => TeamRole::Member]);
        $this->user->switchTeam($team);

        // Instructor plans a practical driving lesson
        $this->actingAs($instructor)
            ->post(route('instructor.lessons.store', ['current_team' => $team->slug]), [
                'title' => 'Conduite pratique - Démarrage en côte',
                'student_id' => $this->user->id,
                'type' => 'conduite',
                'starts_at' => now()->addDay()->setHour(10)->setMinute(0)->toDateTimeString(),
                'ends_at' => now()->addDay()->setHour(11)->setMinute(0)->toDateTimeString(),
                'status' => 'planifie',
                'pickup_location' => 'Rond point Deido',
                'notes' => 'Maîtriser le point de patinage et le frein à main.',
            ])
            ->assertRedirect();

        // Learner checks dashboard
        $learnerResponse = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/mobile/dashboard');

        $learnerResponse->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('courses.0.title', 'Conduite pratique - Démarrage en côte')
            ->assertJsonPath('courses.0.type', 'practical')
            ->assertJsonPath('courses.0.instructor_name', 'Moniteur Paul')
            ->assertJsonPath('courses.0.pickup_location', 'Rond point Deido');
    }
}
