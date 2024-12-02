@if ($paginator->hasPages())
    <ul>
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class=" disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="" aria-hidden="true">&lsaquo;</span>
            </li>
        @else
            <li class="">
                <a class="" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">Prev</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class=" disabled" aria-disabled="true"><span class="">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active" aria-current="page"><a>{{ $page }}</a></li>
                    @else
                        <li class=""><a class="" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="">
                <a class="" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">Next</a>
            </li>
        @else
            <li class=" disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="" aria-hidden="true">Next</span>
            </li>
        @endif
    </ul>
@endif
