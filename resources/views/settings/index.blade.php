@extends('layouts.app')

@section('title', 'Parametrlər')

@section('content')
    <div class="page-header">
        <div>
            <h1>Parametrlər</h1>
            <p>Sistemin default dəyərləri — yeni qrup/müəllim/enrollment yaradılanda tətbiq olunur</p>
        </div>
    </div>

    @include('partials.flash')

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        @foreach ($groups as $groupKey => $items)
            <div class="card mt-4">
                <div class="card__header">
                    <div>
                        <div class="card__title">{{ $groupLabels[$groupKey] ?? ucfirst($groupKey) }}</div>
                    </div>
                </div>
                <div class="card__body">
                    @foreach ($items as $setting)
                        <div class="form-group">
                            <label class="label" for="s-{{ $setting->key }}">{{ $setting->label ?? $setting->key }}</label>
                            <input type="number" step="0.01" min="0" id="s-{{ $setting->key }}"
                                   name="settings[{{ $setting->key }}]" class="input" style="max-width:300px"
                                   value="{{ $setting->value }}">
                            @if ($setting->description)
                                <span class="help">{{ $setting->description }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="mt-4 flex gap-2" style="justify-content:flex-end">
            <button type="submit" class="btn btn--primary">Yadda saxla</button>
        </div>
    </form>
@endsection
