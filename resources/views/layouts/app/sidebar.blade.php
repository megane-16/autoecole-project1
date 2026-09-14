@php
    $user = auth()->user();
    $team = $user?->currentTeam ?? $user?->personalTeam();
    $dashboardUrl = $team ? route('dashboard', ['current_team' => $team->slug]) : route('home');
    $quickLinks = [
        ['label' => 'Dashboard', 'href' => $dashboardUrl],
        ['label' => 'Ajouter un élève', 'href' => $dashboardUrl.'#eleves'],
        ['label' => 'Demandes', 'href' => $team ? route('demandes.index', ['current_team' => $team->slug]) : '#'],
        ['label' => 'Planning', 'href' => $dashboardUrl.'#planning'],
        ['label' => 'Élèves', 'href' => $dashboardUrl.'#eleves'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>
        :root {
            --drive-sidebar: #17212b;
            --drive-sidebar-muted: #9aa8b5;
            --drive-sidebar-line: rgb(255 255 255 / 10%);
            --drive-blue: #2f6fed;
        }

        .drive-shell {
            min-height: 100vh;
            background: #f7f9fb;
        }

        .drive-sidebar {
            position: fixed;
            z-index: 30;
            inset: 0 auto 0 0;
            display: flex;
            width: 250px;
            flex-direction: column;
            padding: 24px 16px;
            color: #fff;
            background: var(--drive-sidebar);
        }

        .drive-brand {
            display: block;
            padding: 0 12px 24px;
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -.03em;
            text-decoration: none;
        }

        .drive-brand span {
            display: block;
            margin-top: 5px;
            color: var(--drive-sidebar-muted);
            font-size: .7rem;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .drive-sidebar__section {
            padding: 20px 12px 8px;
            color: var(--drive-sidebar-muted);
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .drive-nav {
            display: grid;
            gap: 5px;
        }

        .drive-nav__link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 12px;
            border-radius: 9px;
            color: #d7e0e8;
            font-size: .86rem;
            text-decoration: none;
            transition: background .2s, color .2s;
        }

        .drive-nav__link:hover,
        .drive-nav__link.is-active {
            color: #fff;
            background: rgb(255 255 255 / 12%);
        }

        .drive-nav__icon {
            display: grid;
            width: 20px;
            height: 20px;
            place-items: center;
            color: #9fc0ff;
            font-size: .9rem;
            font-weight: 700;
        }

        .drive-team {
            margin: 18px 4px 8px;
            padding: 12px;
            border: 1px solid var(--drive-sidebar-line);
            border-radius: 10px;
            background: rgb(255 255 255 / 5%);
        }

        .drive-team__label {
            display: block;
            margin-bottom: 4px;
            color: var(--drive-sidebar-muted);
            font-size: .68rem;
        }

        .drive-team__name {
            display: block;
            overflow: hidden;
            color: #fff;
            font-size: .8rem;
            font-weight: 600;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .drive-sidebar__bottom {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid var(--drive-sidebar-line);
        }

        .drive-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 8px;
        }

        .drive-profile__avatar {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 10px;
            color: #18335f;
            background: #bcd2ff;
            font-size: .75rem;
            font-weight: 700;
        }

        .drive-profile__details {
            min-width: 0;
            flex: 1;
        }

        .drive-profile__name,
        .drive-profile__email {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .drive-profile__name { color: #fff; font-size: .78rem; font-weight: 600; }
        .drive-profile__email { margin-top: 2px; color: var(--drive-sidebar-muted); font-size: .68rem; }

        .drive-logout {
            border: 0;
            color: var(--drive-sidebar-muted);
            background: transparent;
            cursor: pointer;
            font-size: 1rem;
        }

        .drive-main {
            min-height: 100vh;
            margin-left: 250px;
        }

        .drive-header {
            position: sticky;
            z-index: 20;
            top: 0;
            display: flex;
            min-height: 68px;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 0 34px;
            border-bottom: 1px solid #e7edf2;
            background: rgb(255 255 255 / 92%);
            backdrop-filter: blur(12px);
        }

        .drive-header__title { margin: 0; color: #17212b; font-size: 1rem; font-weight: 700; }
        .drive-header__context { margin: 4px 0 0; color: #718096; font-size: .76rem; }
        .drive-header__actions { display: flex; align-items: center; gap: 12px; }
        .drive-header__badge { padding: 7px 10px; border-radius: 999px; color: #087a62; background: #e5f8f3; font-size: .7rem; font-weight: 700; }
        .drive-mobile-toggle { display: none; border: 0; color: #17212b; background: transparent; font-size: 1.4rem; }

        @media (max-width: 800px) {
            .drive-sidebar { transform: translateX(-100%); transition: transform .2s ease; }
            .drive-sidebar.is-open { transform: translateX(0); }
            .drive-main { margin-left: 0; }
            .drive-header { padding: 0 18px; }
            .drive-mobile-toggle { display: inline-grid; place-items: center; }
            .drive-header__title { display: none; }
        }
    </style>
</head>
<body>
    <div class="drive-shell">
        <aside class="drive-sidebar" id="drive-sidebar">
            <a class="drive-brand" href="{{ $dashboardUrl }}">Drive Flow<span>Gestion auto-école</span></a>

            <div class="drive-team">
                <span class="drive-team__label">Équipe active</span>
                <strong class="drive-team__name">{{ $team?->name ?? 'Aucune équipe' }}</strong>
            </div>

            <span class="drive-sidebar__section">Navigation</span>
            <nav class="drive-nav" aria-label="Navigation principale">
                @foreach ($quickLinks as $link)
                    <a class="drive-nav__link {{ request()->url() == $link['href'] ? 'is-active' : '' }}" href="{{ $link['href'] }}">
                        <span class="drive-nav__icon">{{ $loop->index === 0 ? '⌂' : ($loop->index === 1 ? '+' : ($loop->index === 2 ? '📬' : ($loop->index === 3 ? '⌁' : '◉'))) }}</span>
                        {{ $link['label'] }}
                    </a>
                @endforeach
                @if ($user && $user->role?->value === 'administrateur')
                    <a class="drive-nav__link {{ request()->routeIs('admin.*') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <span class="drive-nav__icon">▦</span> Administration
                    </a>
                @endif
                @if (Route::has('profile.edit'))
                    <a class="drive-nav__link {{ request()->routeIs('profile.edit') ? 'is-active' : '' }}" href="{{ route('profile.edit') }}">
                        <span class="drive-nav__icon">⚙</span> Paramètres
                    </a>
                @endif
            </nav>

            <div class="drive-sidebar__bottom">
                @if ($user)
                    <div class="drive-profile">
                        <span class="drive-profile__avatar">{{ $user->initials() }}</span>
                        <div class="drive-profile__details">
                            <strong class="drive-profile__name">{{ $user->name }}</strong>
                            <span class="drive-profile__email">{{ $user->email }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="drive-logout" type="submit" title="Se déconnecter" aria-label="Se déconnecter">↪</button>
                        </form>
                    </div>
                @endif
            </div>
        </aside>

        <div class="drive-main">
            <header class="drive-header">
                <div class="drive-header__actions">
                    <button class="drive-mobile-toggle" type="button" aria-label="Ouvrir le menu" onclick="document.getElementById('drive-sidebar').classList.toggle('is-open')">☰</button>
                    <div>
                        <h1 class="drive-header__title">{{ $title ?? 'Drive Flow' }}</h1>
                        <p class="drive-header__context">Espace de gestion de votre auto-école</p>
                    </div>
                </div>
                <span class="drive-header__badge">Compte actif</span>
            </header>

            {{ $slot }}
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
