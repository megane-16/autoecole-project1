<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Enums\UserRole;
use App\Models\Lesson;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MoniteurFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private Team $team;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->team = Team::factory()->create([
            'name' => 'Auto-École Etoile',
            'slug' => 'auto-ecole-etoile',
        ]);

        $this->instructor = User::factory()->create([
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'role' => UserRole::Moniteur,
            'current_team_id' => $this->team->id,
            'email_verified_at' => now(),
        ]);

        $this->student = User::factory()->create([
            'name' => 'Alice Martin',
            'email' => 'alice.martin@example.com',
            'role' => UserRole::Apprenant,
            'categorie_permis' => 'B',
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

    public function test_instructor_can_view_schedule(): void
    {
        Lesson::create([
            'team_id' => $this->team->id,
            'instructor_id' => $this->instructor->id,
            'student_id' => $this->student->id,
            'title' => 'Conduite urbaine',
            'type' => 'conduite',
            'starts_at' => now()->startOfWeek()->addHours(10),
            'ends_at' => now()->startOfWeek()->addHours(11),
            'status' => 'planifie',
            'pickup_location' => 'Centre ville',
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('instructor.schedule.index', ['current_team' => $this->team->slug, 'date' => now()->format('Y-m-d')]));

        $response->assertOk()
            ->assertSee('Planning')
            ->assertSee('Conduite urbaine')
            ->assertSee('Alice Martin');
    }

    public function test_instructor_can_view_and_assign_associated_students(): void
    {
        $response = $this->actingAs($this->instructor)
            ->get(route('instructor.students.index', ['current_team' => $this->team->slug]));

        $response->assertOk()
            ->assertSee('Mes Apprenants Associés');

        // Assign student
        $assignResponse = $this->actingAs($this->instructor)
            ->post(route('instructor.students.assign', ['current_team' => $this->team->slug]), [
                'student_id' => $this->student->id,
            ]);

        $assignResponse->assertRedirect();
        $this->assertDatabaseHas('instructor_student', [
            'instructor_id' => $this->instructor->id,
            'student_id' => $this->student->id,
        ]);

        // View student detail
        $showResponse = $this->actingAs($this->instructor)
            ->get(route('instructor.students.show', [
                'current_team' => $this->team->slug,
                'student' => $this->student->id,
            ]));

        $showResponse->assertOk()
            ->assertSee('Alice Martin')
            ->assertSee('alice.martin@example.com');
    }

    public function test_instructor_can_perform_full_crud_on_lessons_with_pdf_attachment(): void
    {
        Storage::fake('public');

        // 1. Create Lesson with PDF document
        $pdfFile = UploadedFile::fake()->create('cours_conduite.pdf', 500, 'application/pdf');

        $storeResponse = $this->actingAs($this->instructor)
            ->post(route('instructor.lessons.store', ['current_team' => $this->team->slug]), [
                'title' => 'Manœuvres et créneau',
                'student_id' => $this->student->id,
                'type' => 'conduite',
                'starts_at' => now()->addDays(2)->setHour(14)->setMinute(0)->toDateTimeString(),
                'ends_at' => now()->addDays(2)->setHour(15)->setMinute(30)->toDateTimeString(),
                'status' => 'planifie',
                'pickup_location' => 'Parking Sud',
                'notes' => 'Travailler le rangement en bataille et créneau droit.',
                'media' => $pdfFile,
            ]);

        $lesson = Lesson::where('title', 'Manœuvres et créneau')->firstOrFail();
        $this->assertNotNull($lesson->media_url);
        $this->assertStringEndsWith('.pdf', $lesson->media_url);

        $storeResponse->assertRedirect(route('instructor.lessons.show', ['current_team' => $this->team->slug, 'lesson' => $lesson->id]));

        // 2. Index Lesson
        $indexResponse = $this->actingAs($this->instructor)
            ->get(route('instructor.lessons.index', ['current_team' => $this->team->slug]));
        $indexResponse->assertOk()->assertSee('Manœuvres et créneau');

        // 3. Show Lesson displays PDF document badge
        $showResponse = $this->actingAs($this->instructor)
            ->get(route('instructor.lessons.show', ['current_team' => $this->team->slug, 'lesson' => $lesson->id]));
        $showResponse->assertOk()
            ->assertSee('Manœuvres et créneau')
            ->assertSee('Document de cours PDF')
            ->assertSee('Parking Sud');

        // 4. Update Lesson with replacement PDF
        $newPdfFile = UploadedFile::fake()->create('cours_conduite_v2.pdf', 600, 'application/pdf');
        $updateResponse = $this->actingAs($this->instructor)
            ->put(route('instructor.lessons.update', ['current_team' => $this->team->slug, 'lesson' => $lesson->id]), [
                'title' => 'Manœuvres avancées',
                'student_id' => $this->student->id,
                'type' => 'conduite',
                'starts_at' => now()->addDays(2)->setHour(14)->setMinute(0)->toDateTimeString(),
                'ends_at' => now()->addDays(2)->setHour(15)->setMinute(30)->toDateTimeString(),
                'status' => 'en_cours',
                'pickup_location' => 'Parking Nord',
                'notes' => 'Mise à jour des objectifs.',
                'media' => $newPdfFile,
            ]);

        $updateResponse->assertRedirect(route('instructor.lessons.index', ['current_team' => $this->team->slug]));
        $lesson->refresh();
        $this->assertEquals('Manœuvres avancées', $lesson->title);
        $this->assertStringEndsWith('.pdf', $lesson->media_url);

        // 5. Delete Lesson
        $destroyResponse = $this->actingAs($this->instructor)
            ->delete(route('instructor.lessons.destroy', ['current_team' => $this->team->slug, 'lesson' => $lesson->id]));

        $destroyResponse->assertRedirect(route('instructor.lessons.index', ['current_team' => $this->team->slug]));
        $this->assertDatabaseMissing('lessons', [
            'id' => $lesson->id,
        ]);
    }

    public function test_instructor_can_create_lesson_without_student(): void
    {
        $response = $this->actingAs($this->instructor)
            ->post(route('instructor.lessons.store', ['current_team' => $this->team->slug]), [
                'title' => 'Cours de code collectif',
                'student_id' => null,
                'type' => 'code',
                'starts_at' => now()->addDays(2)->setHour(10)->setMinute(0)->toDateTimeString(),
                'ends_at' => now()->addDays(2)->setHour(12)->setMinute(0)->toDateTimeString(),
                'status' => 'planifie',
                'pickup_location' => 'Salle 1',
                'notes' => 'Panneaux de signalisation et priorité.',
            ]);

        $lesson = Lesson::where('title', 'Cours de code collectif')->firstOrFail();
        $this->assertNull($lesson->student_id);
        $response->assertRedirect(route('instructor.lessons.show', ['current_team' => $this->team->slug, 'lesson' => $lesson->id]));
    }

    public function test_instructor_can_view_and_collaborate_on_team_lessons(): void
    {
        $otherInstructor = User::factory()->create([
            'name' => 'Autre Moniteur',
            'email' => 'autre@example.com',
            'role' => UserRole::Moniteur,
            'current_team_id' => $this->team->id,
            'email_verified_at' => now(),
        ]);

        Membership::create([
            'team_id' => $this->team->id,
            'user_id' => $otherInstructor->id,
            'role' => TeamRole::Member,
        ]);

        $teamLesson = Lesson::create([
            'team_id' => $this->team->id,
            'instructor_id' => $otherInstructor->id,
            'student_id' => $this->student->id,
            'title' => 'Cours partagé auto-école',
            'type' => 'conduite',
            'starts_at' => now()->addDays(1),
            'ends_at' => now()->addDays(1)->addHour(),
            'status' => 'planifie',
        ]);

        // 1. Accès autorisé en mode collaboratif au sein de la même auto-école
        $this->actingAs($this->instructor)
            ->get(route('instructor.lessons.show', ['current_team' => $this->team->slug, 'lesson' => $teamLesson->id]))
            ->assertOk()
            ->assertSee('Cours partagé auto-école');

        // 2. Modification autorisée
        $this->actingAs($this->instructor)
            ->put(route('instructor.lessons.update', ['current_team' => $this->team->slug, 'lesson' => $teamLesson->id]), [
                'title' => 'Cours partagé modifié',
                'type' => 'conduite',
                'starts_at' => now()->addDays(1)->toDateTimeString(),
                'ends_at' => now()->addDays(1)->addHour()->toDateTimeString(),
                'status' => 'en_cours',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('lessons', [
            'id' => $teamLesson->id,
            'title' => 'Cours partagé modifié',
            'status' => 'en_cours',
        ]);
    }

    public function test_instructor_cannot_access_lessons_from_another_team(): void
    {
        $otherTeam = Team::factory()->create([
            'name' => 'Autre Auto-École',
            'slug' => 'autre-auto-ecole',
        ]);

        $foreignLesson = Lesson::create([
            'team_id' => $otherTeam->id,
            'instructor_id' => $this->instructor->id,
            'title' => 'Cours dans une autre auto école',
            'type' => 'conduite',
            'starts_at' => now()->addDays(1),
            'ends_at' => now()->addDays(1)->addHour(),
            'status' => 'planifie',
        ]);

        // Accès interdit si la leçon est d'une autre auto-école
        $this->actingAs($this->instructor)
            ->get(route('instructor.lessons.show', ['current_team' => $this->team->slug, 'lesson' => $foreignLesson->id]))
            ->assertForbidden();
    }

    public function test_lesson_created_by_instructor_is_immediately_visible_by_learner_on_mobile_api(): void
    {
        // 1. L'instructeur crée un cours assigné à l'apprenant
        $this->actingAs($this->instructor)
            ->post(route('instructor.lessons.store', ['current_team' => $this->team->slug]), [
                'title' => 'Circulation en rond-point',
                'student_id' => $this->student->id,
                'type' => 'conduite',
                'starts_at' => now()->addDays(2)->setHour(14)->setMinute(0)->toDateTimeString(),
                'ends_at' => now()->addDays(2)->setHour(15)->setMinute(0)->toDateTimeString(),
                'status' => 'planifie',
                'pickup_location' => 'Place centrale',
                'notes' => 'Travailler les contrôles d’angle mort et insertion.',
            ])
            ->assertRedirect();

        // 2. L'apprenant se connecte via l'API mobile Sanctum et interroge ses cours
        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/mobile/dashboard');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('courses.0.title', 'Circulation en rond-point')
            ->assertJsonPath('courses.0.instructor_name', $this->instructor->name)
            ->assertJsonPath('courses.0.pickup_location', 'Place centrale');

        $coursesResponse = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/courses');

        $coursesResponse->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.title', 'Circulation en rond-point');
    }

    public function test_instructor_can_export_lessons_as_csv_locally(): void
    {
        Lesson::create([
            'team_id' => $this->team->id,
            'instructor_id' => $this->instructor->id,
            'student_id' => $this->student->id,
            'title' => 'Conduite de nuit',
            'type' => 'conduite',
            'starts_at' => now()->addDays(1)->setHour(20)->setMinute(0),
            'ends_at' => now()->addDays(1)->setHour(21)->setMinute(0),
            'status' => 'planifie',
            'pickup_location' => 'Gare SNCF',
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('instructor.lessons.export', ['current_team' => $this->team->slug]));

        $response->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
