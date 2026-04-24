<div class="form-row">
    <div class="form-group">
        <label class="label label--required" for="full_name">Ad Soyad</label>
        <input type="text" id="full_name" name="full_name" class="input @error('full_name') is-invalid @enderror"
               value="{{ old('full_name', $student->full_name) }}" required>
        @error('full_name') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label class="label" for="phone">Telefon</label>
        <input type="text" id="phone" name="phone" class="input @error('phone') is-invalid @enderror"
               value="{{ old('phone', $student->phone) }}" placeholder="+994...">
        @error('phone') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="label" for="email">Email</label>
        <input type="email" id="email" name="email" class="input @error('email') is-invalid @enderror"
               value="{{ old('email', $student->email) }}">
        @error('email') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label class="label" for="birth_date">Doğum tarixi</label>
        <input type="date" id="birth_date" name="birth_date" class="input @error('birth_date') is-invalid @enderror"
               value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}">
        @error('birth_date') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-group">
    <label class="label" for="notes">Qeydlər</label>
    <textarea id="notes" name="notes" class="textarea @error('notes') is-invalid @enderror">{{ old('notes', $student->notes) }}</textarea>
</div>

<div class="form-group">
    <label class="checkbox-wrap">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $student->is_active ?? true))>
        <span>Aktiv</span>
    </label>
</div>
