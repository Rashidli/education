@extends('layouts.app')

@section('title', $teacher->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>Müəllimi redaktə et</h1>
            <p>{{ $teacher->name }}</p>
        </div>
        <a href="{{ route('teachers.show', $teacher) }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    <div class="card">
        <form method="POST" action="{{ route('teachers.update', $teacher) }}">
            @csrf
            @method('PUT')
            <div class="card__body">
                @include('teachers._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <button type="submit" form="deleteForm" class="btn btn--destructive" style="margin-right:auto"
                        onclick="return confirm('Müəllimi silməyə əminsiniz? (soft delete — data bərpa oluna bilər)')">Sil</button>
                <a href="{{ route('teachers.show', $teacher) }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>

    <form id="deleteForm" method="POST" action="{{ route('teachers.destroy', $teacher) }}" style="display:none">
        @csrf @method('DELETE')
    </form>
@endsection
