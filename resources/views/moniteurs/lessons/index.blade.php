@extends('moniteurs.layout', ['title' => 'Gestion des Cours'])

@section('content')
<style>
    .df-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .df-page-title {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
    }
    .df-page-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }
    .df-filters-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }
    .df-filter-input {
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        background: #fff;
        color: #1e293b;
    }
    .df-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .df-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }
    .df-table th {
        text-align: left;
        padding: 14px 18px;
        color: #64748b;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .df-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        vertical-align: middle;
    }
    .df-table tr:hover {
        background-color: #fafbfc;
    }
    .df-pagination {
        padding: 16px 20px;
        background: #fff;
        border-top: 1px solid #f1f5f9;
    }
</style>

<div class="df-page-header">
    <div>
        <h1 class="df-page-title">Gestion des Cours</h1>
        <p class="df-page-subtitle">Consultez, planifiez, modifiez et suivez tous vos cours dispensés.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('instructor.lessons.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="df-btn df-btn-outline" title="Télécharger la liste complète des cours au format PDF">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Télécharger les cours (PDF)
        </a>
        <a href="{{ route('instructor.lessons.export', request()->query()) }}" class="df-btn df-btn-outline" title="Télécharger la liste complète des cours au format CSV / Excel">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export (CSV)
        </a>
        <a href="{{ route('instructor.lessons.create') }}" class="df-btn df-btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau cours
        </a>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('instructor.lessons.index') }}" class="df-filters-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par titre, élève..." class="df-filter-input" style="flex: 1; min-width: 200px;">

    <select name="type" class="df-filter-input">
        <option value="">Tous les types</option>
        <option value="conduite" @selected(request('type') === 'conduite')>Conduite</option>
        <option value="code" @selected(request('type') === 'code')>Code</option>
        <option value="examen_blanc" @selected(request('type') === 'examen_blanc')>Examen blanc</option>
        <option value="evaluation" @selected(request('type') === 'evaluation')>Évaluation</option>
    </select>

    <select name="status" class="df-filter-input">
        <option value="">Tous les statuts</option>
        <option value="planifie" @selected(request('status') === 'planifie')>Planifié</option>
        <option value="en_cours" @selected(request('status') === 'en_cours')>En cours</option>
        <option value="termine" @selected(request('status') === 'termine')>Terminé</option>
        <option value="annule" @selected(request('status') === 'annule')>Annulé</option>
    </select>

    @if (isset($instructors) && $instructors->isNotEmpty())
        <select name="instructor_id" class="df-filter-input">
            <option value="">Tous les moniteurs</option>
            @foreach ($instructors as $inst)
                <option value="{{ $inst->id }}" @selected(request('instructor_id') == $inst->id)>{{ $inst->name }}</option>
            @endforeach
        </select>
    @endif

    @if ($students->isNotEmpty())
        <select name="student_id" class="df-filter-input">
            <option value="">Tous les élèves</option>
            @foreach ($students as $st)
                <option value="{{ $st->id }}" @selected(request('student_id') == $st->id)>{{ $st->name }}</option>
            @endforeach
        </select>
    @endif

    <button type="submit" class="df-btn df-btn-outline df-btn-sm">
        Filtrer
    </button>

    @if (request()->hasAny(['search', 'type', 'status', 'student_id', 'instructor_id']))
        <a href="{{ route('instructor.lessons.index') }}" class="df-btn df-btn-outline df-btn-sm" style="color: #64748b;">
            Réinitialiser
        </a>
    @endif
</form>

<!-- Table -->
<div class="df-table-card">
    <table class="df-table">
        <thead>
            <tr>
                <th>Cours</th>
                <th>Moniteur</th>
                <th>Apprenant</th>
                <th>Date & Heure</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lessons as $lesson)
                <tr>
                    <td>
                        <strong style="color: #1e293b;">{{ $lesson->title }}</strong>
                        @if ($lesson->pickup_location)
                            <div style="font-size: 12px; color: #64748b;">📍 {{ $lesson->pickup_location }}</div>
                        @endif
                    </td>
                    <td>
                        <strong style="color: #334155;">{{ $lesson->instructor?->name ?? 'Non assigné' }}</strong>
                    </td>

                    <td>
                        @if ($lesson->student)
                            <a href="{{ route('instructor.students.show', $lesson->student) }}" style="color: #2e7d32; font-weight: 600; text-decoration: none;">
                                {{ $lesson->student->name }}
                            </a>
                            <div style="font-size: 11.5px; color: #64748b;">Permis {{ $lesson->student->categorie_permis ?? 'B' }}</div>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Sans élève</span>
                        @endif
                    </td>
                    <td>
                        <div><strong>{{ $lesson->starts_at->translatedFormat('d M Y') }}</strong></div>
                        <div style="font-size: 12px; color: #64748b;">{{ $lesson->starts_at->format('H:i') }} - {{ $lesson->ends_at->format('H:i') }} ({{ $lesson->starts_at->diffInMinutes($lesson->ends_at) }} min)</div>
                    </td>
                    <td>
                        <span style="font-size: 12.5px;">{{ $lesson->formatted_type }}</span>
                    </td>
                    <td>
                        <span class="df-badge df-badge-{{ $lesson->status_badge_class }}">
                            {{ $lesson->status_label }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <a href="{{ route('instructor.lessons.show', $lesson) }}" class="df-btn df-btn-outline df-btn-sm" title="Consulter">
                                Voir
                            </a>
                            <a href="{{ route('instructor.lessons.download', $lesson) }}" target="_blank" class="df-btn df-btn-outline df-btn-sm" title="Télécharger la fiche de séance / émargement">
                                📄 Fiche
                            </a>
                            <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="df-btn df-btn-outline df-btn-sm" title="Modifier">
                                Éditer
                            </a>
                            <form method="POST" action="{{ route('instructor.lessons.destroy', $lesson) }}" onsubmit="return confirm('Confirmer la suppression de ce cours ?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="df-btn df-btn-danger df-btn-sm" title="Supprimer">
                                    Suppr.
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 48px;">
                        <svg width="40" height="40" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <p style="font-weight: 600; color: #1e293b;">Aucun cours trouvé</p>
                        <p style="font-size: 13px; margin-top: 4px;">Modifiez vos critères de recherche ou planifiez un nouveau cours.</p>
                        <a href="{{ route('instructor.lessons.create') }}" class="df-btn df-btn-primary df-btn-sm" style="margin-top: 14px;">
                            + Nouveau cours
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($lessons->hasPages())
        <div class="df-pagination">
            {{ $lessons->links() }}
        </div>
    @endif
</div>
@endsection
