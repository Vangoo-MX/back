@if ($paginator->hasPages())
<nav class="custom-pagination">
    {{-- Prev Page --}}
    @if ($paginator->onFirstPage())
    <span class="page-item disabled">«</span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="page-item">«</a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
    @if (is_string($element))
    <span class="page-item disabled">{{ $element }}</span>
    @endif

    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <span class="page-item active">{{ $page }}</span>
    @else
    <a href="{{ $url }}" class="page-item">{{ $page }}</a>
    @endif
    @endforeach
    @endif
    @endforeach

    {{-- Next Page --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="page-item">»</a>
    @else
    <span class="page-item disabled">»</span>
    @endif
</nav>
@endif
