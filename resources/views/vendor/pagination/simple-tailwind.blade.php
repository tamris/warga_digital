@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex justify-between items-center text-xs">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg shadow-2xs">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-2xs hover:bg-slate-50 hover:text-slate-900 transition">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg shadow-2xs hover:bg-slate-50 hover:text-slate-900 transition">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-3 py-1.5 font-semibold text-slate-400 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg shadow-2xs">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
