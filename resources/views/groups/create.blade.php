@extends('layouts.app')

@section('title', 'Yeni qrup')

@section('content')
    <div class="page-header">
        <div>
            <h1>Yeni qrup</h1>
            <p>Müəllim seçəndə aylıq qiymət default təklif olunur</p>
        </div>
        <a href="{{ route('groups.index') }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    <div class="card">
        <form method="POST" action="{{ route('groups.store') }}">
            @csrf
            <div class="card__body">
                @include('groups._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <a href="{{ route('groups.index') }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>
@endsection
