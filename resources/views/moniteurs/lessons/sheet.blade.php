<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Séance - {{ $lesson->title }} - {{ $lesson->student?->name ?? 'Collectif' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 13.5px;
            line-height: 1.5;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 22px;
            font-weight: 800;
            color: #2e7d32;
            letter-spacing: -0.5px;
        }
        .doc-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 4px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .section-box {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
            background: #f8fafc;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            color: #64748b;
            font-size: 12px;
            display: block;
        }
        .info-value {
            font-weight: 600;
            color: #0f172a;
            font-size: 14px;
        }
        .signature-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        .signature-card {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 16px;
            height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #2e7d32;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }
        @media print {
            .no-print-bar {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>Fiche de séance prête pour impression / téléchargement</strong>
            <span style="font-size: 12px; opacity: 0.8; margin-left: 8px;">(Enregistrez en PDF via la boîte de dialogue)</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">Imprimer / Enregistrer en PDF</button>
            <button onclick="window.close()" style="background: transparent; border: 1px solid #64748b; color: #fff; padding: 7px 14px; border-radius: 6px; cursor: pointer; margin-left: 8px;">Fermer</button>
        </div>
    </div>

    <div class="header">
        <div>
            <div class="school-name">{{ $currentTeam->name }}</div>
            <div class="doc-title">Fiche de Formation & Émargement</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Document officiel de suivi pédagogique</div>
        </div>
        <div style="text-align: right;">
            <span class="badge">{{ $lesson->formatted_type }}</span>
            <div style="font-size: 12px; color: #64748b; margin-top: 6px;">Réf. Cours #{{ $lesson->id }}</div>
        </div>
    </div>

    <div class="section-box">
        <div class="section-title">Informations de la Séance</div>
        <div class="grid-2">
            <div class="info-row">
                <span class="info-label">Intitulé du cours :</span>
                <span class="info-value">{{ $lesson->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Statut :</span>
                <span class="info-value">{{ $lesson->status_label }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date :</span>
                <span class="info-value">{{ $lesson->starts_at->translatedFormat('l d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Horaires & Durée :</span>
                <span class="info-value">{{ $lesson->starts_at->format('H:i') }} - {{ $lesson->ends_at->format('H:i') }} ({{ $lesson->starts_at->diffInMinutes($lesson->ends_at) }} min)</span>
            </div>
            <div class="info-row">
                <span class="info-label">Lieu de prise en charge :</span>
                <span class="info-value">{{ $lesson->pickup_location ?? 'Agence Auto-école' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Moniteur responsable :</span>
                <span class="info-value">{{ $lesson->instructor?->name ?? 'Non assigné' }}</span>
            </div>
        </div>
    </div>

    <div class="section-box">
        <div class="section-title">Apprenant(e)</div>
        <div class="grid-2">
            <div class="info-row">
                <span class="info-label">Nom et prénom :</span>
                <span class="info-value">{{ $lesson->student?->name ?? 'Cours collectif / Sans élève attribué' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Catégorie de Permis :</span>
                <span class="info-value">Permis {{ $lesson->student?->categorie_permis ?? 'B' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email :</span>
                <span class="info-value">{{ $lesson->student?->email ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Téléphone :</span>
                <span class="info-value">{{ $lesson->student?->telephone ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="section-box">
        <div class="section-title">Objectifs Pédagogiques & Bilan de Séance</div>
        <div style="min-height: 70px; font-size: 13px; color: #334155;">
            {{ $lesson->notes ?: 'Aucune consigne spécifique pré-remplie. Évaluation continue des compétences (démarrage, arrêt, trajectoires, giratoires, respect des priorités).' }}
        </div>
    </div>

    <div class="signature-box">
        <div class="signature-card">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b;">Signature de l'Apprenant :</span>
            <span style="font-size: 11px; color: #94a3b8;">Émargement début / fin de séance</span>
        </div>
        <div class="signature-card">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b;">Visa & Signature du Moniteur :</span>
            <span style="font-size: 11px; color: #94a3b8;">Validation de l'heure effectuée</span>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 12px;">
        Document généré automatiquement via DriveFlow le {{ now()->translatedFormat('d F Y à H:i') }} • {{ $currentTeam->name }}
    </div>

</body>
</html>
