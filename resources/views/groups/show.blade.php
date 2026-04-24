@extends('layouts.app')

@section('title', $group->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $group->name }}</h1>
            <p>
                <a href="{{ route('teachers.show', $group->teacher) }}">{{ $group->teacher->name }}</a>
                <span class="badge badge--{{ $group->teacher->type === 'foreign' ? 'info' : 'secondary' }}">{{ $group->teacher->typeLabel() }}</span>
                <span class="text-muted">· {{ number_format($group->monthly_price, 2) }} ₼ / ay</span>
                @if (! $group->is_active)
                    <span class="badge badge--destructive">Bağlı</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('groups.index') }}" class="btn btn--ghost">← Geri</a>
            <a href="{{ route('groups.edit', $group) }}" class="btn btn--primary">Redaktə et</a>
        </div>
    </div>

    @include('partials.flash')

    <div class="card">
        <div class="card__header">
            <div>
                <div class="card__title">Tələbələr</div>
                <div class="card__description">Bu qrupda qeydiyyatlı bütün tələbələr</div>
            </div>
        </div>
        <div class="card__body card__body--flush">
            @if ($group->enrollments->isEmpty())
                <div class="empty">
                    <div class="empty__title">Tələbə yoxdur</div>
                    <div>Tələbəni qrupa qoşmaq üçün tələbə səhifəsinə keçin.</div>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tələbə</th>
                                <th>Qoşulma tarixi</th>
                                <th>İlk ay məbləği</th>
                                <th>Növbəti ödəniş</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($group->enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="table__user">
                                            <div class="avatar avatar--sm">{{ $enrollment->student->initials() }}</div>
                                            <div>
                                                <div class="table__user-name">
                                                    <a href="{{ route('students.show', $enrollment->student) }}">{{ $enrollment->student->full_name }}</a>
                                                </div>
                                                <div class="table__user-email">{{ $enrollment->student->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $enrollment->joined_at->format('d.m.Y') }}</td>
                                    <td>{{ number_format($enrollment->first_month_amount, 2) }} ₼</td>
                                    <td>
                                        @if ($enrollment->next_due_date)
                                            @php $days = $enrollment->daysUntilDue(); @endphp
                                            {{ $enrollment->next_due_date->format('d.m.Y') }}
                                            @if ($days !== null)
                                                <span class="badge badge--{{ $days < 0 ? 'destructive' : ($days <= 5 ? 'warning' : 'secondary') }}" style="margin-left:0.25rem">
                                                    {{ $days < 0 ? abs($days) . ' gün gecikdi' : $days . ' gün' }}
                                                </span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if ($enrollment->is_active)
                                            <span class="badge badge--success">Aktiv</span>
                                        @else
                                            <span class="badge badge--secondary">Passiv</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
