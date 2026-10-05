@extends('moniteurs.layout', ['title' => $lesson->title])

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
    .df-lesson-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }
    .df-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #f1f5f9;
    }
    .df-detail-item label {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
    }
    .df-detail-item strong {
        font-size: 15px;
        color: #1e293b;
    }
    .df-quick-status-form {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 24px;
        flex-wrap: wrap;
    }
</style>

<div class="df-page-header">
    <a href="{{ route('instructor.lessons.index') }}" class="df-btn df-btn-outline df-btn-sm">
        ‹ Retour aux cours
    </a>

    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('instructor.lessons.download', $lesson) }}" target="_blank" class="df-btn df-btn-primary" title="Télécharger la fiche de séance pour émargement">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Télécharger la fiche (PDF / Imprimer)
        </a>
        <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="df-btn df-btn-outline">
            Modifier ce cours
        </a>
        <form method="POST" action="{{ route('instructor.lessons.destroy', $lesson) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="df-btn df-btn-danger">
                Supprimer
            </button>
        </form>
    </div>
</div>

<div class="df-lesson-card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
        <div>
            <span class="df-badge df-badge-{{ $lesson->status_badge_class }}" style="margin-bottom: 8px;">
                {{ $lesson->status_label }}
            </span>
            <h1 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-top: 4px;">{{ $lesson->title }}</h1>
            <p style="font-size: 13.5px; color: #64748b; margin-top: 4px;">Type : {{ $lesson->formatted_type }}</p>
        </div>

        <div style="text-align: right;">
            <div style="font-size: 20px; font-weight: 700; color: #2e7d32;">
                {{ $lesson->starts_at->diffInMinutes($lesson->ends_at) }} min
            </div>
            <div style="font-size: 12px; color: #64748b;">Durée programmée</div>
        </div>
    </div>

    <!-- Quick Status Changer -->
    <form method="POST" action="{{ route('instructor.lessons.status', $lesson) }}" class="df-quick-status-form">
        @csrf
        @method('PATCH')
        <div style="display: flex; align-items: center; gap: 12px;">
            <strong style="font-size: 13.5px; color: #334155;">Changer rapidement le statut :</strong>
            <select name="status" class="df-btn df-btn-outline df-btn-sm" style="background: #fff; cursor: pointer;">
                <option value="planifie" @selected($lesson->status === 'planifie')>Planifié</option>
                <option value="en_cours" @selected($lesson->status === 'en_cours')>En cours</option>
                <option value="termine" @selected($lesson->status === 'termine')>Terminé / Effectué</option>
                <option value="annule" @selected($lesson->status === 'annule')>Annulé</option>
            </select>
        </div>
        <button type="submit" class="df-btn df-btn-primary df-btn-sm">
            Mettre à jour le statut
        </button>
    </form>

    <div class="df-detail-grid">
        <div class="df-detail-item">
            <label>Date</label>
            <strong>{{ $lesson->starts_at->translatedFormat('l d F Y') }}</strong>
        </div>

        <div class="df-detail-item">
            <label>Horaire</label>
            <strong>{{ $lesson->starts_at->format('H:i') }} - {{ $lesson->ends_at->format('H:i') }}</strong>
        </div>

        <div class="df-detail-item">
            <label>Apprenant</label>
            @if ($lesson->student)
                <a href="{{ route('instructor.students.show', $lesson->student) }}" style="color: #2e7d32; font-weight: 700; text-decoration: none;">
                    {{ $lesson->student->name }}
                </a>
                <div style="font-size: 12px; color: #64748b;">{{ $lesson->student->email }}</div>
            @else
                <span style="color: #94a3b8; font-style: italic;">Sans apprenant assigné</span>
            @endif
        </div>

        <div class="df-detail-item">
            <label>Lieu de rendez-vous</label>
            <strong>{{ $lesson->pickup_location ?? 'Non spécifié' }}</strong>
        </div>
    </div>

    @if ($lesson->media_url)
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
            <label style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; display: block; margin-bottom: 8px;">
                📄 Support pédagogique joint
            </label>
            @if (str_ends_with(strtolower($lesson->media_url), '.pdf'))
                <div style="background: #f8fafc; padding: 18px 24px; border-radius: 12px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 16px;">
                    <div style="background: #fee2e2; color: #dc2626; font-size: 28px; padding: 10px 14px; border-radius: 10px;">
                        PDF
                    </div>
                    <div>
                        <strong style="display: block; font-size: 14px; color: #1e293b;">Document de cours PDF</strong>
                        <span style="font-size: 12px; color: #64748b;">Fichier support téléchargeable et consultable</span>
                    </div>
                    <a href="{{ $lesson->media_url }}" target="_blank" class="df-btn df-btn-primary df-btn-sm" style="margin-left: 12px;">
                        Ouvrir le fichier PDF ↗
                    </a>
                </div>
            @else
                <div style="background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid #e2e8f0; display: inline-block;">
                    <a href="{{ $lesson->media_url }}" target="_blank">
                        <img src="{{ $lesson->media_url }}" alt="Support" style="max-width: 100%; max-height: 280px; border-radius: 8px;">
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if ($lesson->notes)
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
            <label style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; display: block; margin-bottom: 8px;">
                Notes et remarques
            </label>
            <div style="background: #f8fafc; padding: 14px 18px; border-radius: 8px; font-size: 14px; line-height: 1.5; color: #334155; white-space: pre-wrap;">{{ $lesson->notes }}</div>
        </div>
    @endif
</div>
@endsection
