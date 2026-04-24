@extends('layouts.app')

@section('title', 'Aylıq maliyyə hesabatı')

@section('content')
    <div class="page-header">
        <div>
            <h1>Aylıq maliyyə</h1>
            <p>{{ $from->format('d.m.Y') }} — {{ $to->format('d.m.Y') }} dövrü</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn--ghost">← Hesabatlar</a>
    </div>

    @include('reports._filter')

    <div class="grid-stats" style="grid-template-columns: repeat(4, 1fr)">
        <div class="card stat">
            <div class="stat__label">Ödəniş sayı</div>
            <div class="stat__value mt-2">{{ $totals['payments'] }}</div>
        </div>
        <div class="card stat">
            <div class="stat__label">Ümumi gəlir</div>
            <div class="stat__value mt-2">{{ number_format($totals['revenue'], 2) }} ₼</div>
        </div>
        <div class="card stat">
            <div class="stat__label">Müəllim komissiyası</div>
            <div class="stat__value mt-2">{{ number_format($totals['commission'], 2) }} ₼</div>
        </div>
        <div class="card stat">
            <div class="stat__label">Xalis</div>
            <div class="stat__value mt-2">{{ number_format($totals['net'], 2) }} ₼</div>
        </div>
    </div>

    <div class="card">
        <div class="card__body card__body--flush">
            @if ($rows->isEmpty())
                <div class="empty"><div class="empty__title">Bu dövrdə ödəniş yoxdur</div></div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ay</th>
                                <th>Ödəniş sayı</th>
                                <th>Gəlir</th>
                                <th>Müəllim komissiyası</th>
                                <th style="text-align:right">Xalis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    <td>{{ $row['month']->translatedFormat('F Y') }}</td>
                                    <td class="text-muted">{{ $row['payments_count'] }}</td>
                                    <td>{{ number_format($row['revenue'], 2) }} ₼</td>
                                    <td class="text-muted">{{ number_format($row['teacher_commission'], 2) }} ₼</td>
                                    <td style="text-align:right;font-weight:600">{{ number_format($row['net'], 2) }} ₼</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
