@extends('layouts.app')

@section('title', 'Yeni rol')

@section('content')
    <div class="page-header">
        <div>
            <h1>Yeni rol</h1>
            <p>Rol adı və icazələr</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    <div class="card">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="card__body">
                @include('roles._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <a href="{{ route('roles.index') }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>
@endsection
