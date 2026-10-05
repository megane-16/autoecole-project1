@php
    $user = auth()->user();
    $roleVal = $user ? (($user->role instanceof \App\Enums\UserRole ? $user->role->value : $user->role)) : null;
    $isAdmin = ($roleVal === 'administrateur');
    $team = $user?->currentTeam ?? $user?->personalTeam();
    $dashboardUrl = $team ? route('dashboard', ['current_team' => $team->slug]) : route('home');

    // Counts
    $pendingRequestsCount = 0;
    $studentsCount = 0;
    $todayLessonsCount = 0;

    if ($team) {
        $pendingRequestsCount = \App\Models\RegistrationRequest::query()
            ->where('team_id', $team->id)
            ->where('status', 'pending')
            ->count();

        $studentsCount = \App\Models\User::query()
            ->where('role', 'apprenant')
            ->whereHas('teams', fn($q) => $q->where('teams.id', $team->id))
            ->count();

        $todayLessonsCount = \App\Models\Lesson::query()
            ->where('team_id', $team->id)
            ->whereDate('starts_at', \Illuminate\Support\Carbon::today())
            ->count();
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')

    <style>
        :root {
            --df-primary: #2e7d32;
            --df-primary-hover: #1b5e20;
            --df-primary-light: #e8f5e9;
            --df-bg: #f8fafc;
            --df-card-bg: #ffffff;
            --df-border: #e2e8f0;
            --df-text-main: #1e293b;
            --df-text-muted: #64748b;
            --df-success: #16a34a;
            --df-warning: #d97706;
            --df-danger: #dc2626;
            --df-info: #2563eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--df-bg);
            color: var(--df-text-main);
            min-height: 100vh;
            display: flex;
        }

        .df-layout-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .df-sidebar {
            width: 270px;
            min-height: 100vh;
            flex-shrink: 0;
            background-color: #ffffff;
            border-right: 1px solid var(--df-border);
            display: flex;
            flex-direction: column;
            padding: 22px 18px;
            box-sizing: border-box;
            font-family: inherit;
            transition: transform 0.25s ease;
        }

        .df-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .df-logo-badge {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: #ffffff;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(46, 125, 50, 0.2);
            text-decoration: none;
        }

        .df-brand-title {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        .df-role-tag {
            font-size: 11px;
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-block;
            margin-top: 3px;
        }

        .df-team-badge {
            margin: 16px 0 10px;
            padding: 10px 12px;
            border-radius: 9px;
            background-color: #f8fafc;
            border: 1px solid #edf2f7;
        }

        .df-team-label {
            display: block;
            font-size: 10.5px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .df-team-name {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 2px;
        }

        .df-nav {
            flex: 1;
            overflow-y: auto;
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .df-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 9px;
            text-decoration: none;
            color: #475569;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .df-link-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .df-link:hover {
            background-color: #f1f8e9;
            color: #2e7d32;
        }

        .df-link.active {
            background-color: #2e7d32;
            color: #ffffff;
            font-weight: 600;
        }

        .df-link.active .df-icon {
            color: #ffffff;
        }

        .df-link.active .df-counter {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .df-icon {
            width: 19px;
            height: 19px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .df-counter {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            background-color: #f1f5f9;
            color: #475569;
        }

        .df-counter-highlight {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .df-counter-warning {
            background-color: #fef3c7;
            color: #b45309;
        }

        .df-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 14px 10px 4px 10px;
            letter-spacing: 0.6px;
        }

        .df-footer {
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .df-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .df-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background-color: #e8f5e9;
            color: #2e7d32;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .df-details {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .df-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .df-email {
            font-size: 11px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .df-logout-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 7px;
            border-radius: 8px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s, color 0.2s;
        }

        .df-logout-btn:hover {
            background-color: #fee2e2;
            color: #dc2626;
        }

        /* Main Wrapper */
        .df-main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow-x: hidden;
        }

        /* Header bar */
        .df-header-bar {
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--df-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            box-sizing: border-box;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .df-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .df-header-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .df-header-subtitle {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }

        .df-header-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .df-header-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 10px;
            border-left: 1px solid #e8f5e9;
        }

        .df-header-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e8f5e9;
            color: #2e7d32;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .df-header-user {
            display: flex;
            flex-direction: column;
        }

        .df-header-name {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
        }

        .df-header-status {
            font-size: 11px;
            color: #2e7d32;
            font-weight: 500;
        }

        .df-mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #1e293b;
            padding: 4px;
        }

        /* Content Area */
        .df-content-area {
            flex: 1;
            padding: 24px 32px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        /* Flash Messages */
        .df-alert {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .df-alert-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .df-alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        @media (max-width: 850px) {
            .df-sidebar {
                position: fixed;
                z-index: 40;
                inset: 0 auto 0 0;
                transform: translateX(-100%);
                box-shadow: 4px 0 16px rgba(0,0,0,0.1);
            }

            .df-sidebar.is-open {
                transform: translateX(0);
            }

            .df-mobile-toggle {
                display: block;
            }

            .df-content-area {
                padding: 16px;
            }

            .df-header-bar {
                padding: 0 16px;
            }
        }
    </style>
</head>
<body>
    <div class="df-layout-container">
        <!-- Sidebar Navigation -->
        <aside class="df-sidebar" id="app-sidebar">
            <div class="df-header">
                <a href="{{ $dashboardUrl }}" class="df-logo-badge">DF</a>
                <div>
                    <h2 class="df-brand-title">DriveFlow</h2>
                    <span class="df-role-tag">
                        @if ($isAdmin)
                            Administration SaaS
                        @elseif ($roleVal === 'moniteur')
                            Espace Moniteur
                        @else
                            Espace Auto-école
                        @endif
                    </span>
                </div>
            </div>

            @if ($team)
                <div class="df-team-badge">
                    <span class="df-team-label">Auto-école</span>
                    <strong class="df-team-name" title="{{ $team->name }}">{{ $team->name }}</strong>
                </div>
            @endif

            <nav class="df-nav" aria-label="Navigation principale">
                @if ($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="df-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <div class="df-link-content">
                            <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span>Supervision SaaS</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.dashboard').'#auto-ecoles' }}" class="df-link">
                        <div class="df-link-content">
                            <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Auto-écoles & Demandes</span>
                        </div>
                    </a>

                    @if ($team)
                        <a href="{{ route('demandes.index', ['current_team' => $team->slug]) }}" class="df-link {{ request()->routeIs('demandes.*') ? 'active' : '' }}">
                            <div class="df-link-content">
                                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Demandes globales</span>
                            </div>
                            @if ($pendingRequestsCount > 0)
                                <span class="df-counter df-counter-warning">{{ $pendingRequestsCount }}</span>
                            @endif
                        </a>
                    @endif
                @else
                    <!-- Auto-école standard menu -->
                    <a href="{{ $dashboardUrl }}" class="df-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <div class="df-link-content">
                            <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Tableau de bord</span>
                        </div>
                    </a>

                    @if ($team && Route::has('instructor.schedule.index'))
                        <a href="{{ route('instructor.schedule.index', ['current_team' => $team->slug]) }}" class="df-link {{ request()->routeIs('instructor.schedule.*') ? 'active' : '' }}">
                            <div class="df-link-content">
                                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Planning</span>
                            </div>
                            @if ($todayLessonsCount > 0)
                                <span class="df-counter {{ request()->routeIs('instructor.schedule.*') ? '' : 'df-counter-highlight' }}">{{ $todayLessonsCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if ($team && Route::has('instructor.students.index'))
                        <a href="{{ route('instructor.students.index', ['current_team' => $team->slug]) }}" class="df-link {{ request()->routeIs('instructor.students.*') ? 'active' : '' }}">
                            <div class="df-link-content">
                                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span>Apprenants</span>
                            </div>
                            @if ($studentsCount > 0)
                                <span class="df-counter">{{ $studentsCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if ($team && Route::has('instructor.lessons.index'))
                        <a href="{{ route('instructor.lessons.index', ['current_team' => $team->slug]) }}" class="df-link {{ request()->routeIs('instructor.lessons.index', 'instructor.lessons.show', 'instructor.lessons.edit') ? 'active' : '' }}">
                            <div class="df-link-content">
                                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span>Gestion des cours</span>
                            </div>
                        </a>
                    @endif

                    @if ($team && Route::has('demandes.index'))
                        <a href="{{ route('demandes.index', ['current_team' => $team->slug]) }}" class="df-link {{ request()->routeIs('demandes.*') ? 'active' : '' }}">
                            <div class="df-link-content">
                                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Demandes</span>
                            </div>
                            @if ($pendingRequestsCount > 0)
                                <span class="df-counter df-counter-warning" title="{{ $pendingRequestsCount }} en attente">{{ $pendingRequestsCount }}</span>
                            @endif
                        </a>
                    @endif
                @endif

                <div class="df-section-title">Paramètres & Compte</div>

                @if (Route::has('profile.edit'))
                    <a href="{{ route('profile.edit') }}" class="df-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <div class="df-link-content">
                            <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Paramètres</span>
                        </div>
                    </a>
                @endif
            </nav>

            <div class="df-footer">
                <div class="df-user-info">
                    <div class="df-avatar">
                        {{ $user ? $user->initials() : 'U' }}
                    </div>
                    <div class="df-details">
                        <span class="df-name">{{ $user->name ?? 'Utilisateur' }}</span>
                        <span class="df-email">{{ $user->email ?? 'compte@driveflow.cm' }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="df-logout-btn" title="Se déconnecter" aria-label="Se déconnecter">
                        <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="df-main-wrapper">
            <!-- Header Bar -->
            <header class="df-header-bar">
                <div class="df-header-left">
                    <button class="df-mobile-toggle" type="button" aria-label="Ouvrir le menu" onclick="document.getElementById('app-sidebar').classList.toggle('is-open')">
                        ☰
                    </button>
                    <div>
                        <h1 class="df-header-title">{{ $title ?? 'DriveFlow' }}</h1>
                        <p class="df-header-subtitle">
                            @if ($isAdmin)
                                Espace d'administration central SaaS
                            @elseif ($team)
                                Espace de gestion • {{ $team->name }}
                            @else
                                Tableau de bord
                            @endif
                        </p>
                    </div>
                </div>

                <div class="df-header-right">
                    <div class="df-header-profile">
                        <div class="df-header-avatar">
                            {{ $user ? $user->initials() : 'U' }}
                        </div>
                        <div class="df-header-user">
                            <span class="df-header-name">{{ $user->name ?? 'Utilisateur' }}</span>
                            <span class="df-header-status">● En service</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="df-content-area">
                @if (session('success'))
                    <div class="df-alert df-alert-success">
                        <span>✓ {{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="df-alert df-alert-error">
                        <span>⚠ {{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="df-alert df-alert-error">
                        <div>
                            <strong>Erreurs rencontrées :</strong>
                            <ul style="margin-top: 5px; padding-left: 18px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <livewire:create-team-modal />

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>
</html>
