<?php

namespace App\Http\Controllers\Moniteur;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request, Team $currentTeam): View
    {
        $user = $request->user();
        $team = $currentTeam;

        $selectedDate = $request->has('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        $viewMode = $request->input('view', 'week'); // 'day' or 'week'
        $instructorId = $request->input('instructor_id', $user->id);

        $query = Lesson::query()
            ->with(['student', 'instructor'])
            ->where('team_id', $team->id);

        if ($instructorId !== 'all') {
            $query->where('instructor_id', $instructorId);
        }

        if ($viewMode === 'day') {
            $lessons = (clone $query)
                ->whereDate('starts_at', $selectedDate)
                ->orderBy('starts_at')
                ->get();
        } else {
            $startOfWeek = $selectedDate->copy()->startOfWeek();
            $endOfWeek = $selectedDate->copy()->endOfWeek();

            $lessons = (clone $query)
                ->whereBetween('starts_at', [$startOfWeek, $endOfWeek])
                ->orderBy('starts_at')
                ->get();
        }

        // Quick counts for instructor
        $todayLessonsCount = Lesson::query()
            ->where('team_id', $team->id)
            ->when($instructorId !== 'all', fn($q) => $q->where('instructor_id', $instructorId))
            ->whereDate('starts_at', Carbon::today())
            ->count();

        $weekLessonsCount = Lesson::query()
            ->where('team_id', $team->id)
            ->when($instructorId !== 'all', fn($q) => $q->where('instructor_id', $instructorId))
            ->whereBetween('starts_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $completedLessonsCount = Lesson::query()
            ->where('team_id', $team->id)
            ->when($instructorId !== 'all', fn($q) => $q->where('instructor_id', $instructorId))
            ->where('status', 'termine')
            ->count();

        $instructors = $team->members()
            ->where('users.role', \App\Enums\UserRole::Moniteur->value)
            ->get();

        return view('moniteurs.schedule.index', [
            'lessons' => $lessons,
            'selectedDate' => $selectedDate,
            'viewMode' => $viewMode,
            'selectedInstructorId' => $instructorId,
            'instructors' => $instructors,
            'todayLessonsCount' => $todayLessonsCount,
            'weekLessonsCount' => $weekLessonsCount,
            'completedLessonsCount' => $completedLessonsCount,
            'currentTeam' => $team,
        ]);

    }
}
