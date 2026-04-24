@extends('layouts.app')

@section('title', 'İstifadəçilər')

@section('content')
    <div class="page-header">
        <div>
            <h1>İstifadəçilər</h1>
            <p>Admin panelə giriş olan şəxslər və rolları</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn--outline">Rollar</a>
            <a href="{{ route('users.create') }}" class="btn btn--primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Yeni istifadəçi
            </a>
        </div>
    </div>

    @include('partials.flash')

    <form method="GET" class="filters">
        <input type="search" name="search" value="{{ $search }}" placeholder="Ad, email..." class="input" style="flex:1;min-width:200px">
        <button type="submit" class="btn btn--secondary btn--sm">Filter</button>
        @if ($search)
            <a href="{{ route('users.index') }}" class="btn btn--ghost btn--sm">Təmizlə</a>
        @endif
    </form>

    <div class="card">
        <div class="card__body card__body--flush">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ad</th>
                            <th>Email</th>
                            <th>Rollar</th>
                            <th>Yaranıb</th>
                            <th style="text-align:right">Əməliyyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>
                                    <div class="table__user">
                                        <div class="avatar avatar--sm">{{ mb_strtoupper(mb_substr($u->name, 0, 2)) }}</div>
                                        <div class="table__user-name">{{ $u->name }}</div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $u->email }}</td>
                                <td>
                                    @foreach ($u->roles as $r)
                                        <span class="badge badge--{{ $r->name === \App\Models\User::ROLE_SUPER_ADMIN ? 'destructive' : ($r->name === \App\Models\User::ROLE_ADMIN ? 'info' : 'secondary') }}">
                                            {{ $r->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-muted">{{ $u->created_at->format('d.m.Y') }}</td>
                                <td style="text-align:right">
                                    <a href="{{ route('users.edit', $u) }}" class="btn btn--ghost btn--sm">Redaktə</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <div class="pagination__info">
                    {{ $users->firstItem() }}-{{ $users->lastItem() }} / {{ $users->total() }}
                </div>
                {{ $users->links('partials.pagination') }}
            </div>
        </div>
    </div>
@endsection
