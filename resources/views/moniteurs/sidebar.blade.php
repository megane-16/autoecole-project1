<!-- resources/views/moniteurs/sidebar.blade.php -->
@php
    $user = auth()->user();
    $team = $user?->currentTeam ?? $user?->personalTeam();
    
    // Dynamic counts for instructor
    $assignedStudentsCount = 0;
    $todayLessonsCount = 0;
    $totalLessonsCount = 0;

    if ($user) {
        // Direct assigned students + students from lessons
        $assignedIds = $user->assignedStudents()->pluck('users.id');
        $lessonStudentIds = \App\Models\Lesson::query()
            ->where('instructor_id', $user->id)
            ->when($team, fn($q) => $q->where('team_id', $team->id))
            ->whereNotNull('student_id')
            ->pluck('student_id');
        $assignedStudentsCount = $assignedIds->merge($lessonStudentIds)->unique()->count();

        // Today lessons
        $todayLessonsCount = \App\Models\Lesson::query()
            ->where('instructor_id', $user->id)
            ->when($team, fn($q) => $q->where('team_id', $team->id))
            ->whereDate('starts_at', \Illuminate\Support\Carbon::today())
            ->count();

        // Total lessons
        $totalLessonsCount = \App\Models\Lesson::query()
            ->where('instructor_id', $user->id)
            ->when($team, fn($q) => $q->where('team_id', $team->id))
            ->count();
    }
@endphp

<style>
    .df-sidebar {
        width: 270px;
        min-height: 100vh;
        background-color: #ffffff;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        padding: 22px 18px;
        box-sizing: border-box;
        font-family: inherit;
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
</style>

<aside class="df-sidebar">
    <!-- En-tête : Logo & Titre -->
    <div class="df-header">
        <div class="df-logo-badge">DF</div>
        <div>
            <h2 class="df-brand-title">DriveFlow</h2>
            <span class="df-role-tag">Espace Moniteur</span>
        </div>
    </div>

    <!-- Auto-école de rattachement -->
    @if ($team)
        <div class="df-team-badge">
            <span class="df-team-label">Auto-école</span>
            <strong class="df-team-name" title="{{ $team->name }}">{{ $team->name }}</strong>
        </div>
    @endif

    <!-- Navigation -->
    <nav class="df-nav">
        <!-- Planning -->
        <a href="{{ Route::has('instructor.schedule.index') ? route('instructor.schedule.index') : '#planning' }}" class="df-link {{ request()->routeIs('instructor.schedule.*') ? 'active' : '' }}">
            <div class="df-link-content">
                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Mon Planning</span>
            </div>
            @if ($todayLessonsCount > 0)
                <span class="df-counter {{ request()->routeIs('instructor.schedule.*') ? '' : 'df-counter-highlight' }}" title="{{ $todayLessonsCount }} cours aujourd'hui">
                    {{ $todayLessonsCount }}
                </span>
            @endif
        </a>

        <!-- Apprenants Associés -->
        <a href="{{ Route::has('instructor.students.index') ? route('instructor.students.index') : '#apprenants' }}" class="df-link {{ request()->routeIs('instructor.students.*') ? 'active' : '' }}">
            <div class="df-link-content">
                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Mes Apprenants</span>
            </div>
            @if ($assignedStudentsCount > 0)
                <span class="df-counter" title="{{ $assignedStudentsCount }} élèves assignés">
                    {{ $assignedStudentsCount }}
                </span>
            @endif
        </a>

        <!-- Gestion des Cours (CRUD) -->
        <div class="df-section-title">Gestion des cours</div>

        <!-- Liste des cours (Lecture / Modification / Suppression) -->
        <a href="{{ Route::has('instructor.lessons.index') ? route('instructor.lessons.index') : '#cours' }}" class="df-link {{ request()->routeIs('instructor.lessons.index', 'instructor.lessons.show', 'instructor.lessons.edit') ? 'active' : '' }}">
            <div class="df-link-content">
                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Liste des cours</span>
            </div>
            @if ($totalLessonsCount > 0)
                <span class="df-counter">
                    {{ $totalLessonsCount }}
                </span>
            @endif
        </a>

        <!-- Créer un cours -->
        <a href="{{ Route::has('instructor.lessons.create') ? route('instructor.lessons.create') : '#nouveau-cours' }}" class="df-link {{ request()->routeIs('instructor.lessons.create') ? 'active' : '' }}">
            <div class="df-link-content">
                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Nouveau cours</span>
            </div>
        </a>
    </nav>

    <!-- Pied de page : Profil & Déconnexion -->
    <div class="df-footer">
        <div class="df-user-info">
            <div class="df-avatar">
                {{ $user ? $user->initials() : 'M' }}
            </div>
            <div class="df-details">
                <span class="df-name">{{ $user->name ?? 'Moniteur' }}</span>
                <span class="df-email">{{ $user->email ?? 'moniteur@driveflow.cm' }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="df-logout-btn" title="Se déconnecter">
                <svg class="df-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>
</aside>