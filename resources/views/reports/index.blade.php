@extends('layouts.app')

@section('title', 'Hesabatlar')

@section('content')
    <div class="page-header">
        <div>
            <h1>Hesabatlar</h1>
            <p>Maliyyə, müəllim qazancı və tələbə ödəniş hesabatları</p>
        </div>
    </div>

    <div class="grid-showcase">
        <a href="{{ route('reports.monthly') }}" class="card" style="text-decoration:none;transition:transform 0.15s;display:block">
            <div class="card__body">
                <div class="flex items-center gap-3 mb-2">
                    <div class="stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg></div>
                    <div>
                        <div class="font-semibold">Aylıq maliyyə</div>
                        <div class="text-xs text-muted">Daxil olma, komissiya, xalis gəlir</div>
                    </div>
                </div>
                <div class="text-sm text-muted">Aylar üzrə — hər ayın toplam gəliri, ümumi müəllim komissiyası və xalis mənfəəti.</div>
            </div>
        </a>

        <a href="{{ route('reports.teachers') }}" class="card" style="text-decoration:none;transition:transform 0.15s;display:block">
            <div class="card__body">
                <div class="flex items-center gap-3 mb-2">
                    <div class="stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg></div>
                    <div>
                        <div class="font-semibold">Müəllim qazancı</div>
                        <div class="text-xs text-muted">Dövr üzrə komissiya hesablaması</div>
                    </div>
                </div>
                <div class="text-sm text-muted">Hər müəllim üçün seçilən dövrdəki real daxil olmuş ödənişlərdən komissiya.</div>
            </div>
        </a>
    </div>

    <div class="mt-4">
        <a href="{{ route('reports.students') }}" class="card" style="text-decoration:none;display:block">
            <div class="card__body">
                <div class="flex items-center gap-3 mb-2">
                    <div class="stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                    <div>
                        <div class="font-semibold">Tələbə ödənişləri</div>
                        <div class="text-xs text-muted">Tələbə üzrə ödənilib statusu</div>
                    </div>
                </div>
                <div class="text-sm text-muted">Tələbələrin seçilən dövrdəki ümumi ödəniş cəmi, qrup üzrə filter.</div>
            </div>
        </a>
    </div>
@endsection
