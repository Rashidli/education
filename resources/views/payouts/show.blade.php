@extends('layouts.app')

@section('title', $teacher->name.' — Ödənişlər')

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $teacher->name }} — payout</h1>
            <p>
                <span class="badge badge--{{ $teacher->type === 'foreign' ? 'info' : 'secondary' }}">{{ $teacher->typeLabel() }}</span>
                <span class="text-muted">Komissiya {{ rtrim(rtrim(number_format($teacher->commission_rate, 2), '0'), '.') }}%</span>
            </p>
        </div>
        <a href="{{ route('teachers.show', $teacher) }}" class="btn btn--ghost">← Müəllim</a>
    </div>

    @include('partials.flash')

    {{-- Ümumi balans --}}
    <div class="card">
        <div class="balance-bar">
            <div class="balance-bar__item">
                <div class="balance-bar__label">Ümumi qazanılıb</div>
                <div class="balance-bar__value">{{ number_format($balance['earned'], 2) }} ₼</div>
            </div>
            <div class="balance-bar__item">
                <div class="balance-bar__label">Ümumi ödənilib</div>
                <div class="balance-bar__value">{{ number_format($balance['paid'], 2) }} ₼</div>
            </div>
            <div class="balance-bar__item">
                <div class="balance-bar__label">
                    {{ $balance['balance'] > 0 ? 'Müəllimə borcluyuq' : ($balance['balance'] < 0 ? 'Artıq ödənib' : 'Tarazlıq') }}
                </div>
                <div class="balance-bar__value {{ $balance['balance'] > 0 ? 'balance-bar__value--owes' : ($balance['balance'] < 0 ? 'balance-bar__value--credit' : '') }}">
                    {{ number_format(abs($balance['balance']), 2) }} ₼
                </div>
            </div>
        </div>
    </div>

    {{-- Yeni ödəniş formu --}}
    @can('payouts.create')
    <div class="card mt-4">
        <div class="card__header">
            <div>
                <div class="card__title">Yeni ödəniş</div>
                <div class="card__description">Müəllimə verilən məbləği burada qeyd edin</div>
            </div>
        </div>
        <form method="POST" action="{{ route('teachers.payouts.store', $teacher) }}">
            @csrf
            <div class="card__body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="label label--required" for="amount">Məbləğ (₼)</label>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount"
                               class="input" value="{{ $balance['balance'] > 0 ? number_format($balance['balance'], 2, '.', '') : '' }}" required>
                        <span class="help">Təklif: qalıq balans ({{ number_format(max(0, $balance['balance']), 2) }} ₼)</span>
                    </div>
                    <div class="form-group">
                        <label class="label label--required" for="paid_at">Tarix</label>
                        <input type="date" id="paid_at" name="paid_at" class="input" value="{{ now()->toDateString() }}" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="label" for="method">Üsul</label>
                        <select id="method" name="method" class="select">
                            <option value="">—</option>
                            @foreach (['nağd', 'kart', 'köçürmə'] as $m)
                                <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="label" for="notes">Qeyd</label>
                        <input type="text" id="notes" name="notes" class="input" maxlength="500">
                    </div>
                </div>
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <button type="submit" class="btn btn--primary">Ödənişi qeyd et</button>
            </div>
        </form>
    </div>
    @endcan

    {{-- Dövr filter + breakdown --}}
    <div class="card mt-4">
        <div class="card__header">
            <div>
                <div class="card__title">Qazanc detalı</div>
                <div class="card__description">{{ $from->format('d.m.Y') }} — {{ $to->format('d.m.Y') }} dövründə hər tələbə ödənişindən müəllim payı</div>
            </div>
        </div>

        <form method="GET" class="filters" style="margin:0;border-radius:0;border-left:0;border-right:0">
            <label class="text-xs text-muted">Başlama:</label>
            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="input">
            <label class="text-xs text-muted">Son:</label>
            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="input">
            <button type="submit" class="btn btn--secondary btn--sm">Göstər</button>
        </form>

        <div class="balance-bar">
            <div class="balance-bar__item">
                <div class="balance-bar__label">Bu dövr qazanılıb</div>
                <div class="balance-bar__value">{{ number_format($periodBalance['earned'], 2) }} ₼</div>
            </div>
            <div class="balance-bar__item">
                <div class="balance-bar__label">Bu dövr ödənilib</div>
                <div class="balance-bar__value">{{ number_format($periodBalance['paid'], 2) }} ₼</div>
            </div>
            <div class="balance-bar__item">
                <div class="balance-bar__label">Dövr qalığı</div>
                <div class="balance-bar__value {{ $periodBalance['balance'] > 0 ? 'balance-bar__value--owes' : '' }}">
                    {{ number_format($periodBalance['balance'], 2) }} ₼
                </div>
            </div>
        </div>

        <div class="card__body card__body--flush">
            @if ($breakdown->isEmpty())
                <div class="empty"><div class="empty__title">Bu dövrdə ödəniş yoxdur</div></div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tarix</th>
                                <th>Tələbə</th>
                                <th>Qrup</th>
                                <th>Dövr</th>
                                <th>Ödəniş</th>
                                <th style="text-align:right">Müəllim payı</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($breakdown as $row)
                                <tr>
                                    <td class="text-muted">{{ $row['payment']->paid_at->format('d.m.Y') }}</td>
                                    <td>{{ $row['student']->full_name }}</td>
                                    <td class="text-muted">{{ $row['group']->name }}</td>
                                    <td class="text-muted">
                                        {{ $row['payment']->period_month->translatedFormat('F Y') }}
                                        @if ($row['payment']->is_prorata) <span class="badge badge--info">pro-rata</span> @endif
                                    </td>
                                    <td>{{ number_format($row['payment']->amount, 2) }} ₼</td>
                                    <td style="text-align:right;font-weight:600">{{ number_format($row['commission'], 2) }} ₼</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--muted);font-weight:600">
                                <td colspan="4">Cəmi</td>
                                <td>{{ number_format((float) $breakdown->sum(fn ($r) => (float) $r['payment']->amount), 2) }} ₼</td>
                                <td style="text-align:right">{{ number_format((float) $breakdown->sum('commission'), 2) }} ₼</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Ödəniş tarixçəsi --}}
    <div class="card mt-4">
        <div class="card__header">
            <div>
                <div class="card__title">Ödəniş tarixçəsi</div>
                <div class="card__description">Müəllimə verilmiş son 50 ödəniş</div>
            </div>
        </div>
        <div class="card__body card__body--flush">
            @if ($payouts->isEmpty())
                <div class="empty"><div class="empty__title">Hələ ödəniş qeydi yoxdur</div></div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tarix</th>
                                <th>Məbləğ</th>
                                <th>Üsul</th>
                                <th>Qeyd</th>
                                <th>Qeyd edən</th>
                                <th style="text-align:right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payouts as $payout)
                                <tr>
                                    <td>{{ $payout->paid_at->format('d.m.Y') }}</td>
                                    <td style="font-weight:600">{{ number_format($payout->amount, 2) }} ₼</td>
                                    <td class="text-muted">{{ $payout->method ?? '—' }}</td>
                                    <td class="text-muted">{{ $payout->notes ?? '—' }}</td>
                                    <td class="text-muted">{{ $payout->creator?->name ?? '—' }}</td>
                                    <td style="text-align:right">
                                        @can('payouts.delete')
                                            <form method="POST" action="{{ route('teachers.payouts.destroy', [$teacher, $payout]) }}"
                                                  onsubmit="return confirm('Ödənişi silmək istəyirsiniz?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn--ghost btn--sm">Sil</button>
                                            </form>
                                        @endcan
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
