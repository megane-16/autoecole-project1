<x-layouts::app :title="__('Dashboard')">
    <livewire:pages::teams.pending-invitations-modal />

    <style>
        .drive-dashboard {
            --ink: #17212b;
            --muted: #718096;
            --line: #e7edf2;
            --blue: #2f6fed;
            --mint: #1dbf9b;
            --gold: #f5b940;
            min-height: 100%;
            color: var(--ink);
            background: transparent;
            font-family: inherit;
        }

        .drive-dashboard *,
        .drive-dashboard *::before,
        .drive-dashboard *::after {
            box-sizing: border-box;
        }

        .drive-dashboard__inner {
            padding: 0;
        }

        .drive-dashboard__header,
        .drive-panel__header,
        .drive-stat__row,
        .drive-progress__labels {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .drive-dashboard__header {
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .drive-dashboard__eyebrow {
            margin: 0 0 8px;
            color: var(--blue);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .drive-dashboard h1 {
            margin: 0;
            font-size: clamp(1.7rem, 3vw, 2.45rem);
            letter-spacing: -.03em;
            line-height: 1.1;
        }

        .drive-dashboard__subtitle {
            margin: 9px 0 0;
            color: var(--muted);
            font-size: .95rem;
        }

        .drive-dashboard__date {
            padding: 10px 13px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--muted);
            font-size: .82rem;
            white-space: nowrap;
        }

        .drive-dashboard__date strong {
            color: var(--ink);
        }

        .drive-stats,
        .drive-dashboard__content,
        .drive-dashboard__footer {
            display: grid;
            gap: 20px;
        }

        .drive-stats {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .drive-dashboard__content,
        .drive-dashboard__footer {
            grid-template-columns: minmax(0, 1.25fr) minmax(290px, .7fr);
        }

        .drive-dashboard__footer {
            margin-top: 16px;
        }

        .drive-stat,
        .drive-panel {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 5px 18px rgb(31 49 65 / 4%);
        }

        .drive-stat {
            position: relative;
            overflow: hidden;
            padding: 15px 16px;
        }

        .drive-stat::after {
            position: absolute;
            top: -26px;
            right: -28px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: var(--stat-color);
            content: '';
            opacity: .1;
        }

        .drive-stat__label,
        .drive-event__meta,
        .drive-progress__labels span {
            color: var(--muted);
        }

        .drive-stat__label {
            font-size: .79rem;
            font-weight: 600;
        }

        .drive-stat__row {
            margin-top: 10px;
        }

        .drive-stat__value {
            font-size: 1.5rem;
            letter-spacing: -.04em;
        }

        .drive-stat__trend {
            color: var(--stat-color);
            font-size: .76rem;
            font-weight: 700;
        }

        .drive-panel {
            padding: 16px;
        }

        .drive-panel__header {
            margin-bottom: 14px;
        }

        .drive-panel__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .drive-link {
            color: var(--blue);
            font-size: .78rem;
            font-weight: 700;
            text-decoration: none;
        }

        .drive-event {
            display: grid;
            grid-template-columns: 54px 1fr auto;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f3f5;
        }

        .drive-event:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .drive-event__time {
            color: var(--muted);
            font-size: .78rem;
            line-height: 1.3;
        }

        .drive-event__time strong {
            display: block;
            color: var(--ink);
            font-size: .9rem;
        }

        .drive-event__name {
            margin: 0 0 4px;
            font-size: .86rem;
            font-weight: 700;
        }

        .drive-event__meta {
            margin: 0;
            font-size: .74rem;
        }

        .drive-tag {
            padding: 5px 9px;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .drive-tag--green { color: #087a62; background: #e5f8f3; }
        .drive-tag--blue { color: #2458bb; background: #e9f0ff; }
        .drive-tag--gold { color: #9a6911; background: #fff4d9; }

        .drive-progress {
            display: grid;
            gap: 17px;
        }

        .drive-progress__item {
            display: grid;
            gap: 8px;
        }

        .drive-progress__labels {
            font-size: .78rem;
        }

        .drive-progress__bar {
            height: 8px;
            overflow: hidden;
            border-radius: 99px;
            background: #edf1f4;
        }

        .drive-progress__fill {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--fill);
        }

        .drive-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .8rem;
        }

        .drive-table th {
            padding: 0 0 12px;
            color: var(--muted);
            font-size: .7rem;
            text-align: left;
            text-transform: uppercase;
        }

        .drive-table td {
            padding: 13px 0;
            border-top: 1px solid #f0f3f5;
        }

        .drive-student {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }

        .drive-avatar {
            display: grid;
            place-items: center;
            width: 31px;
            height: 31px;
            border-radius: 9px;
            color: #fff;
            background: var(--avatar);
            font-size: .7rem;
        }

        .drive-status {
            color: #087a62;
            font-size: .73rem;
            font-weight: 700;
        }

        .drive-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .drive-action {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 11px;
            border: 1px solid var(--line);
            border-radius: 11px;
            color: var(--ink);
            background: #fff;
            font-size: .76rem;
            font-weight: 700;
            text-decoration: none;
        }

        .drive-action:hover {
            border-color: var(--blue);
        }

        .drive-action__icon {
            display: grid;
            place-items: center;
            width: 23px;
            height: 23px;
            border-radius: 8px;
            color: var(--blue);
            background: #e9f0ff;
            font-size: .9rem;
        }

        .drive-notice {
            display: flex;
            gap: 11px;
            margin-top: 18px;
            padding: 13px;
            border-radius: 11px;
            color: #7b5d1b;
            background: #fff8e6;
            font-size: .78rem;
            line-height: 1.45;
        }

        .drive-notice strong {
            display: block;
            margin-bottom: 3px;
            color: #5f4711;
        }

        .drive-learner-form {
            margin-bottom: 18px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #f8fafc;
        }

        .drive-learner-form__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .drive-learner-form__field {
            display: grid;
            gap: 6px;
        }

        .drive-learner-form__field label {
            color: var(--muted);
            font-size: .74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .drive-learner-form__field input,
        .drive-learner-form__field select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--ink);
            font: inherit;
        }

        .drive-learner-form__actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 12px;
        }

        .drive-learner-form__submit {
            padding: 10px 15px;
            border: 0;
            border-radius: 10px;
            background: var(--blue);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .drive-stats,
            .drive-dashboard__content,
            .drive-dashboard__footer {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .drive-dashboard__content > .drive-panel:first-child,
            .drive-dashboard__footer > .drive-panel:first-child {
                grid-column: span 2;
            }
        }

        @media (max-width: 620px) {
            .drive-dashboard__inner { padding: 22px 17px 32px; }
            .drive-dashboard__header { display: block; }
            .drive-dashboard__date { display: inline-block; margin-top: 17px; }
            .drive-stats,
            .drive-dashboard__content,
            .drive-dashboard__footer { grid-template-columns: 1fr; }
            .drive-dashboard__content > .drive-panel:first-child,
            .drive-dashboard__footer > .drive-panel:first-child { grid-column: auto; }
            .drive-event { grid-template-columns: 49px 1fr; }
            .drive-tag { grid-column: 2; justify-self: start; }
        }
    </style>

    @php
        $team = $currentTeam ?? auth()->user()->currentTeam ?? auth()->user()->personalTeam();
        $learners = $learners ?? collect();
        $learnerCount = $learnerCount ?? $learners->count();
        $instructors = $instructors ?? collect();
        $todayLessons = $todayLessons ?? collect();
        $weekLessonsCount = $weekLessonsCount ?? 0;
        $completedLessonsCount = $completedLessonsCount ?? 0;
        $pendingRequestsCount = $pendingRequestsCount ?? 0;
        $examLessonsCount = $examLessonsCount ?? 0;
        $conduiteLessonsCount = $conduiteLessonsCount ?? 0;
        $codeLessonsCount = $codeLessonsCount ?? 0;
    @endphp

    <main class="drive-dashboard">
        <div class="drive-dashboard__inner">
            <header class="drive-dashboard__header">
                <div>
                    <p class="drive-dashboard__eyebrow">Espace auto-école • {{ $team?->name }}</p>
                    <h1>Bonjour, {{ auth()->user()->name }}.</h1>
                    <p class="drive-dashboard__subtitle">Voici l'état opérationnel de votre établissement en temps réel.</p>
                </div>
                <div class="drive-dashboard__date">
                    <strong>{{ now()->translatedFormat('D d M') }}</strong> · {{ now()->format('Y') }}
                </div>
            </header>

            <section class="drive-stats" aria-label="Résumé de l'activité">
                @foreach ([
                    ['label' => 'Apprenants inscrits', 'value' => $learnerCount, 'trend' => 'Total réseau', 'color' => 'var(--blue)'],
                    ['label' => 'Leçons cette semaine', 'value' => $weekLessonsCount, 'trend' => $todayLessons->count().' aujourd\'hui', 'color' => 'var(--mint)'],
                    ['label' => 'Demandes en attente', 'value' => $pendingRequestsCount, 'trend' => 'À valider', 'color' => 'var(--gold)'],
                    ['label' => 'Leçons terminées', 'value' => $completedLessonsCount, 'trend' => 'Heures dispensées', 'color' => '#ef6b73'],
                ] as $stat)
                    <article class="drive-stat" style="--stat-color: {{ $stat['color'] }}">
                        <span class="drive-stat__label">{{ $stat['label'] }}</span>
                        <div class="drive-stat__row">
                            <strong class="drive-stat__value">{{ $stat['value'] }}</strong>
                            <span class="drive-stat__trend">{{ $stat['trend'] }}</span>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="drive-dashboard__content">
                <article class="drive-panel" id="planning">
                    <div class="drive-panel__header">
                        <h2 class="drive-panel__title">Planning du jour ({{ $todayLessons->count() }} cours)</h2>
                        <a class="drive-link" href="{{ route('instructor.schedule.index', ['current_team' => $team?->slug]) }}">Voir le planning complet</a>
                    </div>
                    @forelse ($todayLessons as $lesson)
                        <div class="drive-event">
                            <div class="drive-event__time">
                                <strong>{{ $lesson->starts_at->format('H:i') }}</strong>
                                {{ $lesson->starts_at->diffInMinutes($lesson->ends_at) }} min
                            </div>
                            <div>
                                <p class="drive-event__name">{{ $lesson->title ?? $lesson->formatted_type }}</p>
                                <p class="drive-event__meta">
                                    Élève : <strong>{{ $lesson->student?->name ?? 'Non assigné' }}</strong> • Moniteur : <strong>{{ $lesson->instructor?->name ?? 'Non assigné' }}</strong>
                                </p>
                            </div>
                            <span class="drive-tag drive-tag--{{ $lesson->status === 'termine' ? 'green' : ($lesson->status === 'en_cours' ? 'blue' : 'gold') }}">
                                {{ $lesson->status_label }}
                            </span>
                        </div>
                    @empty
                        <div style="padding: 24px 0; text-align: center; color: var(--muted); font-size: .85rem;">
                            Aucune leçon programmée pour aujourd'hui. Utilisez le formulaire ci-dessous pour planifier un cours.
                        </div>
                    @endforelse

                    <!-- Formulaire de planification de cours direct -->
                    <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--line);">
                        <h3 style="font-size: .92rem; font-weight: 700; margin-bottom: 12px; color: var(--ink);">
                            + Planifier une nouvelle leçon pour un moniteur
                        </h3>
                        <form method="POST" action="{{ route('dashboard.lessons.store', ['current_team' => $team?->slug]) }}" class="drive-learner-form" style="margin-bottom: 0;">
                            @csrf
                            <div class="drive-learner-form__grid">
                                <div class="drive-learner-form__field">
                                    <label for="lesson-title">Titre / Objectif du cours</label>
                                    <input id="lesson-title" type="text" name="title" required placeholder="Ex. Conduite urbaine & créneaux">
                                </div>
                                <div class="drive-learner-form__field">
                                    <label for="lesson-instructor">Moniteur responsable</label>
                                    <select id="lesson-instructor" name="instructor_id" required>
                                        <option value="">Sélectionner un moniteur</option>
                                        @foreach ($instructors as $inst)
                                            <option value="{{ $inst->id }}">{{ $inst->name }} ({{ $inst->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="drive-learner-form__field">
                                    <label for="lesson-student">Apprenant</label>
                                    <select id="lesson-student" name="student_id">
                                        <option value="">Sélectionner un apprenant</option>
                                        @foreach ($learners as $st)
                                            <option value="{{ $st->id }}">{{ $st->name }} (Permis {{ $st->categorie_permis ?? 'B' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="drive-learner-form__field">
                                    <label for="lesson-type">Type de formation</label>
                                    <select id="lesson-type" name="type" required>
                                        <option value="conduite">Leçon de conduite</option>
                                        <option value="code">Cours de code</option>
                                        <option value="examen_blanc">Examen blanc</option>
                                        <option value="evaluation">Évaluation</option>
                                    </select>
                                </div>
                                <div class="drive-learner-form__field">
                                    <label for="lesson-start">Date & Heure de début</label>
                                    <input id="lesson-start" type="datetime-local" name="starts_at" required value="{{ now()->addHour()->format('Y-m-d\TH:00') }}">
                                </div>
                                <div class="drive-learner-form__field">
                                    <label for="lesson-end">Date & Heure de fin</label>
                                    <input id="lesson-end" type="datetime-local" name="ends_at" required value="{{ now()->addHours(2)->format('Y-m-d\TH:00') }}">
                                </div>
                                <div class="drive-learner-form__field">
                                    <label for="lesson-pickup">Lieu de prise en charge</label>
                                    <input id="lesson-pickup" type="text" name="pickup_location" placeholder="Ex. Devant l'agence ou Gare">
                                </div>
                                <div class="drive-learner-form__field">
                                    <label for="lesson-status">Statut initial</label>
                                    <select id="lesson-status" name="status" required>
                                        <option value="planifie">Planifié</option>
                                        <option value="en_cours">En cours</option>
                                    </select>
                                </div>
                            </div>
                            <div class="drive-learner-form__actions">
                                <button type="submit" class="drive-learner-form__submit" style="background: var(--mint);">
                                    Enregistrer & Transmettre au moniteur
                                </button>
                            </div>
                        </form>
                    </div>
                </article>

                <article class="drive-panel">
                    <div class="drive-panel__header">
                        <h2 class="drive-panel__title">Répartition des leçons</h2>
                        <a class="drive-link" href="{{ route('instructor.lessons.index', ['current_team' => $team?->slug]) }}">Voir tout</a>
                    </div>
                    <div class="drive-progress">
                        @php
                            $totalL = max(1, $conduiteLessonsCount + $codeLessonsCount + $examLessonsCount);
                        @endphp
                        <div class="drive-progress__item">
                            <div class="drive-progress__labels">
                                <strong>Leçons de conduite</strong>
                                <span>{{ $conduiteLessonsCount }} ({{ round(($conduiteLessonsCount / $totalL) * 100) }}%)</span>
                            </div>
                            <div class="drive-progress__bar">
                                <span class="drive-progress__fill" style="--fill:var(--blue); width:{{ round(($conduiteLessonsCount / $totalL) * 100) }}%"></span>
                            </div>
                        </div>

                        <div class="drive-progress__item">
                            <div class="drive-progress__labels">
                                <strong>Cours de code</strong>
                                <span>{{ $codeLessonsCount }} ({{ round(($codeLessonsCount / $totalL) * 100) }}%)</span>
                            </div>
                            <div class="drive-progress__bar">
                                <span class="drive-progress__fill" style="--fill:var(--mint); width:{{ round(($codeLessonsCount / $totalL) * 100) }}%"></span>
                            </div>
                        </div>

                        <div class="drive-progress__item">
                            <div class="drive-progress__labels">
                                <strong>Examens blancs</strong>
                                <span>{{ $examLessonsCount }} ({{ round(($examLessonsCount / $totalL) * 100) }}%)</span>
                            </div>
                            <div class="drive-progress__bar">
                                <span class="drive-progress__fill" style="--fill:var(--gold); width:{{ round(($examLessonsCount / $totalL) * 100) }}%"></span>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <section class="drive-dashboard__footer">
                <article class="drive-panel" id="eleves">
                    <div class="drive-panel__header">
                        <h2 class="drive-panel__title">Apprenants inscrits ({{ $learnerCount }})</h2>
                        <a class="drive-link" href="{{ route('instructor.students.index', ['current_team' => $team?->slug]) }}">Gérer les apprenants</a>
                    </div>

                    @if (session('success'))
                        <div class="drive-notice" style="margin-top: 0; margin-bottom: 14px; background: #e5f8f3; color: #087a62;">
                            <span>✓</span>
                            <div><strong>Succès :</strong> {{ session('success') }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dashboard.learners.store', ['current_team' => $team?->slug]) }}" class="drive-learner-form">
                        @csrf
                        <div class="drive-learner-form__grid">
                            <div class="drive-learner-form__field">
                                <label for="learner-name">Nom complet</label>
                                <input id="learner-name" type="text" name="name" value="{{ old('name') }}" required placeholder="Ex. Claire Martin">
                            </div>
                            <div class="drive-learner-form__field">
                                <label for="learner-email">Email</label>
                                <input id="learner-email" type="email" name="email" value="{{ old('email') }}" required placeholder="apprenant@example.com">
                            </div>
                            <div class="drive-learner-form__field">
                                <label for="learner-ville">Ville</label>
                                <input id="learner-ville" type="text" name="ville" value="{{ old('ville') }}" placeholder="Douala / Yaoundé">
                            </div>
                            <div class="drive-learner-form__field">
                                <label for="learner-permis">Catégorie Permis</label>
                                <select id="learner-permis" name="categorie_permis">
                                    <option value="A" @selected(old('categorie_permis') === 'A')>Permis A</option>
                                    <option value="B" @selected(old('categorie_permis', 'B') === 'B')>Permis B</option>
                                    <option value="C" @selected(old('categorie_permis') === 'C')>Permis C</option>
                                    <option value="D" @selected(old('categorie_permis') === 'D')>Permis D</option>
                                </select>
                            </div>
                            <div class="drive-learner-form__field" style="grid-column: 1 / -1;">
                                <label for="learner-password">Mot de passe</label>
                                <input id="learner-password" type="password" name="password" required placeholder="••••••••">
                            </div>
                            <div class="drive-learner-form__field" style="grid-column: 1 / -1;">
                                <label for="learner-password_confirmation">Confirmer le mot de passe</label>
                                <input id="learner-password_confirmation" type="password" name="password_confirmation" required placeholder="••••••••">
                            </div>
                        </div>

                        <div class="drive-learner-form__actions">
                            <button type="submit" class="drive-learner-form__submit">Ajouter l'apprenant</button>
                        </div>
                    </form>

                    <table class="drive-table">
                        <thead>
                            <tr>
                                <th>Apprenant</th>
                                <th>Formation</th>
                                <th>Date Inscription</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($learners as $student)
                                @php
                                    $initials = strtoupper(substr($student->name, 0, 1) . (str_contains($student->name, ' ') ? substr(strrchr($student->name, ' '), 1, 1) : ''));
                                    $color = ['#2f6fed', '#1dbf9b', '#d48546'][($loop->index + 1) % 3];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="drive-student">
                                            <span class="drive-avatar" style="--avatar:{{ $color }}">{{ $initials }}</span>
                                            <div>
                                                <div>{{ $student->name }}</div>
                                                <div style="font-size: 11px; color: var(--muted); font-weight: normal;">{{ $student->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->categorie_permis ? 'Permis '.$student->categorie_permis : 'Permis B' }}</td>
                                    <td>{{ $student->created_at?->translatedFormat('d M Y') ?? 'Récemment' }}</td>
                                    <td><span class="drive-status">Actif</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Aucun apprenant inscrit pour cette auto-école.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </article>

                <aside class="drive-panel">
                    <div class="drive-panel__header"><h2 class="drive-panel__title">Actions rapides</h2></div>
                    <div class="drive-actions">
                        <a class="drive-action" href="#planning"><span class="drive-action__icon">⌁</span>Planifier un cours</a>
                        <a class="drive-action" href="#eleves"><span class="drive-action__icon">+</span>Ajouter un apprenant</a>
                        <a class="drive-action" href="{{ route('demandes.index', ['current_team' => $team?->slug]) }}"><span class="drive-action__icon">📬</span>Voir les demandes</a>
                        <a class="drive-action" href="{{ route('instructor.lessons.index', ['current_team' => $team?->slug]) }}"><span class="drive-action__icon">🚗</span>Tous les cours</a>
                    </div>
                    @if ($pendingRequestsCount > 0)
                        <div class="drive-notice">
                            <span>!</span>
                            <div>
                                <strong>{{ $pendingRequestsCount }} demande(s) en attente</strong>
                                Des candidatures d'inscription attendent votre validation.
                            </div>
                        </div>
                    @endif
                </aside>
            </section>
        </div>
    </main>
</x-layouts::app>

