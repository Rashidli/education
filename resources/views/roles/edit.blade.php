@extends('layouts.app')

@section('title', $role->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>Rol: {{ $role->name }}</h1>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    @php $canDelete = ! in_array($role->name, [\App\Models\User::ROLE_SUPER_ADMIN, \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER]); @endphp

    <div class="card">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')
            <div class="card__body">
                @include('roles._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                @if ($canDelete)
                    <button type="submit" form="deleteForm" class="btn btn--destructive" style="margin-right:auto"
                            onclick="return confirm('Rolu silmək istəyirsiniz?')">Sil</button>
                @endif
                <a href="{{ route('roles.index') }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>

    @if ($canDelete)
        <form id="deleteForm" method="POST" action="{{ route('roles.destroy', $role) }}" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endif
@endsection
