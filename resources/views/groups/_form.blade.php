<div class="form-row">
    <div class="form-group">
        <label class="label label--required" for="name">Qrup adı</label>
        <input type="text" id="name" name="name" class="input @error('name') is-invalid @enderror"
               value="{{ old('name', $group->name) }}" placeholder="məs. İngilis dili — B1" required>
        @error('name') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label class="label label--required" for="teacher_id">Müəllim</label>
        <select id="teacher_id" name="teacher_id" class="select @error('teacher_id') is-invalid @enderror"
                data-default-prices='@json($defaultPrices)'
                data-teacher-types='@json($teachers->pluck('type', 'id'))' required>
            <option value="">Seçin...</option>
            @foreach ($teachers as $t)
                <option value="{{ $t->id }}" @selected(old('teacher_id', $group->teacher_id) == $t->id)>
                    {{ $t->name }} ({{ $t->typeLabel() }})
                </option>
            @endforeach
        </select>
        <span class="help">Müəllim seçəndə aylıq qiymət default-a yenilənir</span>
        @error('teacher_id') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="label label--required" for="monthly_price">Aylıq qiymət (₼)</label>
        <input type="number" step="0.01" min="0" id="monthly_price" name="monthly_price"
               class="input @error('monthly_price') is-invalid @enderror"
               value="{{ old('monthly_price', $group->monthly_price) }}" required>
        @error('monthly_price') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label class="label" for="starts_on">Başlama tarixi</label>
        <input type="date" id="starts_on" name="starts_on" class="input @error('starts_on') is-invalid @enderror"
               value="{{ old('starts_on', $group->starts_on?->format('Y-m-d')) }}">
        @error('starts_on') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-group">
    <label class="label" for="notes">Qeydlər</label>
    <textarea id="notes" name="notes" class="textarea @error('notes') is-invalid @enderror">{{ old('notes', $group->notes) }}</textarea>
    @error('notes') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label class="checkbox-wrap">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $group->is_active ?? true))>
        <span>Aktiv</span>
    </label>
</div>

@push('scripts')
<script>
    (function () {
        const teacherSel = document.getElementById('teacher_id');
        const priceInput = document.getElementById('monthly_price');
        if (!teacherSel || !priceInput) return;
        const prices = JSON.parse(teacherSel.dataset.defaultPrices || '{}');
        const types = JSON.parse(teacherSel.dataset.teacherTypes || '{}');
        teacherSel.addEventListener('change', () => {
            const type = types[teacherSel.value];
            if (type && prices[type] !== undefined && !priceInput.value) {
                priceInput.value = prices[type];
            }
        });
    })();
</script>
@endpush
