@if ($paginator->hasPages())
<nav class="custom-pagination-nav" role="navigation" aria-label="Pagination Navigation">
    <ul class="custom-pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <li class="custom-page disabled" aria-disabled="true">
            <span>«</span>
        </li>
        @else
        <li class="custom-page">
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">«</a>
        </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
        <li class="custom-page disabled" aria-disabled="true">
            <span>{{ $element }}</span>
        </li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="custom-page active" aria-current="page">
            <span>{{ $page }}</span>
        </li>
        @else
        <li class="custom-page">
            <a href="{{ $url }}">{{ $page }}</a>
        </li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li class="custom-page">
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">»</a>
        </li>
        @else
        <li class="custom-page disabled" aria-disabled="true">
            <span>»</span>
        </li>
        @endif
    </ul>
</nav>
@endif
