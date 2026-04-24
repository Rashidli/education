@extends('layouts.app')

@section('title', 'Rollar və icazələr')

@section('content')
    <div class="page-header">
        <div>
            <h1>Rollar və icazələr</h1>
            <p>Sistem rolları və onlara verilmiş icazələr</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('users.index') }}" class="btn btn--outline">İstifadəçilər</a>
            <a href="{{ route('roles.create') }}" class="btn btn--primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Yeni rol
            </a>
        </div>
    </div>

    @include('partials.flash')

    <div class="card">
        <div class="card__body card__body--flush">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rol</th>
                            <th>İcazələr</th>
                            <th style="text-align:right">Əməliyyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>
                                    <span class="badge badge--{{ $role->name === \App\Models\User::ROLE_SUPER_ADMIN ? 'destructive' : ($role->name === \App\Models\User::ROLE_ADMIN ? 'info' : 'secondary') }}">
                                        {{ $role->name }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    @if ($role->name === \App\Models\User::ROLE_SUPER_ADMIN)
                                        <em>Bütün icazələr (Gate::before ilə)</em>
                                    @else
                                        {{ $role->permissions->count() }} icazə
                                    @endif
                                </td>
                                <td style="text-align:right">
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn--ghost btn--sm">Redaktə</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card__header">
            <div>
                <div class="card__title">Mövcud icazələr</div>
                <div class="card__description">Sistemdə tanınan bütün icazə açarları</div>
            </div>
        </div>
        <div class="card__body">
            @foreach ($permissions as $group => $items)
                <div class="mt-3">
                    <div class="font-semibold text-sm" style="text-transform:uppercase;color:var(--muted-foreground);letter-spacing:0.05em">{{ $group }}</div>
                    <div class="flex gap-2 mt-2" style="flex-wrap:wrap">
                        @foreach ($items as $p)
                            <span class="badge badge--outline" title="{{ $p->name }}">
                                {{ $permissionLabels[$p->name] ?? $p->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
