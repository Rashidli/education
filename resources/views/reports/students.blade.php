@extends('layouts.app')

@section('title', 'Tələbə ödənişləri')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tələbə ödənişləri</h1>
            <p>{{ $from->format('d.m.Y') }} — {{ $to->format('d.m.Y') }} dövrü</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn--ghost">← Hesabatlar</a>
    </div>

    @php $groupFilter = $groups; @endphp
    @include('reports._filter')

    <div class="grid-stats" style="grid-template-columns: repeat(2, 1fr)">
        <div class="card stat">
            <div class="stat__label">Tələbə sayı</div>
            <div class="stat__value mt-2">{{ $rows->count() }}</div>
        </div>
        <div class="card stat">
            <div class="stat__label">Ümumi ödəniş</div>
            <div class="stat__value mt-2">{{ number_format($totals['paid'], 2) }} ₼</div>
        </div>
    </div>

    <div class="card">
        <div class="card__body card__body--flush">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tələbə</th>
                            <th>Telefon</th>
                            <th>Qruplar</th>
                            <th style="text-align:right">Ödənilib</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td>
                                    <div class="table__user">
                                        <div class="avatar avatar--sm">{{ $row['student']->initials() }}</div>
                                        <div class="table__user-name">{{ $row['student']->full_name }}</div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $row['student']->phone ?? '—' }}</td>
                                <td class="text-muted">
                                    {{ $row['enrollments']->pluck('group.name')->join(', ') }}
                                </td>
                                <td style="text-align:right;font-weight:600">{{ number_format($row['total_paid'], 2) }} ₼</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
