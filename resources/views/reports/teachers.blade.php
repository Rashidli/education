@extends('layouts.app')

@section('title', 'Müəllim qazancı')

@section('content')
    <div class="page-header">
        <div>
            <h1>Müəllim qazancı</h1>
            <p>{{ $from->format('d.m.Y') }} — {{ $to->format('d.m.Y') }} dövrü</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn--ghost">← Hesabatlar</a>
    </div>

    @include('reports._filter')

    <div class="grid-stats" style="grid-template-columns: repeat(2, 1fr)">
        <div class="card stat">
            <div class="stat__label">Ümumi daxilolma</div>
            <div class="stat__value mt-2">{{ number_format($totals['revenue'], 2) }} ₼</div>
        </div>
        <div class="card stat">
            <div class="stat__label">Müəllim qazancı cəmi</div>
            <div class="stat__value mt-2">{{ number_format($totals['earnings'], 2) }} ₼</div>
        </div>
    </div>

    <div class="card">
        <div class="card__body card__body--flush">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Müəllim</th>
                            <th>Növ</th>
                            <th>Ödəniş sayı</th>
                            <th>Gəlir</th>
                            <th>Komissiya %</th>
                            <th style="text-align:right">Qazanc</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td>
                                    <div class="table__user">
                                        <div class="avatar avatar--sm">{{ mb_strtoupper(mb_substr($row['teacher']->name, 0, 2)) }}</div>
                                        <div class="table__user-name">{{ $row['teacher']->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge--{{ $row['teacher']->type === 'foreign' ? 'info' : 'secondary' }}">
                                        {{ $row['teacher']->typeLabel() }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $row['payments_count'] }}</td>
                                <td>{{ number_format($row['revenue'], 2) }} ₼</td>
                                <td class="text-muted">{{ rtrim(rtrim(number_format($row['commission_rate'], 2), '0'), '.') }}%</td>
                                <td style="text-align:right;font-weight:600">{{ number_format($row['earnings'], 2) }} ₼</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
