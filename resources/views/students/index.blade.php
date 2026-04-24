@extends('layouts.app')

@section('title', 'Tələbələr')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tələbələr</h1>
            <p>Bütün tələbələr və qrup sayı</p>
        </div>
        <a href="{{ route('students.create') }}" class="btn btn--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="M12 5v14"/>
            </svg>
            Yeni tələbə
        </a>
    </div>

    @include('partials.flash')

    <form method="GET" class="filters">
        <input type="search" name="search" value="{{ $search }}" placeholder="Ad, telefon, email..." class="input" style="flex:1;min-width:200px">
        <select name="status" class="select">
            <option value="">Bütün statuslar</option>
            <option value="active" @selected($status === 'active')>Aktiv</option>
            <option value="inactive" @selected($status === 'inactive')>Passiv</option>
        </select>
        <button type="submit" class="btn btn--secondary btn--sm">Filter</button>
        @if ($search || $status)
            <a href="{{ route('students.index') }}" class="btn btn--ghost btn--sm">Təmizlə</a>
        @endif
    </form>

    <div class="card">
        <div class="card__body card__body--flush">
            @if ($students->isEmpty())
                <div class="empty">
                    <div class="empty__title">Tələbə tapılmadı</div>
                    <div>İlk tələbəni əlavə etmək üçün yuxarıdakı düyməyə basın.</div>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tələbə</th>
                                <th>Telefon</th>
                                <th>Qrup sayı</th>
                                <th>Status</th>
                                <th style="text-align:right">Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr>
                                    <td>
                                        <div class="table__user">
                                            <div class="avatar avatar--sm">{{ $student->initials() }}</div>
                                            <div>
                                                <div class="table__user-name">
                                                    <a href="{{ route('students.show', $student) }}">{{ $student->full_name }}</a>
                                                </div>
                                                <div class="table__user-email">{{ $student->email ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $student->phone ?? '—' }}</td>
                                    <td class="text-muted">{{ $student->active_enrollments_count }}</td>
                                    <td>
                                        @if ($student->is_active)
                                            <span class="badge badge--success">Aktiv</span>
                                        @else
                                            <span class="badge badge--secondary">Passiv</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right">
                                        <a href="{{ route('students.edit', $student) }}" class="btn btn--ghost btn--sm">Redaktə</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    <div class="pagination__info">
                        {{ $students->firstItem() }}-{{ $students->lastItem() }} / {{ $students->total() }}
                    </div>
                    {{ $students->links('partials.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection
