@extends('layouts.app')

@section('title', 'Müəllimlər')

@section('content')
    <div class="page-header">
        <div>
            <h1>Müəllimlər</h1>
            <p>Yerli və xarici müəllimlər, komissiya faizləri</p>
        </div>
        <a href="{{ route('teachers.create') }}" class="btn btn--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="M12 5v14"/>
            </svg>
            Yeni müəllim
        </a>
    </div>

    @include('partials.flash')

    <form method="GET" class="filters">
        <input type="search" name="search" value="{{ $search }}" placeholder="Ad, email, telefon..." class="input" style="flex:1;min-width:200px">
        <select name="type" class="select">
            <option value="">Bütün növlər</option>
            <option value="local" @selected($type === 'local')>Yerli</option>
            <option value="foreign" @selected($type === 'foreign')>Xarici</option>
        </select>
        <button type="submit" class="btn btn--secondary btn--sm">Filter</button>
        @if ($search || $type)
            <a href="{{ route('teachers.index') }}" class="btn btn--ghost btn--sm">Təmizlə</a>
        @endif
    </form>

    <div class="card">
        <div class="card__body card__body--flush">
            @if ($teachers->isEmpty())
                <div class="empty">
                    <div class="empty__icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/>
                        </svg>
                    </div>
                    <div class="empty__title">Müəllim tapılmadı</div>
                    <div>İlk müəllimi əlavə etmək üçün yuxarıdakı düyməyə basın.</div>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Müəllim</th>
                                <th>Növ</th>
                                <th>Komissiya</th>
                                <th>Qrup sayı</th>
                                <th>Status</th>
                                <th style="text-align:right">Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teachers as $teacher)
                                <tr>
                                    <td>
                                        <div class="table__user">
                                            <div class="avatar avatar--sm">{{ mb_strtoupper(mb_substr($teacher->name, 0, 2)) }}</div>
                                            <div>
                                                <div class="table__user-name">
                                                    <a href="{{ route('teachers.show', $teacher) }}">{{ $teacher->name }}</a>
                                                </div>
                                                <div class="table__user-email">{{ $teacher->email ?? $teacher->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge--{{ $teacher->type === 'foreign' ? 'info' : 'secondary' }}">
                                            {{ $teacher->typeLabel() }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ rtrim(rtrim(number_format($teacher->commission_rate, 2), '0'), '.') }}%</td>
                                    <td class="text-muted">{{ $teacher->groups_count }}</td>
                                    <td>
                                        @if ($teacher->is_active)
                                            <span class="badge badge--success">Aktiv</span>
                                        @else
                                            <span class="badge badge--secondary">Passiv</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right">
                                        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn--ghost btn--sm">Redaktə</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    <div class="pagination__info">
                        {{ $teachers->firstItem() }}-{{ $teachers->lastItem() }} / {{ $teachers->total() }}
                    </div>
                    {{ $teachers->links('partials.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection
