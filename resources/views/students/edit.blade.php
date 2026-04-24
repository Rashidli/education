@extends('layouts.app')

@section('title', $student->full_name)

@section('content')
    <div class="page-header">
        <div>
            <h1>Tələbəni redaktə et</h1>
            <p>{{ $student->full_name }}</p>
        </div>
        <a href="{{ route('students.show', $student) }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    <div class="card">
        <form method="POST" action="{{ route('students.update', $student) }}">
            @csrf
            @method('PUT')
            <div class="card__body">
                @include('students._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <button type="submit" form="deleteForm" class="btn btn--destructive" style="margin-right:auto"
                        onclick="return confirm('Tələbəni silməyə əminsiniz? (soft delete — data bərpa oluna bilər)')">Sil</button>
                <a href="{{ route('students.show', $student) }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>

    <form id="deleteForm" method="POST" action="{{ route('students.destroy', $student) }}" style="display:none">
        @csrf @method('DELETE')
    </form>
@endsection
