@extends('layouts.app')

@section('title', 'Qruplar')

@section('content')
    <div class="page-header">
        <div>
            <h1>Qruplar</h1>
            <p>Bütün qruplar, müəllim və aylıq qiymətlər</p>
        </div>
        <a href="{{ route('groups.create') }}" class="btn btn--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="M12 5v14"/>
            </svg>
            Yeni qrup
        </a>
    </div>

    @include('partials.flash')

    <form method="GET" class="filters">
        <input type="search" name="search" value="{{ $search }}" placeholder="Qrup adı..." class="input" style="flex:1;min-width:200px">
        <select name="teacher_id" class="select">
            <option value="">Bütün müəllimlər</option>
            @foreach ($teachers as $t)
                <option value="{{ $t->id }}" @selected((int) $teacherId === $t->id)>{{ $t->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn--secondary btn--sm">Filter</button>
        @if ($search || $teacherId)
            <a href="{{ route('groups.index') }}" class="btn btn--ghost btn--sm">Təmizlə</a>
        @endif
    </form>

    <div class="card">
        <div class="card__body card__body--flush">
            @if ($groups->isEmpty())
                <div class="empty">
                    <div class="empty__title">Qrup yoxdur</div>
                    <div>İlk qrupu yaratmaq üçün yuxarıdakı düyməyə basın.</div>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Qrup</th>
                                <th>Müəllim</th>
                                <th>Aylıq qiymət</th>
                                <th>Tələbə sayı</th>
                                <th>Status</th>
                                <th style="text-align:right">Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groups as $group)
                                <tr>
                                    <td>
                                        <div class="table__user-name">
                                            <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('teachers.show', $group->teacher) }}" class="text-muted">{{ $group->teacher->name }}</a>
                                        <span class="badge badge--{{ $group->teacher->type === 'foreign' ? 'info' : 'secondary' }}" style="margin-left:0.375rem">
                                            {{ $group->teacher->typeLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($group->monthly_price, 2) }} ₼</td>
                                    <td class="text-muted">{{ $group->students_count }}</td>
                                    <td>
                                        @if ($group->is_active)
                                            <span class="badge badge--success">Aktiv</span>
                                        @else
                                            <span class="badge badge--secondary">Bağlı</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right">
                                        <a href="{{ route('groups.edit', $group) }}" class="btn btn--ghost btn--sm">Redaktə</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    <div class="pagination__info">
                        {{ $groups->firstItem() }}-{{ $groups->lastItem() }} / {{ $groups->total() }}
                    </div>
                    {{ $groups->links('partials.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection
