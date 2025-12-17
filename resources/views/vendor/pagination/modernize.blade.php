@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination">
  <ul class="pagination pagination-sm justify-content-center gap-1 mb-0">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
      <li class="page-item disabled">
        <span class="page-link"><i class="ti ti-chevron-left"></i></span>
      </li>
    @else
      <li class="page-item">
        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
          <i class="ti ti-chevron-left"></i>
        </a>
      </li>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
          @else
            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
      <li class="page-item">
        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
          <i class="ti ti-chevron-right"></i>
        </a>
      </li>
    @else
      <li class="page-item disabled">
        <span class="page-link"><i class="ti ti-chevron-right"></i></span>
      </li>
    @endif
  </ul>
</nav>
@endif
