<?php

namespace Tests\Feature\Api;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Models\Lesson;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LearnerApiTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;
    private User $instructor;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->team = Team::factory()->create([
            'name' => 'Auto-École Etoile',
            'slug' => 'auto-ecole-etoile',
        ]);

        $this->instructor = User::factory()->create([
            'name' => 'Marc Moniteur',
            'email' => 'marc.moniteur@example.com',
            'role' => UserRole::Moniteur,
            'current_team_id' => $this->team->id,
            'email_verified_at' => now(),
        ]);

        $this->student = User::factory()->create([
            'name' => 'Alice Apprenant',
            'email' => 'alice@example.com',
            'role' => UserRole::Apprenant,
            'current_team_id' => $this->team->id,
            'email_verified_at' => now(),
        ]);

        Membership::create([
            'team_id' => $this->team->id,
            'user_id' => $this->instructor->id,
            'role' => TeamRole::Member,
        ]);

        Membership::create([
            'team_id' => $this->team->id,
            'user_id' => $this->student->id,
            'role' => TeamRole::Member,
        ]);
    }

    public function test_learner_can_retrieve_dashboard_with_lessons_created_by_instructor(): void
    {
        // 1. Le moniteur crée un cours
        $lesson = Lesson::create([
            'team_id' => $this->team->id,
            'instructor_id' => $this->instructor->id,
            'student_id' => $this->student->id,
            'title' => 'Créneau et Manœuvres',
            'type' => 'conduite',
            'starts_at' => now()->addDay()->setHour(10)->setMinute(0),
            'ends_at' => now()->addDay()->setHour(11)->setMinute(0),
            'status' => 'planifie',
            'pickup_location' => 'Devant la gare',
            'notes' => 'Attention aux rétroviseurs',
        ]);

        // 2. L'apprenant mobile interroge l'endpoint /api/mobile/dashboard
        Sanctum::actingAs($this->student);

        $response = $this->getJson('/api/mobile/dashboard');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.name', 'Alice Apprenant')
            ->assertJsonPath('next_lesson.title', 'Créneau et Manœuvres')
            ->assertJsonPath('next_lesson.instructor_name', 'Marc Moniteur')
            ->assertJsonPath('lessons.0.title', 'Créneau et Manœuvres')
            ->assertJsonPath('lessons.0.pickup_location', 'Devant la gare');
    }

    public function test_learner_can_retrieve_courses_list(): void
    {
        Lesson::create([
            'team_id' => $this->team->id,
            'instructor_id' => $this->instructor->id,
            'student_id' => $this->student->id,
            'title' => 'Conduite de nuit',
            'type' => 'conduite',
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHour(),
            'status' => 'planifie',
        ]);

        Sanctum::actingAs($this->student);

        $response = $this->getJson('/api/courses');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Conduite de nuit');
    }
}
