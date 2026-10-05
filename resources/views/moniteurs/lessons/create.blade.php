@extends('moniteurs.layout', ['title' => 'Nouveau Cours'])

@section('content')
<style>
    .df-form-container {
        max-width: 760px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .df-form-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .df-form-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
    }
    .df-form-subtitle {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }
    .df-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    .df-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .df-form-group.full {
        grid-column: 1 / -1;
    }
    .df-form-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }
    .df-form-control {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
        color: #1e293b;
        font-family: inherit;
        transition: border-color 0.2s;
    }
    .df-form-control:focus {
        border-color: #2e7d32;
        outline: none;
    }
    .df-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 28px;
        padding-top: 18px;
        border-top: 1px solid #f1f5f9;
    }
</style>

<div class="df-form-container">
    <div class="df-form-header">
        <h1 class="df-form-title">Créer et planifier un cours</h1>
        <p class="df-form-subtitle">Remplissez les informations pour programmer une nouvelle session.</p>
    </div>

    <form method="POST" action="{{ route('instructor.lessons.store', ['current_team' => $currentTeam->slug]) }}" enctype="multipart/form-data">
        @csrf

        <div class="df-form-grid">
            <div class="df-form-group full">
                <label for="title" class="df-form-label">Titre du cours *</label>
                <input type="text" id="title" name="title" value="{{ old('title', 'Leçon de conduite') }}" required class="df-form-control" placeholder="Ex. Leçon de conduite en agglomération">
            </div>

            <div class="df-form-group">
                <label for="student_id" class="df-form-label">Apprenant</label>
                <select id="student_id" name="student_id" class="df-form-control">
                    <option value="">-- Sans élève spécifique / Cours collectif --</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" @selected(old('student_id', $selectedStudentId) == $student->id)>
                            {{ $student->name }} (Permis {{ $student->categorie_permis ?? 'B' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="df-form-group">
                <label for="type" class="df-form-label">Type de formation *</label>
                <select id="type" name="type" required class="df-form-control">
                    <option value="conduite" @selected(old('type') === 'conduite')>Leçon de conduite</option>
                    <option value="code" @selected(old('type') === 'code')>Cours de code théorique</option>
                    <option value="evaluation" @selected(old('type') === 'evaluation')>Évaluation de départ</option>
                    <option value="examen_blanc" @selected(old('type') === 'examen_blanc')>Examen blanc</option>
                </select>
            </div>

            <div class="df-form-group">
                <label for="starts_at" class="df-form-label">Date & Heure de début *</label>
                <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', now()->addDay()->setHour(9)->setMinute(0)->format('Y-m-d\TH:i')) }}" required class="df-form-control">
            </div>

            <div class="df-form-group">
                <label for="ends_at" class="df-form-label">Date & Heure de fin *</label>
                <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', now()->addDay()->setHour(10)->setMinute(0)->format('Y-m-d\TH:i')) }}" required class="df-form-control">
            </div>

            <div class="df-form-group">
                <label for="status" class="df-form-label">Statut *</label>
                <select id="status" name="status" required class="df-form-control">
                    <option value="planifie" @selected(old('status') === 'planifie')>Planifié</option>
                    <option value="en_cours" @selected(old('status') === 'en_cours')>En cours</option>
                    <option value="termine" @selected(old('status') === 'termine')>Terminé</option>
                    <option value="annule" @selected(old('status') === 'annule')>Annulé</option>
                </select>
            </div>

            <div class="df-form-group">
                <label for="pickup_location" class="df-form-label">Lieu de rendez-vous / Départ</label>
                <input type="text" id="pickup_location" name="pickup_location" value="{{ old('pickup_location', 'Agence Auto-école') }}" class="df-form-control" placeholder="Ex. Agence Principale, Gare...">
            </div>

            <div class="df-form-group full">
                <label for="media" class="df-form-label">📄 Support de cours (Document PDF, Schéma ou Photo depuis la galerie)</label>
                <input type="file" id="media" name="media" accept=".pdf,image/*,application/pdf" class="df-form-control" style="background: #f8fafc; cursor: pointer;">
                <span style="font-size: 12px; color: #64748b;">Sélectionnez un support de cours au format <strong>PDF</strong> ou une image / schéma (jusqu'à <strong>200 Mo</strong>) accessible par l'apprenant.</span>
            </div>

            <div class="df-form-group full">
                <label for="notes" class="df-form-label">Objectifs & Notes pédagogiques</label>
                <textarea id="notes" name="notes" rows="4" class="df-form-control" placeholder="Objectifs de la séance (priorités, manœuvres, trajectoires...)">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="df-form-actions">
            <a href="{{ route('instructor.lessons.index') }}" class="df-btn df-btn-outline">
                Annuler
            </a>
            <button type="submit" class="df-btn df-btn-primary">
                Enregistrer le cours
            </button>
        </div>
    </form>
</div>
@endsection
