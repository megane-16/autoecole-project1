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
            background: var(--drive-bg);
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

    <div class="drive-admin flex w-full flex-1 flex-col gap-6 p-4 lg:p-8">
        <div class="drive-admin__header">
            <div>
                <div class="drive-admin__eyebrow">Espace administration</div>
                <h1 class="mt-2 text-3xl font-black tracking-[-0.06em] text-[#17342d]">Bonjour, Administrateur</h1>
                <p class="mt-1 text-sm text-[#527066]">Voici l’état actuel de votre plateforme.</p>
            </div>
            <div class="drive-admin__badge">Accès administrateur</div>
        </div>

        <div class="drive-admin__stats">
            @foreach ([
                ['label' => 'Auto-écoles', 'value' => $autoEcolesCount, 'icon' => '⌂', 'note' => 'Comptes professionnels'],
                ['label' => 'Utilisateurs', 'value' => $usersCount, 'icon' => '◉', 'note' => 'Comptes inscrits'],
                ['label' => 'Emails vérifiés', 'value' => $verifiedUsersCount, 'icon' => '✓', 'note' => ($usersCount ? round($verifiedUsersCount / $usersCount * 100) : 0) . ' % de la base'],
                ['label' => 'Administrateurs', 'value' => $administratorsCount, 'icon' => '★', 'note' => 'Accès plateforme'],
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
                        <h2 class="drive-panel__title">Croissance de la plateforme</h2>
                        <p class="drive-panel__subtitle">Activité des 30 derniers jours</p>
                    </div>
                    <span class="drive-badge">+18,4 %</span>
                </div>
                <div class="drive-panel__body">
                    <div class="drive-chart" aria-label="Graphique de croissance">
                        @foreach ([35, 48, 42, 65, 58, 78, 92, 72, 86, 100, 88, 96] as $height)
                            <div class="drive-chart__bar" style="height: {{ $height }}%; opacity: {{ 0.45 + $loop->index / 20 }}"></div>
                        @endforeach
                    </div>
                    <div class="mt-3 flex items-center justify-between text-[0.7rem] font-medium text-[#527066]">
                        <span>01 août</span>
                        <span>Aujourd’hui</span>
                    </div>
                </div>
            </div>

            <div class="drive-panel">
                <div class="drive-panel__header">
                    <div>
                        <h2 class="drive-panel__title">Vérification</h2>
                        <p class="drive-panel__subtitle">Progression des emails vérifiés</p>
                    </div>
                </div>
                <div class="drive-panel__body">
                    <div class="text-4xl font-black tracking-[-0.06em] text-[#17342d]">
                        {{ $usersCount ? round($verifiedUsersCount / $usersCount * 100) : 0 }}%
                    </div>
                    <div class="drive-progress">
                        <span style="width: {{ $usersCount ? round($verifiedUsersCount / $usersCount * 100) : 0 }}%"></span>
                    </div>
                    <p class="mt-3 text-sm text-[#527066]">
                        {{ $verifiedUsersCount }} comptes vérifiés sur {{ $usersCount }}
                    </p>
                </div>
            </div>
        </div>

        <div class="drive-panel">
            <div class="drive-panel__header">
                <div>
                    <h2 class="drive-panel__title">Auto-écoles récemment inscrites</h2>
                    <p class="drive-panel__subtitle">Les comptes professionnels à surveiller</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="drive-table w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Nom</th>
                            <th class="px-4 py-3">Membres</th>
                            <th class="px-4 py-3">Créée le</th>
                            <th class="px-4 py-3">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teams as $team)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-[#17342d]">{{ $team->name }}</td>
                                <td class="px-4 py-3 text-[#527066]">{{ $team->members_count }}</td>
                                <td class="px-4 py-3 text-[#527066]">{{ $team->created_at?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3"><span class="drive-table__status">Actif</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-[#527066]">Aucune auto-école inscrite.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts::app>