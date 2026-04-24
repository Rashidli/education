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
            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn--primary">Redaktə et</a>
        </div>
    </div>

    @include('partials.flash')

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
