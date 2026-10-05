@extends('moniteurs.layout', ['title' => 'Planning du Moniteur'])

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
    .df-page-title {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
    }
    .df-page-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }
    .df-schedule-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .df-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .df-stat-num {
        font-size: 24px;
        font-weight: 700;
        color: #2e7d32;
        margin-top: 6px;
    }
    .df-stat-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    .df-schedule-controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .df-date-nav {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .df-timeline {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .df-lesson-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left: 5px solid #2e7d32;
        border-radius: 12px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .df-lesson-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.04);
    }
    .df-lesson-time {
        display: flex;
        flex-direction: column;
        min-width: 90px;
    }
    .df-time-start {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }
    .df-time-duration {
        font-size: 12px;
        color: #64748b;
    }
    .df-lesson-details {
        flex: 1;
    }
    .df-lesson-title {
        font-size: 15px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }
    .df-lesson-meta {
        font-size: 13px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .df-empty-state {
        text-align: center;
        padding: 48px 24px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px dashed #cbd5e1;
        color: #64748b;
    }
</style>

<div class="df-page-header">
    <div>
        <h1 class="df-page-title">Planning & Emploi du temps</h1>
        <p class="df-page-subtitle">Consultez vos cours programmés et suivez vos sessions d'apprentissage.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('instructor.lessons.export', ['instructor_id' => request('instructor_id', auth()->id())]) }}" class="df-btn df-btn-outline" title="Télécharger le planning en local (CSV / Excel)">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Télécharger le planning (CSV)
        </a>
        <a href="{{ route('instructor.lessons.create') }}" class="df-btn df-btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Planifier un cours
        </a>
    </div>
</div>

<!-- Stats bar -->
<div class="df-schedule-stats">
    <div class="df-stat-card">
        <span class="df-stat-title">Aujourd'hui</span>
        <div class="df-stat-num">{{ $todayLessonsCount }} <span style="font-size: 13px; font-weight: normal; color: #64748b;">cours</span></div>
    </div>
    <div class="df-stat-card">
        <span class="df-stat-title">Cette semaine</span>
        <div class="df-stat-num">{{ $weekLessonsCount }} <span style="font-size: 13px; font-weight: normal; color: #64748b;">cours</span></div>
    </div>
    <div class="df-stat-card">
        <span class="df-stat-title">Total terminés</span>
        <div class="df-stat-num">{{ $completedLessonsCount }} <span style="font-size: 13px; font-weight: normal; color: #64748b;">cours</span></div>
    </div>
</div>

<!-- Planning Controls -->
<div class="df-schedule-controls">
    <div class="df-date-nav">
        <a href="{{ route('instructor.schedule.index', ['date' => $selectedDate->copy()->subWeek()->format('Y-m-d'), 'view' => $viewMode]) }}" class="df-btn df-btn-outline df-btn-sm" title="Semaine précédente">
            ‹ Précédent
        </a>
        <strong style="font-size: 15px; color: #1e293b;">
            @if ($viewMode === 'day')
                {{ $selectedDate->translatedFormat('l d F Y') }}
            @else
                Semaine du {{ $selectedDate->copy()->startOfWeek()->translatedFormat('d M') }} au {{ $selectedDate->copy()->endOfWeek()->translatedFormat('d M Y') }}
            @endif
        </strong>
        <a href="{{ route('instructor.schedule.index', ['date' => $selectedDate->copy()->addWeek()->format('Y-m-d'), 'view' => $viewMode]) }}" class="df-btn df-btn-outline df-btn-sm" title="Semaine suivante">
            Suivant ›
        </a>
        <a href="{{ route('instructor.schedule.index', ['date' => now()->format('Y-m-d'), 'view' => $viewMode]) }}" class="df-btn df-btn-outline df-btn-sm">
            Aujourd'hui
        </a>
    </div>

    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        @if (isset($instructors) && $instructors->isNotEmpty())
            <form method="GET" action="{{ route('instructor.schedule.index') }}" style="display: inline-flex; align-items: center; gap: 6px;">
                <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                <select name="instructor_id" onchange="this.form.submit()" class="df-btn df-btn-outline df-btn-sm" style="background: #fff; cursor: pointer; color: #1e293b; font-weight: 600;">
                    <option value="all" @selected(($selectedInstructorId ?? '') === 'all')>🏢 Tout le planning de l'auto-école</option>
                    @foreach ($instructors as $inst)
                        <option value="{{ $inst->id }}" @selected(($selectedInstructorId ?? auth()->id()) == $inst->id)>
                            👤 Moniteur : {{ $inst->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif

        <a href="{{ route('instructor.schedule.index', ['date' => $selectedDate->format('Y-m-d'), 'view' => 'day', 'instructor_id' => $selectedInstructorId ?? '']) }}" class="df-btn {{ $viewMode === 'day' ? 'df-btn-primary' : 'df-btn-outline' }} df-btn-sm">
            Vue Jour
        </a>
        <a href="{{ route('instructor.schedule.index', ['date' => $selectedDate->format('Y-m-d'), 'view' => 'week', 'instructor_id' => $selectedInstructorId ?? '']) }}" class="df-btn {{ $viewMode === 'week' ? 'df-btn-primary' : 'df-btn-outline' }} df-btn-sm">
            Vue Semaine
        </a>
    </div>
</div>


<!-- Lessons Timeline / List -->
<div class="df-timeline">
    @forelse ($lessons as $lesson)
        <div class="df-lesson-card" style="border-left-color: {{ $lesson->status === 'termine' ? '#16a34a' : ($lesson->status === 'en_cours' ? '#2563eb' : ($lesson->status === 'annule' ? '#dc2626' : '#d97706')) }}">
            <div class="df-lesson-time">
                <span class="df-time-start">{{ $lesson->starts_at->format('H:i') }}</span>
                <span class="df-time-duration">{{ $lesson->starts_at->translatedFormat('D d M') }} · {{ $lesson->starts_at->diffInMinutes($lesson->ends_at) }} min</span>
            </div>

            <div class="df-lesson-details">
                <div class="df-lesson-title">{{ $lesson->title ?? $lesson->formatted_type }}</div>
                <div class="df-lesson-meta">
                    @if ($lesson->student)
                        <span>👤 <strong>{{ $lesson->student->name }}</strong> (Permis {{ $lesson->student->categorie_permis ?? 'B' }})</span>
                    @else
                        <span>👤 <em>Cours collectif ou sans élève</em></span>
                    @endif

                    @if ($lesson->pickup_location)
                        <span>📍 {{ $lesson->pickup_location }}</span>
                    @endif

                    <span class="df-badge df-badge-{{ $lesson->status_badge_class }}">
                        {{ $lesson->status_label }}
                    </span>
                </div>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <a href="{{ route('instructor.lessons.show', $lesson) }}" class="df-btn df-btn-outline df-btn-sm">
                    Détails
                </a>
                <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="df-btn df-btn-outline df-btn-sm">
                    Modifier
                </a>
            </div>
        </div>
    @empty
        <div class="df-empty-state">
            <svg width="48" height="48" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Aucun cours programmé</h3>
            <p style="font-size: 13px; margin-bottom: 16px;">Vous n'avez aucun cours prévu pour cette période.</p>
            <a href="{{ route('instructor.lessons.create') }}" class="df-btn df-btn-primary df-btn-sm">
                + Programmer un cours
            </a>
        </div>
    @endforelse
</div>
@endsection
