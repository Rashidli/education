@extends('layouts.app')

@section('title', 'Zibil qutusu')

@section('content')
    <div class="page-header">
        <div>
            <h1>Zibil qutusu</h1>
            <p>Soft-delete olmuş datalar. Geri qaytarın və ya birdəfəlik silin.</p>
        </div>
    </div>

    @include('partials.flash')

    <div class="filters">
        @foreach ($types as $key => $cfg)
            <a href="{{ route('trash.index', ['type' => $key]) }}"
               class="btn btn--{{ $activeType === $key ? 'primary' : 'outline' }} btn--sm">
                {{ $cfg['label'] }}
                @if ($counts[$key] > 0)
                    <span class="badge badge--{{ $activeType === $key ? 'outline' : 'secondary' }}" style="margin-left:0.375rem">{{ $counts[$key] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="card">
        <div class="card__body card__body--flush">
            @if ($items->isEmpty())
                <div class="empty">
                    <div class="empty__icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                    </div>
                    <div class="empty__title">Silinmiş {{ mb_strtolower($config['label']) }} yoxdur</div>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Element</th>
                                <th>Silən istifadəçi</th>
                                <th>Silinmə tarixi</th>
                                <th style="text-align:right">Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>
                                        @switch($activeType)
                                            @case('students')
                                                <div class="font-medium">{{ $item->full_name }}</div>
                                                <div class="text-xs text-muted">{{ $item->phone ?? $item->email ?? '—' }}</div>
                                                @break

                                            @case('teachers')
                                                <div class="font-medium">{{ $item->name }}</div>
                                                <div class="text-xs text-muted">{{ $item->typeLabel() }} · {{ rtrim(rtrim(number_format($item->commission_rate, 2), '0'), '.') }}% komissiya</div>
                                                @break

                                            @case('groups')
                                                <div class="font-medium">{{ $item->name }}</div>
                                                <div class="text-xs text-muted">{{ number_format($item->monthly_price, 2) }} ₼/ay</div>
                                                @break

                                            @case('enrollments')
                                                <div class="font-medium">{{ $item->student?->full_name ?? '—' }} → {{ $item->group?->name ?? '—' }}</div>
                                                <div class="text-xs text-muted">Qoşuldu: {{ $item->joined_at?->format('d.m.Y') }}</div>
                                                @break

                                            @case('payments')
                                                <div class="font-medium">{{ number_format($item->amount, 2) }} ₼</div>
                                                <div class="text-xs text-muted">
                                                    {{ $item->enrollment?->student?->full_name ?? '—' }} ·
                                                    {{ $item->paid_at?->format('d.m.Y') }}
                                                </div>
                                                @break

                                            @case('users')
                                                <div class="font-medium">{{ $item->name }}</div>
                                                <div class="text-xs text-muted">{{ $item->email }}</div>
                                                @break

                                            @default
                                                #{{ $item->id }}
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($item->deleter)
                                            <div class="table__user">
                                                <div class="avatar avatar--sm">{{ mb_strtoupper(mb_substr($item->deleter->name, 0, 2)) }}</div>
                                                <div>
                                                    <div class="table__user-name">{{ $item->deleter->name }}</div>
                                                    <div class="table__user-email">{{ $item->deleter->email }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">naməlum</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $item->deleted_at?->format('d.m.Y H:i') }}</td>
                                    <td style="text-align:right">
                                        <div class="flex gap-2" style="justify-content:flex-end">
                                            <form method="POST" action="{{ route('trash.restore', ['type' => $activeType, 'id' => $item->id]) }}">
                                                @csrf
                                                <button type="submit" class="btn btn--outline btn--sm">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-15-6.7L3 13"/>
                                                    </svg>
                                                    Bərpa et
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('trash.forceDelete', ['type' => $activeType, 'id' => $item->id]) }}"
                                                  onsubmit="return confirm('BİRDƏFƏLİK silmək istəyirsiniz? Bu əməliyyat geri qaytarıla bilməz!')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn--destructive btn--sm">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                                    </svg>
                                                    Birdəfəlik
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    <div class="pagination__info">
                        {{ $items->firstItem() }}-{{ $items->lastItem() }} / {{ $items->total() }}
                    </div>
                    {{ $items->links('partials.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection
