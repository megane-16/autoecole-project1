<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LearnerDashboardController extends Controller
{
    /**
     * Dashboard data for the authenticated learner.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        $teamIds = $user->teams()->pluck('teams.id')->toArray();
        if ($user->current_team_id) {
            $teamIds[] = $user->current_team_id;
        }
        $teamIds = array_unique(array_filter($teamIds));

        // Récupération des cours de l'apprenant (individuels et collectifs de son auto-école)
        $lessonsQuery = Lesson::query()
            ->with(['instructor', 'team'])
            ->where(function ($q) use ($user, $teamIds) {
                $q->where('student_id', $user->id);
                if (! empty($teamIds)) {
                    $q->orWhere(function ($sq) use ($teamIds) {
                        $sq->whereNull('student_id')->whereIn('team_id', $teamIds);
                    });
                }
            })
            ->orderByDesc('starts_at');

        $lessons = $lessonsQuery->get();

        // Formater les leçons pour le mobile
        $formattedLessons = $lessons->map(function (Lesson $lesson) {
            return $this->formatLesson($lesson);
        });

        // Prochaine leçon (future ou en cours, non annulée)
        $nextLesson = Lesson::query()
            ->with(['instructor', 'team'])
            ->where(function ($q) use ($user, $teamIds) {
                $q->where('student_id', $user->id);
                if (! empty($teamIds)) {
                    $q->orWhere(function ($sq) use ($teamIds) {
                        $sq->whereNull('student_id')->whereIn('team_id', $teamIds);
                    });
                }
            })
            ->where('status', '!=', 'annule')
            ->where('starts_at', '>=', Carbon::now()->subHours(2))
            ->orderBy('starts_at')
            ->first();

        // Calcul des heures et statistiques
        $completedLessons = $lessons->where('status', 'termine');
        $completedMinutes = $completedLessons->reduce(function ($carry, Lesson $lesson) {
            if ($lesson->starts_at && $lesson->ends_at) {
                return $carry + $lesson->starts_at->diffInMinutes($lesson->ends_at);
            }
            return $carry + 60; // 1 heure par défaut
        }, 0);

        $completedHours = (int) round($completedMinutes / 60);
        $totalHoursRequired = 20; // Volume standard minimum pour permis B

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->value ?? $user->role,
                'auto_ecole' => $user->currentTeam?->name ?? '',
            ],
            'stats' => [
                'learners_count' => User::where('role', 'apprenant')->count(),
                'courses_count' => $lessons->count(),
                'scans_count' => 0,
            ],
            'progress' => [
                'completed_hours' => $completedHours,
                'total_hours' => $totalHoursRequired,
                'completed_lessons' => $completedLessons->count(),
                'total_lessons' => $lessons->count(),
            ],
            'next_lesson' => $nextLesson ? $this->formatLesson($nextLesson) : null,
            'lessons' => $formattedLessons,
            'courses' => $formattedLessons,
        ]);
    }

    /**
     * Liste des cours uniquement.
     */
    public function courses(Request $request): JsonResponse
    {
        $user = $request->user();

        $teamIds = $user->teams()->pluck('teams.id')->toArray();
        if ($user->current_team_id) {
            $teamIds[] = $user->current_team_id;
        }
        $teamIds = array_unique(array_filter($teamIds));

        $lessons = Lesson::query()
            ->with(['instructor', 'team'])
            ->where(function ($q) use ($user, $teamIds) {
                $q->where('student_id', $user->id);
                if (! empty($teamIds)) {
                    $q->orWhere(function ($sq) use ($teamIds) {
                        $sq->whereNull('student_id')->whereIn('team_id', $teamIds);
                    });
                }
            })
            ->orderByDesc('starts_at')
            ->get();

        $formatted = $lessons->map(fn (Lesson $l) => $this->formatLesson($l));

        return response()->json([
            'status' => 'success',
            'data' => $formatted,
            'courses' => $formatted,
        ]);
    }

    /**
     * Formatage structuré conforme aux modèles de l'application Flutter.
     */
    private function formatLesson(Lesson $lesson): array
    {
        $startsAt = $lesson->starts_at;
        $formattedDate = $startsAt ? $startsAt->translatedFormat('l d F Y') : '';
        $formattedTime = $startsAt ? $startsAt->format('H\hi') : '';
        $startAtDisplay = $startsAt ? "{$startsAt->translatedFormat('d M Y')} à {$formattedTime}" : '';

        return [
            'id' => $lesson->id,
            'title' => $lesson->title ?? $lesson->formatted_type,
            'topic' => $lesson->title,
            'type' => $lesson->type === 'code' ? 'theory' : 'practical',
            'type_label' => $lesson->formatted_type,
            'status' => $lesson->status,
            'status_label' => $lesson->status_label,
            'starts_at' => $startsAt?->toIso8601String(),
            'ends_at' => $lesson->ends_at?->toIso8601String(),
            'formatted_date' => $formattedDate,
            'formatted_time' => $formattedTime,
            'start_at_display' => $startAtDisplay,
            'pickup_location' => $lesson->pickup_location,
            'notes' => $lesson->notes,
            'instructor_id' => $lesson->instructor_id,
            'instructor_name' => $lesson->instructor?->name ?? 'Moniteur assigné',
            'teacher_name' => $lesson->instructor?->name ?? 'Moniteur assigné',
            'media_url' => $lesson->media_url,
            'media_type' => $lesson->media_url ? 'image' : null,
        ];
    }
}
