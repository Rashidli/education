@extends('layouts.app')

@section('title', $student->full_name)

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $student->full_name }}</h1>
            <p>
                @if ($student->phone) <span class="text-muted">{{ $student->phone }}</span> @endif
                @if ($student->email) <span class="text-muted"> · {{ $student->email }}</span> @endif
                @if (! $student->is_active) <span class="badge badge--secondary">Passiv</span> @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('students.index') }}" class="btn btn--ghost">← Geri</a>
            <a href="{{ route('students.edit', $student) }}" class="btn btn--outline">Redaktə</a>
            @if ($availableGroups->isNotEmpty())
                <button type="button" class="btn btn--primary" onclick="document.getElementById('enrollModal').classList.add('is-open')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"/><path d="M12 5v14"/>
                    </svg>
                    Qrupa qoş
                </button>
            @endif
        </div>
    </div>

    @include('partials.flash')

    @if ($student->enrollments->isNotEmpty())
        <div class="card mb-3" style="margin-bottom:1rem">
            <div class="balance-bar">
                <div class="balance-bar__item">
                    <div class="balance-bar__label">Gözlənilən</div>
                    <div class="balance-bar__value">{{ number_format($totalExpected, 2) }} ₼</div>
                </div>
                <div class="balance-bar__item">
                    <div class="balance-bar__label">Ödənilib</div>
                    <div class="balance-bar__value">{{ number_format($totalPaid, 2) }} ₼</div>
                </div>
                <div class="balance-bar__item">
                    <div class="balance-bar__label">
                        {{ $totalBalance > 0 ? 'Borc' : ($totalBalance < 0 ? 'Artıq ödəyib' : 'Tarazlıq') }}
                    </div>
                    <div class="balance-bar__value {{ $totalBalance > 0 ? 'balance-bar__value--owes' : ($totalBalance < 0 ? 'balance-bar__value--credit' : '') }}">
                        {{ number_format(abs($totalBalance), 2) }} ₼
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card__header">
            <div>
                <div class="card__title">Qruplar və ödəniş ledger-i</div>
                <div class="card__description">Hər ay üçün gözlənilən və ödənilmiş məbləğ. Rənglər statusu göstərir.</div>
            </div>
        </div>
        <div class="card__body card__body--flush">
            @if ($student->enrollments->isEmpty())
                <div class="empty">
                    <div class="empty__title">Qrupa qoşulmayıb</div>
                    @if (! $hasAnyActiveGroups)
                        <div>Sistemdə hələ aktiv qrup yoxdur. Əvvəlcə qrup yaradın.</div>
                        @can('groups.manage')
                            <a href="{{ route('groups.create') }}" class="btn btn--primary mt-3">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"/><path d="M12 5v14"/>
                                </svg>
                                Qrup yarat
                            </a>
                        @endcan
                    @else
                        <div>Tələbəni qrupa qoşmaq üçün aşağıdakı düyməyə basın.</div>
                        @can('enrollments.manage')
                            <button type="button" class="btn btn--primary mt-3" onclick="document.getElementById('enrollModal').classList.add('is-open')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"/><path d="M12 5v14"/>
                                </svg>
                                Qrupa qoş
                            </button>
                        @endcan
                    @endif
                </div>
            @else
                @foreach ($student->enrollments as $enrollment)
                    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border)">
                        <div class="flex items-center gap-3" style="justify-content:space-between;flex-wrap:wrap">
                            <div>
                                <div class="font-semibold">
                                    <a href="{{ route('groups.show', $enrollment->group) }}">{{ $enrollment->group->name }}</a>
                                    @if (! $enrollment->is_active) <span class="badge badge--secondary">Çıxıb</span> @endif
                                </div>
                                <div class="text-xs text-muted mt-1">
                                    {{ $enrollment->group->teacher->name }} ·
                                    {{ number_format($enrollment->group->monthly_price, 2) }} ₼/ay ·
                                    Qoşuldu: {{ $enrollment->joined_at->format('d.m.Y') }}
                                </div>
                            </div>
                            <div class="flex gap-2 items-center">
                                @if ($enrollment->is_active && $enrollment->next_due_date)
                                    @php $days = $enrollment->daysUntilDue(); @endphp
                                    <span class="text-xs text-muted">Növbəti: {{ $enrollment->next_due_date->format('d.m.Y') }}</span>
                                    <span class="badge badge--{{ $days < 0 ? 'destructive' : ($days <= 5 ? 'warning' : 'secondary') }}">
                                        {{ $days < 0 ? abs($days) . ' gün gecikdi' : $days . ' gün qalır' }}
                                    </span>
                                @endif
                                <a href="{{ route('payments.create', ['enrollment' => $enrollment->id]) }}" class="btn btn--primary btn--sm">Ödəniş</a>
                                @if ($enrollment->is_active)
                                    <form method="POST" action="{{ route('students.enrollments.destroy', [$student, $enrollment]) }}"
                                          onsubmit="return confirm('Tələbəni bu qrupdan çıxarmaq istəyirsiniz?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn--ghost btn--sm">Çıxar</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @php $ledger = $ledgers[$enrollment->id] ?? null; @endphp
                        @if ($ledger && $ledger['periods']->isNotEmpty())
                            <div class="mt-3" style="border-top:1px solid var(--border);margin-top:0.75rem;padding-top:0.75rem">
                                <div class="flex items-center gap-4" style="flex-wrap:wrap;margin-bottom:0.5rem">
                                    <span class="text-xs text-muted">
                                        Bu qrup üçün: <strong>{{ number_format($ledger['total_expected'], 2) }} ₼</strong> gözlənilib,
                                        <strong>{{ number_format($ledger['total_paid'], 2) }} ₼</strong> ödənilib,
                                        <strong style="color:{{ $ledger['balance'] > 0 ? 'var(--destructive)' : ($ledger['balance'] < 0 ? 'var(--success)' : 'var(--foreground)') }}">
                                            {{ $ledger['balance'] > 0 ? 'borc' : ($ledger['balance'] < 0 ? 'artıq' : 'tarazlıq') }}
                                            {{ number_format(abs($ledger['balance']), 2) }} ₼
                                        </strong>
                                    </span>
                                </div>
                            </div>
                            <div class="ledger-grid" style="padding:0.75rem 0">
                                @foreach ($ledger['periods'] as $p)
                                    <div class="ledger-cell ledger-cell--{{ $p['status'] }}">
                                        <div class="ledger-cell__status"></div>
                                        <div class="ledger-cell__month">
                                            {{ $p['month']->translatedFormat('F Y') }}
                                            @if ($p['is_prorata']) · pro-rata @endif
                                        </div>
                                        <div class="ledger-cell__expected">
                                            Gözlənilən: {{ number_format($p['expected'], 2) }} ₼
                                        </div>
                                        <div class="ledger-cell__paid">
                                            {{ number_format($p['paid'], 2) }} ₼
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($enrollment->payments->isNotEmpty())
                            <details style="margin-top:0.5rem">
                                <summary style="cursor:pointer;font-size:0.75rem;color:var(--muted-foreground)">Bütün ödənişləri göstər ({{ $enrollment->payments->count() }})</summary>
                                <div class="table-wrap mt-2">
                                    <table class="table" style="font-size:0.75rem">
                                        <thead>
                                            <tr>
                                                <th>Tarix</th>
                                                <th>Dövr</th>
                                                <th>Məbləğ</th>
                                                <th>Üsul</th>
                                                <th>Qeyd</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($enrollment->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->paid_at->format('d.m.Y') }}</td>
                                                    <td>
                                                        {{ $payment->period_month->translatedFormat('F Y') }}
                                                        @if ($payment->is_prorata) <span class="badge badge--info">pro-rata</span> @endif
                                                    </td>
                                                    <td>{{ number_format($payment->amount, 2) }} ₼</td>
                                                    <td class="text-muted">{{ $payment->method ?? '—' }}</td>
                                                    <td class="text-muted">{{ $payment->notes ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    @if ($availableGroups->isNotEmpty())
    <div class="modal-overlay" id="enrollModal">
        <div class="modal">
            <form method="POST" action="{{ route('students.enrollments.store', $student) }}">
                @csrf
                <div class="modal__header">
                    <div class="modal__title">Qrupa qoş</div>
                    <div class="modal__description">Qoşulma tarixi ayın 1-i deyilsə, ilk ay avtomatik pro-rata hesablanır</div>
                </div>
                <div class="modal__body">
                    <div class="form-group">
                        <label class="label label--required" for="group_id">Qrup</label>
                        <select id="group_id" name="group_id" class="select" required>
                            <option value="">Seçin...</option>
                            @foreach ($availableGroups as $g)
                                <option value="{{ $g->id }}" data-price="{{ $g->monthly_price }}">
                                    {{ $g->name }} — {{ $g->teacher->name }} ({{ number_format($g->monthly_price, 0) }} ₼)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="label label--required" for="joined_at">Qoşulma tarixi</label>
                        <input type="date" id="joined_at" name="joined_at" class="input" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div id="proRataPreview" class="alert alert--info" style="display:none">
                        <div>
                            <strong>Pro-rata hesablama:</strong>
                            <div id="proRataText"></div>
                        </div>
                    </div>
                </div>
                <div class="modal__footer">
                    <button type="button" class="btn btn--outline" onclick="document.getElementById('enrollModal').classList.remove('is-open')">İmtina</button>
                    <button type="submit" class="btn btn--primary">Qoş</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        (function () {
            const modal = document.getElementById('enrollModal');
            if (!modal) return;
            const groupSel = modal.querySelector('#group_id');
            const dateInput = modal.querySelector('#joined_at');
            const preview = document.getElementById('proRataPreview');
            const text = document.getElementById('proRataText');

            async function updatePreview() {
                if (!groupSel.value || !dateInput.value) {
                    preview.style.display = 'none';
                    return;
                }
                const url = new URL(@json(route('enrollments.preview')), window.location.origin);
                url.searchParams.set('group_id', groupSel.value);
                url.searchParams.set('joined_at', dateInput.value);
                const res = await fetch(url);
                if (!res.ok) return;
                const d = await res.json();
                if (d.is_prorata) {
                    text.innerHTML = `Ayın ${d.remaining_days}/${d.days_in_month} günü qaldı. İlk ay ödəniş: <strong>${d.amount.toFixed(2)} ₼</strong> (tam aylıq ${d.monthly_price.toFixed(2)} ₼ əvəzinə)`;
                } else {
                    text.innerHTML = `Tələbə ayın 1-də qoşulur, tam aylıq: <strong>${d.amount.toFixed(2)} ₼</strong>`;
                }
                preview.style.display = 'flex';
            }
            groupSel.addEventListener('change', updatePreview);
            dateInput.addEventListener('change', updatePreview);
        })();
    </script>
    @endpush
@endsection
