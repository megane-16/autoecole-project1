<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Lesson;
use App\Models\RegistrationRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AutoEcoleDashboardController extends Controller
{
    /**
     * Display the auto-école dashboard with full real-time data.
     */
    public function index(Request $request, Team $currentTeam): View
    {
        $team = $currentTeam;

        // 1. Apprenants de l'auto-école
        $teamLearners = $team->members()
            ->where('users.role', UserRole::Apprenant->value)
            ->orderByDesc('users.created_at')
            ->get();

        $legacyLearners = User::query()
            ->where('role', UserRole::Apprenant->value)
            ->where(function ($query) use ($team) {
                $query->where('auto_ecole', $team->name)
                    ->orWhere('auto_ecole', str_replace(['-', '_'], ' ', $team->name));
            })
            ->orderByDesc('created_at')
            ->get();

        $learners = $teamLearners->merge($legacyLearners)->unique('id')->values();
        $learnerCount = $learners->count();

        // 2. Moniteurs de l'auto-école
        $instructors = $team->members()
            ->where('users.role', UserRole::Moniteur->value)
            ->orderBy('users.name')
            ->get();

        // 3. Planning du jour & de la semaine
        $todayLessons = Lesson::query()
            ->with(['student', 'instructor'])
            ->where('team_id', $team->id)
            ->whereDate('starts_at', Carbon::today())
            ->orderBy('starts_at')
            ->get();

        $weekLessonsCount = Lesson::query()
            ->where('team_id', $team->id)
            ->whereBetween('starts_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $totalLessons = Lesson::query()->where('team_id', $team->id)->get();
        $completedLessonsCount = $totalLessons->where('status', 'termine')->count();

        // 4. Demandes en attente
        $pendingRequestsCount = RegistrationRequest::where('team_id', $team->id)
            ->where('status', 'pending')
            ->count();

        // 5. Progression globale des leçons / examen
        $examLessonsCount = $totalLessons->where('type', 'examen_blanc')->count();
        $conduiteLessonsCount = $totalLessons->where('type', 'conduite')->count();
        $codeLessonsCount = $totalLessons->where('type', 'code')->count();

        return view('dashboard', [
            'currentTeam' => $team,
            'team' => $team,
            'learners' => $learners,
            'learnerCount' => $learnerCount,
            'instructors' => $instructors,
            'todayLessons' => $todayLessons,
            'weekLessonsCount' => $weekLessonsCount,
            'completedLessonsCount' => $completedLessonsCount,
            'pendingRequestsCount' => $pendingRequestsCount,
            'examLessonsCount' => $examLessonsCount,
            'conduiteLessonsCount' => $conduiteLessonsCount,
            'codeLessonsCount' => $codeLessonsCount,
        ]);
    }

    /**
     * Store a new lesson / planning directly from the auto-école dashboard.
     */
    public function storeLesson(Request $request, Team $currentTeam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'exists:users,id'],
            'instructor_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'string', 'in:conduite,code,examen_blanc,evaluation'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', 'string', 'in:planifie,en_cours,termine,annule'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['team_id'] = $currentTeam->id;

        $lesson = Lesson::create($validated);

        // Association automatique apprenant-moniteur si besoin
        if (! empty($validated['student_id'])) {
            $instructor = User::find($validated['instructor_id']);
            if ($instructor && ! $instructor->assignedStudents()->where('student_id', $validated['student_id'])->exists()) {
                $instructor->assignedStudents()->attach($validated['student_id'], [
                    'team_id' => $currentTeam->id,
                ]);
            }
        }

        return redirect()->route('dashboard', ['current_team' => $currentTeam->slug])
            ->with('success', "Leçon « {$lesson->title} » planifiée et transmise au moniteur avec succès.");
    }
}
