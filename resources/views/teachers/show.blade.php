@extends('layouts.app')

@section('title', $teacher->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $teacher->name }}</h1>
            <p>
                <span class="badge badge--{{ $teacher->type === 'foreign' ? 'info' : 'secondary' }}">{{ $teacher->typeLabel() }}</span>
                <span class="text-muted">· Komissiya {{ rtrim(rtrim(number_format($teacher->commission_rate, 2), '0'), '.') }}%</span>
                @if (! $teacher->is_active)
                    <span class="badge badge--destructive">Passiv</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('teachers.index') }}" class="btn btn--ghost">← Geri</a>
            @can('payouts.view')
                <a href="{{ route('teachers.payouts', $teacher) }}" class="btn btn--outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    Ödənişlər / Balans
                </a>
            @endcan
            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn--primary">Redaktə et</a>
        </div>
    </div>

    @include('partials.flash')

    @can('payouts.view')
    <div class="card mb-3" style="margin-bottom:1rem">
        <div class="balance-bar">
            <div class="balance-bar__item">
                <div class="balance-bar__label">Ümumi qazanılıb</div>
                <div class="balance-bar__value">{{ number_format($balance['earned'], 2) }} ₼</div>
            </div>
            <div class="balance-bar__item">
                <div class="balance-bar__label">Ödənilib</div>
                <div class="balance-bar__value">{{ number_format($balance['paid'], 2) }} ₼</div>
            </div>
            <div class="balance-bar__item">
                <div class="balance-bar__label">
                    {{ $balance['balance'] > 0 ? 'Verilməli' : ($balance['balance'] < 0 ? 'Artıq verilib' : 'Tarazlıq') }}
                </div>
                <div class="balance-bar__value {{ $balance['balance'] > 0 ? 'balance-bar__value--owes' : ($balance['balance'] < 0 ? 'balance-bar__value--credit' : '') }}">
                    {{ number_format(abs($balance['balance']), 2) }} ₼
                </div>
            </div>
        </div>
    </div>
    @endcan

    <div class="grid-stats" style="grid-template-columns: repeat(3, 1fr)">
        <div class="card stat">
            <div class="stat__label">Bu ay qazanc</div>
            <div class="stat__value mt-2">{{ number_format($thisMonthEarnings, 2) }} ₼</div>
        </div>
        <div class="card stat">
            <div class="stat__label">Ümumi qazanc</div>
            <div class="stat__value mt-2">{{ number_format($totalEarnings, 2) }} ₼</div>
        </div>
        <div class="card stat">
            <div class="stat__label">Aktiv qruplar</div>
            <div class="stat__value mt-2">{{ $teacher->groups->where('is_active', true)->count() }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <div class="card__title">Əlaqə məlumatları</div>
            </div>
        </div>
        <div class="card__body">
            <div class="form-row">
                <div>
                    <div class="text-xs text-muted">Telefon</div>
                    <div>{{ $teacher->phone ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted">Email</div>
                    <div>{{ $teacher->email ?? '—' }}</div>
                </div>
            </div>
            @if ($teacher->notes)
                <div class="mt-3">
                    <div class="text-xs text-muted">Qeydlər</div>
                    <div>{{ $teacher->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card__header">
            <div>
                <div class="card__title">Qruplar</div>
                <div class="card__description">Bu müəllimin apardığı qruplar və aktiv tələbə sayı</div>
            </div>
        </div>
        <div class="card__body card__body--flush">
            @if ($teacher->groups->isEmpty())
                <div class="empty">
                    <div class="empty__title">Qrup yoxdur</div>
                    <div>Bu müəllimə hələ qrup təyin edilməyib.</div>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Qrup</th>
                                <th>Aylıq qiymət</th>
                                <th>Aktiv tələbə</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teacher->groups as $group)
                                <tr>
                                    <td><a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a></td>
                                    <td>{{ number_format($group->monthly_price, 2) }} ₼</td>
                                    <td>{{ $group->activeEnrollments->count() }}</td>
                                    <td>
                                        @if ($group->is_active)
                                            <span class="badge badge--success">Aktiv</span>
                                        @else
                                            <span class="badge badge--secondary">Bağlı</span>
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
