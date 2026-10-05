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

class LessonController extends Controller
{
    public function index(Request $request, Team $currentTeam): View
    {
        $user = $request->user();
        $team = $currentTeam;

        $query = Lesson::query()
            ->with(['student', 'instructor', 'team'])
            ->where('team_id', $team->id);

        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->input('instructor_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('pickup_location', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('instructor', function ($iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $lessons = $query->orderByDesc('starts_at')->paginate(10)->withQueryString();

        // Get list of students and instructors for the filters
        $students = $team->members()
            ->where('users.role', UserRole::Apprenant->value)
            ->get();

        $instructors = $team->members()
            ->where('users.role', UserRole::Moniteur->value)
            ->get();

        return view('moniteurs.lessons.index', [
            'lessons' => $lessons,
            'students' => $students,
            'instructors' => $instructors,
            'currentTeam' => $team,
        ]);

    }

    public function create(Request $request, Team $currentTeam): View
    {
        $user = $request->user();
        $team = $currentTeam;

        // Retrieve team students
        $students = $team->members()
            ->where('users.role', UserRole::Apprenant->value)
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            $students = $user->assignedStudents;
        }

        return view('moniteurs.lessons.create', [
            'students' => $students,
            'selectedStudentId' => $request->query('student_id'),
            'currentTeam' => $team,
        ]);
    }

