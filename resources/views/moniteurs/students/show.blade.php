@extends('moniteurs.layout', ['title' => 'Fiche Apprenant - ' . $student->name])

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
    .df-student-profile-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }
    .df-profile-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .df-profile-avatar {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 700;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .df-profile-name {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
    }
    .df-profile-email {
        font-size: 13px;
        color: #64748b;
    }
    .df-profile-stats {
        display: flex;
        gap: 24px;
    }
    .df-profile-stat-item {
        text-align: right;
    }
    .df-profile-stat-val {
        font-size: 20px;
        font-weight: 700;
        color: #2e7d32;
    }
    .df-profile-stat-lbl {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
    }
    .df-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .df-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }
    .df-table th {
        text-align: left;
        padding: 12px 14px;
        color: #64748b;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
    }
    .df-table td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
</style>

<div class="df-page-header">
    <a href="{{ route('instructor.students.index') }}" class="df-btn df-btn-outline df-btn-sm">
        ‹ Retour aux apprenants
    </a>

    <div style="display: flex; gap: 10px;">
        <a href="{{ route('instructor.lessons.create', ['student_id' => $student->id]) }}" class="df-btn df-btn-primary">
            + Planifier un cours pour cet élève
        </a>

        <form method="POST" action="{{ route('instructor.students.unassign', $student) }}" onsubmit="return confirm('Voulez-vous retirer l\'association avec cet apprenant ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="df-btn df-btn-danger df-btn-sm">
                Détacher de mon suivi
            </button>
        </form>
    </div>
</div>

<div class="df-student-profile-header">
    <div class="df-profile-left">
        <div class="df-profile-avatar">
            {{ strtoupper(substr($student->name, 0, 2)) }}
        </div>
        <div>
            <div class="df-profile-name">{{ $student->name }}</div>
            <div class="df-profile-email">{{ $student->email }} · {{ $student->telephone ?? 'Pas de téléphone' }}</div>
            <div style="margin-top: 6px; display: flex; gap: 8px;">
                <span class="df-badge df-badge-green">Permis {{ $student->categorie_permis ?? 'B' }}</span>
                <span class="df-badge df-badge-gray">{{ $student->ville ?? 'Cameroun' }}</span>
            </div>
        </div>
    </div>

    <div class="df-profile-stats">
        <div class="df-profile-stat-item">
            <div class="df-profile-stat-val">{{ $totalCompletedHours }} h</div>
            <div class="df-profile-stat-lbl">Heures effectuées</div>
        </div>
        <div class="df-profile-stat-item">
            <div class="df-profile-stat-val">{{ $lessons->count() }}</div>
            <div class="df-profile-stat-lbl">Total sessions</div>
        </div>
    </div>
</div>

<div class="df-table-card">
    <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">
        Historique des cours avec {{ $student->name }}
    </h3>

    <table class="df-table">
        <thead>
            <tr>
                <th>Date & Heure</th>
                <th>Intitulé / Type</th>
                <th>Lieu</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lessons as $lesson)
                <tr>
                    <td>
                        <strong>{{ $lesson->starts_at->translatedFormat('d M Y') }}</strong>
                        <div style="font-size: 12px; color: #64748b;">{{ $lesson->starts_at->format('H:i') }} - {{ $lesson->ends_at->format('H:i') }}</div>
                    </td>
                    <td>
                        <strong>{{ $lesson->title }}</strong>
                        <div style="font-size: 12px; color: #64748b;">{{ $lesson->formatted_type }}</div>
                    </td>
                    <td>{{ $lesson->pickup_location ?? 'Auto-école' }}</td>
                    <td>
                        <span class="df-badge df-badge-{{ $lesson->status_badge_class }}">
                            {{ $lesson->status_label }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('instructor.lessons.show', $lesson) }}" class="df-btn df-btn-outline df-btn-sm">
                            Voir
                        </a>
                        <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="df-btn df-btn-outline df-btn-sm">
                            Éditer
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">
                        Aucun cours enregistré pour le moment pour cet élève.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
