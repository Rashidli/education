@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Təhsil mərkəzinin bu günkü vəziyyətinə baxın.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('payments.create') }}" class="btn btn--primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Yeni ödəniş
            </a>
        </div>
    </div>

    <div class="grid-stats">
        {{-- Aktiv tələbələr --}}
        <div class="card stat">
            <div class="stat__head">
                <span class="stat__label">Aktiv tələbələr</span>
                <div class="stat__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <div class="stat__value">{{ number_format($activeStudents) }}</div>
            <div class="stat__delta">
                <span class="stat__delta-text">{{ $activeGroups }} aktiv qrupda</span>
            </div>
        </div>

        {{-- Aylıq gəlir --}}
        <div class="card stat">
            <div class="stat__head">
                <span class="stat__label">Bu ay gəlir</span>
                <div class="stat__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
            </div>
            <div class="stat__value">{{ number_format($monthlyRevenue, 2) }} ₼</div>
            @if ($revenueChange !== null)
                <div class="stat__delta {{ $revenueChange >= 0 ? 'stat__delta--up' : 'stat__delta--down' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        @if ($revenueChange >= 0)
                            <path d="m6 9 6-6 6 6"/><path d="M12 3v18"/>
                        @else
                            <path d="M12 3v18"/><path d="m18 15-6 6-6-6"/>
                        @endif
                    </svg>
                    {{ abs($revenueChange) }}%
                    <span class="stat__delta-text">keçən aya nisbətən</span>
                </div>
            @else
                <div class="stat__delta">
                    <span class="stat__delta-text">keçən ay gəlir yox idi</span>
                </div>
            @endif
        </div>

        {{-- Müəllim qazancı --}}
        <div class="card stat">
            <div class="stat__head">
                <span class="stat__label">Müəllim qazancı (bu ay)</span>
                <div class="stat__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/>
                    </svg>
                </div>
            </div>
            <div class="stat__value">{{ number_format($teacherEarnings, 2) }} ₼</div>
            <div class="stat__delta">
                <span class="stat__delta-text">real daxil olmuş ödənişlərdən</span>
            </div>
        </div>

        {{-- Yaxınlaşan ödənişlər --}}
        <div class="card stat">
            <div class="stat__head">
                <span class="stat__label">Yaxınlaşan ödənişlər</span>
                <div class="stat__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                    </svg>
                </div>
            </div>
            <div class="stat__value">{{ $upcomingPayments->count() }}</div>
            <div class="stat__delta {{ $overdueCount > 0 ? 'stat__delta--down' : '' }}">
                @if ($overdueCount > 0)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 9v4"/><path d="M12 17h.01"/><circle cx="12" cy="12" r="10"/>
                    </svg>
                    {{ $overdueCount }} gecikmiş
                @else
                    <span class="stat__delta-text">{{ $upcomingWindow }} gün ərzində</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Main grid: upcoming + top groups --}}
    <div class="grid-main">
        <div class="card">
            <div class="card__header">
                <div>
                    <div class="card__title">Yaxınlaşan ödənişlər</div>
                    <div class="card__description">Növbəti {{ $upcomingWindow }} gün ərzində ödəniş günü çatan tələbələr</div>
                </div>
                <a href="{{ route('payments.index') }}" class="btn btn--ghost btn--sm">Hamısına bax</a>
            </div>
            <div class="card__body card__body--flush">
                @if ($upcomingPayments->isEmpty())
                    <div class="empty">
                        <div class="empty__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <div class="empty__title">Yaxınlaşan ödəniş yoxdur</div>
                        <div>Növbəti {{ $upcomingWindow }} gün üçün bütün tələbələr ödənişlidir.</div>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tələbə</th>
                                    <th>Qrup</th>
                                    <th>Müəllim</th>
                                    <th>Ödəniş tarixi</th>
                                    <th style="text-align:right">Qalır</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingPayments as $enrollment)
                                    @php $days = $enrollment->daysUntilDue(); @endphp
                                    <tr>
                                        <td>
                                            <div class="table__user">
                                                <div class="avatar avatar--sm">{{ $enrollment->student->initials() }}</div>
                                                <div>
                                                    <div class="table__user-name">{{ $enrollment->student->full_name }}</div>
                                                    <div class="table__user-email">{{ $enrollment->student->phone }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $enrollment->group->name }}</td>
                                        <td class="text-muted">{{ $enrollment->group->teacher->name }}</td>
                                        <td class="text-muted">{{ $enrollment->next_due_date->format('d.m.Y') }}</td>
                                        <td style="text-align:right">
                                            <span class="badge badge--{{ $days <= 2 ? 'destructive' : ($days <= 3 ? 'warning' : 'info') }}">
                                                {{ $days }} gün
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card__header">
                <div>
                    <div class="card__title">Populyar qruplar</div>
                    <div class="card__description">Ən çox tələbə</div>
                </div>
            </div>
            <div class="card__body card__body--flush">
                @foreach ($topGroups as $group)
                    @php
                        $maxCount = $topGroups->max('students_count') ?: 1;
                        $percent = (int) round(($group->students_count / $maxCount) * 100);
                    @endphp
                    <div class="list-item">
                        <div class="list-item__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                        </div>
                        <div class="list-item__body">
                            <div class="list-item__title">{{ $group->name }}</div>
                            <div class="list-item__meta">{{ $group->students_count }} tələbə · {{ number_format($group->monthly_price, 0) }} ₼/ay</div>
                            <div class="progress mt-2"><div class="progress__bar" style="width: {{ $percent }}%"></div></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Teacher earnings --}}
    <div class="card">
        <div class="card__header">
            <div>
                <div class="card__title">Müəllim qazancı</div>
                <div class="card__description">Bu ay üçün komissiya (real daxil olmuş ödənişlər × müəllim komissiya faizi)</div>
            </div>
            <a href="{{ route('teachers.index') }}" class="btn btn--ghost btn--sm">Bütün müəllimlər</a>
        </div>
        <div class="card__body card__body--flush">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Müəllim</th>
                            <th>Növ</th>
                            <th>Komissiya</th>
                            <th style="text-align:right">Bu ay qazanc</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teacherSummary as $row)
                            <tr>
                                <td>
                                    <div class="table__user">
                                        <div class="avatar avatar--sm">{{ mb_strtoupper(mb_substr($row['teacher']->name, 0, 2)) }}</div>
                                        <div>
                                            <div class="table__user-name">{{ $row['teacher']->name }}</div>
                                            <div class="table__user-email">{{ $row['teacher']->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge--{{ $row['teacher']->type === 'foreign' ? 'info' : 'secondary' }}">
                                        {{ $row['teacher']->typeLabel() }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ rtrim(rtrim(number_format($row['teacher']->commission_rate, 2), '0'), '.') }}%</td>
                                <td style="text-align:right;font-weight:600">{{ number_format($row['earnings'], 2) }} ₼</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
