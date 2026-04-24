@extends('layouts.app')

@section('title', 'Yeni tələbə')

@section('content')
    <div class="page-header">
        <div>
            <h1>Yeni tələbə</h1>
            <p>Əlavə edildikdən sonra qrupa qoşa bilərsiniz</p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn--ghost">← Geri</a>
    </div>

    @include('partials.flash')

    <div class="card">
        <form method="POST" action="{{ route('students.store') }}">
            @csrf
            <div class="card__body">
                @include('students._form')
            </div>
            <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                <a href="{{ route('students.index') }}" class="btn btn--outline">İmtina</a>
                <button type="submit" class="btn btn--primary">Yadda saxla</button>
            </div>
        </form>
    </div>
@endsection
