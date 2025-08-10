@if ($paginator->hasPages())
<nav class="card" role="navigation" aria-label="Pagination" style="padding:10px; display:flex; gap:8px; justify-content:center; margin-top: 2rem;">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span class="btn" aria-disabled="true" style="opacity: 0.6;">Précédent</span>
    @else
        <a class="btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Précédent</a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <span class="btn" aria-disabled="true" style="opacity: 0.6;">{{ $element }}</span>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="btn btn-primary" aria-current="page">{{ $page }}</span>
                @else
                    <a class="btn" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a class="btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant</a>
    @else
        <span class="btn" aria-disabled="true" style="opacity: 0.6;">Suivant</span>
    @endif
</nav>
@endif
