<!-- resources/views/components/instructor-header.blade.php -->
<style>
    .df-header-bar {
        height: 70px;
        background-color: #ffffff;
        border-bottom: 1px solid #c8e6c9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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

    .df-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .df-search-input {
        background-color: #f1f8e9;
        border: 1px solid #c8e6c9;
        border-radius: 8px;
        padding: 8px 12px 8px 36px;
        font-size: 13px;
        color: #1f2937;
        outline: none;
        transition: border-color 0.2s;
    }

    .df-search-input:focus {
        border-color: #2e7d32;
    }

    .df-search-icon {
        position: absolute;
        left: 10px;
        width: 16px;
        height: 16px;
        color: #6b7280;
    }

    .df-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 1px solid #e8f5e9;
        background-color: #ffffff;
        color: #4b5563;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        transition: background-color 0.2s, color 0.2s;
    }

    .df-icon-btn:hover {
        background-color: #f1f8e9;
        color: #2e7d32;
    }

    .df-notification-dot {
        position: absolute;
        top: 7px;
        right: 7px;
        width: 8px;
        height: 8px;
        background-color: #e53935;
        border-radius: 50%;
        border: 2px solid #ffffff;
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
</style>

<header class="df-header-bar">
    <!-- Côté gauche : Titre de section / Date -->
    <div class="df-header-left">
        <div>
            <h1 class="df-header-title">{{ $title ?? 'Espace Moniteur' }}</h1>
            <p class="df-header-subtitle">Bienvenue sur votre espace de gestion DriveFlow</p>
        </div>
    </div>

    <!-- Côté droit : Recherche, Notifications, Statut Moniteur -->
    <div class="df-header-right">
        <!-- Recherche rapide -->
        <form method="GET" action="{{ Route::has('instructor.lessons.index') ? route('instructor.lessons.index') : '#' }}" class="df-search-box">
            <svg class="df-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" class="df-search-input" placeholder="Rechercher un cours, élève...">
        </form>

        <!-- Profil rapide -->
        <div class="df-header-profile">
            <div class="df-header-avatar">
                {{ auth()->user() ? auth()->user()->initials() : 'M' }}
            </div>
            <div class="df-header-user">
                <span class="df-header-name">{{ auth()->user()->name ?? 'Moniteur' }}</span>
                <span class="df-header-status">● En service</span>
            </div>
        </div>
    </div>
</header>