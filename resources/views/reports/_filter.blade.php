@php $exportable = $exportable ?? true; @endphp
<form method="GET" class="filters">
    <label class="text-xs text-muted" style="white-space:nowrap">Başlama:</label>
    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="input">
    <label class="text-xs text-muted" style="white-space:nowrap">Son:</label>
    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="input">

    @isset($groupFilter)
        <select name="group_id" class="select">
            <option value="">Bütün qruplar</option>
            @foreach ($groupFilter as $g)
                <option value="{{ $g->id }}" @selected(($groupId ?? null) == $g->id)>{{ $g->name }}</option>
            @endforeach
        </select>
    @endisset

    <button type="submit" class="btn btn--secondary btn--sm">Göstər</button>

    @if ($exportable)
        @can('reports.export')
            <button type="submit" name="export" value="csv" class="btn btn--outline btn--sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>
                </svg>
                CSV
            </button>
            <button type="submit" name="export" value="pdf" class="btn btn--outline btn--sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>
                </svg>
                PDF
            </button>
        @endcan
    @endif
</form>
