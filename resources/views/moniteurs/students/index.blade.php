@extends('moniteurs.layout', ['title' => 'Mes Apprenants'])

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
    .df-students-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    .df-student-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .df-student-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.05);
    }
    .df-student-top {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }
    .df-student-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 700;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .df-student-name {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }
    .df-student-email {
        font-size: 12px;
        color: #64748b;
    }
    .df-student-info {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px;
        font-size: 12.5px;
        margin-bottom: 16px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .df-info-item span {
        display: block;
        color: #64748b;
        font-size: 11px;
    }
    .df-info-item strong {
        color: #1f2937;
    }
    .df-student-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }
    .df-assign-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 24px;
    }
    .df-assign-form {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    .df-select {
        padding: 9px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        min-width: 250px;
        background: #fff;
    }
</style>

<div class="df-page-header">
    <div>
        <h1 class="df-page-title">Mes Apprenants Associés</h1>
        <p class="df-page-subtitle">Suivez l'évolution et les cours des élèves qui vous sont assignés.</p>
    </div>
</div>

@if ($allTeamLearners->isNotEmpty())
    <div class="df-assign-box">
        <strong style="font-size: 14px; color: #1e293b;">Associer un nouvel apprenant à mon suivi</strong>
        <form method="POST" action="{{ route('instructor.students.assign') }}" class="df-assign-form">
            @csrf
            <select name="student_id" class="df-select" required>
                <option value="">Sélectionner un élève de l'auto-école...</option>
                @foreach ($allTeamLearners as $learner)
                    <option value="{{ $learner->id }}">
                        {{ $learner->name }} ({{ $learner->email }}) - Permis {{ $learner->categorie_permis ?? 'B' }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="df-btn df-btn-primary df-btn-sm">
                + Associer cet apprenant
            </button>
        </form>
    </div>
@endif

<div class="df-students-grid">
    @forelse ($students as $student)
        <div class="df-student-card">
            <div>
                <div class="df-student-top">
                    <div class="df-student-avatar">
                        {{ strtoupper(substr($student->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="df-student-name">{{ $student->name }}</div>
                        <div class="df-student-email">{{ $student->email }}</div>
                    </div>
                </div>

                <div class="df-student-info">
                    <div class="df-info-item">
                        <span>Permis</span>
                        <strong>Catégorie {{ $student->categorie_permis ?? 'B' }}</strong>
                    </div>
                    <div class="df-info-item">
                        <span>Ville</span>
                        <strong>{{ $student->ville ?? 'Non renseignée' }}</strong>
                    </div>
                    <div class="df-info-item">
                        <span>Cours suivis</span>
                        <strong>{{ $student->total_lessons_count }} leçons</strong>
                    </div>
                    <div class="df-info-item">
                        <span>Cours terminés</span>
                        <strong style="color: #15803d;">{{ $student->completed_lessons_count }} validés</strong>
                    </div>
                </div>

                @if ($student->next_lesson)
                    <div style="font-size: 12px; color: #475569; margin-bottom: 12px; background: #eff6ff; padding: 8px 10px; border-radius: 6px;">
                        📅 Prochain cours : <strong>{{ $student->next_lesson->starts_at->translatedFormat('d M à H:i') }}</strong>
                    </div>
                @endif
            </div>

            <div class="df-student-actions">
                <a href="{{ route('instructor.students.show', $student) }}" class="df-btn df-btn-outline df-btn-sm">
                    Voir la fiche & historique
                </a>

                <a href="{{ route('instructor.lessons.create', ['student_id' => $student->id]) }}" class="df-btn df-btn-primary df-btn-sm" title="Programmer un cours avec cet élève">
                    + Cours
                </a>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 48px; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
            <svg width="48" height="48" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Aucun apprenant associé pour le moment</h3>
            <p style="font-size: 13px;">Associez des élèves à votre compte ou planifiez un premier cours avec eux.</p>
        </div>
    @endforelse
</div>
@endsection
