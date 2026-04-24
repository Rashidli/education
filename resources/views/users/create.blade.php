@extends('layouts.app')

@section('title', 'Yeni istifadəçi')

@section('content')
    <div class="page-header">
        <div>
            <h1>Yeni istifadəçi</h1>
            <p>Email və şifrə ilə admin panelə giriş</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    <div class="card">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="card__body">
                @include('users._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <a href="{{ route('users.index') }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>
@endsection
