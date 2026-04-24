@php
    $currentRoute = request()->route()?->getName() ?? '';
    $is = fn ($prefix) => str_starts_with($currentRoute, $prefix);
@endphp

<aside class="sidebar">
    <div class="sidebar__brand">
        <div class="sidebar__brand-logo">E</div>
        <span>Education</span>
    </div>

    <nav class="sidebar__nav">
        <div class="sidebar__section">
            <div class="sidebar__section-title">Ümumi</div>

            @can('dashboard.view')
                <a href="{{ route('dashboard') }}" class="sidebar__item {{ $currentRoute === 'dashboard' ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            @endcan

            @can('students.view')
                <a href="{{ route('students.index') }}" class="sidebar__item {{ $is('students.') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Tələbələr</span>
                </a>
            @endcan

            @can('groups.view')
                <a href="{{ route('groups.index') }}" class="sidebar__item {{ $is('groups.') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                    <span>Qruplar</span>
                </a>
            @endcan

            @can('teachers.view')
                <a href="{{ route('teachers.index') }}" class="sidebar__item {{ $is('teachers.') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/>
                    </svg>
                    <span>Müəllimlər</span>
                </a>
            @endcan
        </div>

        @canany(['payments.view', 'reports.view'])
        <div class="sidebar__section">
            <div class="sidebar__section-title">Maliyyə</div>

            @can('payments.view')
                <a href="{{ route('payments.index') }}" class="sidebar__item {{ $is('payments.') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    <span>Ödənişlər</span>
                </a>
            @endcan

            @can('reports.view')
                <a href="{{ route('reports.index') }}" class="sidebar__item {{ $is('reports.') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>
                    </svg>
                    <span>Hesabatlar</span>
                </a>
            @endcan
        </div>
        @endcanany

        @canany(['users.manage', 'settings.manage'])
        <div class="sidebar__section">
            <div class="sidebar__section-title">Sistem</div>

            @can('users.manage')
                <a href="{{ route('users.index') }}" class="sidebar__item {{ $is('users.') || $is('roles.') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>İstifadəçilər</span>
                </a>
            @endcan

            @can('settings.manage')
                <a href="{{ route('settings.index') }}" class="sidebar__item {{ $is('settings.') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    <span>Parametrlər</span>
                </a>
            @endcan

            @role('Super Admin')
                <a href="{{ route('trash.index') }}" class="sidebar__item {{ $is('trash.') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    <span>Zibil qutusu</span>
                </a>
            @endrole
        </div>
        @endcanany
    </nav>

    <div class="sidebar__footer">
        <div class="flex items-center gap-2">
            <div class="sidebar__user" style="flex:1">
                <div class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()?->name ?? 'A', 0, 2)) }}</div>
                <div class="sidebar__user-info">
                    <div class="sidebar__user-name">{{ auth()->user()?->name }}</div>
                    <div class="sidebar__user-email">
                        {{ auth()->user()?->roles->pluck('name')->join(', ') ?: auth()->user()?->email }}
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="icon-btn" title="Çıxış">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
