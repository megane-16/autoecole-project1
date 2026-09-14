<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            @php
                $headerDashboardUrl = auth()->user()?->currentTeam ? route('dashboard', ['current_team' => auth()->user()->currentTeam->slug]) : route('home');
            @endphp

            <a href="{{ $headerDashboardUrl }}" wire:navigate class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">
                Drive Flow
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="$headerDashboardUrl" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <x-desktop-user-menu :showTeam="false" />

            <div class="max-lg:hidden">
                <livewire:team-switcher />
            </div>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <a href="{{ $headerDashboardUrl }}" wire:navigate class="px-2 text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">
                    Drive Flow
                </a>
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <livewire:team-switcher />

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Navigation')">
                        <flux:sidebar.item icon="layout-grid" :href="$headerDashboardUrl" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

        </flux:sidebar>

        {{ $slot }}

        <livewire:create-team-modal />

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
