@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <span class="is-disabled">←</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev">←</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="is-disabled">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="is-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next">→</a>
    @else
        <span class="is-disabled">→</span>
    @endif
@endif
