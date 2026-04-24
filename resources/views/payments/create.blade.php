@extends('layouts.app')

@section('title', 'Yeni ödəniş')

@section('content')
    <div class="page-header">
        <div>
            <h1>Yeni ödəniş</h1>
            <p>Tələbə-qrup seçin, məbləğ və dövr təklif olunur</p>
        </div>
        <a href="{{ route('payments.index') }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    <div class="card">
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            <div class="card__body">
                <div class="form-group">
                    <label class="label label--required" for="enrollmentSearch">Tələbə və qrup</label>
                    <div class="combobox" id="enrollCombo">
                        <input type="hidden" name="enrollment_id" id="enrollment_id" value="{{ old('enrollment_id', $enrollment?->id) }}" required>
                        <input type="text" id="enrollmentSearch" class="combobox__input @error('enrollment_id') is-invalid @enderror"
                               placeholder="Tələbə adı və ya qrup axtarın..."
                               value="{{ $enrollment ? $enrollment->student->full_name.' — '.$enrollment->group->name : '' }}"
                               autocomplete="off">
                        <span class="combobox__caret">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </span>
                        <div class="combobox__list" id="enrollList">
                            @foreach ($enrollments as $e)
                                <div class="combobox__item"
                                     data-id="{{ $e->id }}"
                                     data-label="{{ $e->student->full_name }} — {{ $e->group->name }}"
                                     data-search="{{ mb_strtolower($e->student->full_name . ' ' . $e->group->name) }}"
                                     data-price="{{ $e->group->monthly_price }}"
                                     data-first="{{ $e->first_month_amount }}"
                                     data-joined="{{ $e->joined_at->format('Y-m-d') }}">
                                    <div>{{ $e->student->full_name }}</div>
                                    <div class="combobox__item-meta">
                                        {{ $e->group->name }} · {{ number_format($e->group->monthly_price, 0) }} ₼/ay
                                    </div>
                                </div>
                            @endforeach
                            <div class="combobox__empty" id="enrollEmpty" style="display:none">Heç nə tapılmadı</div>
                        </div>
                    </div>
                    @error('enrollment_id') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="label label--required" for="amount">Məbləğ (₼)</label>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount"
                               class="input @error('amount') is-invalid @enderror"
                               value="{{ old('amount', $suggestedAmount ?: '') }}" required>
                        <span class="help">Qismən ödənilə bilər — komissiya real məbləğdən hesablanır</span>
                        @error('amount') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label label--required" for="paid_at">Ödəniş tarixi</label>
                        <input type="date" id="paid_at" name="paid_at"
                               class="input @error('paid_at') is-invalid @enderror"
                               value="{{ old('paid_at', now()->toDateString()) }}" required>
                        <span class="help">Növbəti ödəniş tarixi bu tarixə + 30 gün təyin ediləcək</span>
                        @error('paid_at') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="label label--required" for="period_month">Hansı ay üçün</label>
                        <input type="month" id="period_month" name="period_month"
                               class="input @error('period_month') is-invalid @enderror"
                               value="{{ old('period_month', $suggestedPeriod->format('Y-m')) }}" required>
                        @error('period_month') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label" for="method">Ödəniş üsulu</label>
                        <select id="method" name="method" class="select">
                            <option value="">—</option>
                            @foreach (['nağd', 'kart', 'köçürmə'] as $m)
                                <option value="{{ $m }}" @selected(old('method') === $m)>{{ ucfirst($m) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label" for="notes">Qeyd</label>
                    <input type="text" id="notes" name="notes" class="input" value="{{ old('notes') }}" maxlength="500">
                </div>
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <a href="{{ route('payments.index') }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Ödənişi qeyd et</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        (function () {
            const combo = document.getElementById('enrollCombo');
            const search = document.getElementById('enrollmentSearch');
            const hidden = document.getElementById('enrollment_id');
            const list = document.getElementById('enrollList');
            const empty = document.getElementById('enrollEmpty');
            const amount = document.getElementById('amount');
            const period = document.getElementById('period_month');
            const items = [...list.querySelectorAll('.combobox__item')];
            let focusIdx = -1;

            function open() { combo.classList.add('is-open'); }
            function close() { combo.classList.remove('is-open'); focusIdx = -1; updateFocus(); }

            function filter() {
                const q = search.value.toLowerCase().trim();
                let visible = 0;
                items.forEach(it => {
                    const match = !q || it.dataset.search.includes(q);
                    it.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                empty.style.display = visible === 0 ? 'block' : 'none';
                focusIdx = -1;
                updateFocus();
            }

            function updateFocus() {
                items.forEach((it, i) => it.classList.toggle('is-focus', i === focusIdx));
            }

            function select(item) {
                hidden.value = item.dataset.id;
                search.value = item.dataset.label;
                close();
                updateSuggestedAmount(item);
            }

            function updateSuggestedAmount(item) {
                if (!item) return;
                const price = parseFloat(item.dataset.price || 0);
                const firstMonth = parseFloat(item.dataset.first || price);
                const joined = new Date(item.dataset.joined);
                const selectedPeriod = period.value ? new Date(period.value + '-01') : null;

                const isJoinMonth = selectedPeriod
                    && selectedPeriod.getFullYear() === joined.getFullYear()
                    && selectedPeriod.getMonth() === joined.getMonth()
                    && joined.getDate() !== 1;

                amount.value = (isJoinMonth ? firstMonth : price).toFixed(2);
            }

            search.addEventListener('focus', open);
            search.addEventListener('input', () => { filter(); open(); });

            search.addEventListener('keydown', (e) => {
                const visible = items.filter(it => it.style.display !== 'none');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    focusIdx = Math.min(focusIdx + 1, visible.length - 1);
                    items.forEach(it => it.classList.remove('is-focus'));
                    visible[focusIdx]?.classList.add('is-focus');
                    visible[focusIdx]?.scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    focusIdx = Math.max(focusIdx - 1, 0);
                    items.forEach(it => it.classList.remove('is-focus'));
                    visible[focusIdx]?.classList.add('is-focus');
                } else if (e.key === 'Enter' && visible[focusIdx]) {
                    e.preventDefault();
                    select(visible[focusIdx]);
                } else if (e.key === 'Escape') {
                    close();
                }
            });

            items.forEach(it => {
                it.addEventListener('mousedown', (e) => { e.preventDefault(); select(it); });
            });

            document.addEventListener('click', (e) => {
                if (!combo.contains(e.target)) close();
            });

            period.addEventListener('change', () => {
                const current = items.find(it => it.dataset.id === hidden.value);
                if (current) updateSuggestedAmount(current);
            });
        })();
    </script>
    @endpush
@endsection
