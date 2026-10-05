<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste des Cours - {{ $currentTeam->name }}</title>
    <style>
        @page {
            margin: 12mm 15mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            background: #fff;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .school-title {
            font-size: 20px;
            font-weight: bold;
            color: #2e7d32;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .meta-info {
            font-size: 10px;
            color: #64748b;
            text-align: right;
        }
        .lessons-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .lessons-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            font-size: 10.5px;
            text-transform: uppercase;
        }
        .lessons-table td {
            padding: 7px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .lessons-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9.5px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-planifie { background: #e0f2fe; color: #0369a1; }
        .badge-en_cours { background: #fef3c7; color: #b45309; }
        .badge-termine { background: #dcfce7; color: #15803d; }
        .badge-annule { background: #fee2e2; color: #b91c1c; }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="school-title">{{ $currentTeam->name }}</div>
                <div class="report-title">Planning & Registre des Cours dispensés</div>
            </td>
            <td class="meta-info">
                <div>Document généré le : <strong>{{ now()->translatedFormat('d/m/Y à H:i') }}</strong></div>
                <div>Total : <strong>{{ count($lessons) }} cours</strong></div>
            </td>
        </tr>
    </table>

    <table class="lessons-table">
        <thead>
            <tr>
                <th style="width: 22%;">Intitulé du Cours</th>
                <th style="width: 14%;">Type</th>
                <th style="width: 12%;">Date & Heure</th>
                <th style="width: 8%;">Durée</th>
                <th style="width: 16%;">Moniteur</th>
                <th style="width: 16%;">Apprenant(e)</th>
                <th style="width: 12%;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lessons as $lesson)
                <tr>
                    <td>
                        <strong>{{ $lesson->title }}</strong>
                        @if ($lesson->pickup_location)
                            <div style="font-size: 9px; color: #64748b;">📍 {{ $lesson->pickup_location }}</div>
                        @endif
                    </td>
                    <td>{{ $lesson->formatted_type }}</td>
                    <td>
                        <div><strong>{{ $lesson->starts_at?->format('d/m/Y') }}</strong></div>
                        <div style="font-size: 9.5px; color: #64748b;">{{ $lesson->starts_at?->format('H:i') }} - {{ $lesson->ends_at?->format('H:i') }}</div>
                    </td>
                    <td>{{ $lesson->starts_at && $lesson->ends_at ? $lesson->starts_at->diffInMinutes($lesson->ends_at).' min' : '—' }}</td>
                    <td><strong>{{ $lesson->instructor?->name ?? 'Non assigné' }}</strong></td>
                    <td>
                        @if ($lesson->student)
                            <strong>{{ $lesson->student->name }}</strong>
                            <div style="font-size: 9px; color: #64748b;">Permis {{ $lesson->student->categorie_permis ?? 'B' }}</div>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Collectif</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $lesson->status }}">
                            {{ $lesson->status_label }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">
                        Aucun cours trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Document officiel édité depuis l'Espace Moniteur DriveFlow • {{ $currentTeam->name }} • Page 1/1
    </div>

</body>
</html>
