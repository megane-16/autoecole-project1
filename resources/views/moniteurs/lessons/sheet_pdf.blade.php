<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche de Séance - {{ $lesson->title }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header table {
            width: 100%;
        }
        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: #2e7d32;
        }
        .doc-title {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 3px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .section-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 14px;
            background: #f8fafc;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 4px;
            vertical-align: top;
        }
        .info-label {
            color: #64748b;
            font-size: 11px;
            display: block;
        }
        .info-value {
            font-weight: bold;
            color: #0f172a;
            font-size: 12px;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: separate;
            border-spacing: 15px 0;
        }
        .signature-card {
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 12px;
            height: 90px;
            background: #fff;
            vertical-align: top;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="school-name">{{ $currentTeam->name }}</div>
                    <div class="doc-title">Fiche de Formation & Émargement de Séance</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 3px;">Document officiel de suivi pédagogique</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <span class="badge">{{ $lesson->formatted_type }}</span>
                    <div style="font-size: 11px; color: #64748b; margin-top: 6px;">Réf. Cours #{{ $lesson->id }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-box">
        <div class="section-title">Informations de la Séance</div>
        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <span class="info-label">Intitulé du cours :</span>
                    <span class="info-value">{{ $lesson->title }}</span>
                </td>
                <td style="width: 50%;">
                    <span class="info-label">Statut :</span>
                    <span class="info-value">{{ $lesson->status_label }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Date de la séance :</span>
                    <span class="info-value">{{ $lesson->starts_at->translatedFormat('l d F Y') }}</span>
                </td>
                <td>
                    <span class="info-label">Horaires & Durée :</span>
                    <span class="info-value">{{ $lesson->starts_at->format('H:i') }} - {{ $lesson->ends_at->format('H:i') }} ({{ $lesson->starts_at->diffInMinutes($lesson->ends_at) }} min)</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Lieu de prise en charge :</span>
                    <span class="info-value">{{ $lesson->pickup_location ?? 'Agence Auto-école' }}</span>
                </td>
                <td>
                    <span class="info-label">Moniteur responsable :</span>
                    <span class="info-value">{{ $lesson->instructor?->name ?? 'Non assigné' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-box">
        <div class="section-title">Apprenant(e) Assigné(e)</div>
        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <span class="info-label">Nom et prénom :</span>
                    <span class="info-value">{{ $lesson->student?->name ?? 'Cours collectif / Sans élève attribué' }}</span>
                </td>
                <td style="width: 50%;">
                    <span class="info-label">Catégorie de Permis :</span>
                    <span class="info-value">Permis {{ $lesson->student?->categorie_permis ?? 'B' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Email :</span>
                    <span class="info-value">{{ $lesson->student?->email ?? '—' }}</span>
                </td>
                <td>
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">{{ $lesson->student?->telephone ?? '—' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-box">
        <div class="section-title">Objectifs Pédagogiques & Bilan de Séance</div>
        <div style="min-height: 50px; font-size: 11.5px; color: #334155;">
            {{ $lesson->notes ?: 'Aucune consigne spécifique pré-remplie. Évaluation continue des compétences (démarrage, arrêt, trajectoires, giratoires, respect des priorités).' }}
        </div>
    </div>

    <table class="signature-table">
        <tr>
            <td class="signature-card" style="width: 50%;">
                <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 5px;">Signature de l'Apprenant :</div>
                <div style="font-size: 10px; color: #94a3b8;">Émargement début / fin de séance</div>
            </td>
            <td class="signature-card" style="width: 50%;">
                <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 5px;">Visa & Signature du Moniteur :</div>
                <div style="font-size: 10px; color: #94a3b8;">Validation de l'heure effectuée</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document généré automatiquement via DriveFlow le {{ now()->translatedFormat('d F Y à H:i') }} • {{ $currentTeam->name }}
    </div>

</body>
</html>
