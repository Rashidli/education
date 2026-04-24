@extends('layouts.app')

@section('title', $user->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>İstifadəçi: {{ $user->name }}</h1>
            <p>{{ $user->email }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    <div class="card">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="card__body">
                @include('users._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                @if ($user->id !== auth()->id())
                    <button type="submit" form="deleteForm" class="btn btn--destructive" style="margin-right:auto"
                            onclick="return confirm('İstifadəçini silməyə əminsiniz? (soft delete — data bərpa oluna bilər)')">Sil</button>
                @endif
                <a href="{{ route('users.index') }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>

    @if ($user->id !== auth()->id())
        <form id="deleteForm" method="POST" action="{{ route('users.destroy', $user) }}" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endif
@endsection
