@extends('layouts.app')

@section('title', 'Ödənişlər')

@section('content')
    <div class="page-header">
        <div>
            <h1>Ödənişlər</h1>
            <p>Bütün daxil olmuş ödənişlər, filter ilə</p>
        </div>
        <a href="{{ route('payments.create') }}" class="btn btn--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="M12 5v14"/>
            </svg>
            Yeni ödəniş
        </a>
    </div>

    @include('partials.flash')

    <form method="GET" class="filters">
        <select name="teacher_id" class="select">
            <option value="">Bütün müəllimlər</option>
            @foreach ($teachers as $t)
                <option value="{{ $t->id }}" @selected(($filters['teacher_id'] ?? null) == $t->id)>{{ $t->name }}</option>
            @endforeach
        </select>
        <select name="group_id" class="select">
            <option value="">Bütün qruplar</option>
            @foreach ($groups as $g)
                <option value="{{ $g->id }}" @selected(($filters['group_id'] ?? null) == $g->id)>{{ $g->name }}</option>
            @endforeach
        </select>
        <input type="month" name="month" value="{{ $filters['month'] ?? '' }}" class="input" placeholder="Dövr">
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input" placeholder="Başlama">
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input" placeholder="Son">
        <button type="submit" class="btn btn--secondary btn--sm">Filter</button>
        @if (array_filter($filters))
            <a href="{{ route('payments.index') }}" class="btn btn--ghost btn--sm">Təmizlə</a>
        @endif
    </form>

    @if (array_filter($filters))
        <div class="alert alert--info" style="justify-content:space-between">
            <div>
                <strong>Filter nəticəsi:</strong> {{ $payments->total() }} ödəniş,
                ümumi məbləğ <strong>{{ number_format($total, 2) }} ₼</strong>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card__body card__body--flush">
            @if ($payments->isEmpty())
                <div class="empty">
                    <div class="empty__title">Ödəniş yoxdur</div>
                    <div>Filter parametrlərini dəyişin və ya yeni ödəniş əlavə edin.</div>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tarix</th>
                                <th>Tələbə</th>
                                <th>Qrup / Müəllim</th>
                                <th>Dövr</th>
                                <th>Üsul</th>
                                <th style="text-align:right">Məbləğ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="text-muted">{{ $payment->paid_at->format('d.m.Y') }}</td>
                                    <td>
                                        <a href="{{ route('students.show', $payment->enrollment->student) }}">{{ $payment->enrollment->student->full_name }}</a>
                                    </td>
                                    <td>
                                        <div>{{ $payment->enrollment->group->name }}</div>
                                        <div class="text-xs text-muted">{{ $payment->enrollment->group->teacher->name }}</div>
                                    </td>
                                    <td>
                                        {{ $payment->period_month->translatedFormat('F Y') }}
                                        @if ($payment->is_prorata) <span class="badge badge--info">pro-rata</span> @endif
                                    </td>
                                    <td class="text-muted">{{ $payment->method ?? '—' }}</td>
                                    <td style="text-align:right;font-weight:600">{{ number_format($payment->amount, 2) }} ₼</td>
                                    <td style="text-align:right">
                                        <form method="POST" action="{{ route('payments.destroy', $payment) }}"
                                              onsubmit="return confirm('Ödənişi silmək istəyirsiniz? Next due date yenidən hesablanacaq.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn--ghost btn--sm">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    <div class="pagination__info">
                        {{ $payments->firstItem() }}-{{ $payments->lastItem() }} / {{ $payments->total() }}
                    </div>
                    {{ $payments->links('partials.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection
