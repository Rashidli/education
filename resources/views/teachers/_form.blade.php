<div class="form-row">
    <div class="form-group">
        <label class="label label--required" for="name">Ad Soyad</label>
        <input type="text" id="name" name="name" class="input @error('name') is-invalid @enderror"
               value="{{ old('name', $teacher->name) }}" required>
        @error('name') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label class="label label--required" for="type">Növ</label>
        <select id="type" name="type" class="select @error('type') is-invalid @enderror"
                data-default-commission='@json($defaultCommissions)' required>
            <option value="local" @selected(old('type', $teacher->type) === 'local')>Yerli</option>
            <option value="foreign" @selected(old('type', $teacher->type) === 'foreign')>Xarici</option>
        </select>
        <span class="help">Növ dəyişəndə komissiya faizi default-a yenilənir</span>
        @error('type') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="label label--required" for="commission_rate">Komissiya faizi (%)</label>
        <input type="number" step="0.01" min="0" max="100" id="commission_rate" name="commission_rate"
               class="input @error('commission_rate') is-invalid @enderror"
               value="{{ old('commission_rate', $teacher->commission_rate) }}" required>
        @error('commission_rate') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label class="label" for="phone">Telefon</label>
        <input type="text" id="phone" name="phone" class="input @error('phone') is-invalid @enderror"
               value="{{ old('phone', $teacher->phone) }}" placeholder="+994...">
        @error('phone') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-group">
    <label class="label" for="email">Email</label>
    <input type="email" id="email" name="email" class="input @error('email') is-invalid @enderror"
           value="{{ old('email', $teacher->email) }}">
    @error('email') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label class="label" for="notes">Qeydlər</label>
    <textarea id="notes" name="notes" class="textarea @error('notes') is-invalid @enderror">{{ old('notes', $teacher->notes) }}</textarea>
    @error('notes') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label class="checkbox-wrap">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $teacher->is_active ?? true))>
        <span>Aktiv</span>
    </label>
</div>

@push('scripts')
<script>
    (function () {
        const typeSel = document.getElementById('type');
        const commissionInput = document.getElementById('commission_rate');
        if (!typeSel || !commissionInput) return;
        const defaults = JSON.parse(typeSel.dataset.defaultCommission || '{}');
        typeSel.addEventListener('change', () => {
            if (defaults[typeSel.value] !== undefined) {
                commissionInput.value = defaults[typeSel.value];
            }
        });
    })();
</script>
@endpush
