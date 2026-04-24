@extends('layouts.app')

@section('title', 'Yeni müəllim')

@section('content')
    <div class="page-header">
        <div>
            <h1>Yeni müəllim</h1>
            <p>Müəllim əlavə et — növ seçəndə komissiya default təklif olunur</p>
        </div>
        <a href="{{ route('teachers.index') }}" class="btn btn--ghost">← Geri</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('teachers.store') }}">
            @csrf
            <div class="card__body">
                @include('teachers._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <a href="{{ route('teachers.index') }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>
@endsection