    public function store(Request $request, Team $currentTeam): RedirectResponse
    {
        $user = $request->user();
        $team = $currentTeam;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'exists:users,id'],
            'type' => ['required', 'string', 'in:conduite,code,examen_blanc,evaluation'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', 'string', 'in:planifie,en_cours,termine,annule'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:204800'],
        ], [
            'media.uploaded' => 'Le fichier n\'a pas pu être téléchargé. Vérifiez que sa taille ne dépasse pas la limite autorisée (max 200 Mo).',
            'media.mimes' => 'Le fichier doit être au format PDF, JPG ou PNG.',
            'media.max' => 'La taille du document ne doit pas dépasser 200 Mo.',
            'title.required' => 'Le titre du cours est obligatoire.',
            'starts_at.required' => 'La date et l\'heure de début sont obligatoires.',
            'ends_at.after' => 'L\'heure de fin doit être postérieure à l\'heure de début.',
        ]);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('lessons-media', 'public');
            $validated['media_url'] = \Illuminate\Support\Facades\Storage::url($path);
        }

        $validated['instructor_id'] = $user->id;
        $validated['team_id'] = $team->id;

        $lesson = Lesson::create($validated);

        // Also auto-associate student with instructor if not already
        if (! empty($validated['student_id'])) {
            if (! $user->assignedStudents()->where('student_id', $validated['student_id'])->exists()) {
                $user->assignedStudents()->attach($validated['student_id'], [
                    'team_id' => $validated['team_id'],
                ]);
            }
        }

        return redirect()->route('instructor.lessons.show', ['current_team' => $team->slug, 'lesson' => $lesson->id])
            ->with('success', 'Le cours a été planifié avec succès et est visible immédiatement par l’apprenant. Vous pouvez télécharger la fiche de séance ci-dessous.');
    }

    public function show(Request $request, Team $currentTeam, Lesson $lesson): View
    {
        $this->authorizeLesson($request->user(), $lesson);
        $team = $currentTeam;

        return view('moniteurs.lessons.show', [
            'lesson' => $lesson->load(['student', 'team']),
            'currentTeam' => $team,
        ]);
    }

    public function edit(Request $request, Team $currentTeam, Lesson $lesson): View
    {
        $this->authorizeLesson($request->user(), $lesson);
        $team = $currentTeam;

        $students = $team->members()
            ->where('users.role', UserRole::Apprenant->value)
            ->orderBy('name')
            ->get();

        return view('moniteurs.lessons.edit', [
            'lesson' => $lesson,
            'students' => $students,
            'currentTeam' => $team,
        ]);
    }

    public function update(Request $request, Team $currentTeam, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($request->user(), $lesson);
        $team = $currentTeam;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'exists:users,id'],
            'type' => ['required', 'string', 'in:conduite,code,examen_blanc,evaluation'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', 'string', 'in:planifie,en_cours,termine,annule'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:204800'],
        ], [
            'media.uploaded' => 'Le fichier n\'a pas pu être téléchargé. Vérifiez que sa taille ne dépasse pas la limite autorisée (max 200 Mo).',
            'media.mimes' => 'Le fichier doit être au format PDF, JPG ou PNG.',
            'media.max' => 'La taille du document ne doit pas dépasser 200 Mo.',
            'title.required' => 'Le titre du cours est obligatoire.',
            'starts_at.required' => 'La date et l\'heure de début sont obligatoires.',
            'ends_at.after' => 'L\'heure de fin doit être postérieure à l\'heure de début.',
        ]);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('lessons-media', 'public');
            $validated['media_url'] = \Illuminate\Support\Facades\Storage::url($path);
        }

        $lesson->update($validated);

        return redirect()->route('instructor.lessons.index', ['current_team' => $team->slug])
            ->with('success', 'Le cours a été mis à jour avec succès.');
    }

    public function updateStatus(Request $request, Team $currentTeam, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($request->user(), $lesson);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:planifie,en_cours,termine,annule'],
        ]);

        $lesson->update(['status' => $validated['status']]);

        return back()->with('success', 'Le statut du cours a été actualisé.');
    }

    public function destroy(Request $request, Team $currentTeam, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($request->user(), $lesson);
        $team = $currentTeam;

        $lesson->delete();

        return redirect()->route('instructor.lessons.index', ['current_team' => $team->slug])
            ->with('success', 'Le cours a été supprimé avec succès.');
    }

    public function export(Request $request, Team $currentTeam)
    {
        $team = $currentTeam;
        $query = Lesson::query()
            ->with(['student', 'instructor', 'team'])
            ->where('team_id', $team->id);

        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->input('instructor_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('pickup_location', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', "%{$search}%"));
            });
        }

        $lessons = $query->orderBy('starts_at')->get();

        if ($request->query('format') === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('moniteurs.lessons.export_pdf', [
                'lessons' => $lessons,
                'currentTeam' => $team,
            ])->setPaper('a4', 'landscape');

            $filename = 'cours_' . $team->slug . '_' . now()->format('Ymd_His') . '.pdf';

            return $pdf->download($filename);
        }

        $filename = 'cours_' . $team->slug . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($lessons) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'ID Cours',
                'Titre',
                'Type',
                'Statut',
                'Date',
                'Heure Début',
                'Heure Fin',
                'Durée (minutes)',
                'Nom Apprenant',
                'Email Apprenant',
                'Téléphone Apprenant',
                'Permis',
                'Moniteur',
                'Lieu de RDV',
                'Notes & Objectifs',
            ], ';');

            foreach ($lessons as $lesson) {
                fputcsv($handle, [
                    $lesson->id,
                    $lesson->title,
                    $lesson->formatted_type,
                    $lesson->status_label,
                    $lesson->starts_at?->format('d/m/Y') ?? '',
                    $lesson->starts_at?->format('H:i') ?? '',
                    $lesson->ends_at?->format('H:i') ?? '',
                    $lesson->starts_at && $lesson->ends_at ? $lesson->starts_at->diffInMinutes($lesson->ends_at) : '',
                    $lesson->student?->name ?? 'Cours collectif',
                    $lesson->student?->email ?? '',
                    $lesson->student?->telephone ?? '',
                    $lesson->student?->categorie_permis ?? 'B',
                    $lesson->instructor?->name ?? 'Non assigné',
                    $lesson->pickup_location ?? '',
                    $lesson->notes ?? '',
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function downloadSheet(Request $request, Team $currentTeam, Lesson $lesson)
    {
        $this->authorizeLesson($request->user(), $lesson);

        if ($request->query('format') === 'pdf' || ! $request->has('preview')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('moniteurs.lessons.sheet_pdf', [
                'lesson' => $lesson->load(['student', 'instructor', 'team']),
                'currentTeam' => $currentTeam,
            ])->setPaper('a4', 'portrait');

            $filename = 'fiche_cours_' . $lesson->id . '_' . \Illuminate\Support\Str::slug($lesson->title) . '.pdf';

            return $pdf->download($filename);
        }

        return view('moniteurs.lessons.sheet', [
            'lesson' => $lesson->load(['student', 'instructor', 'team']),
            'currentTeam' => $currentTeam,
        ]);
    }

    private function authorizeLesson(User $user, Lesson $lesson): void
    {
        // Un moniteur ou administrateur de l'auto-école peut consulter et gérer les leçons de son équipe
        if ($user->role === UserRole::Administrateur) {
            return;
        }

        if ($user->currentTeam && $lesson->team_id !== $user->currentTeam->id) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à ce cours.');
        }
    }
}

