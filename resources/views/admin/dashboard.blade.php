<x-layouts::app :title="__('Administration')">
    <style>
        .drive-admin {
            --drive-bg: #f5f7f2;
            --drive-panel: #ffffff;
            --drive-panel-soft: #f8faf7;
            --drive-primary: #17342d;
            --drive-primary-strong: #285347;
            --drive-secondary: #e6a43a;
            --drive-muted: #527066;
            --drive-line: #d7e2db;
            --drive-shadow: 0 18px 40px rgba(23, 52, 45, 0.08);
        }

        .drive-admin {
            background: transparent;
            min-height: 100%;
        }

        .drive-admin__header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .drive-admin__header {
                flex-direction: row;
                align-items: end;
                justify-content: space-between;
            }
        }

        .drive-admin__eyebrow {
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-size: 0.72rem;
            font-weight: 800;
            color: var(--drive-secondary);
        }

        .drive-admin__badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            background: rgba(23, 52, 45, 0.08);
            color: var(--drive-primary);
            border: 1px solid rgba(23, 52, 45, 0.1);
            font-weight: 700;
            font-size: 0.75rem;
        }

        .drive-admin__stats {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .drive-admin__stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .drive-admin__stats {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .drive-stat-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 170px;
            padding: 1.2rem;
            border-radius: 1.25rem;
            background: var(--drive-panel);
            border: 1px solid var(--drive-line);
            box-shadow: var(--drive-shadow);
        }

        .drive-stat-card__top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .drive-stat-card__label {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--drive-muted);
        }

        .drive-stat-card__value {
            margin-top: 0.8rem;
            font-size: clamp(1.7rem, 2vw, 2.2rem);
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.06em;
            color: var(--drive-primary);
        }

        .drive-stat-card__note {
            margin-top: 0.55rem;
            color: var(--drive-muted);
            font-size: 0.82rem;
        }

        .drive-stat-card__icon {
            display: grid;
            place-items: center;
            width: 2.8rem;
            height: 2.8rem;
            border-radius: 0.85rem;
            background: rgba(230, 164, 58, 0.14);
            color: var(--drive-secondary);
            font-weight: 800;
        }

        .drive-admin__content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 1024px) {
            .drive-admin__content {
                grid-template-columns: minmax(0, 1.75fr) minmax(280px, 0.9fr);
            }
        }

        .drive-panel {
            border-radius: 1.4rem;
            background: var(--drive-panel);
            border: 1px solid var(--drive-line);
            box-shadow: var(--drive-shadow);
            overflow: hidden;
        }

        .drive-panel__body {
            padding: 1.3rem;
        }

        .drive-panel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.1rem 1.3rem;
            border-bottom: 1px solid var(--drive-line);
            background: var(--drive-panel-soft);
        }

        .drive-panel__title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--drive-primary);
        }

        .drive-panel__subtitle {
            margin: 0.2rem 0 0;
            color: var(--drive-muted);
            font-size: 0.8rem;
        }

        .drive-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
            background: rgba(39, 130, 94, 0.12);
            color: #0f7a5a;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .drive-chart {
            display: flex;
            align-items: end;
            gap: 0.5rem;
            height: 180px;
            padding-top: 0.75rem;
        }

        .drive-chart__bar {
            flex: 1;
            min-height: 28%;
            border-radius: 0.8rem 0.8rem 0 0;
            background: linear-gradient(180deg, var(--drive-secondary) 0%, var(--drive-primary) 100%);
            opacity: 0.9;
        }

        .drive-progress {
            width: 100%;
            height: 0.7rem;
            border-radius: 999px;
            background: rgba(23, 52, 45, 0.08);
            overflow: hidden;
            margin-top: 1rem;
        }

        .drive-progress > span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--drive-primary) 0%, var(--drive-primary-strong) 100%);
        }

        .drive-table th,
        .drive-table td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid var(--drive-line);
        }

        .drive-table thead th {
            background: rgba(23, 52, 45, 0.03);
            color: var(--drive-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 0.7rem;
            font-weight: 800;
        }

        .drive-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .drive-table__status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.42rem 0.7rem;
            border-radius: 999px;
            background: rgba(39, 130, 94, 0.12);
            color: #0d7a5d;
            font-size: 0.72rem;
            font-weight: 700;
        }
    </style>

    <div class="drive-admin flex w-full flex-1 flex-col gap-6">
        <div class="drive-admin__header">
            <div>
                <div class="drive-admin__eyebrow">Administration SaaS</div>
                <h1 class="mt-2 text-3xl font-black tracking-[-0.06em] text-[#17342d]">Bonjour, Administrateur</h1>
                <p class="mt-1 text-sm text-[#527066]">Voici l’état actuel de votre plateforme.</p>
            </div>
            <div class="flex items-center gap-2">
                @if (!empty($preview))
                    <div class="drive-admin__badge bg-amber-100 text-amber-900 border-amber-300">Mode prévisualisation</div>
                @endif
                <div class="drive-admin__badge">Accès administrateur</div>
            </div>
        </div>


        <div class="drive-admin__stats" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
            @foreach ([
                ['label' => 'Auto-écoles', 'value' => $autoEcolesCount, 'icon' => '⌂', 'note' => 'Établissements partenaires'],
                ['label' => 'Utilisateurs', 'value' => $usersCount ?? 0, 'icon' => '👥', 'note' => ($learnersCount ?? 0).' apprenants inscrits'],
                ['label' => 'Total Demandes', 'value' => $totalRequestsCount ?? 0, 'icon' => '📬', 'note' => 'Candidatures reçues'],
                ['label' => 'Demandes en attente', 'value' => $pendingRequestsCount ?? 0, 'icon' => '⏳', 'note' => 'À valider par les écoles'],
                ['label' => 'Demandes validées', 'value' => $validatedRequestsCount ?? 0, 'icon' => '✓', 'note' => 'Élèves intégrés'],
                ['label' => 'Comptes vérifiés', 'value' => $verifiedUsersCount, 'icon' => '🛡️', 'note' => ($usersCount ? round($verifiedUsersCount / $usersCount * 100) : 0) . ' % de la base'],
            ] as $stat)

                <div class="drive-stat-card">
                    <div class="drive-stat-card__top">
                        <div>
                            <div class="drive-stat-card__label">{{ $stat['label'] }}</div>
                            <div class="drive-stat-card__value">{{ number_format($stat['value'], 0, ',', ' ') }}</div>
                        </div>
                        <div class="drive-stat-card__icon">{{ $stat['icon'] }}</div>
                    </div>
                    <div class="drive-stat-card__note">{{ $stat['note'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="drive-admin__content">
            <div class="drive-panel">
                <div class="drive-panel__header">
                    <div>
                        <h2 class="drive-panel__title">Flux des demandes d'inscription</h2>
                        <p class="drive-panel__subtitle">Répartition globale des inscriptions</p>
                    </div>
                    <span class="drive-badge">Temps réel</span>
                </div>
                <div class="drive-panel__body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-top: 8px;">
                        <div style="padding: 16px; border-radius: 12px; background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <span style="font-size: 11.5px; text-transform: uppercase; color: #15803d; font-weight: 700;">Validées</span>
                            <strong style="display: block; font-size: 24px; color: #166534; margin-top: 4px;">{{ $validatedRequestsCount }}</strong>
                            <span style="font-size: 12px; color: #15803d;">{{ $totalRequestsCount ? round(($validatedRequestsCount / $totalRequestsCount) * 100) : 0 }} % du total</span>
                        </div>

                        <div style="padding: 16px; border-radius: 12px; background: #fffbeb; border: 1px solid #fef3c7;">
                            <span style="font-size: 11.5px; text-transform: uppercase; color: #b45309; font-weight: 700;">En attente</span>
                            <strong style="display: block; font-size: 24px; color: #92400e; margin-top: 4px;">{{ $pendingRequestsCount }}</strong>
                            <span style="font-size: 12px; color: #b45309;">{{ $totalRequestsCount ? round(($pendingRequestsCount / $totalRequestsCount) * 100) : 0 }} % du total</span>
                        </div>
                    </div>

                    <div style="margin-top: 24px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12.5px; margin-bottom: 6px; color: #527066; font-weight: 600;">
                            <span>Taux de conversion des demandes</span>
                            <span>{{ $totalRequestsCount ? round(($validatedRequestsCount / $totalRequestsCount) * 100) : 0 }}%</span>
                        </div>
                        <div class="drive-progress" style="margin-top: 0;">
                            <span style="width: {{ $totalRequestsCount ? round(($validatedRequestsCount / $totalRequestsCount) * 100) : 0 }}%"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="drive-panel">
                <div class="drive-panel__header">
                    <div>
                        <h2 class="drive-panel__title">Dernières candidatures</h2>
                        <p class="drive-panel__subtitle">Demandes récentes reçues</p>
                    </div>
                </div>
                <div class="drive-panel__body" style="padding: 0.8rem 1.2rem;">
                    @forelse ($recentRequests ?? [] as $req)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #edf2f7;">
                            <div>
                                <strong style="font-size: 13.5px; color: #17342d; display: block;">{{ $req->name }}</strong>
                                <span style="font-size: 11px; color: #64748b;">
                                    {{ $req->team?->name ?? 'Auto-école' }} • {{ $req->created_at?->diffForHumans() }}
                                </span>
                            </div>
                            <span class="drive-badge" style="background: {{ $req->status === 'validated' ? 'rgba(39, 130, 94, 0.12)' : 'rgba(230, 164, 58, 0.14)' }}; color: {{ $req->status === 'validated' ? '#0d7a5d' : '#b45309' }};">
                                {{ $req->status === 'validated' ? 'Validée' : 'En attente' }}
                            </span>
                        </div>
                    @empty
                        <p style="font-size: 12.5px; color: #64748b; padding: 16px 0; text-align: center;">Aucune demande enregistrée.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="drive-panel" id="auto-ecoles">
            <div class="drive-panel__header">
                <div>
                    <h2 class="drive-panel__title">Statistiques des demandes par Auto-école</h2>
                    <p class="drive-panel__subtitle">Vue détaillée du volume de demandes et membres par auto-école</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="drive-table w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Nom de l'auto-école</th>
                            <th class="px-4 py-3">Total Demandes</th>
                            <th class="px-4 py-3">En attente</th>
                            <th class="px-4 py-3">Validées</th>
                            <th class="px-4 py-3">Membres actuels</th>
                            <th class="px-4 py-3">Inscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teams as $team)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-[#17342d]">{{ $team->name }}</td>
                                <td class="px-4 py-3 font-bold text-[#17342d]">
                                    {{ $team->total_requests_count ?? 0 }}
                                </td>
                                <td class="px-4 py-3 text-amber-700 font-semibold">
                                    {{ $team->pending_requests_count ?? 0 }}
                                </td>
                                <td class="px-4 py-3 text-emerald-700 font-semibold">
                                    {{ $team->validated_requests_count ?? 0 }}
                                </td>
                                <td class="px-4 py-3 text-[#527066]">{{ $team->members_count }} membres</td>
                                <td class="px-4 py-3 text-[#527066]">{{ $team->created_at?->translatedFormat('d M Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-[#527066]">Aucune auto-école enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts::app>