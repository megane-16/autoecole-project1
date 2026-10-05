<?php

namespace App\Http\Controllers\Moniteur;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request, Team $currentTeam): View
    {
        $user = $request->user();
        $team = $currentTeam;

        // Directly assigned students via pivot
        $assignedStudents = $user->assignedStudents()
            ->wherePivot('team_id', $team->id)
            ->get();

        // Students who have lessons with this instructor
        $lessonStudentIds = Lesson::query()
            ->where('instructor_id', $user->id)
            ->where('team_id', $team->id)
            ->whereNotNull('student_id')
            ->distinct()
            ->pluck('student_id');

        $lessonStudents = User::whereIn('id', $lessonStudentIds)->get();

        // Merge both sets of associated students
        $students = $assignedStudents->merge($lessonStudents)->unique('id')->values();

        // Total hours / statistics for each student
        $students->each(function ($student) use ($user, $team) {
            $studentLessons = Lesson::query()
                ->where('instructor_id', $user->id)
                ->where('student_id', $student->id)
                ->where('team_id', $team->id)
                ->get();

            $student->total_lessons_count = $studentLessons->count();
            $student->completed_lessons_count = $studentLessons->where('status', 'termine')->count();
            $student->next_lesson = $studentLessons->where('starts_at', '>=', now())
                ->where('status', '!=', 'annule')
                ->sortBy('starts_at')
                ->first();
        });

        // Other available team learners to associate if needed
        $allTeamLearners = $team->members()
            ->where('users.role', UserRole::Apprenant->value)
            ->whereNotIn('users.id', $students->pluck('id'))
            ->get();

        return view('moniteurs.students.index', [
            'students' => $students,
            'allTeamLearners' => $allTeamLearners,
            'currentTeam' => $team,
        ]);
    }

    public function show(Request $request, Team $currentTeam, User $student): View
    {
        $user = $request->user();
        $team = $currentTeam;

        $lessons = Lesson::query()
            ->with('instructor')
            ->where('student_id', $student->id)
            ->where('team_id', $team->id)
            ->orderByDesc('starts_at')
            ->get();


        $totalCompletedHours = $lessons->where('status', 'termine')->reduce(function ($carry, $lesson) {
            return $carry + $lesson->starts_at->diffInMinutes($lesson->ends_at) / 60;
        }, 0);

        return view('moniteurs.students.show', [
            'student' => $student,
            'lessons' => $lessons,
            'totalCompletedHours' => round($totalCompletedHours, 1),
            'currentTeam' => $team,
        ]);
    }

    public function assign(Request $request, Team $currentTeam): RedirectResponse
    {
        $user = $request->user();
        $team = $currentTeam;

        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
        ]);

        $student = User::findOrFail($validated['student_id']);

        if (! $user->assignedStudents()->where('student_id', $student->id)->exists()) {
            $user->assignedStudents()->attach($student->id, [
                'team_id' => $team->id,
            ]);
        }

        return back()->with('success', "L'apprenant {$student->name} a été associé avec succès.");
    }

    public function unassign(Request $request, Team $currentTeam, User $student): RedirectResponse
    {
        $user = $request->user();
        $user->assignedStudents()->detach($student->id);

        return back()->with('success', "L'association avec {$student->name} a été retirée.");
    }
}
